<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationStep;
use Illuminate\Support\Str;

class ApplicationReadinessService
{
    public function evaluate(Application $application): array
    {
        $reasons = [];
        $profile = $application->user->profile;
        $program = $application->program;

        if (! $profile) {
            $reasons[] = 'Complete your profile before submitting this application.';
        }

        if (! $profile || blank($profile->gpa)) {
            $reasons[] = 'Missing GPA information.';
        }

        if (! $profile || blank($profile->language_test_score)) {
            $reasons[] = 'Missing language test score information.';
        }

        if (! $program || blank($program->application_deadline)) {
            $reasons[] = 'No application deadline recorded for this program.';
        }

        $uploadedDocumentTypes = $application->documents()->pluck('document_type')->filter()->map(fn ($type) => strtolower((string) $type))->all();

        $application->loadMissing('steps.step');
        foreach ($application->steps as $step) {
            if (! $step->step) {
                continue;
            }

            if ($step->status !== 'completed') {
                $reasons[] = 'Complete: ' . $step->step->step_name;
            }

            if ($step->step->related_document_type && ! in_array(strtolower((string) $step->step->related_document_type), $uploadedDocumentTypes, true)) {
                $reasons[] = 'Upload your ' . Str::replace('_', ' ', $step->step->related_document_type) . '.';
            }
        }

        $requiredDocuments = ApplicationStep::query()
            ->whereNotNull('related_document_type')
            ->pluck('related_document_type')
            ->filter();

        foreach ($requiredDocuments as $documentType) {
            if (! in_array(strtolower((string) $documentType), $uploadedDocumentTypes, true)) {
                $reasons[] = 'Upload your ' . Str::replace('_', ' ', $documentType) . '.';
            }
        }

        $reasons = array_values(array_unique($reasons));

        if ($reasons === []) {
            return [
                'status' => 'ready',
                'label' => 'Ready to submit',
                'reasons' => ['All required checklist items and documents are complete.'],
                'percentage' => 100,
            ];
        }

        $completion = max(0, 100 - (count($reasons) * 10));

        return [
            'status' => 'not_ready',
            'label' => 'Not ready yet',
            'reasons' => $reasons,
            'percentage' => min(95, $completion),
        ];
    }
}
