<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acrylic Test</title>
</head>

<body>
    <h1>Login</h1>
    <form method="post">
        <div>
            <label for="user">User</label>
            <input type="text" name="user">
        </div>
        <div>
            <label for="user">Password</label>
            <input type="password" name="password">
        </div>
        <div>
            <button type="submit">送信</button>
        </div>
    </form>
    <?php echo $error; ?>
</body>

</html>