<div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-lg border border-zinc-200 dark:border-zinc-700">
    <!-- Calendar Header -->
    <div class="px-4 sm:px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center justify-between">
            <h2 class="text-lg sm:text-xl font-bold text-zinc-900 dark:text-zinc-100">
                {{ $monthName }}
            </h2>
            <div class="flex items-center gap-1 sm:gap-2">
                <button
                    wire:click="today"
                    class="px-2 sm:px-3 py-1.5 text-xs sm:text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors"
                >
                    Today
                </button>
                <button
                    wire:click="previousMonth"
                    class="p-1.5 sm:p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors"
                >
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button
                    wire:click="nextMonth"
                    class="p-1.5 sm:p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors"
                >
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-7 gap-0">

        <!-- Calendar Body -->
        <div class="p-4 sm:p-6 lg:col-span-5">
            <!-- Days of Week Header -->
            <div class="grid grid-cols-7 gap-1 sm:gap-2 mb-2">
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div class="text-center text-xs font-semibold text-zinc-500 dark:text-zinc-400 py-2">
                        {{ $day }}
                    </div>
                @endforeach
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-1 sm:gap-2">
                @foreach($weeks as $week)
                    @foreach($week as $day)
                        @php
                            $hasScheduled = $day['meetings']->where('status', 'Scheduled')->count() > 0;
                            $hasCompleted = $day['meetings']->where('status', 'Completed')->count() > 0;
                            $hasCancelled = $day['meetings']->where('status', 'Cancelled')->count() > 0;
                            $totalMeetings = $day['meetings']->count();
                        @endphp

                        <button
                            wire:click="selectDate('{{ $day['date'] }}')"
                            class="relative min-h-[60px] sm:min-h-[80px] p-1 sm:p-2 rounded-lg border transition-all duration-200
                            {{ $day['isCurrentMonth']
                                ? 'bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 hover:border-blue-300 dark:hover:border-blue-700 hover:shadow-md'
                                : 'bg-zinc-50 dark:bg-zinc-900/50 border-zinc-100 dark:border-zinc-800 opacity-50' }}
                            {{ $day['isToday'] ? 'ring-1 sm:ring-2 ring-blue-500 dark:ring-blue-400' : '' }}
                            {{ $selectedDate === $day['date'] ? 'bg-orange-50 dark:bg-orange-900/20 border-orange-500 dark:border-orange-500 ring-1 sm:ring-2 ring-orange-500 dark:ring-orange-500' : '' }}"
                        >
                            <!-- Date Number -->
                            <div class="flex items-start justify-between mb-1">
                                <span class="text-xs sm:text-sm font-medium
                                    {{ $day['isToday']
                                        ? 'text-blue-600 dark:text-blue-400 font-bold'
                                        : ($day['isCurrentMonth']
                                            ? 'text-zinc-900 dark:text-zinc-100'
                                            : 'text-zinc-400 dark:text-zinc-600') }}">
                                    {{ $day['day'] }}
                                </span>

                                @if($totalMeetings > 0)
                                    <span class="inline-flex items-center justify-center w-4 h-4 sm:w-5 sm:h-5 text-[10px] sm:text-xs font-bold rounded-full
                                    {{ $hasScheduled ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : '' }}
                                    {{ $hasCompleted ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : '' }}
                                    {{ $hasCancelled && !$hasScheduled && !$hasCompleted ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' : '' }}">
                                        {{ $totalMeetings }}
                                    </span>
                                @endif
                            </div>

                            <!-- Meeting Indicators -->
                            @if($totalMeetings > 0 && $day['isCurrentMonth'])
                                <div class="hidden sm:flex flex-col gap-1 mt-1">
                                    @foreach($day['meetings']->take(2) as $meeting)
                                        <div class="w-full h-1 rounded-full
                                        {{ $meeting['status'] === 'Scheduled' ? 'bg-blue-500' : '' }}
                                        {{ $meeting['status'] === 'Completed' ? 'bg-green-500' : '' }}
                                        {{ $meeting['status'] === 'Cancelled' ? 'bg-red-500' : '' }}">
                                        </div>
                                    @endforeach
                                    @if($totalMeetings > 2)
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400 text-center">
                                            +{{ $totalMeetings - 2 }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </button>
                    @endforeach
                @endforeach
            </div>
        </div>

        <!-- Selected Date Meetings -->
        <div class="lg:col-span-2 border-t lg:border-t-0 lg:border-l border-zinc-200 dark:border-zinc-700">
            @php
                // Show today's meetings if no date is selected
                $displayDate = $selectedDate ?? \Carbon\Carbon::today()->format('Y-m-d');
                $displayMeetings = $selectedDate ? $selectedDateMeetings : ($todayMeetings ?? []);
                $isToday = $displayDate === \Carbon\Carbon::today()->format('Y-m-d');
            @endphp

            @if(count($displayMeetings) > 0)
                <div class="p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        @if($isToday && !$selectedDate)
                            Today's Meetings
                        @else
                            Meetings on {{ \Carbon\Carbon::parse($displayDate)->format('F d, Y') }}
                        @endif
                    </h3>
                    <div class="space-y-3">
                        @foreach($displayMeetings as $meeting)
                            <a
                                href="{{ $meeting['url'] }}"
                                wire:navigate
                                class="block p-3 sm:p-4 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-blue-300 dark:hover:border-blue-700 hover:shadow-md transition-all duration-200 bg-zinc-50 dark:bg-zinc-800/50"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2 mb-1">
                                            <h4 class="font-semibold text-sm sm:text-base text-zinc-900 dark:text-zinc-100 truncate">
                                                {{ $meeting['title'] }}
                                            </h4>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium whitespace-nowrap
                                            {{ $meeting['status'] === 'Scheduled' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : '' }}
                                            {{ $meeting['status'] === 'Completed' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : '' }}
                                            {{ $meeting['status'] === 'Cancelled' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' : '' }}">
                                                {{ $meeting['status'] }}
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2 sm:gap-4 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span class="whitespace-nowrap">{{ \Carbon\Carbon::parse($meeting['start_time'])->format('h:i A') }} - {{ \Carbon\Carbon::parse($meeting['end_time'])->format('h:i A') }}</span>
                                            </span>
                                            <span class="flex items-center gap-1">
                                                @if($meeting['type'] === 'online')
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                                    </svg>
                                                    Online
                                                @else
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    </svg>
                                                    Offline
                                                @endif
                                            </span>
                                            @if($meeting['is_organizer'])
                                                <span class="text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 px-2 py-0.5 rounded-full whitespace-nowrap">
                                                    Organizer
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-4 sm:p-6">
                    <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-xs sm:text-sm">
                            @if($isToday && !$selectedDate)
                                There is no meeting today
                            @else
                                No meetings on {{ \Carbon\Carbon::parse($displayDate)->format('F d, Y') }}
                            @endif
                        </p>
                    </div>
                </div>
            @endif

        </div>

    </div>

    <!-- Legend -->
    <div class="border-t border-zinc-200 dark:border-zinc-700 px-4 sm:px-6 py-4">
        <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-xs sm:text-sm">
            <span class="font-medium text-zinc-700 dark:text-zinc-300">Status:</span>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                <span class="text-zinc-600 dark:text-zinc-400">Scheduled</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                <span class="text-zinc-600 dark:text-zinc-400">Completed</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                <span class="text-zinc-600 dark:text-zinc-400">Cancelled</span>
            </div>
        </div>
    </div>
</div>
