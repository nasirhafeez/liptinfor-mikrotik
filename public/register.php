<?php

require 'header.php';

$user_error = 0;

if (isset($_POST['verify'])) {
  $host_ip = $_SERVER['HOST_IP'];
  $db_user = $_SERVER['DB_USER'];
  $db_pass = $_SERVER['DB_PASS'];
  $db_name = $_SERVER['DB_NAME'];
  $radius_db_name = $_SERVER['RADIUS_DB_NAME'];

  $con = mysqli_connect($host_ip, $db_user, $db_pass);

  if (mysqli_connect_errno()) {
    echo "Failed to connect to SQL: " . mysqli_connect_error();
  }

  mysqli_report(MYSQLI_REPORT_OFF);

  mysqli_select_db($con, $radius_db_name);

  $result = mysqli_query($con, "SELECT * FROM `radusergroup` WHERE username='$_POST[rollno]'");

  if ($result->num_rows >= 1) {
    // TODO: Check whether user already exists in users table?



    // TODO: Insert data into users table
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $reg = $_POST['rollno'];
    $phone = $_POST['phone'];
    $mac = $_SESSION["mac"];
    $last_updated = date("Y-m-d H:i:s");

    mysqli_select_db($con, $db_name);

    mysqli_query($con, "
    CREATE TABLE IF NOT EXISTS `$table_name` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `firstname` varchar(45) NOT NULL,
    `lastname` varchar(45) NOT NULL,
    `reg` varchar(45) NOT NULL,
    `mobile` varchar(45) NOT NULL,
    `mac` varchar(45) NOT NULL,
    `last_updated` varchar(45) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY (reg)
    )");

    mysqli_query($con,"INSERT INTO `$table_name` (firstname, lastname, reg, mobile, mac, last_updated) VALUES ('$fname', '$lname', '$reg', '$phone', '$mac', '$last_updated')");

    // TODO: Generate OTP, send SMS and insert data into radcheck table

    $otp = 1111;

    mysqli_select_db($con, $radius_db_name);
    mysqli_query($con,"INSERT INTO `radcheck` (`username`, `attribute`, `op`, `value`) VALUES ('$reg', 'Cleartext-Password', ':=', '$otp')");

    // TODO: redirect to index page
    header("Location: index.php");
  } else {
    // User not found, display error
    $user_error = 1;
  }
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
              <?php
              if ($user_error == 1) { ?>
                <div class="content is-size-6 has-text-centered">Employment/Student # not found!
              <?php
              }
              ?>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="field">
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="form_font" name="fname" placeholder="First Name" required>
                            <span class="icon is-small is-left">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="form_font" name="lname" placeholder="Last Name" required>
                            <span class="icon is-small is-left">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control has-icons-left">
                            <input class="input" type="email" id="form_font" name="rollno" placeholder="Employment/Student #" required>
                            <span class="icon is-small is-left">
                                <i class="fas fa-id-card"></i>
                            </span>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control has-icons-left">
                            <input class="input" type="email" id="form_font" name="phone" placeholder="Phone" required>
                            <span class="icon is-small is-left">
                                <i class="fas fa-mobile"></i>
                            </span>
                        </div>
                    </div>
                    <div class="buttons is-centered">
<!--                        <button class="button is-link">Connect</button>-->
                        <button class="button is-link" type="submit" name="verify" value="Connect">
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
</body>
</html>