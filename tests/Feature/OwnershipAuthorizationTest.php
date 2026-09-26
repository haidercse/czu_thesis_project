<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\ApplicationStep;
use App\Models\Document;
use App\Models\Program;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OwnershipAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_update_another_users_application_step(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $program = Program::create([
            'university_id' => University::create([
                'name' => 'Test University',
                'location' => 'Prague',
            ])->id,
            'program_name' => 'Test Program',
            'field_of_study' => 'Computer Science',
            'tuition_fee_annual' => 1000,
            'application_deadline' => now()->addMonth()->toDateString(),
            'language_proficiency_requirement' => null,
        ]);
        $application = Application::create([
            'user_id' => $owner->id,
            'program_id' => $program->id,
            'status' => 'planning',
        ]);

        $applicationStep = ApplicationStep::create([
            'step_name' => 'Test step',
            'step_description' => 'Test step description',
            'step_order' => 1,
        ]);
        $step = $application->steps()->create([
            'application_step_id' => $applicationStep->id,
            'status' => 'not_started',
        ]);

        $this->actingAs($intruder)
            ->patchJson('/steps/' . $step->id, ['status' => 'completed'])
            ->assertForbidden();
    }

    public function test_user_cannot_download_or_delete_another_users_document(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $document = Document::create([
            'user_id' => $owner->id,
            'document_type' => 'passport',
            'original_filename' => 'passport.pdf',
            'stored_filename' => 'stored.pdf',
            'file_path' => 'documents/' . $owner->id . '/stored.pdf',
            'file_size' => 10,
        ]);

        $this->actingAs($intruder)
            ->get('/documents/' . $document->id . '/download')
            ->assertForbidden();

        $this->actingAs($intruder)
            ->deleteJson('/documents/' . $document->id)
            ->assertForbidden();
    }
}