<?php
include("../config.inc.php");
global $link_id;

function user_log() {
	global $link_id, $_SERVER;

	$addr = $_SERVER['REMOTE_ADDR'];
	$result = mysql_query("SELECT ip, lastvisit, TO_DAYS(lastvisit) - TO_DAYS(NOW()) as is_today, hits_today, hits_total FROM users WHERE ip='$addr'", $link_id);
	if($result)
		$count = mysql_num_rows($result);
	if($count) {
		$row = mysql_fetch_array($result);
		if($row['is_today'] == 0) {
			# Last visit was today
			$hits_today = $row['hits_today'];
			$hits_today++;
		} else {
			$hits_today = 1;
		}
		$hits_total = $row['hits_total'];
		$hits_total++;
        	$sql = "UPDATE users SET hits_today=$hits_today, hits_total=$hits_total, lastvisit=NOW() WHERE ip='$addr'";
		mysql_query($sql, $link_id);
	} else {
		$sql = "INSERT INTO users(ip, firstvisit, lastvisit, hits_today, hits_total) VALUES('$addr', NOW(), NOW(), 1, 1)";
        mysql_query($sql, $link_id);
    }
}
?>
