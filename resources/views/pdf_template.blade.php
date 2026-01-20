<html>
<head>
    <style>
        body { font-family: sans-serif; text-align: center; }
        .report-section { margin-bottom: 50px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        img { max-width: 100%; height: auto; border: 1px solid #ccc; }
        h2 { color: #2563eb; }
    </style>
</head>
<body>
    <h1>Weekly Combined Report</h1>
    <p>Generated on: {{ $date }}</p>

    <div class="report-section">
        <h2>1. Davinci Overview</h2>
        <img src="{{ $davinci }}">
    </div>

    <div class="report-section">
        <h2>2. Jibble Timesheet Detail</h2>
        <img src="{{ $jibble }}">
    </div>
</body>
</html>