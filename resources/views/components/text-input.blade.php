@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-[#222222] border border-[#333333] rounded-xl px-4 py-2.5 text-white placeholder-[#666666] focus:border-[#3B82F6] focus:outline-none focus:ring-2 focus:ring-[#3B82F6]/20 transition']) }}>