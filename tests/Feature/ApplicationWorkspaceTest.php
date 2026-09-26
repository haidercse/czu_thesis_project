<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\ApplicationStep;
use App\Models\Document;
use App\Models\Program;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_view_application_workspace_summary(): void
    {
        $user = User::factory()->create();
        $university = University::create([
            'name' => 'University of Prague',
            'location' => 'Prague',
        ]);

        $program = Program::create([
            'university_id' => $university->id,
            'program_name' => 'Computer Science',
            'field_of_study' => 'IT',
            'tuition_fee_annual' => 1200,
            'application_deadline' => now()->addMonth()->toDateString(),
            'language_proficiency_requirement' => 'B2 English',
        ]);

        $application = Application::create([
            'user_id' => $user->id,
            'program_id' => $program->id,
            'status' => 'planning',
        ]);

        $completedStep = ApplicationStep::create([
            'step_name' => 'Prepare transcript',
            'step_description' => 'Upload your transcript.',
            'step_order' => 1,
            'related_document_type' => 'transcript',
        ]);

        $application->steps()->create([
            'application_step_id' => $completedStep->id,
            'status' => 'completed',
        ]);

        $activeStep = ApplicationStep::create([
            'step_name' => 'Submit application form',
            'step_description' => 'Complete the online application form.',
            'step_order' => 2,
            'related_document_type' => null,
        ]);

        $application->steps()->create([
            'application_step_id' => $activeStep->id,
            'status' => 'in_progress',
        ]);

        Document::create([
            'user_id' => $user->id,
            'application_id' => $application->id,
            'document_type' => 'transcript',
            'original_filename' => 'transcript.pdf',
            'stored_filename' => 'transcript.pdf',
            'file_path' => 'documents/' . $user->id . '/transcript.pdf',
            'file_size' => 1024,
        ]);

        $this->actingAs($user)
            ->get('/applications')
            ->assertOk()
            ->assertSee('Application workspace')
            ->assertSee('Computer Science')
            ->assertSee('University of Prague')
            ->assertSee('Deadline')
            ->assertSee('Next action')
            ->assertSee('Uploaded documents')
            ->assertSee('Checklist');
    }
}
