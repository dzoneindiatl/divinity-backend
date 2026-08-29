<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $subjectLine }}</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        color: #333;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 10px;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background: #f4f4f4;
    }
    </style>
</head>

<body>
    {!! $emailBody !!}
</body>

</html>