<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
        ]);

        $projects = collect([
            'Website Refresh' => [
                'Finalize homepage hero copy',
                'Audit CTA button hierarchy',
                'Review mobile navigation spacing',
                'Prepare launch-day QA checklist',
            ],
            'Operations Cleanup' => [
                'Rotate staging credentials',
                'Document the backup restore workflow',
                'Verify queue worker health checks',
                'Update incident response notes',
                'Archive stale deployment artifacts',
            ],
            'Hiring Pipeline' => [
                'Shortlist backend take-home submissions',
                'Schedule technical interviews',
                'Draft candidate feedback templates',
                'Sync scorecards with the team',
            ],
        ]);

        $projects->each(function (array $taskNames, string $projectName): void {
            $project = Project::factory()->create([
                'name' => $projectName,
            ]);

            foreach ($taskNames as $index => $taskName) {
                Task::factory()->create([
                    'project_id' => $project->id,
                    'name' => $taskName,
                    'priority' => $index + 1,
                ]);
            }
        });

        foreach ([
            'Review unassigned maintenance requests',
            'Sort incoming product ideas',
            'Clean up task naming conventions',
        ] as $index => $taskName) {
            Task::factory()->create([
                'project_id' => null,
                'name' => $taskName,
                'priority' => $index + 1,
            ]);
        }
    }
}
