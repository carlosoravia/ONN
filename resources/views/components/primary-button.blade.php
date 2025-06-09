<button {{ $attributes->merge(['type' => 'submit', 'class' => '  px-4 py-2 bg-azure-400 border border-transparent rounded-md font-semibold text-xs text-dark uppercase tracking-widest hover:bg-azure-600 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 text-center transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
