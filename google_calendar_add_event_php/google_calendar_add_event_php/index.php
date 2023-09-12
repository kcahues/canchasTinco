<?php
// Include configuration file
include_once 'config.php';

$postData = '';
if(!empty($_SESSION['postData'])){
    $postData = $_SESSION['postData'];
    unset($_SESSION['postData']);
}

$status = $statusMsg = '';
if(!empty($_SESSION['status_response'])){
    $status_response = $_SESSION['status_response'];
    $status = $status_response['status'];
    $statusMsg = $status_response['status_msg'];
    
    unset($_SESSION['status_response']);
}
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
<title>Add Event to Google Calendar using PHP by CodexWorld</title>
<meta charset="utf-8">

<!-- Bootstrap library -->
<link href="css/bootstrap.min.css" rel="stylesheet">

<!-- Stylesheet file -->
<link rel="stylesheet" href="css/style.css">

</head>
<body>
<div class="container">
    <h1>ADD EVENT TO GOOGLE CALENDAR</h1>
	
	<div class="wrapper">
		
		<!-- Status message -->
        <?php if(!empty($statusMsg)){ ?>
            <div class="alert alert-<?php echo $status; ?>"><?php echo $statusMsg; ?></div>
        <?php } ?>
		
		<div class="col-md-12">
            <form method="post" action="addEvent.php" class="form">
                <div class="form-group">
                    <label>Event Title</label>
                    <input type="text" class="form-control" name="title" value="<?php echo !empty($postData['title'])?$postData['title']:''; ?>" required="">
                </div>
                <div class="form-group">
                    <label>Event Description</label>
					<textarea name="description" class="form-control"><?php echo !empty($postData['description'])?$postData['description']:''; ?></textarea>
                </div>
				<div class="form-group">
                    <label>Location</label>
					<input type="text" name="location" class="form-control" value="<?php echo !empty($postData['location'])?$postData['location']:''; ?>">
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" class="form-control" value="<?php echo !empty($postData['date'])?$postData['date']:''; ?>" required="">
                </div>
				<div class="form-group time">
                    <label>Time</label>
                    <input type="time" name="time_from" class="form-control" value="<?php echo !empty($postData['time_from'])?$postData['time_from']:''; ?>">
					<span>TO</span>
					<input type="time" name="time_to" class="form-control" value="<?php echo !empty($postData['time_to'])?$postData['time_to']:''; ?>">
                </div>
				<div class="form-group">
					<input type="submit" class="form-control btn-primary" name="submit" value="Add Event"/>
				</div>
            </form>
        </div>
	</div>
</div>
</body>
</html>