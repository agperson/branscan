<?php
include("../config.inc.php");
include("users.inc.php");
global $types;
# display.inc.php
# Page display functions

# getmicrotime()				Returns the current microtime (for benchmarking)
# print_title($title)			Unified title display function
# return_page($case, $content)	Unified page display function

function getmicrotime() {
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}

function print_title($title) {
    return "<img width=\"1\" height=\"2\" src=\"neg.gif\"><br><table border=\"0\" bgcolor=\"#AAAAAA\" width=\"100%\"><tr><td><font color=\"#FFFFFF\" size=\"+1\"><b>&nbsp;$title</b></font></td></tr></table>";
}

function make_tabs($selected="search") {
	$search = $popular = $hosts = $stats = "tabs";
	$$selected = "tabs_sel";

	$stuff = "<td width=\"11\">&nbsp;</td>
<td width=\"143\" align=\"center\" class=\"$search\"><a href=\"index.php\" class=\"$search\">Search " . BS_ORG_NAME . "</a></td>
<td width=\"5\">&nbsp;</td>
<td width=\"143\" align=\"center\" class=\"$popular\"><a href=\"popular.php\" class=\"$popular\">Popular Searches</a></td>
<td width=\"5\">&nbsp;</td>
<td width=\"143\" align=\"center\" class=\"$hosts\"><a href=\"hosts.php\" class=\"$hosts\">Host List</a></td>
<td width=\"5\">&nbsp;</td>
<td width=\"143\" align=\"center\" class=\"$stats\"><a href=\"stats.php\" class=\"$stats\">Statistics</a></td>
<td width=\"11\">&nbsp;</td>";

	return $stuff;
}

