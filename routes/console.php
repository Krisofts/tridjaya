<?php

use App\Console\Commands\SendTaskDueReminders;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SendTaskDueReminders::class)
    ->everyFifteenMinutes()
    ->withoutOverlapping();