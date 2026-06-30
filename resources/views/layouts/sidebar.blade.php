@php
    use App\Helpers\MenuHelper;
    $menuGroups = MenuHelper::getMenuGroups();
@endphp

<aside id="sidebar"
    class="fixed flex flex-col mt-0 top-0 px-5 left-0 bg-white dark:bg-gray-900 dark:border-gray-800 text-gray-900 h-screen transition-all duration-300 ease-in-out z-99999 border-r border-gray-200"
    x-data="{
        openSubmenus: {
            {{-- Pre-compute di PHP — tidak ada JS logic untuk active detection --}}
            @foreach ($menuGroups as $groupIndex => $menuGroup)
                @foreach ($menuGroup['items'] as $itemIndex => $item)
                    @if (isset($item['subItems']) && MenuHelper::isMenuActive($item['subItems']))
                        '{{ $groupIndex }}-{{ $itemIndex }}': true,
                    @endif
                @endforeach
            @endforeach
        },
        toggleSubmenu(key) {
            const isOpen = this.openSubmenus[key] ?? false;
            {{-- Tutup semua dulu --}}
            this.openSubmenus = {};
            {{-- Toggle yang diklik --}}
            if (!isOpen) this.openSubmenus[key] = true;
        },
        isOpen(key) {
            return this.openSubmenus[key] ?? false;
        }
    }"
    :class="{
        'w-[290px]': $store.sidebar.isExpanded || $store.sidebar.isMobileOpen || $store.sidebar.isHovered,
        'w-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
        'translate-x-0': $store.sidebar.isMobileOpen,
        '-translate-x-full xl:translate-x-0': !$store.sidebar.isMobileOpen
    }"
    @mouseenter="if (!$store.sidebar.isExpanded) $store.sidebar.setHovered(true)"
    @mouseleave="$store.sidebar.setHovered(false)">

    {{-- Logo --}}
    <div class="pt-8 pb-7 flex"
        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'">
        <a href="{{ route('dashboard.home') }}">
            <img x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                class="dark:hidden" src="/images/logo/logo.svg" alt="Logo" width="150" height="40" />
            <img x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                class="hidden dark:block" src="/images/logo/logo-dark.svg" alt="Logo" width="150" height="40" />
            <img x-show="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen"
                src="/images/logo/logo-icon.svg" alt="Logo" width="32" height="32" />
        </a>
    </div>

    {{-- Navigation --}}
    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <nav class="mb-6">
            <div class="flex flex-col gap-4">

                @foreach ($menuGroups as $groupIndex => $menuGroup)
                    <div>
                        {{-- Group Title --}}
                        <h2 class="mb-4 text-xs uppercase flex leading-[20px] text-gray-400"
                            :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'lg:justify-center' : 'justify-start'">
                            <template x-if="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                                <span>{{ $menuGroup['title'] }}</span>
                            </template>
                            <template x-if="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z" fill="currentColor"/>
                                </svg>
                            </template>
                        </h2>

                        {{-- Items --}}
                        <ul class="flex flex-col gap-1">
                            @foreach ($menuGroup['items'] as $itemIndex => $item)
                                @php
                                    $key         = $groupIndex . '-' . $itemIndex;
                                    $parentActive = isset($item['subItems']) && MenuHelper::isMenuActive($item['subItems']);
                                    $icon        = $item['icon'] ?? 'default';
                                    $component   = view()->exists("components.icons.{$icon}") ? "icons.{$icon}" : "icons.default";
                                @endphp
                                <li>
                                    @if (isset($item['subItems']))

                                        {{-- Parent dengan submenu --}}
                                        <button @click="toggleSubmenu('{{ $key }}')"
                                            class="menu-item group w-full"
                                            :class="[
                                                isOpen('{{ $key }}') ? 'menu-item-active' : 'menu-item-inactive',
                                                !$store.sidebar.isExpanded && !$store.sidebar.isHovered ? 'xl:justify-center' : 'xl:justify-start'
                                            ]">

                                            <span :class="isOpen('{{ $key }}') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                                <x-dynamic-component :component="$component" />
                                            </span>

                                            <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                                class="menu-item-text">
                                                {{ $item['name'] }}
                                            </span>

                                            <svg x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                                class="ml-auto w-5 h-5 transition-transform duration-200"
                                                :class="{ 'rotate-180 text-brand-500': isOpen('{{ $key }}') }"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>

                                        {{-- Submenu items --}}
                                        <div x-show="isOpen('{{ $key }}') && ($store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen)"
                                            x-collapse>
                                            <ul class="mt-2 space-y-1 ml-9">
                                                @foreach ($item['subItems'] as $subItem)
                                                    @php
                                                        $url       = MenuHelper::url($subItem['route']);
                                                        $itemActive = MenuHelper::isActive($subItem['route']);
                                                    @endphp
                                                    <li>
                                                        <a href="{{ $url }}"
                                                            class="menu-dropdown-item {{ $itemActive ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' }}">
                                                            {{ $subItem['name'] }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                    @else

                                        {{-- Simple item tanpa submenu --}}
                                        @php
                                            $url       = MenuHelper::url($item['route'] ?? '');
                                            $itemActive = isset($item['route']) && MenuHelper::isActive($item['route']);
                                        @endphp
                                        <a href="{{ $url }}" class="menu-item group"
                                            :class="[
                                                '{{ $itemActive ? 'menu-item-active' : 'menu-item-inactive' }}',
                                                (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'
                                            ]">
                                            <span class="{{ $itemActive ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}">
                                                {!! MenuHelper::getIconSvg($icon) !!}
                                            </span>
                                            <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                                class="menu-item-text">
                                                {{ $item['name'] }}
                                            </span>
                                        </a>

                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach

            </div>
        </nav>

        {{-- Sidebar Widget --}}
        <div x-data
            x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
            x-transition
            class="mt-auto">
            @include('layouts.sidebar-widget')
        </div>

    </div>
</aside>

{{-- Mobile Overlay --}}
<div x-show="$store.sidebar.isMobileOpen"
    @click="$store.sidebar.setMobileOpen(false)"
    class="fixed z-50 h-screen w-full bg-gray-900/50"></div>