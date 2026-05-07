<?php

namespace App\Http\Controllers;

use App\Contracts\IssueApiInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class IssueController extends Controller
{
    public function __construct(
        protected IssueApiInterface $issueService
    ) {}

    public function index(Request $request)
    {
        $issue = null;
        $issues = [];
        $showAll = $request->boolean('show_all');
        $searchId = $request->query('search_id');
        $userRole = $request->user()?->role?->name;
        $userStoreCode = $request->user()?->store_code;

        if ($showAll) {
            if ($userRole !== 'Dirección') {
                return redirect()->route('issues.index')->with('error', 'No tienes permisos para ver el listado completo.');
            }

            if (! $userStoreCode) {
                return redirect()->route('issues.index')->with('error', 'Tu tienda no tiene aún incidencias tramitadas.');
            }

            $allIssues = $this->issueService->getAllIssues($userStoreCode);

            usort($allIssues, function ($a, $b) {
                $dateA = $a->createdAt ? strtotime($a->createdAt) : 0;
                $dateB = $b->createdAt ? strtotime($b->createdAt) : 0;

                if ($dateA === $dateB) {
                    return $b->id <=> $a->id;
                }

                return $dateB <=> $dateA;
            });

            $isMobile = $this->isMobileDevice($request);
            $perPage = $isMobile ? 5 : 10;
            $currentPage = Paginator::resolveCurrentPage();
            $total = count($allIssues);
            $offset = ($currentPage - 1) * $perPage;
            $pageItems = array_slice($allIssues, $offset, $perPage);

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

            return view('issues.index', compact('issue', 'issues', 'showAll'));
        }

        if ($searchId) {
            $issue = $this->issueService->find((int) $searchId);

            if (! $issue) {
                return redirect()->route('issues.index')->with('error', 'Incidencia no encontrada.');
            }
        }

        return view('issues.index', compact('issue', 'issues', 'showAll'));
    }

    public function show(Request $request, $id)
    {
        $issue = $this->issueService->find((int) $id);

        if (! $issue) {
            return redirect()->route('issues.index')->with('error', 'Incidencia no encontrada.');
        }

        return view('issues.show', compact('issue'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'comment' => 'nullable|string|max:500',
            'action' => 'required|in:comment,close',
        ]);

        $issue = $this->issueService->find((int) $id);

        if (! $issue) {
            return redirect()->route('issues.index')->with('error', 'Incidencia no encontrada.');
        }

        // Validar que la incidencia pertenece a la tienda del usuario
        $userStoreCode = $request->user()?->store_code;
        if ($userStoreCode && $issue->storeCode && $issue->storeCode !== $userStoreCode) {
            return redirect()->route('issues.index')->with('error', 'No tienes acceso a esta incidencia.');
        }

        if ($issue->status === 'Closed') {
            return redirect()
                ->route('issues.index', ['search_id' => $id])
                ->with('error', 'La incidencia #'.$id.' ya esta cerrada y solo puede visualizarse.');
        }

        $employeeId = (int) $request->user()->employee_id;

        $newStatus = $request->input('action') === 'close' ? 'Closed' : $issue->status;
        $payload = [
            'status' => $newStatus,
            'storeCode' => $userStoreCode,
        ];

        $comment = $request->input('comment');
        if ($comment !== null && $comment !== '') {
            $payload['comment'] = $comment;
        }

        $updated = $this->issueService->updateIssue((int) $id, $payload, $employeeId);

        if (! $updated) {
            return redirect()
                ->route('issues.show', $id)
                ->with('error', 'No se pudo actualizar la incidencia #'.$id.'.');
        }

        $message = $newStatus === 'Closed'
            ? 'Incidencia #'.$id.' cerrada correctamente.'
            : 'Comentario añadido a la incidencia #'.$id.'.';

        return redirect()->route('issues.show', $id)->with('success', $message);
    }

    private function isMobileDevice(Request $request): bool
    {
        $userAgent = $request->userAgent() ?? '';
        $mobilePatterns = [
            '/mobile/i',
            '/android/i',
            '/iphone/i',
            '/ipad/i',
            '/ipod/i',
            '/windows phone/i',
            '/blackberry/i',
        ];

        foreach ($mobilePatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return true;
            }
        }

        return false;
    }
}
