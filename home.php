<?php
    $conn = $conn = mysqli_connect("localhost","root", "", "Laundry");
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

    date_default_timezone_set('America/New_York');
    $currentTimeZone = timezone_open("America/New_York");

    // create a DateTime object and string representation of current date/time
    $currentDateTime = date_create('now', $currentTimeZone);
    $currentDateTimeString = date('Y-m-d H:i:s', $currentDateTime->getTimestamp());

    // create a DateTime object and string representation for the most recent monday at midnight
    $daysFromMonday = (int)date_format($currentDateTime, 'N') - 1;
    $lastMonday = date_create('now', $currentTimeZone);
    $lastMonday->sub(new DateInterval('P' . $daysFromMonday . 'D'));
    $lastMonday->setTime(0, 0, 0);
    $lastMondayString = date('Y-m-d H:i:s', $lastMonday->getTimestamp());

    // get all reservations for current week, store data in two associative arrays for later access
    $sql_reservations = "SELECT users.userid, users.aptNumber, slots.start FROM users JOIN slots ON users.userid = slots.userid WHERE slots.start > '$lastMondayString' ORDER BY start ASC;";
    $reservationsResult = mysqli_query($conn, $sql_reservations);
    $reservations = array();
    $aptNums = array();
    while($row = mysqli_fetch_assoc($reservationsResult))
    {
        $reservations[$row['start']] = $row['userid'];
        $aptNums[$row['userid']] = $row['aptNumber'];
        if ($username == $row['userid']) // get reservation of current user to display/compare
        {
            $userReservation = $row['start'];
        }
    }

?>

<html>
    <head>
    <title>Home Page</title>
    <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <center>
        <?php
            echo "<p>Welcome, " . $username . "!</p>";
            if (isset($userReservation))
            {
                echo "<p>You have a current reservation for " . $userReservation . "</p>";
                $url = array('oldTime' => $userReservation);
                echo "<a href='cancel.php?" . http_build_query($url) . "'>Click here to cancel your reservation.</a>";

                echo "<p>You can change your reservation by selecting a new time slot below:</p>";
            }
            else 
            {
                echo "<p>You do not currently have a reservation, select a time slot below:</p>";
            }

            // create table of time slots
            $times = array("12:00 am", "03:00 am", "06:00 am", "09:00 am", "12:00 pm", "3:00 pm", "6:00 pm", "9:00 pm");
            $days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");

            echo "<table border = '1'>";
            echo "<tr>";
            for ($i = 0; $i < 7; $i++)
            {
                echo "<td>" . $days[$i] . "</td>";
            }
            echo "</tr>";

            for ($r = 0; $r < 8; $r++)
            {
                echo "<tr>";
                for ($c = 0; $c < 7; $c++)
                {
                    // calculate start time for the given cell
                    $thisTime = clone($lastMonday);
                    $thisTime->add(new DateInterval('P' . $c . 'DT' . $r * 3 . 'H')); // adds c days and 3*r hours to monday 00:00:00 to get the time for this cell
                    $thisTimeString = date('Y-m-d H:i:s', $thisTime->getTimestamp());

                    // calculate end time for the given cell for comparison
                    $endTime = clone($thisTime);
                    $endTime->add(new DateInterval('PT3H')); // adds 3 hours
                    $endTimeString = date('Y-m-d H:i:s', $endTime->getTimestamp());
                    
                    if ($currentDateTimeString > $endTimeString) // ending time has already passed
                    {
                        echo "<td>Unavailable</td>";
                    }
                    else if (array_key_exists($thisTimeString, $reservations)) // if reserved by someone
                    {
                        echo "<td>Reserved by apt#" . $aptNums[$reservations[$thisTimeString]] . "</td>"; // displays the apt number of account who reserved it
                    }
                    else // slot is reservable, generate a link for confirmation page
                    {
                        if (isset($userReservation))
                        {
                            $encode = array('oldTime' => $userReservation, 'newTime' => $thisTimeString);
                        }
                        else
                        {
                            $encode = array('oldTime' => "none", 'newTime' => $thisTimeString);
                        }
                        echo "<td><a href='confirm.php?" . http_build_query($encode) . "' id='" . $r . "," . $c . "'>" . $times[$r] . "</a></td>";
                    }
                }
                echo "</tr>";
            }
            echo "</table>";         
        ?>
        <br>
        <a href="logout.php">Click here to logout</a>

    </center>
    </body>
</html>