<?php
session_start();

//Make sure user is logged in
if(!isset($_SESSION["sponsor_loggedin"]) || $_SESSION["sponsor_loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "../config.php";

//Check that event_id is set and not empty
if(isset($_GET["event_id"]) && !empty(trim($_GET["event_id"]))){


    $sql = "SELECT * FROM events WHERE event_id = ?";
    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $param_event_id);

        // Set params
        $param_event_id = trim($_GET["event_id"]);

        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result) == 1){
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                $event_id = $row["event_id"];
                $event_name = $row["event_name"];
                $sponsor_name = $row["sponsor_name"];
                $description = $row["description"];
                $location = $row["location"];
                $contribution_type = $row["contribution_type"];
                $contact_name = $row["contact_name"];
                $contact_phone = $row["contact_phone"];
                $contact_email = $row["contact_email"];
                $registration_start = $row["registration_start"];
                $registration_end = $row["registration_end"];
                $event_start = $row["event_start"];
                $event_end = $row["event_end"];

            } else{
                //NOTE: ERROR!
                header("Location: error.php");
                exit();
            }

        } else{
            echo "ERROR! Something has gone wrong...";
        }
    }

    mysqli_stmt_close($stmt);

} else{
    //NOTE: Error!
    header("Location: error.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Event</title>

        <!--Load required libraries-->
        <?php include '../head.php'?>

</head>
<body>
  <?php $thisPage='Events'; include 'navbar.php';?>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1>View Event</h1>
                    </div>

                    <div class="form-group">
                        <label>Event Name</label>
                        <p class="form-control-static"><?php echo $row["event_name"]; ?></p>
                    </div>

                    <div class="form-group">
                        <label>Sponsor Name</label>
                        <p class="form-control-static"><?php echo $row["sponsor_name"]; ?></p>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <p class="form-control-static"><?php echo $row["description"]; ?></p>
                    </div>

                    <div class="form-group">
                        <label>Location</label>
                        <p class="form-control-static"><?php echo $row["location"]; ?></p>
                    </div>

                    <div class="form-group">
                        <label>Contribution Type</label>
                        <p class="form-control-static"><?php echo $row["contribution_type"]; ?></p>
                    </div>

                    <div class="form-group">
                        <label>Contact Name(s)</label>
                        <p class="form-control-static"><?php echo $row["contact_name"]; ?></p>
                    </div>

                    <div class="form-group">
                        <label>Contact Phone(s)</label>
                        <p class="form-control-static"><?php echo $row["contact_phone"]; ?></p>
                    </div>

                    <div class="form-group">
                        <label>Contact Email(s)</label>
                        <p class="form-control-static"><?php echo $row["contact_email"]; ?></p>
                    </div>

                    <div class="form-group">
                        <label>Registration Start Date</label>
                        <p class="form-control-static"><?php echo $row["registration_start"]; ?></p>
                    </div>

                    <div class="form-group">
                        <label>Registration End Date</label>
                        <p class="form-control-static"><?php echo $row["registration_end"]; ?></p>
                    </div>

                    <div class="form-group">
                        <label>Event Start Date</label>
                        <p class="form-control-static"><?php echo $row["event_start"]; ?></p>
                    </div>

                    <div class="form-group">
                        <label>Event End Date</label>
                        <p class="form-control-static"><?php echo $row["event_end"]; ?></p>
                    </div>

                    <p><a href="events.php" class="btn btn-primary">Back</a></p>
                </div>
            </div>
        </div>
    </div>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Opportunities</h2>
                        <a href="opportunity-create.php?event_id=<?php echo $_GET["event_id"]?>" class="btn btn-success pull-right">Add New Opportunity</a>
                    </div>

                    <?php
                    // Select all relevant Opportunities

                    $sql = "SELECT opportunities.opportunity_id AS opportunity_id, opportunities.event_id AS event_id, role_name, description, start_date, start_time, end_date, end_time, total_positions, COUNT(engagement_id) AS positions_filled
                    FROM opportunities LEFT JOIN engagements ON opportunities.opportunity_id = engagements.opportunity_id
                    WHERE opportunities.event_id = '{$_GET["event_id"]}'
                    GROUP BY role_name, description, start_date, start_time, end_date, end_time, total_positions, opportunities.opportunity_id";
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>Role Name</th>";
                                        echo "<th>Description</th>";
                                        echo "<th>Start Date</th>";
                                        echo "<th>End Date</th>";
                                        echo "<th>Positions Filled</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['role_name'] . "</td>";
                                        echo "<td>" . $row['description'] . "</td>";
                                        echo "<td>" . $row['start_date'] . " " . $row['start_time'] ."</td>";
                                        echo "<td>" . $row['end_date'] . " " . $row['end_time'] . "</td>";
                                        echo "<td>" . $row['positions_filled'] . "/" . $row['total_positions'] . "</td>";
                                        echo "<td>";
                                            echo "<a href='opportunity-read.php?event_id=" . $_GET["event_id"] . "&opportunity_id=". $row['opportunity_id'] ."' title='View Opportunity' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                            echo "<br>";
                                            echo "<a href='opportunity-update.php?event_id=" . $_GET["event_id"] . "&opportunity_id=". $row['opportunity_id'] ."' title='Update Opportunity' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                            echo "<br>";
                                            echo "<a href='opportunity-delete.php?event_id=" . $_GET["event_id"] . "&opportunity_id=". $row['opportunity_id'] ."' title='Delete Opportunity' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";
                            echo "</table>";
                            mysqli_free_result($result);
                        } else{
                            echo "<p class='lead'><em>No opportunities were found.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
                    }
                    mysqli_close($link);
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php include '../footer.php';?>
</body>
</html>
