<?php
    $conn = mysqli_connect("localhost", "root", "", "Laundry");
    if (!$conn)
    {
        die("Connection failed: " . mysqli_connect_error());
    }

    session_start();
    if (isset($_SESSION['user']))
    {
        $username = $_SESSION['user'];
    }
    else
    {
        header('location:error.html');
    }

    if(isset($_REQUEST['confirm']))
    {
        $oldTime = $_SESSION['oldTime'];
        $newTime = $_SESSION['newTime'];
        
        if ($oldTime == "none")
		{
			$sql_insert = "INSERT INTO Laundry.slots(start, userid) VALUES ('" . $newTime . "', '" . $username . "')";
			$insertReservation = mysqli_query($conn, $sql_insert);
		
			if($insertReservation){
				header('location:home.php');
			}
        }
        else
        {
            $sql_delete = "DELETE FROM Laundry.slots WHERE userid = '" . $username . "'";
			$deleteReservation = mysqli_query($conn, $sql_delete);	
		
			$sql_insert = "INSERT INTO Laundry.slots(start, userid) VALUES ('" . $newTime . "', '" . $username . "')";
			$insertReservation = mysqli_query($conn, $sql_insert);
		
			if($insertReservation){
				header('location:home.php');
            }
            else
            {
                echo("Error adding to database, make sure you are logged in properly.");
            }
        }
    }
    else if (isset($_REQUEST['cancel']))
    {
        header('location:home.php');
    }
    else
    {
        $oldTime = $_REQUEST['oldTime'];
        $newTime = $_REQUEST['newTime'];
        $_SESSION['oldTime'] = $oldTime;
        $_SESSION['newTime'] = $newTime;
    }
?>

<html>
    <head>
        <title>Confirmation Page</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <center>
        <?php
            if ($oldTime == "none")
            {
                echo "<p>You do not have a current reservation.</p>";
            }
            else 
            {
                echo "<p>You have an old reservation for " . $oldTime . " (this will be cancelled).</p>";
            }
            echo "<p>Confirm your new reservation for " . $newTime . "?</p>";

        ?>
        <form>
			<table>
				<tr>
                    <td><input type="submit" value="Confirm" name="confirm"></td>
                    <td><input type="submit" value="Cancel" name="cancel"></td>
				</tr>
			</table>
		</form>
		<br>
        </center>
    </body>
</html>