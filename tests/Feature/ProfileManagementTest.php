<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_save_additional_profile_details(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from('/profile')
            ->post('/profile', [
                'country_of_origin' => 'Bangladesh',
                'previous_degree' => 'BSc in Computer Science',
                'previous_institution' => 'University of Dhaka',
                'target_field' => 'Computer Science',
                'gpa' => 3.7,
                'language_test_type' => 'IELTS',
                'language_test_score' => 7.0,
                'annual_budget' => 6000,
                'preferred_city' => 'Prague',
                'target_intake' => '2027/28',
            ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHas('status', 'Profile updated.');

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'country_of_origin' => 'Bangladesh',
            'preferred_city' => 'Prague',
            'target_intake' => '2027/28',
        ]);
    }

    public function test_profile_page_shows_completion_status(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertSee('Profile completion');
    }
}
