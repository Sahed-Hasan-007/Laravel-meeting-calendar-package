<?php

namespace Shahed\MeetingCalendar\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MeetingCalendar extends Component
{
    public $currentDate;
    public $selectedDate = null;
    public $monthName;
    public $weeks = [];
    public $selectedDateMeetings = [];
    public $todayMeetings = [];

    public function mount()
    {
        $this->currentDate = Carbon::now();
        $this->loadCalendar();
    }

    public function loadCalendar()
    {
        $this->monthName = $this->currentDate->format('F Y');
        $this->weeks = $this->generateCalendarWeeks();

        // Load today's meetings by default
        $this->loadTodayMeetings();
    }

    public function generateCalendarWeeks()
    {
        $startOfMonth = $this->currentDate->copy()->startOfMonth();
        $endOfMonth = $this->currentDate->copy()->endOfMonth();

        // Start from the Sunday of the week containing the first day of the month
        $startDate = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);

        // End on the Saturday of the week containing the last day of the month
        $endDate = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);

        $weeks = [];
        $currentWeek = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dayData = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->day,
                'isCurrentMonth' => $date->month === $this->currentDate->month,
                'isToday' => $date->isToday(),
                'meetings' => $this->getMeetingsForDate($date),
            ];

            $currentWeek[] = $dayData;

            if ($date->dayOfWeek === Carbon::SATURDAY) {
                $weeks[] = $currentWeek;
                $currentWeek = [];
            }
        }

        return $weeks;
    }

    public function getMeetingsForDate($date)
    {
        if (!Schema::hasTable('meetings')) {
            return collect([]);
        }

        try {
            $dateString = $date->format('Y-m-d');

            $meetings = DB::table('meetings')
                ->whereDate('start_time', $dateString)
                ->orderBy('start_time')
                ->get();

            return $meetings->map(function ($meeting) {
                return [
                    'id' => $meeting->id,
                    'title' => $meeting->title ?? 'Untitled Meeting',
                    'status' => $meeting->status ?? 'Scheduled',
                    'start_time' => $meeting->start_time,
                    'end_time' => $meeting->end_time,
                    'type' => $meeting->type ?? 'online',
                    'is_organizer' => isset($meeting->organizer_id) && Auth::check()
                        ? $meeting->organizer_id == Auth::id()
                        : false,
                    'url' => route('meetings.show', $meeting->id),
                ];
            });
        } catch (\Throwable $e) {
            logger()->warning('MeetingCalendar: unable to load meetings for date', [
                'date' => $date->format('Y-m-d'),
                'error' => $e->getMessage()
            ]);
            return collect([]);
        }
    }

    public function loadTodayMeetings()
    {
        $this->todayMeetings = $this->getMeetingsForDate(Carbon::today())->toArray();
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->selectedDateMeetings = $this->getMeetingsForDate(Carbon::parse($date))->toArray();
    }

    public function previousMonth()
    {
        $this->currentDate = $this->currentDate->copy()->subMonth();
        $this->selectedDate = null;
        $this->selectedDateMeetings = [];
        $this->loadCalendar();
    }

    public function nextMonth()
    {
        $this->currentDate = $this->currentDate->copy()->addMonth();
        $this->selectedDate = null;
        $this->selectedDateMeetings = [];
        $this->loadCalendar();
    }

    public function today()
    {
        $this->currentDate = Carbon::now();
        $this->selectedDate = null;
        $this->selectedDateMeetings = [];
        $this->loadCalendar();
    }

    public function render()
    {
        return view('meeting-calendar::livewire.meeting-calendar');
    }
}
