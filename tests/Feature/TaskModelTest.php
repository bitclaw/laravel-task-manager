<?php

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('associates tasks with projects and orders project tasks by priority', function (): void {
    $project = Project::factory()->create();

    $secondTask = Task::factory()->for($project)->create([
        'name' => 'Second task',
        'priority' => 2,
    ]);

    $firstTask = Task::factory()->for($project)->create([
        'name' => 'First task',
        'priority' => 1,
    ]);

    expect($firstTask->project->is($project))->toBeTrue();
    expect($project->tasks->modelKeys())->toBe([
        $firstTask->id,
        $secondTask->id,
    ]);
});
