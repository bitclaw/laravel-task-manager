<?php

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('reorders tasks within the current project scope', function (): void {
    $project = Project::factory()->create();

    $firstTask = Task::factory()->for($project)->create(['priority' => 1]);
    $secondTask = Task::factory()->for($project)->create(['priority' => 2]);
    $thirdTask = Task::factory()->for($project)->create(['priority' => 3]);

    $this->postJson(route('tasks.reorder'), [
        'project_id' => $project->id,
        'task_ids' => [$thirdTask->id, $firstTask->id, $secondTask->id],
    ])->assertOk();

    expect($thirdTask->fresh()->priority)->toBe(1)
        ->and($firstTask->fresh()->priority)->toBe(2)
        ->and($secondTask->fresh()->priority)->toBe(3);
});

it('rejects reorder requests that do not match the current project scope', function (): void {
    $project = Project::factory()->create();
    $otherProject = Project::factory()->create();

    $taskA = Task::factory()->for($project)->create(['priority' => 1]);
    $taskB = Task::factory()->for($project)->create(['priority' => 2]);
    $foreignTask = Task::factory()->for($otherProject)->create(['priority' => 1]);

    $this->postJson(route('tasks.reorder'), [
        'project_id' => $project->id,
        'task_ids' => [$taskA->id, $foreignTask->id, $taskB->id],
    ])->assertStatus(422)->assertJsonValidationErrors('task_ids');
});
