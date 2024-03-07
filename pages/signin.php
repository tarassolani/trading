<?php
if(isset($_COOKIE['login_info'])) {
    header("Location: ");   //pagina taras :)
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'>
    <link rel="stylesheet" href="../css/signin.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>Regolare.com - Sign Up</title>

    <style>
        .error-message.centered {
            text-align: center;
            margin-left: auto;
            margin-right: auto;
            margin-top: 12px;
        }
    </style>
</head>
<body>
<nav>
        <div id="menu-icon" class="material-symbols-outlined">&#xe5d2;</div>

        <a id="logo" href="../index.php">Regolare.com</a>

    </nav>
    <div class="wrapper">
        <form action="check-signin.php" method="post">
            <h1>Sign in</h1>
            <div class="input-box">
                <input type="text" name="username" placeholder="Username" maxlength="50" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" maxlength="255" required>
                <i class='bx bxs-lock-alt' ></i>
            </div>

            <div class="remember-forgot">
                <label>
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="">Forgot password?</a>
            </div>

            <button type="submit" class="btn">Sign in</button>

            <div class="register-link">
                <p>
                    Don't have an account? <a href="signup.html">Sign up</a>
                </p>
            </div>

            <?php
            if (isset($_GET['error']) && $_GET['error'] == 'invalid') {
                echo "<p class='error-message centered'>Invalid username or password.</p>";
            }
            ?>
        </form>
    </div>
</body>
</html>