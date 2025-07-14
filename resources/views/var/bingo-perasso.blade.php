<x-app-layout>
    <div class="max-w-4xl mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-6">Avvia Gioco Bingo</h1>

    <form id="giocoForm" method="POST" action="{{ route('bingo.avvia') }}">
        @csrf
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Avvia il Gioco
        </button>
    </form>

    <div id="output" class="mt-6 p-4 border rounded bg-gray-100 text-sm font-mono whitespace-pre-wrap"></div>
</div>

<script>
    document.getElementById('giocoForm').addEventListener('submit', function (e) {
        e.preventDefault();

        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': this.querySelector('input[name="_token"]').value,
            },
        })
        .then(response => response.json())
        .then(data => {
            const output = document.getElementById('output');
            output.textContent = data.output ?? data.error;
        })
        .catch(error => {
            document.getElementById('output').textContent = 'Errore durante l\'esecuzione.';
            console.error(error);
        });
    });
</script>
</x-app-layout>
