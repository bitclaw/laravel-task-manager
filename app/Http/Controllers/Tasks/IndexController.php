<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $selectedProject = null;

        if ($request->filled('project')) {
            $selectedProject = Project::query()->find($request->integer('project'));
        }

        $selectedProjectId = $selectedProject?->id;

        $projects = Project::query()
            ->orderBy('name')
            ->get();

        $tasks = Task::query()
            ->with('project')
            ->when(
                $selectedProject,
                fn (Builder $query) => $query->whereBelongsTo($selectedProject)
            )
            ->orderBy('priority')
            ->orderBy('id')
            ->get();

        return view('welcome', [
            'projects' => $projects,
            'tasks' => $tasks,
            'selectedProjectId' => $selectedProjectId,
            'canReorderTasks' => $tasks->pluck('project_id')->unique()->count() === 1 && $tasks->isNotEmpty(),
        ]);
    }
}
