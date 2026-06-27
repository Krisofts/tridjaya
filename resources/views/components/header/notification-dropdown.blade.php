{{-- Notification Dropdown --}}
{{-- Generic: bisa tampil notif dari CRM, ecommerce, dan modul lain --}}

<div
    class="relative"
    x-data="{
        open:   false,
        total:  0,
        items:  [],
        loading: false,

        async fetch() {
            this.loading = true;
            try {
                const res  = await fetch('{{ route('notifications.index') }}');
                const data = await res.json();
                this.total = data.total;
                this.items = data.items;
            } catch(e) {
                console.error('Notifikasi gagal dimuat', e);
            } finally {
                this.loading = false;
            }
        },

        async toggle() {
            this.open = !this.open;
            if (this.open) await this.fetch();
        },

        async markRead(id, url) {
            await fetch('{{ route('notifications.read', ':id') }}'.replace(':id', id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            });
            if (url) window.location.href = url;
            else {
                this.items = this.items.map(n => n.id === id ? { ...n, is_read: true } : n);
                this.total = Math.max(0, this.total - 1);
            }
        },

        async markAllRead() {
            await fetch('{{ route('notifications.read-all') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
            });
            this.items = this.items.map(n => ({ ...n, is_read: true }));
            this.total = 0;
        },

        priorityColor(p) {
            return { urgent: 'bg-red-500', high: 'bg-orange-400', medium: 'bg-yellow-400' }[p] ?? 'bg-gray-300';
        },

        typeIcon(type) {
            return {
                task_due:       'ti-clock',
                stage_changed:  'ti-arrow-right',
                order_created:  'ti-shopping-cart',
                order_paid:     'ti-circle-check',
            }[type] ?? 'ti-bell';
        },
    }"
    x-init="fetch(); setInterval(() => { if (!open) fetch(); }, 60000)"
    @click.away="open = false"
>

    {{-- BELL BUTTON --}}
    <button
        @click="toggle()"
        type="button"
        class="relative flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
    >
        {{-- BADGE COUNT --}}
        <span
            x-show="total > 0"
            x-text="total > 9 ? '9+' : total"
            class="absolute -right-1 -top-1 z-10 flex h-5 w-5 items-center justify-center rounded-full text-xs font-bold text-white"
            :class="total > 0 ? 'bg-red-500' : 'bg-brand-500'"
        ></span>

        {{-- PING saat ada unread --}}
        <span x-show="total > 0" class="absolute -right-1 -top-1 z-0 h-5 w-5 animate-ping rounded-full bg-red-400 opacity-50"></span>

        {{-- BELL ICON --}}
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H4.37504H15.625H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875ZM8.00004 17.7085C8.00004 18.1228 8.33583 18.4585 8.75004 18.4585H11.25C11.6643 18.4585 12 18.1228 12 17.7085C12 17.2943 11.6643 16.9585 11.25 16.9585H8.75004C8.33583 16.9585 8.00004 17.2943 8.00004 17.7085Z"
            />
        </svg>
    </button>

    {{-- DROPDOWN PANEL --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 z-50 mt-3 w-80 rounded-2xl border border-gray-200 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900 sm:w-96"
    >

        {{-- HEADER --}}
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3 dark:border-gray-800">
            <div class="flex items-center gap-2">
                <h5 class="text-sm font-semibold text-gray-800 dark:text-white">Notifikasi</h5>
                <span x-show="total > 0"
                    class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-600 dark:bg-red-500/15 dark:text-red-400">
                    <span x-text="total + ' belum dibaca'"></span>
                </span>
            </div>
            <div class="flex items-center gap-2">
                <button x-show="total > 0" @click="markAllRead()"
                    class="text-xs text-brand-500 hover:underline">
                    Tandai semua dibaca
                </button>
                <button @click="open = false"
                    class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800" type="button">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- BODY --}}
        <div class="max-h-96 overflow-y-auto">

            {{-- LOADING --}}
            <div x-show="loading" class="flex items-center justify-center py-8">
                <svg class="h-5 w-5 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>

            {{-- EMPTY --}}
            <div x-show="!loading && items.length === 0" class="py-10 text-center">
                <i class="ti ti-bell-off text-3xl text-gray-300 dark:text-gray-600"></i>
                <p class="mt-2 text-sm text-gray-400">Tidak ada notifikasi</p>
            </div>

            {{-- LIST --}}
            <template x-for="item in items" :key="item.id">
                <button
                    @click="markRead(item.id, item.url)"
                    class="flex w-full items-start gap-3 border-b border-gray-50 px-4 py-3 text-left transition last:border-0 dark:border-gray-800"
                    :class="item.is_read ? 'hover:bg-gray-50 dark:hover:bg-gray-800' : 'bg-brand-50/40 hover:bg-brand-50 dark:bg-brand-500/5 dark:hover:bg-brand-500/10'"
                >
                    {{-- ICON --}}
                    <span class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full"
                        :class="item.is_read ? 'bg-gray-100 dark:bg-gray-800' : 'bg-brand-100 dark:bg-brand-500/20'">
                        <i class="text-sm" :class="[typeIcon(item.type), item.is_read ? 'text-gray-400' : 'text-brand-500 ti']"></i>
                    </span>

                    {{-- CONTENT --}}
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-gray-800 dark:text-white" x-text="item.title"></p>
                        <p class="mt-0.5 truncate text-xs text-gray-500 dark:text-gray-400" x-text="item.body"></p>
                        <p x-show="item.lead_name" class="mt-0.5 truncate text-xs text-gray-400" x-text="item.lead_name"></p>
                    </div>

                    {{-- TIME --}}
                    <div class="shrink-0 text-right">
                        <p x-show="item.due_at" class="text-xs font-medium text-gray-500" x-text="item.due_at"></p>
                        <p class="text-xs text-gray-400" x-text="item.created_at"></p>
                        {{-- UNREAD DOT --}}
                        <span x-show="!item.is_read" class="mt-1 inline-block h-2 w-2 rounded-full bg-brand-500"></span>
                    </div>
                </button>
            </template>

        </div>

        {{-- FOOTER --}}
        <div class="border-t border-gray-100 p-3 dark:border-gray-800">
            <a href="{{ route('crm.leads.index') }}" @click="open = false"
                class="flex w-full items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700">
                <i class="ti ti-layout-dashboard text-base"></i>
                Buka Dashboard
            </a>
        </div>

    </div>
</div>