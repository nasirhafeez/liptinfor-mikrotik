<?php

require 'header.php';
include 'config.php';

$user_error = 0;

$mac = $_SESSION["mac"];
$ip = $_SESSION["ip"];
$link_login = $_SESSION["link-login"];
$link_login_only = $_SESSION["link-login-only"];
$linkorig = $_SERVER['REDIRECT_URL'];

$last_updated = date("Y-m-d H:i:s");

if ($_SESSION['user_type'] == "register") {
    $username = $_POST['username'];
    $password = $_POST['password'];
} elseif ($_SESSION['user_type'] == "repeat") {
    $username = $_SESSION['username'];
    $password = $_SESSION['password'];
} else {
    // For user_type new
    $username = $_POST['username'];
    $password = $_POST['password'];

    // TODO: Mac Binding Check
    # Checking DB to see if user exists or not.
    $host_ip = $_SERVER['HOST_IP'];
    $db_user = $_SERVER['DB_USER'];
    $db_pass = $_SERVER['DB_PASS'];
    $db_name = $_SERVER['DB_NAME'];
    $table_name = $_SERVER['TABLE_NAME'];

    $con = mysqli_connect($host_ip, $db_user, $db_pass);

    mysqli_report(MYSQLI_REPORT_OFF);
    mysqli_select_db($con, $db_name);
    $result = null;
    $result = mysqli_query($con, "SELECT * FROM `$table_name` WHERE reg='$username'");
    if ($result->num_rows >= 1) {
        $row = mysqli_fetch_array($result);
        $mac_old = $row['mac'];
        if ($mac_old != $mac) {
            $user_error = 1;
        }
    }
}
//echo $_SESSION['user_type'];
//echo $username;
//echo $password;
?>
<!DOCTYPE HTML>
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
            <img src="assets/images/logo.png">
        </figure>
    </div>

    <div class="main">
        <seection class="section">
            <div class="container">
                <?php
                if ($user_error == 1) { ?>
                    <div id="error" class="content is-size-6 has-text-centered has-text-danger">Access from unregistered device blocked!
                <?php
                } else {
                ?>
                <div id="margin_zero" class="content has-text-centered is-size-6">Please wait, you are being</div>
                <div id="margin_zero" class="content has-text-centered is-size-6">authorized on the network</div>
                <?php
                }
                ?>
            </div>
        </seection>
    </div>

</div>

<script type="text/javascript">
    function formAutoSubmit () {
        var frm = document.getElementById("login");
        document.getElementById("login").submit();
        frm.submit();
    }
    // window.onload = formAutoSubmit;
    var error = document.getElementById('error');
    if (!error) {
        window.onload = setTimeout(formAutoSubmit, 2500);
    }
</script>

<form id="login" method="post" action="<?php echo $link_login_only; ?>" >
    <input name="dst" type="hidden" value="<?php echo $linkorig; ?>" />
    <input name="popup" type="hidden" value="false" />
    <input name="username" type="hidden" value="<?php echo $username; ?>"/>
    <input name="password" type="hidden" value="<?php echo $password; ?>"/>
</form>

</body>
</html>