<?php

namespace Shahed\MeetingCalendar\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MeetingCalendar extends Component
{
    public $meetings = [];

    public function mount()
    {
        // Check if 'meetings' table exists before querying
        if (Schema::hasTable('meetings')) {
            try {
                $this->meetings = DB::table('meetings')->get();
            } catch (\Throwable $e) {
                // Log error but don’t break
                logger()->warning('MeetingCalendar: unable to load meetings', ['error' => $e->getMessage()]);
                $this->meetings = [];
            }
        } else {
            $this->meetings = [];
        }
    }

    public function render()
    {
        return view('meeting-calendar::livewire.meeting-calendar', [
            'meetings' => $this->meetings,
        ]);
    }
}
