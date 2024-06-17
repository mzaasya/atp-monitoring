<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        .container {
            display: block;
            width: 400px;
            height: auto;
            padding: 25px;
            text-align: center;
            background: #f5f5f5;
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>ATP has been confirmed!</h2>
        <p>{{ $history->user->name }} confirmed your ATP #{{ $history->task->sonumb }}.</p>
        <p>Wait for the date {{ date_format(date_create($history->task->atp_date), 'd F Y') }}</p>
        <p>Check it out now!</p>
        <br>
        <hr>
        <small>By ATP Monitoring App</small>
    </div>

</body>

</html>
