<?php

require 'header.php';
include 'config.php';

$user_error = 1;

$_SESSION["mac"] = $_POST['mac'];
$_SESSION["ip"] = $_POST['ip'];
$_SESSION["link-login"] = $_POST['link-login'];
$_SESSION["link-login-only"] = $_POST['link-login-only'];

$_SESSION["user_type"] = "new";
$table_name = "users";

# Checking DB to see if user exists or not.
echo $_SESSION["mac"];
mysqli_report(MYSQLI_REPORT_OFF);
$result = mysqli_query($con, "SELECT * FROM `$table_name` WHERE mac='$_SESSION[mac]'");

if ($result->num_rows >= 1) {
  // TODO: MAC Binding check

  $row = mysqli_fetch_array($result);

  mysqli_close($con);

  $_SESSION["user_type"] = "repeat";
  $date_old = $row['last_updated'];
  echo $date_old;

  mysqli_close($con);

  date_default_timezone_set("Asia/Jerusalem");

  // if previous login was less than 60 min ago, connect directly
  $date_now = date('Y-m-d H:i:s');
  $date_diff = abs(strtotime($date_now) - strtotime($date_old)) / (60 * 60 * 24);
  echo "date diff: ";
  echo $date_diff;
  if ($date_diff < 7) {
    $last_updated = date("Y-m-d H:i:s");
    $sql = "UPDATE `$table_name` SET last_updated='$last_updated' WHERE mac='$_SESSION[mac]'";
    echo $sql;
    echo $con->query($sql);
    
    //   header("Location: welcome.php");
  } else {
      $sql = "DELETE FROM `$table_name` WHERE mac='$_SESSION[mac]'";
      $con->query($sql);
      header("Location: register.php");
  }
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
            <img src="assets/images/logo.png">
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
                        <button class="button is-link">Connect</button>
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