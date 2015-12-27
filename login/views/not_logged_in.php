<!-- login form box -->
<div class="login_container">

	<div class="login_title">
		LOGIN TO FOODBAH
	</div>

	<?php
	// show potential errors / feedback (from login object)
	if (isset($login)) {
		if ($login->errors) {
			foreach ($login->errors as $error) {
				echo '<span style="color:white">';
				echo $error;
				echo '</span>';
				echo '<br /><br />';
			}
		}
		if ($login->messages) {
			foreach ($login->messages as $message) {
				echo '<span style="color:white">';
				echo $message;
				echo '</span>';
				echo '<br /><br />';
			}
		}
	}
	?>

	<form method="post" action="index.php" name="loginform">

		<input id="login_input_username" class="login" type="text" name="user_name" placeholder="Username" required />
		<br><br>
		
		<input id="login_input_password" class="login" type="password" name="user_password" placeholder="Password" required />
		<br><br>
		
		<input id="login_remember_me" class="login" type="checkbox" name="remember_me" value="remember_me">&nbsp;<span style="color:white">Remember me</span>
		<br><br>

		<input type="submit"  name="login" class="login" value="Log in" />
		
		<br><br>
		
		<a href="register.php">Create an account</a>

	</form>
</div>
