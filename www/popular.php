<?
require("../config.inc.php");
require("../includes/display.inc.php");
global $link_id;

if($_GET['by'] == "day")
	$title = "Most Popular Searches Today";
elseif($_GET['by'] == "week")
	$title = "Most Popular Searches This Week";
else
	$title = "Most Popular Searches Ever";

$content = print_title($title);
$content .= "<table border=0 width=100%><td width=33%><a href=popular.php>Most Popular Queries</a></td><td align=center width=34%><a href=popular.php?by=week>Popular This Week</a></td><td align=right width=33%><a href=popular.php?by=day>Popular Today</a></td></tr></table><br>";

if($_GET['by'] == "day") {
	$sql = "select * from search_terms WHERE
		TO_DAYS(NOW()) - TO_DAYS(first) = 0 order by hits desc limit 20";
} elseif($_GET['by'] == "week") {
	$sql = "select * from search_terms WHERE
		TO_DAYS(NOW()) - TO_DAYS(first) < 7 order by hits desc limit 20";
} else {
	$sql = "select * from search_terms order by hits desc limit 20";
}

$result = mysql_query($sql, $link_id);
$content .= "<table border=0 cellpadding=4 cellspacing=0 width=100% align=center>";
$content .= "<tr bgcolor=\"#BBBBBB\"><td width=\"60%\"><b>Search Term</b></td><td align=\"center\" nowrap=\"nowrap\"><b>First Search</b></td><td align=\"center\" nowrap=\"nowrap\"><b>Most Recent Search</b></td><td align=\"center\" width=5% nowrap=\"nowrap\"><b>Total</b></td></tr>";

while($foo = mysql_fetch_array($result)) {
	if($i % 2)
		$content .= "<tr bgcolor=#EEEEEE><td><a href=\"index.php?search={$foo['term']}\">{$foo['term']}</a></td><td align=center nowrap>{$foo['first']}</a></td><td align=center nowrap>{$foo['last']}</td><td align=center nowrap>{$foo['hits']}</td></tr>";
	else
		$content .= "<tr><td><a href=\"index.php?search={$foo['term']}\">{$foo['term']}</a></td><td align=center nowrap>{$foo['first']}</td><td align=center nowrap>{$foo['last']}</td><td align=center nowrap>{$foo['hits']}</td></tr>";
	$i++;
}
$content .= "</table>";
return_page("Popular",$content);
?>
