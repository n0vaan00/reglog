<?php 
  require_once('./server.php');

  if (!isset($_SESSION['username'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }
  if (isset($_GET['logout'])) {
  	session_destroy();
  	unset($_SESSION['username']);
  	header("location: login.php");
  }
   $contacts = getInfo();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
</head>
<body>

<div class="header">
	<h2>Home Page</h2>
</div>
<div class="content">
  	<!-- notification message -->
  	<?php if (isset($_SESSION['success'])) : ?>
      <div class="error success" >
      	<h3>
          <?php 
          	echo $_SESSION['success']; 
          	unset($_SESSION['success']);
          ?>
      	</h3>
      </div>
  	<?php endif ?>

    <!-- logged in user information -->
    <?php  if (isset($_SESSION['username'])) : ?>
    	<p>Welcome <strong><?php echo $_SESSION['username']; ?></strong></p>
		
		<div class="content">
			<h2 class="content-title">Information</h2>
            <?php foreach ($contacts as $contact): ?>
	            <h3><?php echo $contact['email'] ?></h3>
				<h3><?php echo $contact['phone'] ?></h3>
            <?php endforeach ?>
		</div>
		<form method="post" action="">
  		<?php include('errors.php'); ?>
			<div>
				<label>Phonenumber</label>
				<input name="phone"></input>
				<button type="submit" class="btn" name="edit_phone">Change phonenumber</button>
			</div>
			</form>	
    	<p> <a href="index.php?logout='1'">logout</a> </p>
    <?php endif ?>
</div>
		
</body>
</html>