<?php

namespace App\Support\Tasks;

use App\Models\Task;

class TaskPriorityManager
{
    public function nextPriorityForProject(?int $projectId): int
    {
        return (int) Task::query()
            ->where('project_id', $projectId)
            ->max('priority') + 1;
    }

    public function normalizePriorities(?int $projectId): void
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
}
