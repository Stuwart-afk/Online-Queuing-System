<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QueueTicket;

class UserControllers extends Controller
{

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'device_id' => ['required', 'uuid'],
            'mobile_number' => ['nullable', 'string', 'max:20'],
            'platform' => 'required',
        ]);

        $today = now()->toDateString();

        $existingTicket = QueueTicket::where('device_id', $validated['device_id'])
            ->where('queue_date', $today)
            ->whereIn('status', ['holding', 'active'])
            ->first();

        if ($existingTicket) {
            return redirect()->back()->with('tracking_number', $existingTicket->tracking_number);
        }

        $lastTicket = QueueTicket::where('queue_date', $today)
            ->orderByDesc('id')
            ->first();

        if ($lastTicket) {
            $lastNumber = (int) substr($lastTicket->tracking_number, 1);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $trackingNumber = 'A' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $queue = QueueTicket::create([
            'name' => $validated['name'],
            'mobile_number' => $validated['mobile_number'],
            'device_id' => $validated['device_id'],
            'platform' => $validated['platform'],
            'tracking_number' => $trackingNumber,
            'status' => 'holding',
            'queue_date' => $today,
        ]);

        app(\App\Http\Controllers\QueueController::class)->fillActiveQueue();

        return redirect()->back()->with('tracking_number', $trackingNumber);
    }
}
