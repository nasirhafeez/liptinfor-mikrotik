<?php

require 'header.php';

$user_error = 0;

if (isset($_POST['verify'])) {
  $host_ip = $_SERVER['HOST_IP'];
  $db_user = $_SERVER['DB_USER'];
  $db_pass = $_SERVER['DB_PASS'];
  $db_name = $_SERVER['DB_NAME'];
  $radius_db_name = $_SERVER['RADIUS_DB_NAME'];
  $table_name = $_SERVER['TABLE_NAME'];

  $con = mysqli_connect($host_ip, $db_user, $db_pass);

  if (mysqli_connect_errno()) {
    echo "Failed to connect to SQL: " . mysqli_connect_error();
  }

  mysqli_report(MYSQLI_REPORT_OFF);

  mysqli_select_db($con, $radius_db_name);

  $result = mysqli_query($con, "SELECT * FROM `radusergroup` WHERE username='$_POST[rollno]'");

  if ($result->num_rows >= 1) {
    // TODO: Check whether user already exists in users table?

    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $reg = $_POST['rollno'];
    $phone = $_POST['phone'];
    $mac = $_SESSION["mac"];

    mysqli_select_db($con, $db_name);
    $user = mysqli_query($con, "SELECT * FROM `$table_name` WHERE reg='$reg'");

    $last_updated = date("Y-m-d H:i:s");
    if ($user->num_rows == 0) {
        // TODO: Insert data into users table
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
        UNIQUE KEY (mac)
        )");

        mysqli_select_db($con, $db_name);
        mysqli_query($con,"INSERT INTO `$table_name` (firstname, lastname, reg, mobile, mac, last_updated) VALUES ('$fname', '$lname', '$reg', '$phone', '$mac', '$last_updated')");

        // TODO: Generate OTP, send SMS and insert data into radcheck table
        $digits = 4;
        $otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
        $auth_code = $_SERVER['AUTH_CODE'];
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://sawkiwebsms.dev4smart.net/secure/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "to": "'.$phone.'",
            "from": "HotSpot",
            "content": "'.$otp.'",
            "dlr": "yes",
            "dlr-level": 3,
            "dlr-method": "GET",
            "dlr-url": "https://sms.ne/dlr",
            "sdt": "000000000000000R"
        }',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic ' . $auth_code,
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

        mysqli_select_db($con, $radius_db_name);
        mysqli_query($con,"INSERT INTO `radcheck` (`username`, `attribute`, `op`, `value`) VALUES ('$reg', 'Cleartext-Password', ':=', '$otp')");


        $_SESSION["user_type"] = "register";
        header("Location: index.php");
    } else {
        $user_error = 2;
    }
    // TODO: redirect to index page
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
            <img id="img1" src="assets/images/uni-logo1.jpg">
            <img id="img2" src="assets/images/uni-logo2.jpg">
        </figure>
    </div>

    <div class="main">
        <section class="section">
            <div class="container">
              <?php
              if ($user_error == 1) { ?>
                <div class="content is-size-6 has-text-centered has-text-danger">Matricule universitaire pas trouv??!
              <?php
              }
              ?>
              <?php
              if ($user_error == 2) { ?>
                <div class="content is-size-6 has-text-centered has-text-danger">Matricule universitaire existe d??j??!
              <?php
              }
              ?>
                <div id="contact_form" class="content is-size-5 has-text-centered has-text-weight-bold has-text-black">Saisir vos information
                </div>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="field">
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="form_font" name="fname" placeholder="Pr??nom" required>
                            <span class="icon is-small is-left">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="form_font" name="lname" placeholder="Nom de famille" required>
                            <span class="icon is-small is-left">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="form_font" name="rollno" placeholder="Matricule universitaire" required>
                            <span class="icon is-small is-left">
                                <i class="fas fa-id-card"></i>
                            </span>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control has-icons-left">
                            <input class="input" type="tel" id="form_font" name="phone" placeholder="Num??ro de t??l??phone" required>
                            <span class="icon is-small is-left">
                                <i class="fas fa-mobile"></i>
                            </span>
                        </div>
                    </div>
                    <div class="buttons is-centered">
                        <input class="button is-link" type="submit" name="verify" value="S'inscrire">
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
</body>
</html>