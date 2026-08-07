{{-- Single chat message bubble. Expected: $msg = ['role' => 'user'|'assistant', 'content' => string, 'time' => string] --}}
<div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }} msg-in">
    <div class="flex items-end gap-3 max-w-[85%] {{ $msg['role'] === 'user' ? 'flex-row-reverse' : 'flex-row' }}">
        @if($msg['role'] === 'assistant')
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#3B82F6] to-[#1D4ED8] flex items-center justify-center flex-shrink-0 shadow-md shadow-[#3B82F6]/20 text-[10px] font-bold text-white">AI</div>
        @else
            <div class="w-9 h-9 rounded-full bg-[#3B82F6]/20 border border-[#3B82F6]/30 flex items-center justify-center text-[#60A5FA] text-xs font-bold flex-shrink-0">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}</div>
        @endif
        <div class="flex flex-col gap-1 {{ $msg['role'] === 'user' ? 'items-end' : 'items-start' }}">
            <div class="px-4 py-3 rounded-2xl text-[15px] leading-relaxed {{ $msg['role'] === 'user' ? 'bg-[#3B82F6] text-white rounded-br-md shadow-lg shadow-[#3B82F6]/10' : 'bg-[#222222] text-[#E5E7EB] rounded-bl-md border border-[#2A2A2A]' }}">
                <div class="whitespace-pre-wrap break-words">{{ $msg['content'] }}</div>
            </div>
            <span class="text-[10px] text-[#555555] px-1">{{ $msg['time'] ?? '' }}</span>
        </div>
    </div>
</div>
