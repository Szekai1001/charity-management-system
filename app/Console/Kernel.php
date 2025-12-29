<?php

namespace App\Console;

use App\Models\ActivityLog;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define your application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $today = now();

            // ✅ Only track weekdays (Mon–Fri)
            if (!$today->isWeekday()) {
                return;
            }

            $date = $today->toDateString();

            // ✅ Find teachers who have NOT scanned attendance today
            $teachers = Teacher::whereDoesntHave('attendance', function ($q) use ($date) {
                $q->where('date', $date);
            })->get();

            foreach ($teachers as $teacher) {
                // ✅ Prevent duplicates
                TeacherAttendance::firstOrCreate(
                    [
                        'teacher_id' => $teacher->id,
                        'date' => $date,
                    ],
                    [
                        'status' => 'absent',
                    ]
                );
            }

            Log::info('✅ Teacher absence check completed for ' . $date);
        })->dailyAt('23:59');

        $schedule->call(function() {
            ActivityLog::where('created_at', '<', now()->subDays(30))->delete();
        })->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
    }
}
