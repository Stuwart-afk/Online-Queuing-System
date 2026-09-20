<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\QueueTicket;
use App\Http\Controllers\QueueController;

new class extends Component {
    public $deviceId = null;
    public $platform = 'desktop';
    public $name = '';
    public $mobile_number = '';
    public $dismissedTicketIds = [];

    public function dismissTicket($id)
    {
        $this->dismissedTicketIds[] = $id;
    }

    public function mount()
    {
        // Initial state before JS sets the device ID
    }

    #[On('set-device')]
    public function setDevice($id, $platform)
    {
        $this->deviceId = $id;
        $this->platform = $platform;
    }

    public function submitForm()
    {
        $this->validate([
            'name' => 'required',
            'deviceId' => ['required', 'uuid'],
        ]);

        $today = now()->toDateString();

        $existingTicket = QueueTicket::where('device_id', $this->deviceId)
            ->where('queue_date', $today)
            ->whereIn('status', [QueueTicket::STATUS_HOLDING, QueueTicket::STATUS_ACTIVE, QueueTicket::STATUS_SERVING])
            ->first();

        if (!$existingTicket) {
            $lastTicket = QueueTicket::where('queue_date', $today)
                ->orderByDesc('id')
                ->first();

            $nextNumber = $lastTicket ? ((int) substr($lastTicket->tracking_number, 1)) + 1 : 1;
            $trackingNumber = 'A' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            QueueTicket::create([
                'name' => $this->name,
                'mobile_number' => $this->mobile_number,
                'device_id' => $this->deviceId,
                'platform' => $this->platform,
                'tracking_number' => $trackingNumber,
                'status' => QueueTicket::STATUS_HOLDING,
                'queue_date' => $today,
            ]);

            app(QueueController::class)->fillActiveQueue();
        }
    }

    public function with()
    {
        $ticket = null;
        if ($this->deviceId) {
            // Only ever care about the absolute latest ticket for today
            $ticket = QueueTicket::where('device_id', $this->deviceId)
                ->where('queue_date', today()->toDateString())
                ->orderByDesc('id')
                ->first();
                
            // If they dismissed their latest ticket, they want to start a new one
            if ($ticket && in_array($ticket->id, $this->dismissedTicketIds)) {
                $ticket = null;
            }
        }

        return [
            'ticket' => $ticket,
            'activeList' => QueueTicket::active()->get()
        ];
    }
};
?>

<div wire:poll.2s>
    @if($ticket)
        @if($ticket->status === 'completed')
            <!-- Thank You Screen -->
            <div class="flex flex-col items-center justify-center space-y-8 w-full max-w-2xl mx-auto mt-20 text-center">
                <div class="bg-white p-10 rounded-3xl shadow-xl border-t-8 border-green-500 w-full">
                    <h2 class="text-4xl font-black text-gray-800 mb-4">Transaction Complete</h2>
                    <p class="text-xl text-gray-600 mb-8">Thank you, <span class="font-bold text-gray-800">{{ $ticket->name }}</span>! Your transaction has been completed.</p>
                    <button wire:click="dismissTicket({{ $ticket->id }})" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-4 px-8 rounded-xl transition shadow-md text-lg">
                        Make Another Request
                    </button>
                </div>
            </div>
        @elseif($ticket->status === 'no-show')
            <!-- No Show Screen -->
            <div class="flex flex-col items-center justify-center space-y-8 w-full max-w-2xl mx-auto mt-20 text-center">
                <div class="bg-white p-10 rounded-3xl shadow-xl border-t-8 border-red-500 w-full">
                    <h2 class="text-4xl font-black text-gray-800 mb-4">Queue Missed</h2>
                    <p class="text-xl text-gray-600 mb-8">Sorry, <span class="font-bold text-gray-800">{{ $ticket->name }}</span>. You missed your queue and your ticket was marked as a no-show.</p>
                    <button wire:click="dismissTicket({{ $ticket->id }})" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-4 px-8 rounded-xl transition shadow-md text-lg">
                        Make a New Request
                    </button>
                </div>
            </div>
        @else
            <!-- TRACKING VIEW -->
        <div class="flex flex-col items-center justify-center space-y-10 w-full max-w-4xl mx-auto mt-10">
            
            <!-- Massive Tracking Number -->
            <div class="bg-white p-10 rounded-3xl shadow-2xl border-t-8 border-indigo-600 w-full text-center">
                <h2 class="text-2xl font-bold text-gray-500 uppercase tracking-widest mb-4">Your Number</h2>
                <div class="text-9xl font-black text-gray-900 tracking-tighter mb-6">
                    {{ $ticket->tracking_number }}
                </div>
                <h3 class="text-3xl text-gray-700 font-semibold mb-6">{{ $ticket->name }}</h3>
                
                <div class="inline-block px-8 py-4 rounded-full text-2xl font-bold uppercase tracking-wide
                    @if($ticket->status === 'holding') bg-blue-100 text-blue-800
                    @elseif($ticket->status === 'active') bg-yellow-100 text-yellow-800
                    @elseif($ticket->status === 'serving') bg-green-100 text-green-800
                    @endif">
                    @if($ticket->status === 'holding')
                        Waiting in Virtual Line
                    @elseif($ticket->status === 'active')
                        Please Proceed to Waiting Area
                    @elseif($ticket->status === 'serving')
                        Go to {{ $ticket->assigned_teller }}
                    @endif
                </div>
            </div>

            <!-- Active Queue List -->
            <div class="bg-white p-8 rounded-3xl shadow-xl w-full border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex justify-between items-center border-b pb-4">
                    Currently Calling
                    <span class="bg-green-100 text-green-800 text-lg py-1 px-4 rounded-full">{{ $activeList->count() }} Waiting</span>
                </h3>
                
                @if($activeList->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach($activeList as $active)
                            <div class="bg-gray-50 p-6 rounded-2xl text-center shadow-sm border border-gray-200 
                                {{ $active->id === $ticket->id ? 'ring-4 ring-indigo-500 bg-indigo-50' : '' }}">
                                <div class="text-4xl font-black text-gray-800">{{ $active->tracking_number }}</div>
                                @if($active->id === $ticket->id)
                                    <div class="text-indigo-600 font-bold mt-2 text-sm uppercase">That's You!</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-400 py-6 italic text-lg">No one is currently in the active line.</p>
                @endif
            </div>
        @endif
    @else
        <!-- REGISTRATION FORM -->
        <div class="form-card">
            <h2>Queue Registration Form</h2>

            <form wire:submit="submitForm">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" wire:model="name" placeholder="Enter your full name" required>
                </div>

                <div class="form-group" style="display: {{ $platform === 'ios' ? 'block' : 'none' }};">
                    <label for="mobile_number">Mobile Number</label>
                    <input type="text" id="mobile_number" wire:model="mobile_number" placeholder="Enter your mobile number">
                </div>

                <button type="submit" class="submit-btn" wire:loading.attr="disabled" wire:target="submitForm">
                    <span wire:loading.remove wire:target="submitForm">Get Tracking Number</span>
                    <span wire:loading wire:target="submitForm">Processing...</span>
                </button>
            </form>
        </div>
    @endif
</div>
