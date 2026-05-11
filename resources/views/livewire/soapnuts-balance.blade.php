<div>
    @auth
        <span class="text-gold-400 font-semibold">
        {{ number_format((int) auth()->user()->soapnuts) }} 🌰
    </span>
    @endauth
</div>
