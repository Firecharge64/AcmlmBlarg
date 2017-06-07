<?php

CheckPermission('mod.issuewarnings');

if ($_POST['userid'] && $_POST['warningtext'])
{
	Query("INSERT INTO {warnings} (userid,warningtext,title) VALUES ({0},{1})",
		$_POST['userid'], $_POST['warningtext']);
}

?>
<form action="" method="POST">
User ID: <input type="text" name="userid"><br>
Title: <input type="text" name="title"><br>
Reason: <input type="text" name="warningtext"><br>
<input type="submit" value="Add">
</form>