<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Env</title>
</head>
<body>
    <strong>Username</strong><br>
    <?php echo $_SERVER['MIAPP_USERNAME']; ?><br><br>

    <strong>User Path</strong><br>
    <?php echo $_SERVER['MIAPP_USERPATH']; ?><br><br>

    <strong>Platform</strong><br>
    <?php echo $_SERVER['MIAPP_PLATFORM']; ?><br><br>

    <strong>Language</strong><br>
    <?php echo $_SERVER['MIAPP_LANG']; ?><br><br>
</body>
</html>