<x-guest-layout>
    @auth
        <h1 class="text-white text-center text-5xl">Ciao <span class="text-blue-400">{{ Auth::user()->name }}</span></h1>
        <div class="h-fit flex justify-center my-5">
            <a class="text-white text-center my-auto p-5 mx-3 shadow-md border rounded-xl focus:color-dark" href="{{Auth::user()->role == 'Admin' ? route('admin.index') : route('operator.index')}}">Vai alla dashboard</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-center my-auto p-5 mx-3 shadow-md border border-red-500 text-red-500 rounded-xl focus:color-dark" href="{{route('logout')}}">Log Out</button>
            </form>
        </div>
    @endauth
    @guest
        <div class="h-fit flex justify-center">
            <a class="text-white text-center my-auto p-5 shadow-md border rounded-xl focus:color-dark" href="{{route('login')}}">Vai al login</a>
        </div>
    @endguest
</x-guest-layout>
