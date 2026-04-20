<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Support\Tasks\TaskPriorityManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class DestroyController extends Controller
{
    public function __invoke(Task $task, TaskPriorityManager $priorityManager): RedirectResponse
    {
        $projectId = $task->project_id;

        DB::transaction(function () use ($task, $projectId, $priorityManager): void {
            $task->delete();
            $priorityManager->normalizePriorities($projectId);
        });

        return $this->redirectToIndex($projectId, 'Task deleted.');
    }

    private function redirectToIndex(?int $projectId, string $status): RedirectResponse
    {
        $parameters = [];

        if ($projectId !== null) {
            $parameters['project'] = $projectId;
        }

        return redirect()
            ->route('tasks.index', $parameters)
            ->with('status', $status);
    }
}
