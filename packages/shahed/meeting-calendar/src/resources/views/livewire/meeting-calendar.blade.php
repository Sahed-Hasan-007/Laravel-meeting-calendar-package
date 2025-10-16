<div class="p-6 bg-white dark:bg-zinc-900 rounded-xl shadow-md">
    <h2 class="text-2xl font-semibold text-zinc-800 dark:text-zinc-100 mb-4">
        🗓️ Meeting Calendar
    </h2>

    @if(empty($meetings) || count($meetings) === 0)
        <div class="text-center py-6 text-zinc-500 dark:text-zinc-400">
            <p>No meetings found or the database is not configured yet.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($meetings as $meeting)
                <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">
                    <h3 class="text-lg font-medium text-zinc-800 dark:text-zinc-100">
                        {{ $meeting->title ?? 'Untitled Meeting' }}
                    </h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ $meeting->description ?? 'No description available.' }}
                    </p>
                    <div class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">
                        <strong>Start:</strong>
                        {{ $meeting->start_time ?? 'N/A' }}<br>
                        <strong>End:</strong>
                        {{ $meeting->end_time ?? 'N/A' }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="mt-6 text-sm text-zinc-400 text-center">
        <em>Powered by MeetingCalendar Package</em>
    </div>
</div>
