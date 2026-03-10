<?php

namespace App\Http\Controllers;

use App\Interfaces\IssueApiInterface;
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
            $issue = $this->issueService->findIssue((int) $searchId);

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
        ]);

        // TODO: sustituir por Auth::user() cuando esté implementada la autenticación (Fase 3)
        $storeCode = 'Tienda-001';
        $employeeId = 123;

        $newStatus = $request->input('action') === 'close' ? 'Closed' : 'Open';

        $this->issueService->updateIssue(
            $id,
            [
                'storeCode' => $storeCode,
                'status' => $newStatus,
                'comment' => $request->input('comment'),
                'updatedBy' => $employeeId,
            ]
        );

        $message = $newStatus === 'Closed'
            ? 'Incidencia #'.$id.' cerrada correctamente.'
            : 'Comentario añadido a la incidencia #'.$id.'.';

        return redirect()->route('issues.index')->with('success', $message);
    }
}
