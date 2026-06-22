<div class="relative" x-data="{ open: @entangle('isOpen') }" @click.outside="open = false" wire:poll.10s="poll">

    {{-- Bell Icon --}}
    <button @click="$wire.toggle()" id="notification-bell-btn"
            class="relative p-2 text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 transition-colors focus:outline-none">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        {{-- Unread Badge --}}
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white dark:ring-slate-900">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Panel --}}
    <div x-show="open" style="display: none;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute left-0 mt-2 w-80 rounded-2xl bg-white dark:bg-slate-800 shadow-xl ring-1 ring-black/5 dark:ring-white/10 origin-top-left z-50 overflow-hidden">

        {{-- Header --}}
        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 class="font-bold text-slate-900 dark:text-white text-sm">الإشعارات</h3>
            <div class="flex items-center gap-2">
                @if($unreadCount > 0)
                    <span class="text-xs bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 px-2 py-0.5 rounded-full font-bold">
                        {{ $unreadCount }} جديد
                    </span>
                    <button wire:click="markAllRead"
                            class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                        تحديد الكل كمقروء
                    </button>
                @endif
            </div>
        </div>

        {{-- Notifications List --}}
        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-700">
            @forelse($notifications as $notification)
                <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors
                            {{ is_null($notification->read_at) ? 'bg-indigo-50/60 dark:bg-indigo-900/10' : '' }}">
                    <div class="flex items-start gap-3">

                        {{-- Icon --}}
                        <div class="w-9 h-9 rounded-full shrink-0 flex items-center justify-center
                                    {{ isset($notification->data['amount']) ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-indigo-100 dark:bg-indigo-900/40' }}">
                            @if(isset($notification->data['amount']))
                                {{-- XP icon --}}
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @else
                                {{-- Generic bell icon --}}
                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif

                            {{-- Unread dot --}}
                            @if(is_null($notification->read_at))
                                <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-slate-800 dark:text-slate-200 leading-snug">
                                {{ $notification->data['message'] ?? 'إشعار جديد' }}
                            </p>
                            <p class="text-xs text-slate-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center">
                    <svg class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-3" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="text-sm text-slate-500 dark:text-slate-400">لا توجد إشعارات بعد</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
