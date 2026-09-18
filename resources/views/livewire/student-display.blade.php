<?php

use Livewire\Component;
use App\Models\QueueTicket;

new class extends Component {
    public function with()
    {
        return [
            'servingList' => QueueTicket::serving()->get(),
            'activeList' => QueueTicket::active()->get(),
            'holdingList' => QueueTicket::holding()->get()
        ];
    }
};
?>

<div class="p-8 font-sans bg-gray-100 min-h-screen" wire:poll.2s>
    <div class="text-center mb-10">
        <h1 class="text-5xl font-black text-gray-800 tracking-wider uppercase shadow-sm">Queue Status</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        <div class="lg:col-span-2 space-y-10">
            
            <div class="bg-white p-10 rounded-2xl shadow-xl border-t-8 border-yellow-500">
                <h2 class="text-3xl mb-8 font-bold text-gray-700 uppercase tracking-widest text-center">Currently Serving</h2>
                
                @if($servingList->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @foreach($servingList as $ticket)
                            <div class="bg-yellow-50 p-8 rounded-xl text-center shadow-md border border-yellow-200 animate-pulse">
                                <p class="text-3xl text-gray-500 font-semibold mb-2">Window: {{ $ticket->assigned_teller }}</p>
                                <p class="text-7xl font-black text-gray-900">{{ $ticket->tracking_number }}</p>
                                <p class="text-2xl text-gray-600 mt-2">{{ $ticket->name }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-400 text-2xl font-semibold">No one is currently being served.</p>
                    </div>
                @endif
            </div>

            <div class="bg-white p-10 rounded-2xl shadow-xl border-t-8 border-green-500">
                <h2 class="text-3xl mb-8 font-bold text-gray-700 uppercase tracking-widest flex justify-between items-center">
                    Physical Line (Active)
                    <span class="bg-green-100 text-green-800 text-xl py-2 px-6 rounded-full">{{ $activeList->count() }} Waiting</span>
                </h2>
                
                @if($activeList->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach($activeList as $ticket)
                            <div class="bg-green-50 p-6 rounded-xl text-center shadow-sm border border-green-200">
                                <p class="text-4xl font-black text-gray-800">{{ $ticket->tracking_number }}</p>
                                <p class="text-lg text-gray-600 mt-1 truncate">{{ $ticket->name }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <p class="text-gray-400 text-xl italic">Physical line is empty.</p>
                    </div>
                @endif
            </div>

        </div>

        <div class="bg-white p-10 rounded-2xl shadow-xl border-t-8 border-blue-500 flex flex-col max-h-[85vh] overflow-hidden">
            <h2 class="text-3xl mb-6 font-bold text-gray-700 uppercase tracking-widest flex justify-between items-center border-b pb-4">
                Virtual Line
                <span class="bg-blue-100 text-blue-800 text-xl py-2 px-6 rounded-full">{{ $holdingList->count() }}</span>
            </h2>
            
            <div class="overflow-y-auto flex-1 pr-4 space-y-4">
                @forelse($holdingList as $ticket)
                    <div class="bg-blue-50 p-5 rounded-lg border border-blue-100 flex justify-between items-center shadow-sm">
                        <span class="text-3xl font-black text-gray-800">{{ $ticket->tracking_number }}</span>
                        <span class="text-lg text-gray-600">{{ $ticket->name }}</span>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <p class="text-gray-400 text-lg italic">Virtual line is empty.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
