<x-guest-layout>
    @auth
        <h1 class="text-white text-center text-3xl uppercase">Ciao <span class="text-azure-400">{{ Auth::user()->name }}</span></h1>
        <div class="h-fit flex justify-center my-5">
            <a class="hover:bg-azure-400 hover:text-dark transition delay-150 duration-300 ease-in-out text-center text-white my-auto p-5 mx-3 shadow-md border border-azure-500 rounded-xl focus:color-dark" href="{{Auth::user()->role == 'Admin' ? route('admin.index') : route('operator.index')}}">Vai alla dashboard</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-center bg-red-500 hover:bg-red-700 transition delay-150 duration-300 ease-in-out my-auto p-5 mx-3 shadow-md border border-red-500 text-dark rounded-xl focus:color-dark" href="{{route('logout')}}">Esci</button>
            </form>
        </div>
    @endauth
    @guest
        <div class="h-fit flex justify-center">
            <a class="px-4 py-2 bg-azure-400 border border-transparent rounded-md font-semibold text-xs text-dark uppercase tracking-widest hover:bg-azure-600 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 text-center transition ease-in-out duration-150" href="{{route('login')}}">Vai al login</a>
        </div>
    @endguest
    <x-footer />
</x-guest-layout>
