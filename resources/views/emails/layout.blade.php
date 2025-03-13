<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            padding: 20px;
            background-color: #edf2f7;
        }
        .email-content {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background-color: #FFFFFF;
        }
        h1 {
            color: #2d3748;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4f46e5;
            color: #FFFFFF !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        .button:hover {
            background-color: #4338ca;
        }
        p {
            margin-bottom: 16px;
        }

        .email-subtext {
            font-size: 12px;
            color:rgb(104, 104, 104);
            margin-top: 16px;
            font-style: italic;
        }

        .email-subtext a {
            color:rgb(104, 104, 104) !important;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        @yield('content')
    </div>
</body>
</html>
