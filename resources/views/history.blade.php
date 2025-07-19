<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('History Anda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if(session('status'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(Auth::user()->role === 'Kepala Dinas')
                        @include('logbooks.history.verify')
                    @elseif(Auth::user()->role === 'Kepala Bagian 1' || Auth::user()->role === 'Kepala Bagian 2')
                        @include('logbooks.history.index')
                        @include('logbooks.history.verify')
                        </div>
                    @elseif(Auth::user()->role === 'Staf Bagian 1' || Auth::user()->role === 'Staf Bagian 2')
                        @include('logbooks.history.index')
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>