function return_page($case="Top", $main_content) {
	global $link_id;

	user_log();
	$sizefile = fopen("../stats/size.txt", r);
	$hostfile = fopen("../stats/hosts-total.txt", r);
	$filefile = fopen("../stats/files.txt", r);
	$bot_size = fread($sizefile, 1024);
	$bot_host = fread($hostfile, 1024);
	$bot_file = fread($filefile, 1024);
	fclose($sizefile);
	fclose($hostfile);
	fclose($filefile);
	$bottom = "Searching $bot_file files ($bot_size gigabytes) on $bot_host hosts.";
	$format = $_GET['format'];
	global $type;
	global $order;
	
	if($type)
		$$type = " selected";
	else
		$All = " selected";
	
	$$order = " selected";
		
	# If no format is selected, guess based on USER_AGENT
	if(!$format) {
		if(eregi("Win", $_SERVER["HTTP_USER_AGENT"]))
			$format = "windows";
		else
			$format = "linux";
	}

	switch($format) {	
		case "linux":
			$format_html = "<option label=\"Windows\" value=\"windows\">Windows</option>
<option label=\"Mac/Linux\" value=\"linux\" selected>Mac/Linux</option>";
			break;
		default:
			$format_html = "<option label=\"Windows\" value=\"windows\" selected>Windows</option>
<option label=\"Mac/Linux\" value=\"linux\">Mac/Linux</option>";
			break;

	}
	
	# Switch layout based on page being viewed
	if($case != "Search") {
		$main_content = "<table border=\"0\" width=\"600\" cellpadding=\"0\" cellspacing=\"0\"><tr><td>$main_content</td></tr></table>";
	} 
    if($case == "Top") {
    	if(BS_SHARE_NAG == 1 && !is_user_sharing())
			$top_content = "
<tr valign=\"middle\"><td><center><font size=+1
color=red><b>SHARING: 
ITS NOT JUST FOR KINDERGARTENERS!</b></font></center>
" . BS_SYS_NAME . " is successful because users freely share files of interest to the general community.  We encourage you to learn about the methods (and
security implications) of sharing, and to grant <b>read-only access</b>
to shares on your computer that you think others might find valuable. " . BS_SYS_NAME . " looks for <b>new</b> shares every " . (BS_SCAN_HOURS-2) . " to " . (BS_SCAN_HOURS+2) . " hours.
<br><br></td></tr>";
	
	$search_tabs = make_tabs("search");
	if (BS_TEXT_VIRUS_ALERT != "")
		$virus_alert_formatted = BS_TEXT_VIRUS_ALERT . "<br><br>";
	if (BS_TEXT_ANNOUNCEMENTS != "")
    		$top_content .= "
    <tr align=\"center\" valign=\"middle\"> 
      <td><table width=\"600\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
          <tr> 
            <td width=11>&nbsp;</td>
            <td width=\"143\" align=\"center\" class=\"tabs_sel\">Announcements</td>
            <td width=446>&nbsp;</td>
          </tr>
          <tr> 
            <td colspan=\"3\" bgcolor=\"" . BS_TAB_COLOR . "\" height=1><img width=\"1\" height=\"1\"></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">
          <tr> 
            <td>" . BS_TEXT_ANNOUNCEMENTS . "</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>";
    } elseif($case == "Search") {
    	# we want global of the search string, format, orderby, type
    	global $search_with_keywords;
		$search_tabs = make_tabs("search");
	} elseif($case == "Popular") {
		$search_tabs = make_tabs("popular");
	} elseif($case == "Hosts") {
		$search_tabs = make_tabs("hosts");
	} elseif($case == "Stats") {
		$search_tabs = make_tabs("stats");
	} else {
		$search_tabs = make_tabs("search");
	}
    
    print <<< EOT
<html>
<head>
<title>
EOT;
	print BS_TITLE;
	print <<< EOT
</title>
<style type="text/css">
	.tabs, a.tabs { color: black; background-color: #DDDDDD; font-weight: bold; font-size: 13px; height: 18px }
EOT;
print ".tabs_sel, a.tabs_sel, a.tabs_sel:hover { color: white; text-decoration: none; background-color: " . BS_TAB_COLOR . "; font-weight: bold; font-size: 13px; height: 18px }";
print <<< EOT
	body { font-family: Arial }
	td { font-size: 12px }
	.search { font-size: 12px; color: white}
	.size { font-size: 12px; font-weight: bold }
	.results { font-size: 11px }
	.copyright { font-size: 10px }
	a.sortterms { color: black }
	a:hover { color: #000000 }
</style></head>
<body bgcolor="#FFFFFF" link="
EOT;
print BS_TAB_COLOR;
print <<< EOT
" vlink="#333333">
<div align="center"> 
  <table width="600" border="0" cellspacing="0" cellpadding="0">
    <tr align="center"> 
      <td colspan=5><a href="index.php"><img src="
EOT;
print BS_LOGO;
print <<< EOT
" border="0"></a></td>
    </tr>
{$top_content}
<tr>
      <td><table width="600" border="0" cellpadding="0" cellspacing="0">
          <tr height=18> 
{$search_tabs}
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="1" bgcolor="
EOT;
print BS_TAB_COLOR;
print <<< EOT
"><img width="1" height="1"></td>
    </tr></table>
EOT;

if(BS_NO_SEARCH == 1) {
	print <<< EOT
  	<table border="0" cellspacing="0" cellpadding="0">
	<tr><td align=center valign=middle><font size=+2 color=red>Down for maintenance...</font>
	<br>Sorry, we'll be right back!</td></tr>
	</table>
EOT;
} elseif(($case != "Top") && ($case != "Search")) {
	print "";
} else {
	print <<< EOT
  <table border="0" cellspacing="0" cellpadding="0">
<tr><td align=center valign=middle><form name='search_form'
action="{$PHP_SELF}" method='get'><table border=0 cellpadding=5><tr><td
align=center valign=middle>
{$virus_alert_formatted}
<input name=search type='text' value="$search_with_keywords" size=40>
<input type='submit' value=' Search '></td>
</tr><tr>
<td align=center valign=middle>Format:&nbsp;<select name="format">{$format_html}</select>&nbsp;&nbsp;Order:&nbsp;<select name="order">
<option label="Relevance" value="best"{$best}>Relevance</option>
<option label="Path" value="path"{$path}>Path</option>
<option label="File Name" value="file"{$file}>File Name</option>
<option label="Size" value="size"{$size}>Size</option>
</select>&nbsp;&nbsp;Types:&nbsp;<select
name="type">
<option label="All" value="All"{$All}>All</option>
EOT;

global $types;
foreach($types as $onetype => $array)
	print "<option label=\"$onetype\" value=\"$onetype\"${$onetype}>$onetype</option>";

print <<< EOT
</select>
</td></tr></table></form></td></tr>
</table>
EOT;
}

	print <<< EOT
   </table>
{$main_content}
  <table width="600" border="0" cellpadding="0" cellspacing="0">
    <tr> 
      <td height="1" bgcolor="
EOT;
print BS_TAB_COLOR;
print <<< EOT
"><img width="1" height="1"></td>
    </tr>
    <tr> 
      <td><table width="100%" border="0" cellspacing="0" cellpadding="4">
          <tr> 
            <td align="center"><a href="about.php">About</a></td>
            <td align="center"><a href="faq.php">FAQ</a></td>
            <td align="center"><a href="abuse.php">Report Abuse</a></td>
            <td align="center"><a href="privacy.php">Privacy Policy</a></td>
            <td align="center"><a href="history.php">Logo History</a></td>
            <td align="center"><a href="mailto:
EOT;
print BS_CONTACT;
print <<< EOT
">Contact Us</a></td>
          </tr>
        </table>
<div align="center">
<span class="size">$bottom</span><br>
<span class="copyright">
EOT;
print BS_TEXT_COPYRIGHT;
print <<< EOT
<br><br><a href="http://branscan.sourceforge.net/">Powered by BranScan 
EOT;
print BS_VERSION;
print <<< EOT
</a><br></span></div>
</td></tr></table>
</div>
</body>
</html>
EOT;
}

function jump_to($offset, $limit, $num_rows_all, $link_args) {
    global $PHP_SELF;

    $offset_back = $offset - $limit;
    $offset_next = $offset + $limit;
    $num_pages = ceil($num_rows_all/$limit);

    if($num_pages > 1) {
        $current_page = $offset/$limit;
		#$jump_to = "Viewing page " . round($current_page+1,0) . " of $num_pages<br />";

        if($offset_back >= 0) {
            $jump_to.="<a href='$PHP_SELF?$link_args&offset=$offset_back'>&lt; Prev</a>&nbsp;&nbsp;";
        }
        for($page=0; $page<$num_pages; $page++) {
            if( ($current_page-5) < $page && ($current_page+5) > $page ) {
                $offset_page = $page * $limit;
                $page_num = $page+1;

                if(round($current_page, 0) == $page) {
                    $jump_to.="&nbsp;<b>$page_num</b>";
                } else {
                    $jump_to.="&nbsp;<a href='$PHP_SELF?$link_args&offset=$offset_page'>$page_num</a>";
                }
            }
        }
        if($offset_next <= $num_rows_all) {
            $jump_to.="&nbsp;&nbsp;&nbsp;<a href='$PHP_SELF?$link_args&offset=$offset_next'>Next &gt;</a></b>";
        }
    }
    return $jump_to;
}

?>
