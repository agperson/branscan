<?php
require("../config.inc.php");
require("../includes/display.inc.php");

global $link_id;

$time_start = getmicrotime();
$offset	= $_GET['offset'];
$order 	= $_GET['order'];
$total_results = $_GET['total_results'];

# Zero our offset
$offset = $offset ? $offset : 0;
	
$query_sql = "SELECT id, name, ip, lastscan, files, size FROM hosts WHERE isup=1 AND files > 0 AND size > 0";

# Append our ordering method
switch($order) {
    case "uptime":
		$order = "avg desc";
		break;
	case "files":
		$order = "files desc";
		break;
	case "size":
		$order = "size desc";
		break;
	case "lastscan":
		$order = "lastscan desc";
		break;
	default:
		$order = "name asc";
		break;
}

# Put in the order statement
$query_sql .= " ORDER BY $order";

# If we're not on the first results page, limit accordingly
if($offset > 0)
	$query_sql .= " LIMIT $offset," . BS_FPP;
		
# Do the damn query already!
$results = mysql_query($query_sql, $link_id);

# Debugging
# print $query_sql;
	
# How many returns did we get?
if($results)
	$num_results = mysql_num_rows($results);

# Total results means we don't have to do the whole query everytime someone loads another results page
if(!isset($total_results))
	$total_results = $num_results;

# Create the jump_to HTML
$search_enc = urlencode($query);
$jump_to = jump_to($offset, BS_FPP, $total_results, "total_results=$total_results&order=$order");

# Figure out where we are in the results
$first = $offset+1;
$last = ($offset+BS_FPP > $total_results) ? $total_results : $offset+BS_FPP;
	
# The next loop needs to know how far to go
$files_to_show = $last - $offset;
	
# Give us the sort bar
$content .= "<tr bgcolor=\"#BBBBBB\" height=\"22\">";
$content .= "<td width=\"50%\" nowrap=\"nowrap\"><b><a href=\"$PHP_SELF?total_results=$total_results\" class=\"sortterms\">Host Name</a></b></td>";
$content .= "<td width=\"10%\" nowrap=\"nowrap\"><b>IP Address</b></td>";
$content .= "<td width=\"10%\" nowrap=\"nowrap\"><b><a href=\"$PHP_SELF?total_results=$total_results&order=lastscan\" class=\"sortterms\">Last Scanned</a></b></td>";
$content .= "<td width=\"10%\" nowrap=\"nowrap\"><b><a href=\"$PHP_SELF?total_results=$total_results&order=files\" class=\"sortterms\"># of Files</a></b></td>";
$content .= "<td width=\"10%\" nowrap=\"nowrap\"><b><a href=\"$PHP_SELF?total_results=$total_results&order=size\" class=\"sortterms\">Size in MB</a></b></td>";
$content .= "</tr>";

for($row_num = 0; $row_num < $files_to_show; $row_num++) {
	$host = mysql_fetch_array($results);
	$ip = $host['ip'];
	$name = $host['name'];
	$lastscan = $host['lastscan'];
	$files = $host['files'];
	$size = round($host['size']/1024/1024, 0);
		
	$bgcolor = ($row_num % 2) ? "#DDDDDD" : "#FFFFFF";
	$content .= "<tr bgcolor=\"$bgcolor\" height=\"22\">";
	$content .= "<td nowrap=\"nowrap\"><a href=\"index.php?search=host: $name\">$name</a></td>
	<td nowrap=\"nowrap\"><a href=\"\\\\$ip\">$ip</a></td>
	<td align=\"center\" nowrap=\"nowrap\">$lastscan</td>
	<td align=\"right\" nowrap=\"nowrap\">$files</td>
	<td align=\"right\" nowrap=\"nowrap\">$size</td>
	</tr>";
}	
$content .= "</table>\n";
$content .= "<table width=600 cellpadding=6 align=center><tr><td>";
$content .= $jump_to ? "<table align=\"center\" width=\"350\" border=\"0\" cellspacing=\"1\" cellpadding=\"6\" bgcolor=\"" . BS_TAB_COLOR . "\" nowrap-\"nowrap\"><tr><td bgcolor=\"#DDDDDD\" align=\"center\">$jump_to</td></tr></table>" : "";
	
$time_stop = getmicrotime();
$time_query = $time_stop - $time_start;
		
$returned_stuff = print_title("Alphabetic Host Listing");
$returned_stuff .= "This list includes only computers that are in the " . BS_SYS_NAME . " search index.  Every computer showed below is on the residential network, is sharing at least one file of a type that " . BS_SYS_NAME . " cataloges, and was online the last time " . BS_SYS_NAME . " pinged it.  If a computer is not on this list, which is updated every " . BS_SCAN_HOURS . " hours, the system is not indexing it.<br /><br />
<table width=100% border=0 bgcolor=" . BS_TAB_COLOR . ">
<tr><td class=\"search\">Searched for <b>Indexed Hosts</b></td>
<td align=right class=\"search\">Results " . ($first) . " - " . ($last) . " of $total_results (<i>" . round($time_query, 3) . " seconds</i>)</td></tr></table>";
$returned_stuff .= "<table width=100% cellspacing=0 cellpadding=4>$content</table>";

# All done!
return_page("Hosts",$returned_stuff);
?>
