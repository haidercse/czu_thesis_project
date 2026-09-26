<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecommendationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_sees_programs_ranked_by_profile_match(): void
    {
        $user = User::factory()->create();
        $user->profile()->create([
            'target_field' => 'Computer Science',
            'gpa' => 3.6,
            'language_test_type' => 'IELTS',
            'language_test_score' => 7.0,
            'annual_budget' => 5000,
        ]);

        $university = University::create(['name' => 'CZU', 'location' => 'Prague']);
        $best = Program::create([
            'university_id' => $university->id,
            'program_name' => 'Computer Science',
            'field_of_study' => 'Computer Science',
            'tuition_fee_annual' => 4000,
            'application_deadline' => '2027-05-15',
            'language_proficiency_requirement' => 'IELTS 6.5',
            'minimum_gpa' => 3.0,
        ]);
        Program::create([
            'university_id' => $university->id,
            'program_name' => 'Business Studies',
            'field_of_study' => 'Business',
            'tuition_fee_annual' => 9000,
            'application_deadline' => '2027-05-15',
            'language_proficiency_requirement' => 'IELTS 7.5',
            'minimum_gpa' => 3.8,
        ]);

        $response = $this->actingAs($user)->get('/recommendations');

        $response->assertOk();
        $response->assertSeeText('Computer Science');
        $response->assertSeeText('95% match');
        $this->assertSame($best->id, $response->viewData('recommendations')->first()->id);
    }

    public function test_student_without_profile_is_prompted_to_complete_it(): void
    {
        $response = $this->actingAs(User::factory()->create())->get('/recommendations');

        $response->assertOk()->assertSeeText('Complete your profile first');
    }

    public function test_student_sees_eligibility_summary_on_program_details(): void
    {
        $user = User::factory()->create();
        $user->profile()->create([
            'target_field' => 'Computer Science',
            'gpa' => 3.7,
            'language_test_type' => 'IELTS',
            'language_test_score' => 7.0,
            'country_of_origin' => 'Czech Republic',
            'previous_degree' => 'Bachelor',
            'annual_budget' => 5000,
        ]);

        $university = University::create(['name' => 'CZU', 'location' => 'Prague']);
        $program = Program::create([
            'university_id' => $university->id,
            'program_name' => 'Computer Science',
            'field_of_study' => 'Computer Science',
            'tuition_fee_annual' => 4000,
            'application_deadline' => '2027-05-15',
            'language_proficiency_requirement' => 'IELTS 6.5',
            'minimum_gpa' => 3.0,
        ]);

        $response = $this->actingAs($user)->get('/programs/' . $program->id);

        $response->assertOk();
        $response->assertSeeText('Eligible');
        $response->assertSeeText('GPA meets the program requirement');
        $response->assertSeeText('Language score meets the program requirement');
    }

    public function test_student_sees_review_status_when_profile_data_is_missing(): void
    {
        $user = User::factory()->create();
        $user->profile()->create([
            'target_field' => 'Computer Science',
            'language_test_type' => 'IELTS',
            'language_test_score' => 7.0,
            'country_of_origin' => 'Czech Republic',
        ]);

        $university = University::create(['name' => 'CZU', 'location' => 'Prague']);
        $program = Program::create([
            'university_id' => $university->id,
            'program_name' => 'Computer Science',
            'field_of_study' => 'Computer Science',
            'tuition_fee_annual' => 4000,
            'application_deadline' => '2027-05-15',
            'language_proficiency_requirement' => 'IELTS 6.5',
            'minimum_gpa' => 3.0,
        ]);

        $response = $this->actingAs($user)->get('/programs/' . $program->id);

        $response->assertOk();
        $response->assertSeeText('Needs review');
        $response->assertSeeText('Missing GPA information');
    }

    public function test_student_can_compare_programs_by_direct_url_query(): void
    {
        $user = User::factory()->create();
        $university = University::create(['name' => 'CZU', 'location' => 'Prague']);

        $first = Program::create([
            'university_id' => $university->id,
            'program_name' => 'Computer Science',
            'field_of_study' => 'Computer Science',
            'tuition_fee_annual' => 4000,
            'application_deadline' => '2027-05-15',
            'language_proficiency_requirement' => 'IELTS 6.5',
            'minimum_gpa' => 3.0,
        ]);

        $second = Program::create([
            'university_id' => $university->id,
            'program_name' => 'Data Science',
            'field_of_study' => 'Data Science',
            'tuition_fee_annual' => 4500,
            'application_deadline' => '2027-05-20',
            'language_proficiency_requirement' => 'IELTS 6.0',
            'minimum_gpa' => 3.2,
        ]);

        $response = $this->actingAs($user)->get('/programs/compare?ids=' . $first->id . ',' . $second->id);

        $response->assertOk();
        $response->assertSeeText('Compare programs');
        $response->assertSeeText('Computer Science');
        $response->assertSeeText('Data Science');
    }

    public function test_recommendation_reasons_are_transparent_and_rule_based(): void
    {
        $user = User::factory()->create();
        $user->profile()->create([
            'target_field' => 'Computer Science',
            'gpa' => 3.8,
            'language_test_type' => 'IELTS',
            'language_test_score' => 7.5,
            'annual_budget' => 5000,
            'preferred_city' => 'Prague',
            'target_intake' => '2027 Autumn',
        ]);

        $university = University::create(['name' => 'CZU', 'location' => 'Prague']);
        Program::create([
            'university_id' => $university->id,
            'program_name' => 'Computer Science',
            'field_of_study' => 'Computer Science',
            'tuition_fee_annual' => 4000,
            'application_deadline' => '2027-09-15',
            'language_proficiency_requirement' => 'IELTS 6.5',
            'minimum_gpa' => 3.0,
        ]);

        $response = $this->actingAs($user)->get('/recommendations');

        $response->assertOk();
        $response->assertSeeText('Matches your target field');
        $response->assertSeeText('Within your annual budget');
        $response->assertSeeText('Preferred city matches your choice');
    }

    public function test_deadline_status_is_displayed_for_program_details_and_application_workspace(): void
    {
        \Carbon\Carbon::setTestNow('2026-09-01');

        $user = User::factory()->create();
        $user->profile()->create([
            'country_of_origin' => 'Czech Republic',
        ]);

        $university = University::create(['name' => 'CZU', 'location' => 'Prague']);
        $program = Program::create([
            'university_id' => $university->id,
            'program_name' => 'Computer Science',
            'field_of_study' => 'Computer Science',
            'tuition_fee_annual' => 4000,
            'application_deadline' => '2026-09-10',
            'language_proficiency_requirement' => 'IELTS 6.5',
            'minimum_gpa' => 3.0,
        ]);

        $application = \App\Models\Application::create([
            'user_id' => $user->id,
            'program_id' => $program->id,
            'status' => 'planning',
        ]);

        $programResponse = $this->actingAs($user)->get('/programs/' . $program->id);
        $programResponse->assertOk();
        $programResponse->assertSeeText('Due soon');

        $workspaceResponse = $this->actingAs($user)->get('/applications');
        $workspaceResponse->assertOk();
        $workspaceResponse->assertSeeText('Due soon');

        \Carbon\Carbon::setTestNow();
    }

    public function test_missing_deadline_is_shown_as_no_deadline_recorded(): void
    {
        $user = User::factory()->create();
        $university = University::create(['name' => 'CZU', 'location' => 'Prague']);
        $program = Program::create([
            'university_id' => $university->id,
            'program_name' => 'Open Studies',
            'field_of_study' => 'Interdisciplinary',
            'tuition_fee_annual' => 5000,
            'language_proficiency_requirement' => 'IELTS 6.0',
            'minimum_gpa' => 2.5,
        ]);

        \App\Models\Application::create([
            'user_id' => $user->id,
            'program_id' => $program->id,
            'status' => 'planning',
        ]);

        $response = $this->actingAs($user)->get('/applications');

        $response->assertOk();
        $response->assertSeeText('No deadline recorded');
    }

    public function test_application_readiness_lists_the_missing_blocks_before_submission(): void
    {
        $user = User::factory()->create();
        $user->profile()->create([
            'country_of_origin' => 'Czech Republic',
            'target_field' => 'Computer Science',
            'language_test_type' => 'IELTS',
            'language_test_score' => 7.0,
        ]);

        $university = University::create(['name' => 'CZU', 'location' => 'Prague']);
        $program = Program::create([
            'university_id' => $university->id,
            'program_name' => 'Computer Science',
            'field_of_study' => 'Computer Science',
            'tuition_fee_annual' => 4000,
            'application_deadline' => '2027-09-15',
            'language_proficiency_requirement' => 'IELTS 6.5',
            'minimum_gpa' => 3.0,
        ]);

        $application = \App\Models\Application::create([
            'user_id' => $user->id,
            'program_id' => $program->id,
            'status' => 'planning',
        ]);

        $step = \App\Models\ApplicationStep::create([
            'step_name' => 'Prepare degree documents',
            'step_description' => 'Upload your transcript and diploma.',
            'step_order' => 1,
            'related_document_type' => 'transcript',
        ]);

        $application->steps()->create([
            'application_step_id' => $step->id,
            'status' => 'not_started',
        ]);

        $response = $this->actingAs($user)->get('/applications');

        $response->assertOk();
        $response->assertSeeText('Application readiness');
        $response->assertSeeText('Missing GPA information');
    }

    public function test_student_can_save_and_unsave_programs(): void
    {
        $user = User::factory()->create();
        $university = University::create(['name' => 'CZU', 'location' => 'Prague']);
        $program = Program::create([
            'university_id' => $university->id,
            'program_name' => 'Computer Science',
            'field_of_study' => 'Computer Science',
            'tuition_fee_annual' => 4000,
            'application_deadline' => '2027-09-15',
            'language_proficiency_requirement' => 'IELTS 6.5',
            'minimum_gpa' => 3.0,
        ]);

        $saveResponse = $this->actingAs($user)->post('/saved-programs/' . $program->id);
        $saveResponse->assertRedirect();
        $this->assertDatabaseHas('program_user', [
            'user_id' => $user->id,
            'program_id' => $program->id,
        ]);

        $savedPage = $this->actingAs($user)->get('/saved-programs');
        $savedPage->assertOk();
        $savedPage->assertSeeText('Computer Science');

        $deleteResponse = $this->actingAs($user)->delete('/saved-programs/' . $program->id);
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('program_user', [
            'user_id' => $user->id,
            'program_id' => $program->id,
        ]);
    }
}
