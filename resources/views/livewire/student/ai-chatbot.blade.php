<div class="fixed bottom-6 left-6 z-50 font-sans" dir="rtl">
    
    {{-- Chat Toggle Button --}}
    <button wire:click="toggleChat" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-full p-4 shadow-lg shadow-indigo-600/30 transition-transform hover:scale-105 focus:outline-none flex items-center justify-center">
        @if($isOpen)
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        @else
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
        @endif
    </button>

    {{-- Chat Window --}}
    @if($isOpen)
        <div class="absolute bottom-16 left-0 w-80 sm:w-96 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col origin-bottom-left transition-all" style="height: 500px; max-height: calc(100vh - 120px);">
            
            {{-- Header --}}
            <div class="bg-indigo-600 text-white p-4 flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm">المساعد الذكي (AI)</h3>
                    <p class="text-xs text-indigo-200">متاح لمساعدتك الآن</p>
                </div>
            </div>

            {{-- Messages Area --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50 dark:bg-slate-950" id="chat-messages" x-init="$el.scrollTop = $el.scrollHeight">
                @if(count($messages) === 0)
                    <div class="text-center text-sm text-slate-500 dark:text-slate-400 mt-10">
                        مرحباً بك! كيف يمكنني مساعدتك في @if($lesson) "{{ $lesson->title }}" @else دورتك @endif ؟
                    </div>
                @else
                    @foreach($messages as $msg)
                        <div class="flex {{ $msg->role === 'user' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[85%] rounded-2xl px-4 py-2 text-sm {{ $msg->role === 'user' ? 'bg-indigo-600 text-white rounded-bl-none' : 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-br-none' }}">
                                {{ $msg->content }}
                            </div>
                        </div>
                    @endforeach
                @endif
                
                {{-- Loading indicator during request --}}
                <div wire:loading wire:target="sendMessage" class="flex justify-start">
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl rounded-br-none px-4 py-3 flex gap-1 items-center">
                        <div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                </div>
            </div>

            {{-- Input Area --}}
            <div class="p-3 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-700">
                <form wire:submit="sendMessage" class="flex items-center gap-2">
                    <input type="text" wire:model="newMessage" placeholder="اسأل المساعد..." 
                           class="flex-1 bg-slate-100 dark:bg-slate-800 border-transparent focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-900 focus:ring-0 rounded-xl text-sm px-4 py-2 text-slate-800 dark:text-slate-100 transition-colors"
                           required>
                    <button type="submit" class="w-10 h-10 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl flex items-center justify-center shrink-0 transition-colors" wire:loading.attr="disabled">
                        <svg class="w-5 h-5 rtl:-scale-x-100" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
