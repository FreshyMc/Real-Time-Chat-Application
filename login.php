<?php
session_start();

include_once 'php/dbconfig.php';

if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
    header('Location: chat.php');
    exit;
}

if (isset($_POST['sign_in'])) {
    $password = trim(htmlspecialchars($_POST['password']));
    $username = trim(htmlspecialchars($_POST['username']));

    if (!empty($password) && !empty($username)) {
        $mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME);

        $stmt = $mysqli->prepare(LOGON);

        $stmt->bind_param('s', $username);

        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        if (!empty($result['password']) && password_verify($password, $result['password'])) {
            $_SESSION['user'] = $result['token'];
            $_SESSION['username'] = $result['username'];

            header('Location: chat.php');
            exit;
        } else {
            $invalid = 'Invalid credentials. Check your username/password again!';
        }
    }else{
        $invalid = 'All fields are required!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link href="./assets/css/chat.css" rel="stylesheet" type="text/css">
    <!-- Fonts and Icons -->
    <link href="assets/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
    <link rel="manifest" href="favicon/site.webmanifest">
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>Hidden Chat | Talk freely!</title>
</head>

<body class="container-fluid m-0 p-0 g-0 login">
    <div class="row g-0 p-0 m-0">
        <div class="col position-relative" style="min-height: 100vh !important;">
            <form class="login-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                <h2 class="mb-3">Sign In</h2>
                <div class="mb-3">
                    <input class="form-control" type="text" name="username" placeholder="Username">
                </div>
                <div class="mb-3">
                    <input class="form-control" type="password" name="password" placeholder="Password">
                </div>
                <button class="btn btn-lg" type="submit" name="sign_in">
                    Sign In
                </button>
                <div class="mt-2">
                    <a href="./register.php">I don't have an account. Register here!</a>
                </div>
                <?php if (isset($invalid)): ?>
                    <div class="mt-3 alert alert-danger" role="alert">
                        <?php echo $invalid; ?>
                    </div>
                <?php endif;?>
            </form>
        </div>
    </div>
</body>

</html>