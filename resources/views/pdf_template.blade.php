<html>
<head>
    <style>
        body { font-family: sans-serif; text-align: center; margin: 20px; }
        .report-section { margin-bottom: 40px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        img { max-width: 100%; height: auto; border: 1px solid #ccc; margin-top: 10px; }
        h2 { color: #2563eb; margin-bottom: 10px; }
        .messages, .comments { text-align: left; margin-top: 20px; }
        .message-item { margin-bottom: 10px; }
        .message-item img { max-width: 80%; margin-top: 5px; display: block; }
    </style>
</head>
<body>

    <!-- Title & Date -->
    <h2>Attendance Report</h2>
    <p>{{ $date }}</p>

    <!-- Davinci Image -->
    <div class="report-section">
        <h3>Davinci Image</h3>
        <img src="{{ $davinci }}">
    </div>

    <!-- Jibble Image -->
    <div class="report-section">
        <h3>Jibble Image</h3>
        <img src="{{ $jibble }}">
    </div>

    <!-- Optional Chat Messages -->
    @if(!empty($messages))
        <div class="messages">
            <h3>Chat / Messages</h3>
            @foreach($messages as $msg)
                <div class="message-item">
                    @if(isset($msg['text']) && $msg['text'] !== '')
                        <p>- {{ $msg['text'] }}</p>
                    @endif
                    @if(isset($msg['image']) && $msg['image'] !== '')
                        <img src="{{ $msg['image'] }}">
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <!-- Optional Comments -->
    @if(!empty($comments))
        <div class="comments">
            <h3>Additional Comments</h3>
            <p>{{ $comments }}</p>
        </div>
    @endif

</body>
</html>
