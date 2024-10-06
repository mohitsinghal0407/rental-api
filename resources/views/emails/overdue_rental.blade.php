<!DOCTYPE html>
<html>
<head>
    <title>Overdue Rental Notification</title>
</head>
<body>
<h1>Overdue Rental Notification</h1>
<p>Dear {{ $rental->user->name }},</p>
<p>Your rental for the book "{{ $rental->book->title }}" is overdue.</p>
<p>Please return it as soon as possible.</p>
<p>Thank you!</p>
</body>
</html>
