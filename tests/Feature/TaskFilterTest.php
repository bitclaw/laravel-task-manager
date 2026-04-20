<?php

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('filters the task list by the selected project', function (): void {
    $websiteProject = Project::factory()->create(['name' => 'Website Refresh']);
    $apiProject = Project::factory()->create(['name' => 'API Cleanup']);

    $websiteTask = Task::factory()->for($websiteProject)->create([
        'name' => 'Refresh landing page copy',
        'priority' => 1,
    ]);

    Task::factory()->for($apiProject)->create([
        'name' => 'Remove legacy endpoint',
        'priority' => 1,
    ]);

    Task::factory()->create([
        'project_id' => null,
        'name' => 'Inbox task',
        'priority' => 1,
    ]);

    $response = $this->get(route('tasks.index', ['project' => $websiteProject->id]));

    $response->assertOk();
    $response->assertSee('Website Refresh');
    $response->assertSee($websiteTask->name);
    $response->assertDontSee('Remove legacy endpoint');
    $response->assertDontSee('Inbox task');
});
