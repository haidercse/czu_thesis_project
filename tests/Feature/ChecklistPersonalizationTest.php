<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\ApplicationStep;
use App\Models\Program;
use App\Models\University;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChecklistPersonalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_country_specific_steps_are_filtered_from_the_application_for_ineligible_students(): void
    {
        $user = User::factory()->create();
        UserProfile::create([
            'user_id' => $user->id,
            'country_of_origin' => 'Germany',
        ]);

        $program = Program::create([
            'university_id' => University::create([
                'name' => 'Test University',
                'location' => 'Prague',
            ])->id,
            'program_name' => 'Test Program',
            'field_of_study' => 'Computer Science',
            'tuition_fee_annual' => 1500,
            'application_deadline' => now()->addMonth()->toDateString(),
            'language_proficiency_requirement' => 'B2 English',
        ]);

        ApplicationStep::create([
            'step_name' => 'Research qualification recognition requirements',
            'step_description' => 'Check whether your previous degree needs formal recognition.',
            'step_order' => 1,
            'applicable_countries' => ['Bangladesh', 'India'],
            'related_document_type' => null,
        ]);

        ApplicationStep::create([
            'step_name' => 'Prepare and certify academic documents',
            'step_description' => 'Obtain certified copies of your transcript and diploma.',
            'step_order' => 2,
            'applicable_countries' => null,
            'related_document_type' => 'transcript',
        ]);

        ApplicationStep::create([
            'step_name' => 'Submit qualification recognition application',
            'step_description' => 'Submit to the relevant Czech authority.',
            'step_order' => 3,
            'applicable_countries' => ['Bangladesh', 'India'],
            'related_document_type' => null,
        ]);

        ApplicationStep::create([
            'step_name' => 'Submit university application',
            'step_description' => 'Complete and submit through the university admission portal.',
            'step_order' => 4,
            'applicable_countries' => null,
            'related_document_type' => null,
        ]);

        ApplicationStep::create([
            'step_name' => 'Apply for a Czech study visa',
            'step_description' => 'Apply at the nearest Czech embassy after admission.',
            'step_order' => 5,
            'applicable_countries' => null,
            'related_document_type' => null,
        ]);

        $this->actingAs($user)->post('/applications', ['program_id' => $program->id]);

        $application = Application::first();

        $this->assertNotNull($application);
        $this->assertCount(3, $application->steps()->get());

        $this->actingAs($user)
            ->get('/applications')
            ->assertOk()
            ->assertSee('Only relevant checklist items are shown for your profile')
            ->assertDontSee('Research qualification recognition requirements')
            ->assertSee('Submit university application');
    }
}
