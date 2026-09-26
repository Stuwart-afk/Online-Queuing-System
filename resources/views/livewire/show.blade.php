<?php

use function Livewire\Volt\{state, mount};
use App\Models\QueueTicket;

state([
    'token' => null,
    'queue' => null,
    'position' => null,
]);

/*
|--------------------------------------------------------------------------
| Mount
|--------------------------------------------------------------------------
*/

mount(function ($token) {

    $this->token = $token;

    $this->queue = QueueTicket::where(
        'access_token',
        $token
    )->firstOrFail();

    $this->checkNoShow();

    $this->updatePosition();
});


/*
|--------------------------------------------------------------------------
| Update Position
|--------------------------------------------------------------------------
*/

$updatePosition = function () {

    if (!$this->queue) {

        $this->position = null;

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Cancelled / completed / no-show tickets
    |--------------------------------------------------------------------------
    */

    if (in_array($this->queue->status, [

        QueueTicket::STATUS_CANCELLED,
        QueueTicket::STATUS_COMPLETED,
        QueueTicket::STATUS_NO_SHOW,

    ])) {

        $this->position = null;

        return;
    }


    $this->position =
        QueueTicket::where(
            'created_at',
            '<',
            $this->queue->created_at
        )
        ->whereIn('status', [

            QueueTicket::STATUS_HOLDING,
            QueueTicket::STATUS_ACTIVE,
            QueueTicket::STATUS_SERVING,

        ])
        ->count() + 1;
};


/*
|--------------------------------------------------------------------------
| Check No Show
|--------------------------------------------------------------------------
*/

$checkNoShow = function () {

    if (!$this->queue) {
        return;
    }

    $this->queue->refresh();

    if (
        $this->queue->status ===
        QueueTicket::STATUS_SERVING
    ) {

        $this->queue->checkNoShow();

        $this->queue->refresh();
    }
};


/*
|--------------------------------------------------------------------------
| Refresh Queue
|--------------------------------------------------------------------------
*/

$refreshQueue = function () {

    if (!$this->token) {
        return;
    }


    $queue = QueueTicket::where(
        'access_token',
        $this->token
    )->first();


    if (!$queue) {

        $this->queue = null;

        $this->position = null;

        return;
    }


    $this->queue = $queue;

    $this->checkNoShow();

    $this->updatePosition();
};


/*
|--------------------------------------------------------------------------
| I'm Here
|--------------------------------------------------------------------------
*/

$imHere = function () {

    if (!$this->queue) {
        return;
    }


    $this->queue->refresh();

    $this->queue->markArrived();

    $this->queue->refresh();

    $this->updatePosition();
};


/*
|--------------------------------------------------------------------------
| Cancel Ticket
|--------------------------------------------------------------------------
*/

$cancelTicket = function () {

    if (!$this->queue) {
        return;
    }


    $this->queue->refresh();


    /*
    |--------------------------------------------------------------------------
    | Attempt cancellation
    |--------------------------------------------------------------------------
    */

    $cancelled = $this->queue->cancel();


    if (!$cancelled) {

        session()->flash(
            'error',
            'This ticket can no longer be cancelled.'
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Refresh queue after cancellation
    |--------------------------------------------------------------------------
    */

    $this->queue->refresh();

    $this->position = null;


    session()->flash(
        'success',
        'Your queue ticket has been cancelled.'
    );
};

?>


{{-- ================================================================ --}}
{{-- CSS / Fonts --}}
{{-- ================================================================ --}}

@assets

<link
    rel="preconnect"
    href="https://fonts.googleapis.com">

<link
    rel="preconnect"
    href="https://fonts.gstatic.com"
    crossorigin>

<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@200;300;400;500;600;700&display=swap"
    rel="stylesheet">

<link
    rel="stylesheet"
    href="{{ asset('css/number-tracking.css') }}">

@endassets


{{-- ================================================================ --}}
{{-- Warning --}}
{{-- ================================================================ --}}

@if (session('warning'))

<script>
    alert(
        @js(session('warning'))
    );
</script>

@endif


<div class="number-tracking">


    {{-- ============================================================ --}}
    {{-- Background --}}
    {{-- ============================================================ --}}

    <div class="background">

        <div class="background-image"></div>

        <div class="background-overlay"></div>

        <div class="background-gradient"></div>

    </div>


    {{-- ============================================================ --}}
    {{-- Main Body --}}
    {{-- ============================================================ --}}

    <main class="main-body">


        {{-- ======================================================== --}}
        {{-- Tracking Number --}}
        {{-- ======================================================== --}}

        <section class="tracking-card glass-card">

            <div class="tracking-header">


                <div class="tracking-label">

                    YOUR TRACKING NUMBER

                </div>


                <div class="tracking-number">

                    {{ $queue->tracking_number ?? '---' }}

                </div>


                <div class="customer-name">

                    {{ $queue->name ?? '---' }}

                </div>


            </div>

        </section>


        {{-- ======================================================== --}}
        {{-- Cancel Ticket --}}
        {{-- This is intentionally UNDER the tracking number --}}
        {{-- ======================================================== --}}

        @if(
        $queue &&
        in_array(
        $queue->status,
        [
        QueueTicket::STATUS_HOLDING,
        QueueTicket::STATUS_ACTIVE,
        QueueTicket::STATUS_HELD,
        ]
        )
        )

        <div class="cancel-container">

            <button
                type="button"
                wire:click="cancelTicket"
                wire:confirm="Are you sure you want to cancel your queue ticket?"
                class="cancel-button cancel-ticket-button">

                CANCEL TICKET

            </button>

        </div>

        @endif


        {{-- ======================================================== --}}
        {{-- Success Message --}}
        {{-- ======================================================== --}}

        @if(session('success'))

        <div class="queue-success-message">

            {{ session('success') }}

        </div>

        @endif


        {{-- ======================================================== --}}
        {{-- Error Message --}}
        {{-- ======================================================== --}}

        @if(session('error'))

        <div class="queue-error-message">

            {{ session('error') }}

        </div>

        @endif


        {{-- ======================================================== --}}
        {{-- Position --}}
        {{-- ======================================================== --}}

        <section class="position-card glass-card">

            <div class="position-content">


                {{-- ================================================= --}}
                {{-- Position Title --}}
                {{-- ================================================= --}}

                @if(
                $queue &&
                in_array(
                $queue->status,
                [
                QueueTicket::STATUS_HOLDING,
                QueueTicket::STATUS_ACTIVE,
                QueueTicket::STATUS_SERVING,
                ]
                )
                )

                <div class="position-title">

                    YOU'RE IN THE<br>

                    {{ $position ? '#' . $position : '---' }}

                    POSITION

                </div>

                @else

                <div class="position-title">

                    QUEUE STATUS

                </div>

                @endif


                {{-- ================================================= --}}
                {{-- Position Icon --}}
                {{-- ================================================= --}}

                <div class="position-number-circle">

                    <div class="users-icon">

                        <img
                            class="icon-line"
                            src="{{ asset('images/users-line-solid-full.svg') }}"
                            alt="Queue position">

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- Position Progress --}}
                {{-- ================================================= --}}

                @if(
                $queue &&
                in_array(
                $queue->status,
                [
                QueueTicket::STATUS_HOLDING,
                QueueTicket::STATUS_ACTIVE,
                QueueTicket::STATUS_SERVING,
                ]
                )
                )

                <div class="position-status">

                    <div class="position-status-fill"></div>

                </div>

                @endif


                {{-- ================================================= --}}
                {{-- Reminder --}}
                {{-- ================================================= --}}

                <div class="reminder">


                    <svg
                        class="hour"
                        width="25"
                        height="32"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 640 640"
                        aria-hidden="true">

                        <path
                            fill="#ffffff"
                            d="M192 64C156.7 64 128 92.7 128 128L128 544C128 555.5 134.2 566.2 144.2 571.8C154.2 577.4 166.5 577.3 176.4 571.4L320 485.3L463.5 571.4C473.4 577.3 485.7 577.5 495.7 571.8C505.7 566.1 512 555.5 512 544L512 128C512 92.7 483.3 64 448 64L192 64z" />

                    </svg>


                    <span>

                        KEEP AN EYE ON YOUR NUMBER!

                    </span>


                </div>


            </div>

        </section>


        {{-- ======================================================== --}}
        {{-- Queue Status --}}
        {{-- ======================================================== --}}

        <section class="queue-card glass-card">

            <div class="queue-content">


                {{-- ================================================= --}}
                {{-- Queue Title --}}
                {{-- ================================================= --}}

                <div class="queue-title">

                    QUEUE STATUS

                </div>


                {{-- ================================================= --}}
                {{-- Queue Icon --}}
                {{-- ================================================= --}}

                <div class="queue-icon-circle">

                    <div class="bell-icon">


                        @if(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_HOLDING
                        )

                        <span aria-hidden="true">
                            🕒
                        </span>


                        @elseif(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_ACTIVE
                        )

                        <span aria-hidden="true">
                            📢
                        </span>


                        @elseif(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_SERVING
                        )

                        <span aria-hidden="true">
                            🔔
                        </span>


                        @elseif(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_NO_SHOW
                        )

                        <span aria-hidden="true">
                            ❌
                        </span>


                        @elseif(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_COMPLETED
                        )

                        <span aria-hidden="true">
                            ✅
                        </span>


                        @elseif(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_HELD
                        )

                        <span aria-hidden="true">
                            ⏸️
                        </span>


                        @elseif(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_CANCELLED
                        )

                        <span aria-hidden="true">
                            🚫
                        </span>


                        @else

                        <span aria-hidden="true">
                            🔔
                        </span>

                        @endif


                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- Queue Status --}}
                {{-- ================================================= --}}

                <div class="queue-status">


                    <span class="status-dot">

                        •

                    </span>


                    <span class="queue-status-text">


                        @if(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_HOLDING
                        )

                        WAITING IN LINE


                        @elseif(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_ACTIVE
                        )

                        PLEASE STAND BY


                        @elseif(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_SERVING
                        )

                        IT'S YOUR TURN


                        @elseif(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_NO_SHOW
                        )

                        NO SHOW


                        @elseif(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_COMPLETED
                        )

                        COMPLETED


                        @elseif(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_HELD
                        )

                        ON HOLD


                        @elseif(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_CANCELLED
                        )

                        CANCELLED


                        @else

                        {{ strtoupper($queue->status ?? 'UNKNOWN') }}

                        @endif


                    </span>


                </div>


                {{-- ================================================= --}}
                {{-- Queue Description --}}
                {{-- ================================================= --}}

                <div class="hour-glass queue-description">


                    @if(
                    ($queue->status ?? '') ===
                    QueueTicket::STATUS_HOLDING
                    )

                    <svg
                        class="glasshour"
                        width="25"
                        height="32"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 640 640"
                        aria-hidden="true">

                        <path
                            fill="#ffffff"
                            d="M160 64C142.3 64 128 78.3 128 96C128 113.7 142.3 128 160 128L160 139C160 181.4 176.9 222.1 206.9 252.1L274.8 320L206.9 387.9C176.9 417.9 160 458.6 160 501L160 512C142.3 512 128 526.3 128 544C128 561.7 142.3 576 160 576L480 576C497.7 576 512 561.7 512 544C512 526.3 497.7 512 480 512L480 501C480 458.6 463.1 417.9 433.1 387.9L365.2 320L433.1 252.1C463.1 222.1 480 181.4 480 139L480 128C497.7 128 512 113.7 512 96C512 78.3 497.7 64 480 64L160 64zM416 501L416 512L224 512L224 501C224 475.5 234.1 451.1 252.1 433.1L320 365.2L387.9 433.1C405.9 451.1 416 475.5 416 501z">

                        </path>

                    </svg>

                    @endif


                    <span>


                        @if(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_HOLDING
                        )

                        PLEASE WAIT. WE'LL NOTIFY YOU WHEN
                        YOU ARE CLOSE TO BEING SERVED.


                        @elseif(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_ACTIVE
                        )

                        YOU ARE CLOSE TO BEING SERVED.
                        PLEASE STAY NEARBY.


                        @elseif(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_SERVING
                        )

                        PLEASE PROCEED TO
                        {{ $queue->assigned_teller ?? 'THE COUNTER' }}


                        @elseif(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_NO_SHOW
                        )

                        YOUR 3-MINUTE ARRIVAL TIME HAS EXPIRED.


                        @elseif(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_COMPLETED
                        )

                        YOUR TRANSACTION HAS BEEN COMPLETED.


                        @elseif(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_HELD
                        )

                        YOUR TICKET IS CURRENTLY ON HOLD.


                        @elseif(
                        ($queue->status ?? '') ===
                        QueueTicket::STATUS_CANCELLED
                        )

                        YOUR QUEUE TICKET HAS BEEN CANCELLED.


                        @else

                        CURRENT STATUS:
                        {{ strtoupper($queue->status ?? 'UNKNOWN') }}

                        @endif


                    </span>


                </div>


                {{-- ================================================= --}}
                {{-- Serving Popup --}}
                {{-- ================================================= --}}




            </div>

        </section>


        {{-- ======================================================== --}}
        {{-- Dividers --}}
        {{-- ======================================================== --}}

        <div class="divider divider-left"></div>

        <div class="divider divider-right"></div>


        {{-- ======================================================== --}}
        {{-- How To Know --}}
        {{-- ======================================================== --}}

        <section class="how-to-card glass-card">


            {{-- ================================================= --}}
            {{-- How To Header --}}
            {{-- ================================================= --}}

            <div class="how-to-header">
                @if(
                ($queue->status ?? '') ===
                QueueTicket::STATUS_SERVING
                )


                @if(
                !$queue->arrived_at &&
                $queue->serving_started_at
                )


                <div class="serving-popup-overlay">

                    <div class="serving-popup">


                        <div class="serving-popup-icon">

                            🔔

                        </div>


                        <div class="serving-popup-title">

                            IT'S YOUR TURN!

                        </div>


                        <div class="serving-popup-text">

                            Please proceed to

                            <strong>
                                {{ $queue->assigned_teller ?? 'the counter' }}
                            </strong>

                            and confirm your arrival.

                        </div>


                        <div
                            id="arrival-countdown"
                            data-expires-at="{{ $queue->serving_started_at->copy()->addMinutes(5)->toIso8601String() }}"
                            class="serving-countdown">

                            05:00

                        </div>


                        <div class="serving-countdown-label">

                            TIME TO CONFIRM YOUR ARRIVAL

                        </div>


                        <button
                            type="button"
                            wire:click="imHere"
                            id="im-here-button"
                            class="notification-button serving-button">

                            🙋 I'M HERE

                        </button>


                        <p
                            id="countdown-message"
                            class="serving-countdown-message"></p>


                    </div>

                </div>


                @elseif($queue->arrived_at)


                <div class="serving-popup-overlay">
                    <div class="serving-popup">

                        <div class="serving-popup-icon">

                            ✅

                        </div>


                        <div class="serving-popup-title">

                            Please proceed to
                            <strong>
                                {{ $queue->assigned_teller ?? 'the counter' }}
                            </strong>


                        </div>







                    </div>

                </div>


                @endif


                @endif

                <div class="star">

                    ★

                </div>


                <div class="how-to-title">

                    HOW TO KNOW IT'S YOUR TURN

                </div>


            </div>


            {{-- ================================================= --}}
            {{-- Instructions --}}
            {{-- ================================================= --}}

            <div class="instructions">

                Keep an eye on your tracking number and queue status.

                <br><br>

                When your ticket is <strong>ACTIVE</strong>, you will
                receive an SMS with your queue position.

                <br><br>

                When your status changes to
                <strong>IT'S YOUR TURN</strong>, you will receive an SMS
                and an <strong>I'm Here</strong> button will appear.

                <br><br>

                Click <strong>I'm Here</strong> within
                <strong>5 minutes</strong> to confirm your arrival.
                Otherwise, your ticket will be marked
                <strong>NO SHOW</strong>.

            </div>


        </section>


    </main>


    {{-- ============================================================ --}}
    {{-- Livewire Poll --}}
    {{-- ============================================================ --}}

    <div
        wire:poll.5s="refreshQueue"
        style="
            position: fixed;
            width: 1px;
            height: 1px;
            overflow: hidden;
            opacity: 0;
            pointer-events: none;
        "
        aria-hidden="true"></div>


