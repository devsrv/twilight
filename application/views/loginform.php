<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Login</title>
</head>
<body>

<div id="container">
	<h1>Login Form</h1>

	<?php if($error === 1): ?>
		<p>Error! Incorrect credentials provided</p>
	<?php endif; ?>


	<div id="body">
		<form action="" method="post">
			<input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
			<input type="text" name="email" value="developer.srv1@gmail.com" placeholder="Email-ID" />
			<input type="password" name="password" value="sourav" />
			<button type="submit">Login</button>
		</form>
	</div>
</div>

</body>
</html>
