<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use App\Support\Tasks\TaskPriorityManager;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{
    public function __invoke(
        StoreTaskRequest $request,
        TaskPriorityManager $priorityManager,
    ): RedirectResponse {
        $validated = $request->validated();
        $projectId = $validated['project_id'] ?? null;

        Task::create([
            'name' => $validated['name'],
            'project_id' => $projectId,
            'priority' => $priorityManager->nextPriorityForProject($projectId),
        ]);

        return $this->redirectToIndex($projectId, 'Task created.');
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
