<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fraternity Calendar</title>
    <style>
        body {
            background-color: lavender; 
            font-family: Arial, sans-serif;
            padding: 20px;
            color: purple;
        }

        .back-button {
            display: inline-block;
            background-color: purple;
            color: yellow;
            padding: 10px 20px;
            margin: 20px 0;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .back-button:hover {
            background-color: navy;
        }

        iframe {
            display: block; /* To prevent inline spacing */
            margin: auto; /* Center the calendar */
        }
    </style>
</head>
<body>
    <h1>Fraternity Event Calendar</h1>
    <iframe src="https://calendar.google.com/calendar/embed?src=classroom105040982567758969081%40group.calendar.google.com&ctz=America%2FNew_York" style="border: 0" width="800" height="600" frameborder="0" scrolling="no"></iframe>
    <a href="event.php" class="back-button">Back to Events</a>
</body>
</html>