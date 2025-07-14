<button {{ $attributes->merge(['type' => 'submit', 'class' => '  px-4 py-2 bg-azure-400 border border-transparent rounded-md font-semibold text-xs text-dark uppercase tracking-widest hover:text-azure-600 hover:bg-gray-400 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out']) }}>
    {{ $slot }}
</button>
