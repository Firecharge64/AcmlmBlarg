<table class="outline margin center" style="width: 60%; overflow: auto; margin: auto; margin-top: 40px; margin-bottom: 40px;">
<tr><td class="cell0" style="padding:60px">
<?php
$ipban['date'] = (int) $ipban['date'];
if($ipban['date'])
	print format(__("You have been banned from this board until {0}. That's {1} left."),
				gmdate("M jS Y, G:i:s",$ipban['date']),
				TimeUnits($ipban['date']-time()));
else
	print __("Access to this board from your IP address is <strong>permanently</strong>banned");
print "<br />";
print __("Attempting to get around this in any way will result in worse things.");
print "<br />";
print "<br />";
print "<b>".__("Reason")."</b>: ".htmlspecialchars($ipban['reason']);
if ($adminemail) print '<br><br><br><b>Board email:</b> '.$adminemail;
print '<br><br>
		<small>Note: if you are using a proxy, this likely means that someone else abused the proxy before you.</small>
		<br><br>'
print "<br />";
$bucket = "ipbanned"; include('lib/pluginloader.php');
?>

</td></tr></table>
