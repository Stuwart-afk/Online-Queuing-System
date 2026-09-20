<?php

use Livewire\Component;
use App\Models\QueueTicket;
use App\Http\Controllers\QueueController;

new class extends Component {
    public $tellerName = 'Window 1'; 
    public ?QueueTicket $activeTicket = null;
    public $pinInput = '';
    public $isAuthenticated = false;
    public $isOpen = false;
    
    private $correctPin = '1234';

    public function mount()
    {
        if (session()->get('cashier_authenticated') === true) {
            $this->isAuthenticated = true;
            $this->loadActiveTicket();
            if ($this->activeTicket) {
                $this->isOpen = true;
            }
        }
    }

    public function authenticate()
    {
        if ($this->pinInput === $this->correctPin) {
            session()->put('cashier_authenticated', true);
            $this->isAuthenticated = true;
            $this->loadActiveTicket();
            $this->pinInput = '';
        } else {
            session()->flash('auth_error', 'Invalid PIN. Please try again.');
            $this->pinInput = '';
        }
    }

    public function logout()
    {
        session()->forget('cashier_authenticated');
        $this->isAuthenticated = false;
        $this->activeTicket = null;
        $this->isOpen = false;
    }

    public function toggleWindow()
    {
        $this->isOpen = !$this->isOpen;
        $controller = app(QueueController::class);
        
        if ($this->isOpen) {
            $controller->startQueue($this->tellerName);
        } else {
            $controller->stopQueue($this->tellerName);
            $this->activeTicket = null;
        }
    }

    public function loadActiveTicket()
    {
        $this->activeTicket = QueueTicket::serving()->where('assigned_teller', $this->tellerName)->first();
    }

    public function callNext()
    {
        if (!$this->isOpen) {
            session()->flash('error', 'Please open your window first!');
            return;
        }

        $next = app(QueueController::class)->callNext($this->tellerName);
        
        if (!$next) {
            session()->flash('error', 'The active line is empty!');
        }
        
        $this->loadActiveTicket();
    }

    public function completeCurrent()
    {
        app(QueueController::class)->completeCurrent($this->tellerName);
        $this->loadActiveTicket();
    }

    public function noShowCurrent()
    {
        app(QueueController::class)->noShowCurrent($this->tellerName);
        $this->loadActiveTicket();
    }

    public function endOfDay()
    {
        app(QueueController::class)->endOfDay();
        $this->activeTicket = null;
        $this->isOpen = false;
        session()->flash('success', 'The queue has been successfully closed and reset for the day.');
    }

    public function with()
    {
        return [
            'activeList' => QueueTicket::active()->get(),
            'holdingList' => QueueTicket::holding()->get()
        ];
    }
};
?>

<div class="p-8 font-sans" wire:poll.2s>
    @if(!$isAuthenticated)
        <div class="max-w-md mx-auto mt-20 bg-white p-8 rounded-xl border-2 border-gray-200 shadow-sm text-center">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Cashier Login</h2>
            <p class="text-gray-600 mb-6">Please enter your PIN to access the dashboard.</p>
            
            @if (session()->has('auth_error'))
                <div class="bg-red-100 text-red-700 p-3 mb-6 rounded border border-red-300 font-medium text-sm">
                    {{ session('auth_error') }}
                </div>
            @endif

            <form wire:submit="authenticate">
                <input 
                    type="password" 
                    wire:model="pinInput" 
                    class="w-full text-center text-3xl tracking-[1em] p-4 mb-6 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100 transition"
                    placeholder="••••"
                    maxlength="4"
                    required
                    autofocus
                >
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-4 rounded-lg font-bold text-lg transition shadow-md">
                    Enter Dashboard
                </button>
            </form>
        </div>
    @else
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-4">
                <h1 class="text-3xl font-bold text-gray-800">Cashier: {{ $tellerName }}</h1>
                <button wire:click="toggleWindow" class="px-4 py-2 rounded-lg font-bold text-white transition shadow-sm {{ $isOpen ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }}">
                    {{ $isOpen ? 'Close Window' : 'Open Window' }}
                </button>
                <button wire:click="endOfDay" onclick="confirm('Are you sure you want to end the day? This will clear all active queues and mark waiting students as no-shows.') || event.stopImmediatePropagation()" class="px-4 py-2 rounded-lg font-bold text-white transition shadow-sm bg-gray-800 hover:bg-gray-900 ml-4">
                    🛑 End Day
                </button>
            </div>
            <button wire:click="logout" class="text-red-600 hover:text-red-800 font-semibold underline">Log Out</button>
        </div>

        @if (session()->has('error'))
            <div class="bg-red-100 text-red-700 p-4 mb-6 rounded border border-red-300 font-medium">
                {{ session('error') }}
            </div>
        @endif
        
        @if (session()->has('success'))
            <div class="bg-green-100 text-green-800 p-4 mb-6 rounded border border-green-300 font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-2 bg-gray-50 p-8 rounded-xl border-2 border-gray-200 shadow-sm">
                <h2 class="text-xl mb-6 font-semibold text-gray-600 uppercase tracking-wide">Currently Serving</h2>
                
                @if($activeTicket)
                    <div class="mb-8 text-center bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                        <p class="text-7xl font-black text-gray-900 mb-2">{{ $activeTicket->tracking_number }}</p>
                        <p class="text-2xl text-gray-600">{{ $activeTicket->name }}</p>
                    </div>
                    
                    <div class="flex gap-4">
                        <button wire:click="completeCurrent" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-4 rounded-lg font-bold text-lg transition shadow-md">
                            ✓ Complete Transaction
                        </button>
                        <button wire:click="noShowCurrent" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-4 rounded-lg font-bold text-lg transition shadow-md">
                            ⚠️ No Show
                        </button>
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500 mb-8 text-xl">No one is currently at your window.</p>
                        @if($isOpen)
                            <button wire:click="callNext" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg font-bold text-xl transition shadow-md">
                                Call Next Student
                            </button>
                        @else
                            <p class="text-red-500 font-semibold mt-4">Window is closed. Open it to start calling students.</p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-lg font-bold mb-4 flex justify-between items-center border-b pb-2">
                    Active Line
                    <span class="bg-green-100 text-green-800 text-sm py-1 px-3 rounded-full">{{ $activeList->count() }}</span>
                </h2>
                <ul class="space-y-3">
                    @forelse($activeList as $ticket)
                        <li class="py-3 px-4 bg-green-50 rounded border border-green-100 flex flex-col">
                            <strong class="text-lg text-gray-800">{{ $ticket->tracking_number }}</strong> 
                            <span class="text-gray-600 text-sm">{{ $ticket->name }}</span>
                        </li>
                    @empty
                        <li class="py-4 text-center text-gray-400 italic">No one in physical line</li>
                    @endforelse
                </ul>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-lg font-bold mb-4 flex justify-between items-center border-b pb-2">
                    Holding Line
                    <span class="bg-blue-100 text-blue-800 text-sm py-1 px-3 rounded-full">{{ $holdingList->count() }}</span>
                </h2>
                <ul class="space-y-3">
                    @forelse($holdingList as $ticket)
                        <li class="py-3 px-4 bg-gray-50 rounded border border-gray-100 flex flex-col">
                            <strong class="text-lg text-gray-800">{{ $ticket->tracking_number }}</strong> 
                            <span class="text-gray-600 text-sm">{{ $ticket->name }}</span>
                        </li>
                    @empty
                        <li class="py-4 text-center text-gray-400 italic">Holding line is empty</li>
                    @endforelse
                </ul>
            </div>
        </div>
    @endif
</div>