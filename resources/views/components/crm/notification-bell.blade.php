{{--
    resources/views/components/crm/notification-bell.blade.php
    Taruh di layout header: @include('components.crm.notification-bell')
--}}
 
<div class="relative" x-data="notificationBell()" x-init="init()">

    {{-- Bell button --}}
    <button @click="toggle()"
            class="relative p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        {{-- Badge --}}
        <span x-show="count > 0"
              x-text="count > 99 ? '99+' : count"
              class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none">
        </span>
    </button>

    {{-- Dropdown --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.outside="open = false"
         class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl z-50 overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
            <span class="text-sm font-semibold text-gray-800 dark:text-white">Notifikasi</span>
            <div class="flex items-center gap-2">
                <button @click="markAllRead()"
                        x-show="count > 0"
                        class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                    Tandai semua dibaca
                </button>
                <a href="{{ route('crm.notifications.index') }}"
                   class="text-xs text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                    Lihat semua
                </a>
            </div>
        </div>

        {{-- List --}}
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
            <template x-if="items.length === 0">
                <div class="px-4 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                    Tidak ada notifikasi baru.
                </div>
            </template>
            <template x-for="item in items" :key="item.id">
                <div @click="readAndGo(item)"
                     class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors">

                    {{-- Icon --}}
                    <div class="flex-shrink-0 mt-0.5">
                        <div :class="{
                                'bg-green-100 dark:bg-green-900/30': item.color === 'green',
                                'bg-red-100 dark:bg-red-900/30':     item.color === 'red',
                                'bg-yellow-100 dark:bg-yellow-900/30': item.color === 'yellow',
                                'bg-orange-100 dark:bg-orange-900/30': item.color === 'orange',
                                'bg-blue-100 dark:bg-blue-900/30':   item.color === 'blue',
                             }"
                             class="w-8 h-8 rounded-full flex items-center justify-center">
                            {{-- Task icon --}}
                            <template x-if="item.icon === 'task'">
                                <svg class="w-4 h-4" :class="{
                                        'text-green-600 dark:text-green-400': item.color === 'green',
                                        'text-red-600 dark:text-red-400':     item.color === 'red',
                                        'text-yellow-600 dark:text-yellow-400': item.color === 'yellow',
                                        'text-blue-600 dark:text-blue-400':   item.color === 'blue',
                                     }"
                                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                </svg>
                            </template>
                            {{-- Won icon --}}
                            <template x-if="item.icon === 'won'">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </template>
                            {{-- Lost / followup icon --}}
                            <template x-if="item.icon === 'lost' || item.icon === 'followup'">
                                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                            </template>
                            {{-- Bell / default --}}
                            <template x-if="item.icon === 'bell'">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                                </svg>
                            </template>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 leading-snug" x-text="item.title"></p>
                        <p x-show="item.message" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate" x-text="item.message"></p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5" x-text="item.created_at"></p>
                    </div>
                </div>
            </template>
        </div>

    </div>
</div>

@push('scripts')
<script>
function notificationBell() {
    return {
        open:  false,
        count: 0,
        items: [],
        interval: null,

        init() {
            this.fetch();
            // Poll setiap 30 detik
            this.interval = setInterval(() => this.fetch(), 30000);
        },

        toggle() {
            this.open = !this.open;
            if (this.open) this.fetch();
        },

        async fetch() {
            try {
                const res  = await fetch('{{ route('crm.notifications.unread') }}', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.count = data.count;
                this.items = data.items;
            } catch (e) {
                console.warn('Notification fetch error:', e);
            }
        },

        async markAllRead() {
            await fetch('{{ route('crm.notifications.mark-all-read') }}', {
                method : 'POST',
                headers: {
                    'X-CSRF-TOKEN'    : document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept'          : 'application/json',
                },
            });
            this.count = 0;
            this.items = [];
        },

        async readAndGo(item) {
            // Mark as read
            await fetch(`{{ url('/crm/notifications') }}/${item.id}/read`, {
                method : 'POST',
                headers: {
                    'X-CSRF-TOKEN'    : document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept'          : 'application/json',
                },
            });

            // Kurangi count
            this.count = Math.max(0, this.count - 1);
            this.items = this.items.filter(i => i.id !== item.id);

            // Redirect
            if (item.action_url) {
                window.location.href = item.action_url;
            }

            this.open = false;
        },
    };
}
</script>
@endpush