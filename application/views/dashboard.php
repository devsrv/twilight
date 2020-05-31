<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Dashboard</title>
</head>
<body>

<div id="container">
	<h1>Dashboard</h1>

	<p>Logged in as, <strong><?=$user?></strong> &nbsp;&nbsp;<a href="#" onclick="document.getElementById('logout-form').submit();">logout</a></p>


	<div id="body">
		<form action="/logout" method="post" id="logout-form">
			<input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
			<input type="hidden" name="logout" />
		</form>
	</div>

</div>

</body>
</html>
