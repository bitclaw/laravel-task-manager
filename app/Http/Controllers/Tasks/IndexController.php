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
            'canReorderTasks' => $tasks->pluck('project_id')->unique()->count() === 1 && $tasks->isNotEmpty(),
        ]);
    }
}
