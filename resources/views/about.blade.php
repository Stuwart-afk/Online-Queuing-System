@extends('layout')

@section('content')

<style>
    .about-page {
        min-height: 100vh;
        padding: 120px 20px 50px;
        background-color: #f5f5f5;
        color: #333;
    }

    .about-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 40px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .about-container h1 {
        text-align: center;
        color: #1a4d8f;
        margin-bottom: 30px;
    }

    .about-container h2 {
        color: #1a4d8f;
        margin-top: 30px;
    }

    .about-container p {
        font-size: 17px;
        line-height: 1.7;
        text-align: justify;
    }

    .highlight {
        background-color: #eef5ff;
        padding: 20px;
        border-left: 5px solid #1a4d8f;
        margin-top: 25px;
        border-radius: 5px;
    }
</style>


<div class="about-page">

    <div class="about-container">

        <h1>About Our Online Queuing System</h1>

        <p>
            Our <strong>Online Queuing System</strong> is designed to make the
            cashier process easier and more convenient for students, parents,
            and other users. Instead of waiting in a long line at the cashier
            area, users can get a <strong>tracking number online</strong>
            through our website.
        </p>

        <p>
            To get a tracking number, the user only needs to provide their
            <strong>Student USN ID</strong> and the
            <strong>purpose of their transaction</strong>. After submitting
            the information, they will receive a tracking number and can wait
            for their turn without staying in the cashier area.
        </p>

        <p>
            When their turn is near, the system will send a
            <strong>text message</strong> to their phone informing them that
            they are next and can proceed to the cashier to complete their
            transaction.
        </p>

        <h2>Why We Built This System</h2>

        <p>
            We built this system to help
            <strong>reduce long lines and waiting times</strong> in the
            cashier area. It allows students and parents to wait somewhere
            more comfortable instead of standing in line for a long time.
        </p>

        <p>
            The system also helps make the cashier area more organized because
            users are served based on their tracking numbers and queue order.
        </p>

        <div class="highlight">
            <p>
                Our goal is to provide a
                <strong>simple, convenient, and organized way of managing
                queues</strong> while making the cashier experience better
                for both users and staff.
            </p>
        </div>

    </div>

</div>

@endsection