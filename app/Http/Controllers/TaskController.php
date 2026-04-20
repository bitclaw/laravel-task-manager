<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $selectedProjectId = $request->integer('project');

        $projects = Project::query()
            ->orderBy('name')
            ->get();

        $tasks = Task::query()
            ->with('project')
            ->when(
                $selectedProjectId,
                fn (Builder $query) => $query->where('project_id', $selectedProjectId)
            )
            ->orderBy('priority')
            ->orderBy('id')
            ->get();

        return view('welcome', [
            'projects' => $projects,
            'tasks' => $tasks,
            'selectedProjectId' => $selectedProjectId,
        ]);
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $projectId = $validated['project_id'] ?? null;

        Task::create([
            'name' => $validated['name'],
            'project_id' => $projectId,
            'priority' => $this->nextPriorityForProject($projectId),
        ]);

        return $this->redirectToIndex($projectId, 'Task created.');
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $validated = $request->validated();
        $originalProjectId = $task->project_id;
        $newProjectId = $validated['project_id'] ?? null;

        DB::transaction(function () use ($task, $validated, $originalProjectId, $newProjectId): void {
            $task->fill([
                'name' => $validated['name'],
            ]);

            if ($task->project_id === $newProjectId) {
                $task->save();

                return;
            }

            $task->project_id = $newProjectId;
            $task->priority = $this->nextPriorityForProject($newProjectId);
            $task->save();

            $this->normalizePriorities($originalProjectId);
        });

        return $this->redirectToIndex($newProjectId, 'Task updated.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $projectId = $task->project_id;

        DB::transaction(function () use ($task, $projectId): void {
            $task->delete();
            $this->normalizePriorities($projectId);
        });

        return $this->redirectToIndex($projectId, 'Task deleted.');
    }

    private function nextPriorityForProject(?int $projectId): int
    {
        return (int) Task::query()
            ->where('project_id', $projectId)
            ->max('priority') + 1;
    }

    private function normalizePriorities(?int $projectId): void
    {
        Task::query()
            ->where('project_id', $projectId)
            ->orderBy('priority')
            ->orderBy('id')
            ->get()
            ->each(function (Task $task, int $index): void {
                $expectedPriority = $index + 1;

                if ($task->priority !== $expectedPriority) {
                    $task->updateQuietly(['priority' => $expectedPriority]);
                }
            });
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
