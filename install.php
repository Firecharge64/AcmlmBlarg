<!doctype html>
<html>
<head>
<title>AcmlmBlarg install</title>
<style type="text/css">
a:link { color: #0e5; }
a:visited { color: #0e5; }
a:hover, a:active { color: #bfb; }

html, body { width: 100%; height: 100%; }

body
{
	font: 12pt 'Arial', 'Helvetica', sans-serif;
	background: #800000;
	margin: 0;
	padding: 0;
	text-align: center;
	color: white;
}

#container
{
	background: #0000FF;
	min-height: 100%;
	box-sizing: border-box;
	-moz-box-sizing: border-box;
	max-width: 1000px;
	margin: 0 auto;
	padding-top: 20px;
	padding-bottom: 0;
}

h1, h3
{
	border-bottom: 2px solid #eee;
	padding-bottom: 1em;
}

input, select
{
	background: black;
	color: white;
	border: 1px solid #FF0000;
}

input[type='submit'], input[type='button']
{
	border: 2px ridge #eee;
}

.blarg
{
	margin: 1em;
}

table
{
	width: 100%;
	border-collapse: collapse;
}

td:not([colspan='2'])
{
	border-bottom: 1px solid #FF0000;
}
</style>
</head>
<body>
<div id="container">
<h1>AcmlmBlarg install</h1>
<br>
<?php

function phpescape($var)
{
	$var = addslashes($var);
	$var = str_replace('\\\'', '\'', $var);
	return '"'.$var.'"';
}

function Shake($len=16)
{
	$cset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
	$salt = "";
	$chct = strlen($cset) - 1;
	while (strlen($salt) < $len)
		$salt .= $cset[mt_rand(0, $chct)];
	return $salt;
}

// Acmlmboard 1.x style
$footer = '</div></body></html>';

// pre-install checks

if (file_exists('config/database.php'))
	die('The database details already exist, so the board is installed.<br>If not, please delete config/database.php.<br><br>DO NOT DELETE
	THE FOLDER OR EVERY PASSWORD WILL NOT WORK.'.$copyright ' .$footer);
	
$footer = '<br><br><a href="javascript:window.history.back();">Go back and try again</a></div></body></html>';

$copyright = '<br><br><p>&copy; 2017 - theninja1000. This software may not be reproduced without permission from
              theninja1000.';

if (version_compare(PHP_VERSION, '5.5.0') < 0)
	die('Error: AcmlmBlarg requires PHP 5.5 or above.'.$footer);
	
if (!is_dir('config'))
	if (!mkdir('config'))
		die('Error: failed to create the config directory. Check the permissions of the user running PHP.'.$footer);
	
@mkdir('templates_c');

if ($_POST['submit'])
{
	$boardusername = trim($_POST['boardusername']);
	$boardpassword = $_POST['boardpassword'];
	
	if (!$boardusername || !$boardpassword)
		die('Please enter a username and password.'.$footer);
		
	if ($boardpassword !== $_POST['bpconfirm'])
		die('Error: the passwords you entered don\'t match.'.$footer);
	
	$test = new mysqli($_POST['dbserver'], $_POST['dbusername'], $_POST['dbpassword'], $_POST['dbname']);
	if ($test->connect_error)
		die('Error: failed to connect to the MySQL server: '.$test->connect_error.'<br><br>Check your parameters.'.$footer);
	
	$test->close();
	
	$dbconfig = 
'<?php
$dbserv = '.phpescape($_POST['dbserver']).';
$dbuser = '.phpescape($_POST['dbusername']).';
$dbpass = '.phpescape($_POST['dbpassword']).';
$dbname = '.phpescape($_POST['dbname']).';
$dbpref = '.phpescape($_POST['dbprefix']).';
$debugMode = 0;
$logSqlErrors = 0;
?>';
	if (file_put_contents('config/database.php', $dbconfig) === FALSE)
		die('Error: failed to create the config file. Check the permissions of the user running PHP.'.$footer);
	
	$salt = Shake(24);
	$saltfile = '<?php $salt = '.phpescape($salt).'; ?>';
	file_put_contents('config/salt.php', $saltfile);
	
	$kurifile = '<?php $kurikey = '.phpescape(Shake(32)).'; ?>';
	file_put_contents('config/kurikey.php', $kurifile);
	
	require('lib/mysql.php');
	require('lib/mysqlfunctions.php');
	$debugMode = 1;
	
	Upgrade();
	Import('database.sql');
	
	$pss = Shake(16);
	$sha = hash('sha256', $boardpassword.$salt.$pss, FALSE);
	
	Query("insert into {users} (id, name, password, pss, primarygroup, regdate, lastactivity, lastip, email, sex, theme) values ({0}, {1}, {2}, {3}, {4}, {5}, {5}, {6}, {7}, {8}, {9})", 
		1, $boardusername, $sha, $pss, 4, time(), $_SERVER['REMOTE_ADDR'], '', 2, 'blargboard');
		
?>
	<h3>Your new AcmlmBlarg board has been successfully installed!</h3>
	<br>
	You should now:
	<ul>
		<li>delete install.php and database.sql
		<li><a href="./?page=login">log in to your board</a> and configure it
	</ul>
	<br>
	Thank you for choosing AcmlmBlarg!<br>
	<br>
<?php
}
else
{
?>
	<form action="" method="POST">
	<div class="blarg">
	<table>
	<h3>Credentials</h3>
	<p>Let's get going. Here you will need to type in your MySQL details.<br>Make sure your
	webserver's permissions are set so AcmlmBlarg can generate the config folder with
	database.php, kurikey.php and salt.php.</p>
	<tr><td>MySQL server:</td><td><input type="text" name="dbserver" size=64 value="localhost"></td></tr>
	<tr><td>MySQL username:</td><td><input type="text" name="dbusername" size=64 value="acmlmblarg"></td></tr>
	<tr><td>MySQL password:</td><td><input type="password" name="dbpassword" size=64 value=""></td></tr>
	<tr><td>MySQL database:</td><td><input type="text" name="dbname" size=64 value="acmlmblarg"></td></tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	
	<tr><td>Database table name prefix:</td><td><input type="text" name="dbprefix" size=64 value=""></td></tr>
	<tr><td colspan=2>This setting can be useful when the board's database is shared with other applications. If you're not sure what to put in there, leave it blank unless you use other applications with the same database.</td></tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	
	<tr><td>Board username:</td><td><input type="text" name="boardusername" size=64 maxlength=20 value=""></td></tr>
	<tr><td>Board password:</td><td><input type="password" name="boardpassword" size=64 value=""></td></tr>
	<tr><td>Confirm board password:</td><td><input type="password" name="bpconfirm" size=64 value=""></td></tr>
	<tr><td colspan=2>An owner account with these credentials will be created on your board after the install process has completed.</td></tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	
	<tr><td colspan=2><input type="submit" name="submit" value="Install Now"></td></tr>
	<br><br><br><br>
	<?php
	echo '.$copyright';
	?>
	</table>
	</div>
	</form>
<?php
}
?>
</div>
</body>
</html>
