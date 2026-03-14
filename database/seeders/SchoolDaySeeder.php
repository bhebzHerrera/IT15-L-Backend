<?php

namespace Database\Seeders;

use App\Models\SchoolDay;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SchoolDaySeeder extends Seeder
{
    public function run(): void
    {
        SchoolDay::query()->delete();

        $start = Carbon::create(2026, 1, 1);
        $end = Carbon::create(2026, 12, 31);
        $holidays = [
            '2026-01-01' => 'New Year\'s Day',
            '2026-04-09' => 'Araw ng Kagitingan',
            '2026-06-12' => 'Independence Day',
            '2026-08-31' => 'National Heroes Day',
            '2026-11-30' => 'Bonifacio Day',
            '2026-12-25' => 'Christmas Day',
        ];
        $events = [
            '2026-03-15' => 'College Week Opening',
            '2026-05-20' => 'Research Expo',
            '2026-09-10' => 'Foundation Day',
            '2026-12-05' => 'Year-End Recognition',
        ];

        $records = [];
        $cursor = $start->copy();
        $now = now();

        while ($cursor->lte($end)) {
            $date = $cursor->toDateString();
            $dayType = 'class_day';
            $attendanceRate = null;
            $eventName = null;
            $notes = null;

            if (isset($holidays[$date])) {
                $dayType = 'holiday';
                $eventName = $holidays[$date];
                $notes = 'No classes';
            } elseif (isset($events[$date])) {
                $dayType = 'event';
                $eventName = $events[$date];
                $attendanceRate = random_int(85, 99);
            } elseif ($cursor->isWeekend()) {
                $dayType = 'holiday';
                $eventName = 'Weekend';
                $notes = 'No classes';
            } else {
                $attendanceRate = random_int(75, 99);
            }

            $records[] = [
                'date' => $date,
                'day_type' => $dayType,
                'attendance_rate' => $attendanceRate,
                'event_name' => $eventName,
                'notes' => $notes,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $cursor->addDay();
        }

        SchoolDay::query()->insert($records);
    }
}
