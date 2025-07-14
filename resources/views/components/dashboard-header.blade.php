<div class="py-9 mb-6 text-white">
    <h1 class="text-5xl font-bold mb-2">Benvenuto <span class="text-azure-500">{{ Auth::user()->name }}</span></h1>
    <div class="mt-6 text-lg">
        <p class="text-2xl">Panoramica generale di oggi:</p>
        <p class="mt-3">Lotti creati in giornata: <span class="text-azure-600">{{ $lottosCount }}</span></p>
        <p>Ultimo lotto creato <span class="text-azure-600 hover:text-white"><a href="{{ route('lotto.edit', $lastLotto->id) }}">{{ $lastLotto->code_lotto }}</a></span></p>
    </div>
</div>
