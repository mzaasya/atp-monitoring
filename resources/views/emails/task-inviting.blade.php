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
        <h2>ATP has been created!</h2>
        <p>{{ $task->user->name }} created ATP #{{ $task->sonumb }} and invite you to confirm the date.</p>
        <p>Inviting date {{ date_format(date_create($task->inviting_date), 'd F Y') }}</p>
        <p>Check it out now!</p>
        <br>
        <hr>
        <small>By ATP Monitoring App</small>
    </div>

</body>

</html>
