
<!-- register form -->
<div class="register_container">

<div class="register_title">
	CREATE AN ACCOUNT
</div>

<?php
// show potential errors / feedback (from registration object)
if (isset($registration)) {
    if ($registration->errors) {
        foreach ($registration->errors as $error) {
			echo "<span style='color:white'>$error</span><br /><br />";
        }
    }
    if ($registration->messages) {
        foreach ($registration->messages as $message) {
            echo "<span style='color:white'>$message</span><br /><br />";
        }
    }
}
?>

<form method="post" action="register.php" name="registerform">

	<input id="login_input_username" class="login" type="text" pattern="[a-zA-Z0-9]{2,64}" name="user_name" placeholder="Username" required />
	<br /><br />
	
	<!-- (Not required)
	<input id="login_input_email" class="login" type="email" name="user_email" placeholder="Email" required />
	<br /><br />
	-->
	
	<input id="login_input_password_new" class="login" type="password" name="user_password_new" pattern=".{6,}" placeholder="Password" required autocomplete="off" />
	<br /><br />
	
	<!-- (Not required)
	<input id="login_input_password_repeat" class="login" type="password" name="user_password_repeat" pattern=".{6,}" placeholder="Repeat Password" required autocomplete="off" />
    <br /><br />
	-->
	
	<div class="register_captcha">
		<div class="g-recaptcha" data-sitekey="6LchmgYTAAAAABmg0kWgNkvLQ6cqcodL0gAC-8XK"></div>
	</div>
	
	<input type="submit" class="login" name="register" value="Register" />

</form>

<br /><br />

<a href="index.php">Return to login</a>&nbsp;&nbsp;&nbsp;
<!--<a href="#">Your privacy</a>
To be done soon. Basically, we hash+salt your password, but keep everything else as plaintext (for now).
-->

</div>


