@extends('layouts.app')

@section('layout', 'full')
@section('title', 'AI Coach')

@section('content')

<div class="flex h-dvh w-full overflow-hidden bg-[#0A0A0A]">

    {{-- ==================== COACH SIDEBAR ==================== --}}
    <aside id="coachSidebar"
           class="fixed inset-y-0 left-0 z-40 w-[280px] lg:static flex flex-col bg-[#0D0D0D] border-r border-[#1d1d1d] transition-transform duration-300 -translate-x-full lg:translate-x-0">
        {{-- Header / New chat --}}
        <div class="p-4 border-b border-[#1a1a1a] space-y-3">
            <div class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#3B82F6] to-[#1E90FF] flex items-center justify-center text-[15px] shadow-lg shadow-[#3B82F6]/25">✦</span>
                <div>
                    <p class="text-sm font-black text-white leading-none">VYRON Coach</p>
                    <p class="text-[10px] text-[#666666] mt-1 uppercase tracking-[0.2em]">Intelligence Hub</p>
                </div>
                <button onclick="Vyron.toggleCoachSidebar(false)" class="lg:hidden ml-auto p-1.5 text-[#888] hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <button id="newChatBtn" onclick="Vyron.startNewChat()"
                    class="w-full bg-gradient-to-r from-[#3B82F6] to-[#1E90FF] text-white text-sm font-bold rounded-xl py-2.5 hover:brightness-110 active:scale-[0.99] transition shadow-lg shadow-[#3B82F6]/25 flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                New Chat
            </button>
        </div>

        {{-- Threads --}}
        <div id="threadList" class="flex-1 overflow-y-auto thin-scroll py-3 px-3 space-y-1.5">
            @forelse($threads as $thread)
                <button type="button" onclick="Vyron.fillDraft({{ json_encode($thread['title']) }})"
                        class="w-full text-left group rounded-xl px-3.5 py-2 bg-[#151515] border border-[#222222] hover:border-[#3B82F6]/40 hover:bg-[#181818] transition-all duration-200">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-[#3B82F6]/10 border border-[#3B82F6]/25 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-3.5 h-3.5 text-[#60A5FA]"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/></svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-[13px] font-semibold text-[#E5E7EB] truncate">{{ $thread['title'] }}</p>
                            <p class="text-[10px] text-[#666666] mt-0.5">{{ $thread['time'] }} · {{ $thread['count'] }} msgs</p>
                        </div>
                    </div>
                </button>
            @empty
                <p class="text-[12px] text-[#555555] text-center leading-relaxed px-4 pt-8">
                    No conversations yet.<br>Ask your first question below. ✦
                </p>
            @endforelse
        </div>

        {{-- Coach state --}}
        <div class="p-4 border-t border-[#1a1a1a] bg-[#0B0B0B] space-y-3">
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#555555]">Coach State</p>
            <div class="space-y-2.5">
                @foreach([$coachState['recovery'], $coachState['fatigue'], $coachState['adaptation']] as $state)
                    <div class="flex items-center justify-between">
                        <span class="text-[12.5px] text-[#a3a3a3]">{{ $state['label'] }}</span>
                        <span class="text-[12.5px] font-black {{ $state['color'] }}">{{ $state['value'] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="pt-3 mt-1 border-t border-[#1a1a1a] flex items-center gap-2 text-[11px] text-[#666666]">
                <span class="relative flex w-2 h-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-60"></span>
                    <span class="relative inline-flex rounded-full w-2 h-2 bg-emerald-500"></span>
                </span>
                VYRON Intelligence · {{ $coachState['sessions'] }} logged sessions
            </div>
        </div>
    </aside>

    {{-- Mobile backdrop --}}
    <div id="coachBackdrop" class="fixed inset-0 z-30 bg-black/60 backdrop-blur-sm hidden" onclick="Vyron.toggleCoachSidebar(false)"></div>

    {{-- ==================== MAIN CHAT ==================== --}}
    <div class="flex-1 flex flex-col min-w-0 relative">

        {{-- Glass header --}}
        <header class="glass-strong border-b border-white/10 px-4 sm:px-6 py-3.5 flex items-center gap-4">
            <button onclick="Vyron.toggleCoachSidebar(true)" class="lg:hidden p-1.5 -ml-1 text-[#a3a3a3] hover:text-white rounded-lg hover:bg-white/5 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
            </button>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <h1 class="text-lg font-extrabold text-white tracking-wide">AI Coach</h1>
                    <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-[#3B82F6]/10 text-[#60A5FA] border border-[#3B82F6]/30">Beta</span>
                </div>
                <p class="text-[11px] text-[#8f95a5] truncate mt-0.5">Reasoning about your fatigue, goals & history.</p>
            </div>
            <span class="hidden sm:flex items-center gap-1.5 text-[11px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/25 px-3 py-1.5 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Online
            </span>
            <button onclick="Vyron.clearChat()" class="flex items-center gap-1.5 text-[11px] font-semibold text-[#8f95a5] hover:text-red-300 bg-white/[0.03] border border-white/10 hover:border-red-500/40 px-3 py-2 rounded-xl transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                Clear Chat
            </button>
        </header>

        {{-- Messages --}}
        <div id="chatContainer" class="flex-1 overflow-y-auto chat-scroll px-4 sm:px-8 py-8 space-y-6 scroll-smooth">

            <div id="chatGreeting" class="{{ $conversation->isNotEmpty() ? 'hidden' : '' }} max-w-4xl mx-auto pt-6 sm:pt-10 animate-fade-up">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#3B82F6] to-[#1E90FF] flex items-center justify-center text-2xl shadow-lg shadow-[#3B82F6]/25 glow-blue mb-5">✦</div>
                <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">What can I push you on today?</h2>
                <p class="text-[#8f95a5] text-sm mt-2 max-w-lg leading-relaxed">Ask me about training, nutrition or recovery — or have me build you a full program right here.</p>
                <div class="flex flex-wrap gap-2 mt-5 mb-2">
                    @foreach($suggestedQuestions as $question)
                        <button type="button" onclick="Vyron.ask({{ json_encode($question) }})"
                                class="text-[13px] font-medium text-[#a3a3a3] bg-[#171717] border border-[#2a2a2a] hover:border-[#3B82F6]/50 hover:text-white hover:bg-[#1d1d1d] px-4 py-2 rounded-full transition-all duration-200">
                            {{ $question }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- History bubbles --}}
            @foreach($conversation as $message)
                @if($message['role'] === 'user')
                    <div class="flex justify-end msg-in">
                        <div class="max-w-[85%] sm:max-w-[70%] bg-gradient-to-br from-[#4B82FC] to-[#1E90FF] text-white rounded-2xl rounded-tr-md px-5 py-3.5 shadow-lg shadow-[#3B82F6]/20">
                            <p class="text-sm leading-relaxed whitespace-pre-wrap break-words">{{ $message['content'] }}</p>
                            <p class="text-right text-[10px] text-white/60 mt-1.5">{{ $message['time'] }}</p>
                        </div>
                    </div>
                @else
                    @php($clean = trim(preg_replace('/```(?:json)?\s*.*?```/s', '', $message['content'])))
                    @if($clean !== '')
                        <div class="flex items-start gap-3 msg-in">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#3B82F6] to-[#1E90FF] flex items-center justify-center text-white text-[10px] font-black flex-shrink-0 border border-white/10">AI</div>
                            <div class="max-w-[85%] sm:max-w-[72%] bg-[#161616] rounded-2xl rounded-tl-md px-5 py-3.5 border border-[#262626]">
                                <p class="text-sm text-[#D6DCE6] leading-relaxed whitespace-pre-wrap break-words">{!! nl2br(e($clean)) !!}</p>
                                <p class="text-[10px] text-[#666666] mt-1.5">{{ $message['time'] }}</p>
                            </div>
                        </div>
                    @endif
                @endif
            @endforeach

            {{-- Typing indicator (assistant side, right under the user's message) --}}
            <style>
                .vyron-typing-dot {
                    display: inline-block;
                    width: 10px;
                    height: 10px;
                    border-radius: 9999px;
                    background: #60A5FA;
                    animation: vyronTypingBounce 1.3s ease-in-out infinite;
                    will-change: transform;
                }
                .vyron-typing-dot:nth-of-type(2) { animation-delay: 0.15s; }
                .vyron-typing-dot:nth-of-type(3) { animation-delay: 0.3s; }
                @keyframes vyronTypingBounce {
                    0%, 60%, 100% { transform: translateY(0); opacity: 0.35; }
                    30% { transform: translateY(-7px); opacity: 1; }
                }
            </style>
            <div id="typingIndicator" class="hidden items-center gap-3 msg-in">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#3B82F6] to-[#1E90FF] flex items-center justify-center text-white text-[10px] font-black flex-shrink-0 border border-white/10">AI</div>
                <div class="bg-[#161616] rounded-2xl rounded-tl-md px-5 py-4 border border-[#262626]">
                    <div class="flex gap-1.5 items-center">
                        <span class="vyron-typing-dot"></span>
                        <span class="vyron-typing-dot"></span>
                        <span class="vyron-typing-dot"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Glass input --}}
        <div class="glass-strong border-t border-white/10 px-3 sm:px-8 pt-4 pb-[calc(env(safe-area-inset-bottom)+1rem)]">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-end gap-2.5">
                    <textarea id="messageInput" rows="1" placeholder="Ask for a plan, a setup, or the science behind it…"
                              class="flex-1 w-full bg-[#161616] border border-white/10 rounded-2xl px-4 py-3.5 text-[14px] text-white placeholder-[#666666] resize-none focus:outline-none focus:border-[#3B82F6]/60 transition-all duration-200 min-h-[52px] max-h-[150px]"></textarea>
                    <button onclick="Vyron.send()" id="sendBtn"
                            class="h-11 w-11 flex-shrink-0 rounded-xl bg-gradient-to-br from-[#3B82F6] to-[#1E90FF] text-white flex items-center justify-center hover:brightness-110 active:scale-95 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-[#3B82F6]/30">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                    </button>
                </div>
            </div>
            <p class="max-w-4xl mx-auto mt-2 text-[10px] text-[#555555] pl-1">✦ VYRON may make mistakes — verify medical advice with a professional.</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    'use strict';

    const chat = document.getElementById('chatContainer');
    const input = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const typing = document.getElementById('typingIndicator');
    const greeting = document.getElementById('chatGreeting');
    const threadList = document.getElementById('threadList');

    function scrollBottom() {
        chat.scrollTop = chat.scrollHeight;
    }

    function showGreeting(show) {
        if (greeting) greeting.classList.toggle('hidden', !show);
    }

    function autosize() {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 150) + 'px';
    }

    function stripFences(text) {
        return (text || '').replace(/```[\s\S]*?```/g, '').trim();
    }

    function appendUser(text) {
        const wrap = document.createElement('div');
        wrap.className = 'flex justify-end msg-in';
        const bubble = document.createElement('div');
        bubble.className = 'max-w-[85%] sm:max-w-[70%] bg-gradient-to-br from-[#4B82FC] to-[#1E90FF] text-white rounded-2xl rounded-tr-md px-5 py-3.5 shadow-lg shadow-[#3B82F6]/20';
        bubble.innerHTML = '<p class="text-sm leading-relaxed whitespace-pre-wrap break-words"></p><p class="text-right text-[10px] text-white/60 mt-1.5"></p>';
        bubble.querySelector('p').textContent = input.value.trim() || text;
        bubble.querySelector('p:last-of-type').textContent = new Date().toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
        wrap.appendChild(bubble);
        chat.appendChild(wrap);

        // Moves the typing indicator directly under the user's message.
        chat.appendChild(typing);
        scrollBottom();
    }

    function appendAssistant(json) {
        const strip = stripFences(json.message);
        const wrap = document.createElement('div');
        wrap.className = 'flex items-start gap-3 msg-in';
        wrap.innerHTML = '<div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#3B82F6] to-[#1E90FF] flex items-center justify-center text-white text-[10px] font-black flex-shrink-0 border border-white/10">AI</div>';
        const body = document.createElement('div');
        body.className = 'max-w-[85%] sm:max-w-[72%] bg-[#161616] rounded-2xl rounded-tl-md px-5 py-3.5 border border-[#262626]';

        if (strip) {
            const p = document.createElement('p');
            p.className = 'text-sm text-[#D6DCE6] leading-relaxed whitespace-pre-wrap break-words';
            p.textContent = strip;
            body.appendChild(p);
            const time = document.createElement('p');
            time.className = 'text-[10px] text-[#666666] mt-1.5';
            time.textContent = json.time || new Date().toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
            body.appendChild(time);
        }

        wrap.appendChild(body);
        chat.appendChild(wrap);

        // Inline plan card
        if (json.plan_html) {
            const planHolder = document.createElement('div');
            planHolder.className = 'max-w-[85%] sm:max-w-[85%] msg-in mt-2';
            planHolder.innerHTML = json.plan_html;
            chat.appendChild(planHolder);
        }
        scrollBottom();
    }

    function send() {
        const text = input.value.trim();
        if (!text || sendBtn.disabled) return;
        sendBtn.disabled = true;

        showGreeting(false);
        appendUser(text);
        input.value = '';
        autosize();

        typing.classList.remove('hidden');
        typing.classList.add('flex');
        scrollBottom();

        window.Vyron.post('/ai-coach/send', { message: text })
            .then((json) => {
                appendAssistant(json);
                if (json.thread) prependThread(json.thread);
            })
            .catch(() => {
                appendAssistant({ success: false, message: 'Network error — please try again in a moment.', time: new Date().toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' }) });
            })
            .finally(() => {
                typing.classList.add('hidden');
                typing.classList.remove('flex');
                sendBtn.disabled = false;
            });
    }

    function prependThread(thread) {
        const empty = threadList.querySelector('p');
        if (empty) empty.remove();

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.onclick = () => { input.value = thread.title || ''; autosize(); };
        btn.className = 'w-full text-left group rounded-xl px-3.5 py-2 bg-[#151515] border border-[#222222] hover:border-[#3B82F6]/40 hover:bg-[#181818] transition-all duration-200';
        btn.innerHTML =
            '<div class="flex items-center gap-2.5">' +
            '  <span class="w-7 h-7 rounded-lg bg-[#3B82F6]/10 border border-[#3B82F6]/25 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition">✦</span>' +
            '  <div class="min-w-0 flex-1"><p class="text-[13px] font-semibold text-[#E5E7EB] truncate"></p>' +
            '  <p class="text-[10px] text-[#666666] mt-0.5"></p></div>' +
            '</div>';
        btn.querySelector('p').textContent = thread.title || 'New chat';
        btn.querySelector('p:nth-of-type(2)').textContent = (thread.time || 'Just now') + ' · ' + (thread.count || '1') + ' msg' + (thread.count > 1 ? 's' : '');
        threadList.prepend(btn);
    }

    function clearChat() {
        if (!confirm('Clear your entire conversation history?')) return;
        Vyron.post('/ai-coach/clear', {})
            .then(() => {
                chat.querySelectorAll('.msg-in').forEach((el) => el.remove());
                threadList.querySelectorAll('button').forEach((el) => el.remove());
                showGreeting(true);
                Vyron.toast('Conversation cleared.', 'info');
                input.focus();
            })
            .catch(() => Vyron.toast('Could not clear the chat right now.', 'error'));
    }

    // Expose the coach API used by inline handlers
    Vyron.toggleCoachSidebar = function (force) {
        const aside = document.getElementById('coachSidebar');
        const backdrop = document.getElementById('coachBackdrop');
        const open = typeof force === 'boolean' ? force : !aside.classList.contains('-translate-x-full');
        aside.classList.toggle('-translate-x-full', !open);
        backdrop.classList.toggle('hidden', !open);
    };

    Vyron.startNewChat = function () {
        chat.querySelectorAll('.msg-in').forEach((el) => el.remove());
        typing.classList.add('hidden');
        showGreeting(true);
        input.value = '';
        autosize();
        input.focus();
        Vyron.toggleCoachSidebar(false);
    };

    Vyron.clearChat = clearChat;

    Vyron.send = send;

    Vyron.ask = function (question) {
        input.value = question;
        autosize();
        send();
    };

    Vyron.fillDraft = function (title) {
        input.value = title || '';
        autosize();
        input.focus();
        Vyron.toggleCoachSidebar(false);
    };

    // Enter to send, Shift+Enter for a new line
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            send();
        }
    });
    input.addEventListener('input', autosize);

    scrollBottom();
})();
</script>
@endpush

@endsection