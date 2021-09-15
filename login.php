<?php
    $conn = mysqli_connect("localhost", "root", "", "Laundry");
    if (!$conn)
    {
        die("Connection failed: " . mysqli_connect_error());
    } 

    $username = $_POST['user'];
    $password = $_POST['pass'];

    $sql = "SELECT userid, password FROM users WHERE userid = '$username' LIMIT 1";  
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    if ($row)
    {
        $storedHash = $row['password'];   
        if(password_verify($password, $storedHash))
        {
            $message = "<p>Login successful.</p>";
            session_start();
            $_SESSION['user'] = $username;
            header('location:home.php');
        } 
        else
        {
            $errormsg = "Login failed. Invalid password. Try again or register.";
        }
    }
    else
    {  
        $errormsg = "Login failed. Invalid userid. Try again or register.";
    }     
?>

<html>
<head>
   <title>Login Page</title>
   <link rel="stylesheet" href="style.css">
   <script src="check.js"></script>
</head>
<body>
  <center>
    <form action="login.php" method="post" name="form" onsubmit="return check(document.form.user.value, 'Username') && check(document.form.pass.value, 'Password');"  autocomplete="off" >
      <h1>Log In</h1><br>
      <input type="text" name="user" id="user" placeholder="USER ID"><br><br>
      <input type="password" name="pass" id="pass" placeholder="PASSWORD"><br><br>
      <input type="submit" name="" value="Login"> <br><br>
      <?php echo "<p>" . $errormsg . "</p>"?>
      <p>New User? <a href="register.html">Register Here</a></p>
    </form>
  </center>
</body>
</html>