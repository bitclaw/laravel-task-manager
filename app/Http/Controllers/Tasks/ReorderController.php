<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReorderTasksRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReorderController extends Controller
{
    public function __invoke(ReorderTasksRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $projectId = $validated['project_id'] ?? null;
        $taskIds = array_map('intval', $validated['task_ids']);

        $scopeTaskIds = Task::query()
            ->where('project_id', $projectId)
            ->orderBy('priority')
            ->orderBy('id')
            ->pluck('id')
            ->map(fn (int $id) => (int) $id)
            ->all();

        $submittedIds = $taskIds;
        sort($scopeTaskIds);
        sort($submittedIds);

        if ($scopeTaskIds !== $submittedIds) {
            throw ValidationException::withMessages([
                'task_ids' => 'The submitted task order does not match the current list scope.',
            ]);
        }

        DB::transaction(function () use ($taskIds): void {
            foreach ($taskIds as $index => $taskId) {
                Task::query()
                    ->whereKey($taskId)
                    ->update(['priority' => $index + 1]);
            }
        });

        return response()->json([
            'message' => 'Task order updated.',
        ]);
    }
}
