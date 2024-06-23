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
        <h2>ATP has been on site!</h2>
        <p>{{ $history->user->name }} is working on site for your ATP #{{ $history->task->sonumb }}.</p>
        <p>Wait for the decision, it may be passed or rectified.</p>
        <p>Check it out now!</p>
        <br>
        <hr>
        <small>By Kertas Kerja ATP App</small>
    </div>

</body>

</html>
