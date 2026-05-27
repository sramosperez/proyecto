<?php

namespace App\Http\Controllers;

use App\Contracts\IssueApiInterface;
use App\Exceptions\ServiceUnavailableException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class IssueController extends Controller
{
    public function __construct(
        protected IssueApiInterface $issueService
    ) {}

    public function index(Request $request)
    {
        $issue = null;
        $issues = [];
        $showAll = false;
        $updatedByLabels = [];
        $searchId = $request->query('search_id');
        $userRole = $request->user()?->role?->name;

        if ($searchId) {
            try {
                $issue = $this->issueService->find((int) $searchId);
            } catch (ServiceUnavailableException) {
                return redirect()->route('issues.index')->with('error', $this->systemDownMessage());
            }

            if (! $issue) {
                return redirect()->route('issues.index')->with('error', 'Incidencia no encontrada.');
            }

            $issue = $this->maskIssueForRole($issue, $userRole);
        }

        return view('issues.index', compact('issue', 'issues', 'showAll', 'updatedByLabels'));
    }

    public function listStoreIssues(Request $request)
    {
        $issue = null;
        $issues = [];
        $showAll = true;
        $userRole = $request->user()?->role?->name;
        $userStoreCode = $request->user()?->store_code;

        if (! $userStoreCode) {
            return redirect()->route('issues.index')->with('error', 'Tu tienda no tiene aún incidencias tramitadas.');
        }

        try {
            $allIssues = $this->issueService->getAllIssues($userStoreCode);
        } catch (ServiceUnavailableException) {
            return redirect()->route('issues.index')->with('error', $this->systemDownMessage());
        }

        $allIssues = $this->maskIssuesForRole($allIssues, $userRole);
        $updatedByLabels = $this->buildUpdatedByLabels($allIssues);

        $allIssues = collect($allIssues)
            ->sortByDesc(function ($issue) {
                return $issue->createdAt ?: '';
            })
            ->values();

        $perPage = 5;
        $currentPage = max(1, (int) $request->query('page', 1));
        $total = $allIssues->count();
        $pageItems = $allIssues->forPage($currentPage, $perPage)->values();

        $issues = new LengthAwarePaginator(
            $pageItems,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('issues.index', compact('issue', 'issues', 'showAll', 'updatedByLabels'));
    }

    private function buildUpdatedByLabels(array $issues): array
    {
        $labels = [];
        $nameCache = [];

        foreach ($issues as $issue) {
            $employeeId = $issue->updatedBy ?? null;
            if ($employeeId === null) {
                $labels[$issue->id] = '—';

                continue;
            }

            if (! array_key_exists($employeeId, $nameCache)) {
                $nameCache[$employeeId] = User::query()
                    ->where('employee_id', $employeeId)
                    ->value('name');
            }

            $employeeName = trim((string) ($nameCache[$employeeId] ?? ''));

            $labels[$issue->id] = $employeeName !== ''
                ? $employeeName.' - '.$employeeId
                : (string) $employeeId;
        }

        return $labels;
    }

    public function show(Request $request, $id)
    {
        try {
            $issue = $this->issueService->find((int) $id);
        } catch (ServiceUnavailableException) {
            return redirect()->route('issues.index')->with('error', $this->systemDownMessage());
        }

        $userRole = $request->user()?->role?->name;

        if (! $issue) {
            return redirect()->route('issues.index')->with('error', 'Incidencia no encontrada.');
        }

        $issue = $this->maskIssueForRole($issue, $userRole);
        $returnTo = $this->safeReturnTo($request->query('return_to'));

        return view('issues.show', compact('issue', 'returnTo'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'comment' => 'nullable|string|max:500',
            'action' => 'required|in:comment,close',
            'return_to' => 'nullable|string',
        ]);

        $returnTo = $this->safeReturnTo($request->input('return_to'));

        try {
            $issue = $this->issueService->find((int) $id);
        } catch (ServiceUnavailableException) {
            return redirect()->route('issues.index')->with('error', $this->systemDownMessage());
        }

        if (! $issue) {
            return redirect()->route('issues.index')->with('error', 'Incidencia no encontrada.');
        }

        $userStoreCode = $request->user()?->store_code;
        if ($userStoreCode && $issue->storeCode && $issue->storeCode !== $userStoreCode) {
            return redirect()->route('issues.index')->with('error', 'No tienes acceso a esta incidencia.');
        }

        if ($issue->status === 'Closed') {
            $redirect = $returnTo
                ? redirect()->to($returnTo)
                : redirect()->route('issues.index', ['search_id' => $id]);

            return $redirect->with('error', 'La incidencia #'.$id.' ya esta cerrada y solo puede visualizarse.');
        }

        $employeeId = (int) $request->user()->employee_id;

        $newStatus = $request->input('action') === 'close' ? 'Closed' : $issue->status;
        $payload = [
            'status' => $newStatus,
            'storeCode' => $userStoreCode,
        ];

        $comment = $request->input('comment');
        if ($request->input('action') === 'comment') {
            $payload['comment'] = $comment ?? '';
        } elseif ($comment !== null && $comment !== '') {
            $payload['comment'] = $comment;
        }

        try {
            $updated = $this->issueService->updateIssue((int) $id, $payload, $employeeId);
        } catch (ServiceUnavailableException) {
            return redirect()->route('issues.index')->with('error', $this->systemDownMessage());
        }

        if (! $updated) {
            return redirect()
                ->route('issues.show', ['id' => $id, 'return_to' => $returnTo])
                ->with('error', 'No se pudo actualizar la incidencia #'.$id.'.');
        }

        return redirect()->route('issues.show', ['id' => $id, 'return_to' => $returnTo]);
    }

    private function safeReturnTo(?string $returnTo): ?string
    {
        if (! $returnTo) {
            return null;
        }

        $decoded = urldecode($returnTo);
        $parsed = parse_url($decoded);

        $path = $parsed['path'] ?? '';
        if (! str_starts_with($path, '/issues')) {
            return null;
        }

        return $decoded;
    }

    private function systemDownMessage(): string
    {
        return 'Sistema de incidencias caido temporalmente. Intentalo de nuevo mas tarde.';
    }

    private function maskIssueForRole(object $issue, ?string $role): object
    {
        if ($role === 'Dirección') {
            return $issue;
        }

        $maskedIssue = clone $issue;
        $maskedIssue->email = $this->maskEmail($issue->email ?? null);

        if ($role === 'Empleado') {
            $maskedIssue->surname = $this->maskSurname($issue->surname ?? null);
        }

        return $maskedIssue;
    }

    private function maskIssuesForRole(array $issues, ?string $role): array
    {
        $masked = [];

        foreach ($issues as $issue) {
            $masked[] = $this->maskIssueForRole($issue, $role);
        }

        return $masked;
    }

    private function maskSurname(?string $surname): string
    {
        $value = trim((string) $surname);
        if ($value === '') {
            return '—';
        }

        $parts = preg_split('/\s+/u', $value, -1, PREG_SPLIT_NO_EMPTY);
        if (! $parts) {
            return '—';
        }

        $maskedParts = [];

        foreach ($parts as $part) {
            $visible = \Illuminate\Support\Str::substr($part, 0, 2);
            $length = \Illuminate\Support\Str::length($part);

            if ($length <= 2) {
                $maskedParts[] = $part;

                continue;
            }

            $maskedParts[] = $visible.str_repeat('*', max($length - 2, 2));
        }

        return implode(' ', $maskedParts);
    }

    private function maskEmail(?string $email): string
    {
        if (! $email || ! str_contains($email, '@')) {
            return 'No disponible';
        }

        [$name, $domain] = explode('@', $email);

        return substr($name, 0, 2).'****@'.$domain;
    }
}
