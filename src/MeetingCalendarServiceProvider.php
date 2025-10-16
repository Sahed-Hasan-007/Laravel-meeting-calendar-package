<?php

namespace Shahed\MeetingCalendar;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Shahed\MeetingCalendar\Livewire\MeetingCalendar;

class MeetingCalendarServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Load views - FIXED PATH
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'meeting-calendar');

        // Register Livewire component
        Livewire::component('meeting-calendar', MeetingCalendar::class);

        // Allow publishing views
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/meeting-calendar'),
        ], 'meeting-calendar-views');
    }
}
