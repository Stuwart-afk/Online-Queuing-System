<?php

namespace App\Http\Controllers;

use App\Models\QueueTicket;

class QueueController extends Controller
{
    const MAX_ACTIVE_CAPACITY = 10;

    public function requestQueue(string $studentName)
    {
        $count = QueueTicket::count();
        $trackingNumber = 'TKT-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        $ticket = QueueTicket::create([
            'name' => $studentName,
            'tracking_number' => $trackingNumber,
            'status' => QueueTicket::STATUS_HOLDING,
        ]);

        $this->fillActiveQueue();

        return $ticket;
    }

    public function startQueue(string $tellerName)
    {
        $this->fillActiveQueue();
    }

    public function stopQueue(string $tellerName)
    {
        $serving = QueueTicket::serving()->where('assigned_teller', $tellerName)->first();
        if ($serving) {
            $serving->update([
                'status' => QueueTicket::STATUS_ACTIVE,
                'assigned_teller' => null
            ]);
        }
    }

    public function callNext(string $tellerName)
    {
        $nextInLine = QueueTicket::active()->first();

        if ($nextInLine) {
            $nextInLine->update([
                'status' => QueueTicket::STATUS_SERVING,
                'assigned_teller' => $tellerName
            ]);
            
            $this->fillActiveQueue();
        }

        return $nextInLine;
    }

    public function noShowCurrent(string $tellerName)
    {
        $serving = QueueTicket::serving()->where('assigned_teller', $tellerName)->first();
        if ($serving) {
            $serving->update(['status' => QueueTicket::STATUS_NOSHOW]);
        }
    }

    public function endOfDay()
    {
        QueueTicket::where('queue_date', today()->toDateString())
            ->whereIn('status', [QueueTicket::STATUS_HOLDING, QueueTicket::STATUS_ACTIVE, QueueTicket::STATUS_SERVING])
            ->update([
                'status' => QueueTicket::STATUS_NOSHOW,
                'assigned_teller' => null
            ]);
    }

    public function completeCurrent(string $tellerName)
    {
        $serving = QueueTicket::serving()->where('assigned_teller', $tellerName)->first();
        if ($serving) {
            $serving->update(['status' => QueueTicket::STATUS_COMPLETED]);
        }
    }

    public function fillActiveQueue()
    {
        $activeCount = QueueTicket::active()->count();

        while ($activeCount < self::MAX_ACTIVE_CAPACITY) {
            $nextHolding = QueueTicket::holding()->first();
            
            if (!$nextHolding) {
                break;
            }

            $nextHolding->update([
                'status' => QueueTicket::STATUS_ACTIVE,
                'assigned_teller' => null
            ]);

            $activeCount++;
        }
    }
}
