<?php

require 'header.php';
include 'config.php';

$host_ip = $_SERVER['HOST_IP'];
$db_user = $_SERVER['DB_USER'];
$db_pass = $_SERVER['DB_PASS'];
$db_name = $_SERVER['DB_NAME'];
$radius_db_name = $_SERVER['RADIUS_DB_NAME'];

$con = mysqli_connect($host_ip, $db_user, $db_pass);

if (mysqli_connect_errno()) {
  echo "Failed to connect to SQL: " . mysqli_connect_error();
}

if (isset($_POST['mac'])) {
    $_SESSION["user_type"] = "new";
    $_SESSION["mac"] = $_POST['mac'];
    $_SESSION["ip"] = $_POST['ip'];
    $_SESSION["link-login"] = $_POST['link-login'];
    $_SESSION["link-login-only"] = $_POST['link-login-only'];
}

$table_name = $_SERVER['TABLE_NAME'];

# Checking DB to see if user exists or not.
mysqli_report(MYSQLI_REPORT_OFF);
mysqli_select_db($con, $db_name);
$result = null;

$result = mysqli_query($con, "SELECT * FROM `$table_name` WHERE mac='$_SESSION[mac]'");

if ($result->num_rows >= 1) {
    $row = mysqli_fetch_array($result);

    $date_old = $row['last_updated'];
    $date_now = date('Y-m-d H:i:s');
    $date_diff = abs(strtotime($date_now) - strtotime($date_old)) / (60 * 60 * 24);

    if ($date_diff < 7) {
        $last_updated = date("Y-m-d H:i:s");
        $result = mysqli_query($con, "UPDATE `$table_name` SET last_updated='$last_updated' WHERE mac='$_SESSION[mac]'");
        if ($_SESSION['user_type'] != "register") {
            $_SESSION["user_type"] = "repeat";
            $_SESSION["username"] = $row['reg'];
            mysqli_select_db($con, $radius_db_name);
            $result2 = mysqli_query($con, "SELECT * FROM `radcheck` WHERE username='$_SESSION[username]'");
            $row2 = mysqli_fetch_array($result2);
            $_SESSION["password"] = $row2['value'];
            header("Location: welcome.php");
        }
    } else {
        $sql = "DELETE FROM `$table_name` WHERE mac='$_SESSION[mac]'";
        $con->query($sql);
        $reg = $row['reg'];
        mysqli_select_db($con, $radius_db_name);
        $sql = "DELETE FROM `radcheck` WHERE username='$reg'";
        $con->query($sql);
        header("Location: register.php");
    }
    mysqli_close($con);
} else {
    mysqli_close($con);
}

?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>
      <?php echo htmlspecialchars($business_name); ?> WiFi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <link rel="stylesheet" href="assets/styles/bulma.min.css"/>
    <link rel="stylesheet" href="vendor/fortawesome/font-awesome/css/all.css"/>
    <link rel="icon" type="image/png" href="assets/images/favicomatic/favicon-32x32.png" sizes="32x32"/>
    <link rel="icon" type="image/png" href="assets/images/favicomatic/favicon-16x16.png" sizes="16x16"/>
    <link rel="stylesheet" href="assets/styles/style.css"/>
</head>

<body>
<div class="page">

    <div class="head">
        <br>
        <figure id="logo">
            <img id="img1" src="assets/images/uni-logo1.jpg">
            <img id="img2" src="assets/images/uni-logo2.jpg">
        </figure>
    </div>

    <div class="main">
        <section class="section">
            <div class="container">
                <div id="contact_form" class="content is-size-5 has-text-centered has-text-weight-bold">Enter your details
                </div>
                <form method="post" action="connect.php">
                    <div class="field">
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="form_font" name="username" placeholder="Employment/Student #" required>
                            <span class="icon is-small is-left">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="form_font" name="password" placeholder="Password" required>
                            <span class="icon is-small is-left">
                                <i class="fas fa-key"></i>
                            </span>
                        </div>
                    </div>
                    <div class="buttons is-centered">
<!--                        <button class="button is-link">Connect</button>-->
                        <input class="button is-link" type="submit" name="verify" value="Connect">
                    </div>
                </form>
            </div>
            <br>
            <div class="content is-size-6 has-text-centered">New User? <a href="register.php">Register here</a>
        </section>
    </div>
</div>
</body>
</html>