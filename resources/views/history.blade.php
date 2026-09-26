@extends('layout')

@section('content')
<style>
    /* Main wrapper */
    .dashboard-container {
        display: flex;
        max-width: 1000px;
        margin: 0 auto;
        gap: 20px;
        background-color: rgba(255, 255, 255, 0.1);
        padding: 20px;
        border-radius: 12px;
        backdrop-filter: blur(10px);
    }

    /* Left Side: Transaction List (Wider) */
    .content-left {
        flex: 2.5; 
        background-color: rgba(0, 0, 0, 0.4);
        padding: 20px;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        color: white;
    }

    /* Right Side: User Info (Narrower) */
    .sidebar-right {
        flex: 1; 
        background-color: rgba(0, 0, 0, 0.4);
        padding: 20px;
        border-radius: 8px;
        color: white;
    }

    /* Individual Transaction Cards */
    .transaction-card {
        background-color: rgba(255, 255, 255, 0.15); 
        padding: 15px 20px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .transaction-card h4 {
        margin: 0 0 10px 0;
        font-size: 1.1rem;
    }

    .transaction-card p {
        margin: 5px 0;
        font-size: 0.9rem;
    }

    /* Status Colors */
    .status-completed { color: #4ade80; font-weight: bold; } 
    .status-ongoing { color: #facc15; font-weight: bold; }   
    .status-noshow { color: #f87171; font-weight: bold; }    
</style>

<div class="dashboard-container">
    
    <!-- Left Column: History and Tracking -->
    <div class="content-left">
        <h3 style="margin-top: 0;">Transaction History</h3>
        
        <!-- Transaction 1: Completed -->
        <div class="transaction-card">
            <h4>Tracking Number: #TRK-987654</h4>
            <p><strong>Purpose:</strong> Cashier Payment</p>
            <p><strong>Date:</strong> Oct 15, 2026 - 10:30 AM</p>
            <p><strong>Status:</strong> <span class="status-completed">Completed</span></p>
        </div>

        <!-- Transaction 2: Ongoing -->
        <div class="transaction-card">
            <h4>Tracking Number: #TRK-987655</h4>
            <p><strong>Purpose:</strong> Registrar Request (TOR)</p>
            <p><strong>Date:</strong> Oct 17, 2026 - 09:15 AM</p>
            <p><strong>Status:</strong> <span class="status-ongoing">Ongoing</span></p>
        </div>

        <!-- Transaction 3: Did Not Show Up -->
        <div class="transaction-card">
            <h4>Tracking Number: #TRK-987656</h4>
            <p><strong>Purpose:</strong> Clinic Consultation</p>
            <p><strong>Date:</strong> Oct 12, 2026 - 02:00 PM</p>
            <p><strong>Status:</strong> <span class="status-noshow">Did Not Show Up</span></p>
        </div>
    </div>

    <!-- Right Column: User Names / Details -->
    <div class="sidebar-right">
        <h3 style="margin-top: 0;">User Profile</h3>
        
        <div style="margin-top: 20px; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 6px;">
            <p style="margin-top:0;"><strong>Name:</strong> Juan Dela Cruz</p>
            <p><strong>USN:</strong> C25-01-12345-MAN121</p>
            <p style="margin-bottom:0;"><strong>Course:</strong> BSIT</p>
        </div>
    </div>

</div>
@endsection