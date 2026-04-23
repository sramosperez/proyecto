<?php

namespace App\Http\Controllers;

use App\Contracts\IssueApiInterface;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function __construct(
        protected IssueApiInterface $issueService
    ) {}

    public function index(Request $request)
    {
        $issue = null;
        $searchId = $request->query('search_id');

        if ($searchId) {
            $issue = $this->issueService->find((int) $searchId);

            if (! $issue) {
                return redirect()->route('issues.index')->with('error', 'Incidencia no encontrada.');
            }
        }

        return view('issues.index', compact('issue'));
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

        if ($issue->status === 'Closed') {
            return redirect()
                ->route('issues.index', ['search_id' => $id])
                ->with('error', 'La incidencia #'.$id.' ya esta cerrada y solo puede visualizarse.');
        }

        $employeeId = (int) $request->user()->employee_id;

        $newStatus = $request->input('action') === 'close' ? 'Closed' : $issue->status;
        $payload = [
            'status' => $newStatus,
        ];

        $comment = $request->input('comment');
        if ($comment !== null && $comment !== '') {
            $payload['comment'] = $comment;
        }

        $updated = $this->issueService->updateIssue((int) $id, $payload, $employeeId);

        if (! $updated) {
            return redirect()
                ->route('issues.index', ['search_id' => $id])
                ->with('error', 'No se pudo actualizar la incidencia #'.$id.'.');
        }

        $message = $newStatus === 'Closed'
            ? 'Incidencia #'.$id.' cerrada correctamente.'
            : 'Comentario añadido a la incidencia #'.$id.'.';

        return redirect()->route('issues.index', ['search_id' => $id])->with('success', $message);
    }
}
