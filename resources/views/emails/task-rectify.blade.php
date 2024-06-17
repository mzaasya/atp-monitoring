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
        <h2>ATP need rectification!</h2>
        <p>Oh no, {{ $history->user->name }} rectified your ATP #{{ $history->task->sonumb }}.</p>
        <p>You need to fix it and wait for the decision.</p>
        <p>Check it out now!</p>
        <br>
        <hr>
        <small>By ATP Monitoring App</small>
    </div>

</body>

</html>
