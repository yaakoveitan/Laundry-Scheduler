<?php
    // TODO: test this page
    $conn = mysqli_connect("localhost","root", "", "Laundry");
    if (!$conn)
    {
        die("Connection failed: " . mysqli_connect_error());
    } 

    if (isset($_REQUEST['submit']))
    {
        $userID = $_POST['id'];
        $password = $_POST['password'];
        $firstName = $_POST['first'];
        $lastName = $_POST['last'];
        $aptNumber = $_POST['apt'];
        $email = $_POST['email'];
        $phoneNumber = $_POST['phone'];

        $errormsg = "";

        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        $sql_id = "SELECT * FROM users WHERE userid = '$userID';";
        $idResult = mysqli_query($conn, $sql_id);

        $sql_apt = "SELECT * FROM users WHERE aptNumber = '$aptNumber';";
        $aptResult = mysqli_query($conn, $sql_apt);

        // check if username and apt number are unique and if password is strong enough
        if (mysqli_num_rows($idResult) > 0){
            $errormsg = 'Username already exists.';
        }
        else if (mysqli_num_rows($aptResult) > 0)
        {
            $errormsg = 'Apartment number already in use.';
        }
        else if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8)
        {
            $errormsg = 'Password is not strong enough.';
        }
        else
        {
            // all tests passed, insert into db
            // currently, aptNumber is a TINYINT in the database, may want to add some input validation or change that
            $password = password_hash($password, PASSWORD_DEFAULT);
            $sql_insert = "INSERT INTO Laundry.users(userid, password, firstName, lastName, aptNumber, email, phone) VALUES ('$userID', '$password', '$firstName', '$lastName', '$aptNumber', '$email', '$phoneNumber');";
            $insertResult = mysqli_query($conn, $sql_insert);
            if ($insertResult)
            {
                header('location:registerSuccess.html');
            }
            else
            {
                $errormsg = $errormsg . ' Failed adding new user to database!';
            }
        }
    }
?>

<html>
	<head>
    <title>New User Registration</title>
    <META http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="style.css">
    <script src="check.js"></script>
	</head>
	<body>
    <center>
        <form action="register.php" method="post" onsubmit="return checkFilled(document.getElementById('id').value, document.getElementById('password').value, document.getElementById('first').value, 
        document.getElementById('last').value, document.getElementById('apt').value, document.getElementById('email').value, document.getElementById('phone').value);">
        <h1>New User Registration</h1>
            <table>
                <tr>
                    <td>User ID</td>
                    <td><input type="text" name="id" id="id"></td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td><input type="password" name="password" id="password"></td>
                </tr>
                <tr>
                    <td>First Name</td>
                    <td><input type="text" name="first" id="first"></td>
                  </tr>
                  <tr>
                    <td>Last Name</td>
                    <td><input type="text" name="last" id="last"></td>
                  </tr>
                  <tr>
                    <td>Apartment Number</td>
                    <td><input type="text" name="apt" id="apt"></td>
                  </tr>
                  <tr>
                    <td>Email</td>
                    <td><input type="text" name="email" id="email"></td>
                </tr>
                <tr>
                    <td>Phone Number</td>
                    <td><input type="text" name="phone" id="phone"></td>
                </tr>
            </table>
        <br>
        <p>
            Password must consist of minimum 8 characters with one of each:<br>
            upper/lowercase letters, numbers and special characters
        </p>
                <br>
                <input type="submit" value ="Continue" name="submit">
        </form>
				
        <?php
            echo "<p>" . $errormsg . "</p>"
        ?>
		<br>
				<a href="login.html">Return to login page</a>
  </center>
	</body>
</html>

