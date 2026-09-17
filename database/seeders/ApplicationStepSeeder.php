<?php

namespace Database\Seeders;

use App\Models\ApplicationStep;
use Illuminate\Database\Seeder;

class ApplicationStepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $qualificationRecognitionCountries = ['Bangladesh', 'India', 'Pakistan', 'Nigeria'];

        $steps = [
            ['step_name' => 'Research qualification recognition requirements', 'step_description' => 'Check whether your previous degree needs formal recognition (nostrifikace).', 'step_order' => 1, 'applicable_countries' => $qualificationRecognitionCountries, 'related_document_type' => null],
            ['step_name' => 'Prepare and certify academic documents', 'step_description' => 'Obtain certified copies of your transcript and diploma.', 'step_order' => 2, 'applicable_countries' => null, 'related_document_type' => 'transcript'],
            ['step_name' => 'Submit qualification recognition application', 'step_description' => 'Submit to the relevant Czech authority. Typical processing: 30-60 days.', 'step_order' => 3, 'applicable_countries' => $qualificationRecognitionCountries, 'related_document_type' => null],
            ['step_name' => 'Submit university application', 'step_description' => 'Complete and submit through the university admission portal.', 'step_order' => 4, 'applicable_countries' => null, 'related_document_type' => null],
            ['step_name' => 'Apply for a Czech study visa', 'step_description' => 'Apply at the nearest Czech embassy after admission.', 'step_order' => 5, 'applicable_countries' => null, 'related_document_type' => null],
        ];

        foreach ($steps as $step) {
            ApplicationStep::updateOrCreate(
                ['step_name' => $step['step_name']],
                $step
            );
        }
    }
}