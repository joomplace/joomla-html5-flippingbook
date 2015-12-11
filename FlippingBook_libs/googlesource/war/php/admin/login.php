<?php

/**
 * Copyright 2011 Google Inc.
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */ 

$errorMessage = '';

// Logout (unset cookie)
if( isset($_GET['logout']) ) {
	setcookie("f46f703eb202ff76ce8700e6b6b0e678", FALSE);
}

if( isset($_POST['txtUserId']) && isset($_POST['txtPassword']) ) {
	// check if the user id and password combination is correct
	if (( $_POST['txtUserId'] === 'testUsername' ) && ( $_POST['txtPassword'] === 'testPassword' )) {
		$expire = time()+60*60;
		setcookie("f46f703eb202ff76ce8700e6b6b0e678", "3ad6ada94da82d1729d9c826d417b16b", $expire);	
		$refPath = isset($_GET['ref']) ? urldecode($_GET['ref']) : '/cmshome';	
			header('Location: ' . $refPath);
	} else {
		$errorMessage = 'Sorry, wrong user id / password';
	}
}

?>
<html>
<head>
<title>Basic Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
	html, body {
		background-color: #eeeeee;
		color: #333333;
		font-family: Arial;
	}
	form {
		margin-top: 10%;
	}
	tr, td {
		border: none;
	}
	table {
		padding: 10px;
		background-color: #ffffff;
		border: #999999 1px solid;
		border-radius: 3px;
		-webkit-box-shadow: 3px 3px 1px #bbbbbb;
	}
	input {
		border: #999999 1px solid;
		border-radius: 2px;
		height: 25px;
	}
	input[type="text"],
	input[type="password"] {
		padding: 3px;
	}
	input[type="submit"] {
		margin-top: 10px;
	}
</style>
</head>

<body>
<?php
if ($errorMessage != '') {
?>
<p align="center"><strong><font color="#990000"><?php echo $errorMessage; ?></font></strong></p>
<?php
}
?>
<form method="post" name="frmLogin" id="frmLogin">
<table width="290" border="1" align="center" cellpadding="2" cellspacing="2">
<tr>
<td width="110">User ID</td>
<td><input name="txtUserId" type="text" id="txtUserId"></td>
</tr>
<tr>
<td width="110">Password</td>
<td><input name="txtPassword" type="password" id="txtPassword"></td>
</tr>
<tr>
<td width="110">&nbsp;</td>
<td><input type="submit" name="btnLogin" value="Login"></td>
</tr>
</table>
</form>
<script type="text/javascript">
document.getElementById('txtUserId').focus();
</script>
</body>
</html>