<?php

namespace App\Services;

use Carbon\Carbon;

class DeadlineService
{
    public function status(?string $deadline): array
    {
        if (blank($deadline)) {
            return [
                'label' => 'No deadline recorded',
                'days' => null,
                'date' => null,
            ];
        }

        try {
            $date = Carbon::parse($deadline, config('app.timezone'))->setTimezone(config('app.timezone'))->startOfDay();
        } catch (\Throwable $e) {
            return [
                'label' => 'No deadline recorded',
                'days' => null,
                'date' => null,
            ];
        }

        $today = Carbon::now(config('app.timezone'))->startOfDay();
        $daysUntil = $today->diffInDays($date, false);

        if ($date->isPast()) {
            return [
                'label' => 'Deadline passed',
                'days' => $daysUntil,
                'date' => $date,
            ];
        }

        if ($daysUntil <= 30) {
            return [
                'label' => 'Due soon',
                'days' => $daysUntil,
                'date' => $date,
            ];
        }

        return [
            'label' => 'Upcoming',
            'days' => $daysUntil,
            'date' => $date,
        ];
    }
}