</div>


{{-- ================================================================ --}}
{{-- Arrival Countdown --}}
{{-- ================================================================ --}}

<script>
    (function() {

        let countdownInterval = null;
        let countdownExpiresAt = null;
        let countdownElement = null;

        /*
        |--------------------------------------------------------------------------
        | Initialize / Sync Countdown
        |--------------------------------------------------------------------------
        */

        function initializeArrivalCountdown() {

            const countdown =
                document.getElementById('arrival-countdown');

            /*
            |--------------------------------------------------------------------------
            | No countdown currently displayed
            |--------------------------------------------------------------------------
            */

            if (!countdown) {

                if (countdownInterval) {

                    clearInterval(countdownInterval);

                    countdownInterval = null;

                }

                countdownElement = null;
                countdownExpiresAt = null;

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Get expiration time from the server
            |--------------------------------------------------------------------------
            */

            const expiresAt =
                new Date(
                    countdown.dataset.expiresAt
                ).getTime();


            /*
            |--------------------------------------------------------------------------
            | Invalid expiration date
            |--------------------------------------------------------------------------
            */

            if (
                !expiresAt ||
                Number.isNaN(expiresAt)
            ) {

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | If this is the same countdown, DO NOT restart it.
            |
            | Livewire can re-render the element every 0.5 seconds.
            | We keep using the same expiration timestamp.
            |--------------------------------------------------------------------------
            */

            if (
                countdownExpiresAt === expiresAt &&
                countdownInterval
            ) {

                countdownElement = countdown;

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | New countdown / new expiration time
            |--------------------------------------------------------------------------
            */

            countdownExpiresAt = expiresAt;
            countdownElement = countdown;


            /*
            |--------------------------------------------------------------------------
            | Get button and message
            |--------------------------------------------------------------------------
            */

            function getButton() {

                return document.getElementById(
                    'im-here-button'
                );

            }


            function getMessage() {

                return document.getElementById(
                    'countdown-message'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Update Countdown
            |--------------------------------------------------------------------------
            */

            function updateCountdown() {

                /*
                |--------------------------------------------------------------------------
                | Find the latest Livewire element
                |--------------------------------------------------------------------------
                */

                const currentCountdown =
                    document.getElementById(
                        'arrival-countdown'
                    );


                if (!currentCountdown) {

                    return false;

                }


                countdownElement =
                    currentCountdown;


                /*
                |--------------------------------------------------------------------------
                | Calculate remaining time
                |--------------------------------------------------------------------------
                */

                const remaining =
                    countdownExpiresAt -
                    Date.now();


                /*
                |--------------------------------------------------------------------------
                | Expired
                |--------------------------------------------------------------------------
                */

                if (remaining <= 0) {

                    currentCountdown.textContent =
                        '00:00';


                    const button =
                        getButton();


                    if (button) {

                        button.disabled = true;

                        button.textContent =
                            'TIME EXPIRED';

                    }


                    const message =
                        getMessage();


                    if (message) {

                        message.textContent =
                            'Your 5-minute arrival time has expired.';

                    }


                    if (countdownInterval) {

                        clearInterval(
                            countdownInterval
                        );

                        countdownInterval = null;

                    }


                    return false;

                }


                /*
                |--------------------------------------------------------------------------
                | Calculate minutes / seconds
                |--------------------------------------------------------------------------
                */

                const totalSeconds =
                    Math.ceil(
                        remaining / 1000
                    );


                const minutes =
                    Math.floor(
                        totalSeconds / 60
                    );


                const seconds =
                    totalSeconds % 60;


                /*
                |--------------------------------------------------------------------------
                | Update display
                |--------------------------------------------------------------------------
                */

                currentCountdown.textContent =
                    String(minutes).padStart(2, '0') +
                    ':' +
                    String(seconds).padStart(2, '0');


                return true;

            }


            /*
            |--------------------------------------------------------------------------
            | Clear old timer
            |--------------------------------------------------------------------------
            */

            if (countdownInterval) {

                clearInterval(
                    countdownInterval
                );

                countdownInterval = null;

            }


            /*
            |--------------------------------------------------------------------------
            | Immediately update
            |--------------------------------------------------------------------------
            */

            updateCountdown();


            /*
            |--------------------------------------------------------------------------
            | Start ONE timer
            |--------------------------------------------------------------------------
            */

            countdownInterval =
                setInterval(
                    function() {

                        updateCountdown();

                    },
                    250
                );

        }


        /*
        |--------------------------------------------------------------------------
        | DOM Ready
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'DOMContentLoaded',
            function() {

                initializeArrivalCountdown();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Livewire Initialized
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'livewire:initialized',
            function() {

                initializeArrivalCountdown();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Livewire Navigated
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'livewire:navigated',
            function() {

                initializeArrivalCountdown();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Livewire DOM Updates
        |--------------------------------------------------------------------------
        |
        | Livewire 3 dispatches morph events while replacing DOM elements.
        | After the DOM changes, we reconnect to the new countdown element
        | WITHOUT resetting the actual expiration timestamp.
        |
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'livewire:navigated',
            initializeArrivalCountdown
        );


        /*
        |--------------------------------------------------------------------------
        | MutationObserver
        |--------------------------------------------------------------------------
        |
        | This catches Livewire replacing #arrival-countdown.
        |--------------------------------------------------------------------------
        */

        const observer =
            new MutationObserver(
                function() {

                    const countdown =
                        document.getElementById(
                            'arrival-countdown'
                        );


                    if (!countdown) {

                        return;

                    }


                    const expiresAt =
                        new Date(
                            countdown.dataset.expiresAt
                        ).getTime();


                    /*
                    |--------------------------------------------------------------------------
                    | Same countdown:
                    | just point to the new DOM element.
                    |--------------------------------------------------------------------------
                    */

                    if (
                        countdownExpiresAt === expiresAt &&
                        countdownInterval
                    ) {

                        countdownElement =
                            countdown;

                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | New countdown
                    |--------------------------------------------------------------------------
                    */

                    initializeArrivalCountdown();

                }
            );


        observer.observe(
            document.body, {
                childList: true,
                subtree: true
            }
        );

    })();
</script>