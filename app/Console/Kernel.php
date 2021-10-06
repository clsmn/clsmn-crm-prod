<?php

namespace App\Console;

use App\Models\Lead\Lead;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $date = date('Y-m-d');
            Lead::whereDate('next_follow_up', $date)->update(['call_date' => $date, 'done' => '0']);
        })->dailyAt('3:00');

        // $schedule->call('App\Http\Controllers\TestController@index')->everyMinute()->name('callHistory')->withoutOverlapping();;
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
