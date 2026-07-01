{{-- Notification Dropdown Component --}}
{{-- Taruh di layouts/app-header.blade.php menggantikan komponen notifikasi lama --}}

@php
    $notifService     = app(\App\CRM\Services\NotificationService::class);
    $unreadCount      = $notifService->unreadCount(Auth::user());
    $recentNotifs     = $notifService->unread(Auth::user(), 8);
@endphp

<div class="relative" x-data="{
    dropdownOpen: false,
    count: {{ $unreadCount }},
    toggleDropdown() { this.dropdownOpen = !this.dropdownOpen; },
    closeDropdown() { this.dropdownOpen = false; },
}" @click.away="closeDropdown()">

    {{-- Bell Button --}}
    <button
        class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-dark-900 h-11 w-11 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
        @click="toggleDropdown()"
        type="button"
    >
        {{-- Badge --}}
        <span x-show="count > 0"
              class="absolute right-0 top-0.5 z-10 h-2 w-2 rounded-full bg-orange-400">
            <span class="absolute inline-flex w-full h-full bg-orange-400 rounded-full opacity-75 animate-ping"></span>
        </span>

        {{-- Bell Icon --}}
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H4.37504H15.625H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875ZM8.00004 17.7085C8.00004 18.1228 8.33583 18.4585 8.75004 18.4585H11.25C11.6643 18.4585 12 18.1228 12 17.7085C12 17.2943 11.6643 16.9585 11.25 16.9585H8.75004C8.33583 16.9585 8.00004 17.2943 8.00004 17.7085Z" fill=""/>
        </svg>
    </button>

    {{-- Dropdown --}}
    <div
        x-show="dropdownOpen"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute -right-[240px] mt-[17px] flex h-[480px] w-[350px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark sm:w-[361px] lg:right-0"
        style="display: none;"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2">
                <h5 class="text-lg font-semibold text-gray-800 dark:text-white/90">Notifikasi</h5>
                @if($unreadCount > 0)
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                        {{ $unreadCount }}
                    </span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if($unreadCount > 0)
                    <form method="POST" action="{{ route('crm.notifications.mark-all-read') }}">
                        @csrf
                        <button type="submit"
                                class="text-xs text-blue-600 dark:text-blue-400 hover:underline"
                                @click="count = 0; closeDropdown()">
                            Tandai semua dibaca
                        </button>
                    </form>
                @endif
                <button @click="closeDropdown()" class="text-gray-500 dark:text-gray-400" type="button">
                    <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M6.21967 7.28131C5.92678 6.98841 5.92678 6.51354 6.21967 6.22065C6.51256 5.92775 6.98744 5.92775 7.28033 6.22065L11.999 10.9393L16.7176 6.22078C17.0105 5.92789 17.4854 5.92788 17.7782 6.22078C18.0711 6.51367 18.0711 6.98855 17.7782 7.28144L13.0597 12L17.7782 16.7186C18.0711 17.0115 18.0711 17.4863 17.7782 17.7792C17.4854 18.0721 17.0105 18.0721 16.7176 17.7792L11.999 13.0607L7.28033 17.7794C6.98744 18.0722 6.51256 18.0722 6.21967 17.7794C5.92678 17.4865 5.92678 17.0116 6.21967 16.7187L10.9384 12L6.21967 7.28131Z" fill=""/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Notification List --}}
        <ul class="flex flex-col h-auto overflow-y-auto custom-scrollbar">
            @forelse($recentNotifs as $notif)
            @php
                $iconColors = [
                    'red'    => ['bg' => 'bg-red-100 dark:bg-red-900/30',    'text' => 'text-red-500'],
                    'green'  => ['bg' => 'bg-green-100 dark:bg-green-900/30','text' => 'text-green-500'],
                    'yellow' => ['bg' => 'bg-yellow-100 dark:bg-yellow-900/30','text' => 'text-yellow-500'],
                    'orange' => ['bg' => 'bg-orange-100 dark:bg-orange-900/30','text' => 'text-orange-500'],
                    'blue'   => ['bg' => 'bg-blue-100 dark:bg-blue-900/30',  'text' => 'text-blue-500'],
                ];
                $color = $iconColors[$notif->iconColor()] ?? $iconColors['blue'];
            @endphp
            <li>
                <form method="POST" action="{{ route('crm.notifications.read', $notif) }}" class="block">
                    @csrf
                    <button type="submit"
                            class="w-full text-left flex gap-3 rounded-lg border-b border-gray-100 px-4.5 py-3 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-white/5 transition-colors
                                   {{ ! $notif->is_read ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}"
                            @click="count = Math.max(0, count - 1)">

                        {{-- Icon --}}
                        <span class="relative flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center {{ $color['bg'] }}">
                            @if($notif->icon() === 'won')
                                <svg class="w-4 h-4 {{ $color['text'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                            @elseif($notif->icon() === 'task')
                                <svg class="w-4 h-4 {{ $color['text'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            @elseif($notif->icon() === 'assigned')
                                <svg class="w-4 h-4 {{ $color['text'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            @elseif(in_array($notif->icon(), ['lost', 'followup']))
                                <svg class="w-4 h-4 {{ $color['text'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            @else
                                <svg class="w-4 h-4 {{ $color['text'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                            @endif
                            {{-- Dot unread --}}
                            @if(! $notif->is_read)
                                <span class="absolute top-0 right-0 w-2 h-2 bg-blue-500 rounded-full border border-white dark:border-gray-900"></span>
                            @endif
                        </span>

                        {{-- Content --}}
                        <span class="block flex-1 min-w-0">
                            <span class="mb-1 block text-sm {{ ! $notif->is_read ? 'font-medium text-gray-800 dark:text-white/90' : 'text-gray-600 dark:text-gray-300' }}">
                                {{ $notif->title }}
                            </span>
                            @if($notif->message)
                                <span class="block text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ $notif->message }}
                                </span>
                            @endif
                            <span class="flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500 mt-1">
                                @php
                                    $typeLabel = match($notif->type) {
                                        'task_overdue'     => 'Task',
                                        'followup_overdue' => 'Follow-up',
                                        'lead_won'         => 'Won',
                                        'lead_lost'        => 'Lost',
                                        'lead_assigned'    => 'Lead Baru',
                                        'task_reminder'    => 'Reminder',
                                        default            => 'Notifikasi',
                                    };
                                @endphp
                                <span>{{ $typeLabel }}</span>
                                <span class="w-1 h-1 bg-gray-400 rounded-full"></span>
                                <span>{{ $notif->created_at->diffForHumans() }}</span>
                            </span>
                        </span>
                    </button>
                </form>
            </li>
            @empty
            <li class="flex flex-col items-center justify-center py-10 text-center">
                <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                <p class="text-sm text-gray-400 dark:text-gray-500">Tidak ada notifikasi baru</p>
            </li>
            @endforelse
        </ul>

        {{-- View All Button --}}
        <a href="{{ route('crm.notifications.index') }}"
           class="mt-3 flex justify-center rounded-lg border border-gray-300 bg-white p-3 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-200 transition-colors"
           @click="closeDropdown()">
            Lihat Semua Notifikasi
            @if($unreadCount > 0)
                <span class="ml-1.5 px-1.5 py-0.5 text-xs bg-red-500 text-white rounded-full">{{ $unreadCount }}</span>
            @endif
        </a>
    </div>
</div>