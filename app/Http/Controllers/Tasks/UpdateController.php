<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Support\Tasks\TaskPriorityManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class UpdateController extends Controller
{
    public function __invoke(
        UpdateTaskRequest $request,
        Task $task,
        TaskPriorityManager $priorityManager,
    ): RedirectResponse {
        $validated = $request->validated();
        $originalProjectId = $task->project_id;
        $newProjectId = $validated['project_id'] ?? null;

        DB::transaction(function () use ($task, $validated, $originalProjectId, $newProjectId, $priorityManager): void {
            $task->fill([
                'name' => $validated['name'],
            ]);

            if ($task->project_id === $newProjectId) {
                $task->save();

                return;
            }

            $task->project_id = $newProjectId;
            $task->priority = $priorityManager->nextPriorityForProject($newProjectId);
            $task->save();

            $priorityManager->normalizePriorities($originalProjectId);
        });

        return $this->redirectToIndex($newProjectId, 'Task updated.');
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
