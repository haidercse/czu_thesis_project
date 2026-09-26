<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\ApplicationStep;
use App\Models\Document;
use App\Models\Program;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_uploading_a_relevant_document_updates_the_matching_requirement_without_marking_it_approved(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $program = Program::create([
            'university_id' => University::create([
                'name' => 'Test University',
                'location' => 'Prague',
            ])->id,
            'program_name' => 'Test Program',
            'field_of_study' => 'Computer Science',
            'tuition_fee_annual' => 1200,
            'application_deadline' => now()->addMonth()->toDateString(),
            'language_proficiency_requirement' => 'B2 English',
        ]);

        $application = Application::create([
            'user_id' => $user->id,
            'program_id' => $program->id,
            'status' => 'planning',
        ]);

        $step = ApplicationStep::create([
            'step_name' => 'Prepare and certify academic documents',
            'step_description' => 'Upload your transcript and diploma.',
            'step_order' => 2,
            'related_document_type' => 'transcript',
        ]);

        $application->steps()->create([
            'application_step_id' => $step->id,
            'status' => 'not_started',
        ]);

        Document::create([
            'user_id' => $user->id,
            'application_id' => $application->id,
            'document_type' => 'transcript',
            'original_filename' => 'old_transcript.pdf',
            'stored_filename' => 'old_transcript.pdf',
            'file_path' => 'documents/' . $user->id . '/old_transcript.pdf',
            'file_size' => 100,
        ]);

        $this->actingAs($user)
            ->post('/documents', [
                'file' => UploadedFile::fake()->create('transcript.pdf', 50, 'application/pdf'),
                'document_type' => 'transcript',
            ])
            ->assertOk();

        $this->assertDatabaseCount('documents', 1);
        $this->assertSame('in_progress', $application->fresh()->steps()->where('application_step_id', $step->id)->first()->status);
        $this->assertDatabaseHas('documents', [
            'application_id' => $application->id,
            'document_type' => 'transcript',
            'original_filename' => 'transcript.pdf',
        ]);
    }

    public function test_admin_can_review_a_document_and_student_sees_the_result(): void
    {
        Storage::fake('local');

        $student = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        $program = Program::create([
            'university_id' => University::create([
                'name' => 'Review University',
                'location' => 'Prague',
            ])->id,
            'program_name' => 'Review Program',
            'field_of_study' => 'Business',
            'tuition_fee_annual' => 2000,
            'application_deadline' => now()->addMonth()->toDateString(),
            'language_proficiency_requirement' => 'B2 English',
        ]);

        $application = Application::create([
            'user_id' => $student->id,
            'program_id' => $program->id,
            'status' => 'submitted',
        ]);

        $document = Document::create([
            'user_id' => $student->id,
            'application_id' => $application->id,
            'document_type' => 'transcript',
            'original_filename' => 'transcript.pdf',
            'stored_filename' => 'transcript.pdf',
            'file_path' => 'documents/' . $student->id . '/transcript.pdf',
            'file_size' => 100,
            'review_status' => 'uploaded',
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/admin/documents/' . $document->id . '/review', [
                'review_status' => 'rejected',
                'review_comment' => 'Please upload a certified transcript in English.',
            ]);

        $response->assertOk();
        $response->assertJsonPath('data.review_status', 'rejected');
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'review_status' => 'rejected',
            'review_comment' => 'Please upload a certified transcript in English.',
        ]);

        $studentResponse = $this->actingAs($student)->get('/documents');
        $studentResponse->assertOk();
        $studentResponse->assertSeeText('Rejected');
        $studentResponse->assertSeeText('Please upload a certified transcript in English.');
    }

    public function test_admin_can_open_the_document_review_queue(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $student = User::factory()->create();
        $program = Program::create([
            'university_id' => University::create([
                'name' => 'Queue University',
                'location' => 'Prague',
            ])->id,
            'program_name' => 'Queue Program',
            'field_of_study' => 'Business',
            'tuition_fee_annual' => 2000,
        ]);
        $application = Application::create([
            'user_id' => $student->id,
            'program_id' => $program->id,
            'status' => 'submitted',
        ]);

        Document::create([
            'user_id' => $student->id,
            'application_id' => $application->id,
            'document_type' => 'transcript',
            'original_filename' => 'queue-transcript.pdf',
            'stored_filename' => 'queue-transcript.pdf',
            'file_path' => 'documents/' . $student->id . '/queue-transcript.pdf',
            'file_size' => 100,
            'review_status' => 'uploaded',
        ]);

        $response = $this->actingAs($admin)->get('/admin/documents/review');

        $response->assertOk();
        $response->assertSeeText('Document Review');
        $response->assertSeeText('queue-transcript.pdf');
        $response->assertSeeText('Queue Program');
    }

    public function test_admin_review_form_redirects_back_to_the_queue(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $student = User::factory()->create();
        $program = Program::create([
            'university_id' => University::create([
                'name' => 'Redirect University',
                'location' => 'Prague',
            ])->id,
            'program_name' => 'Redirect Program',
            'field_of_study' => 'Business',
            'tuition_fee_annual' => 2000,
        ]);
        $application = Application::create([
            'user_id' => $student->id,
            'program_id' => $program->id,
            'status' => 'submitted',
        ]);
        $document = Document::create([
            'user_id' => $student->id,
            'application_id' => $application->id,
            'document_type' => 'passport',
            'original_filename' => 'passport.jpg',
            'stored_filename' => 'passport.jpg',
            'file_path' => 'documents/' . $student->id . '/passport.jpg',
            'file_size' => 100,
            'review_status' => 'uploaded',
        ]);

        $response = $this->actingAs($admin)->patch('/admin/documents/' . $document->id . '/review', [
            'review_status' => 'in_review',
            'review_comment' => 'Initial review completed successfully.',
        ]);

        $response->assertRedirect('/admin/documents/review');
    }

    public function test_admin_review_requires_a_comment_for_every_status(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $document = Document::create([
            'user_id' => $admin->id,
            'document_type' => 'passport',
            'original_filename' => 'passport.jpg',
            'stored_filename' => 'passport.jpg',
            'file_path' => 'documents/' . $admin->id . '/passport.jpg',
            'file_size' => 100,
            'review_status' => 'uploaded',
        ]);

        $response = $this->actingAs($admin)->patchJson('/admin/documents/' . $document->id . '/review', [
            'review_status' => 'approved',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['review_comment']);
    }

    public function test_authorized_reviewer_can_view_a_document_inline(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create(['is_admin' => true]);
        $path = 'documents/' . $admin->id . '/passport.jpg';
        Storage::disk('local')->put($path, 'fake-image-content');

        $document = Document::create([
            'user_id' => $admin->id,
            'document_type' => 'passport',
            'original_filename' => 'passport.jpg',
            'stored_filename' => 'passport.jpg',
            'file_path' => $path,
            'file_size' => 100,
        ]);

        $response = $this->actingAs($admin)->get('/documents/' . $document->id . '/view');

        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'inline; filename=passport.jpg');
    }
}
