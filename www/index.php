<?php
require("../config.inc.php");
require("../includes/search.inc.php");
require("../includes/display.inc.php");

global $link_id;

if(!isset($_GET['search']) || BS_NO_SEARCH) {
	return_page("Top","");
} else {
	$time_start = getmicrotime();
	$offset	= $_GET['offset'];
	$query  = trim(stripslashes($_GET['search']));
	$order 	= $_GET['order'];
	$type	= $_GET['type'];
	$format = $_GET['format'];
	$keyword = $_GET['keyword'];
	$total_results = $_GET['total_results'];
	
	# If no format is selected, guess based on USER_AGENT
	if(!$format) {
		if(eregi("Win", $_SERVER["HTTP_USER_AGENT"]))
			$format = "windows";
		else
			$format = "linux";
	}
	
	# Zero our offset
	$offset = $offset ? $offset : 0;

	# Strip bad characters from the query
	#$query = clean_search($search);

	# Redirect to hosts page if necessary (kinda silly, but who knows...)
	if($query == "Indexed Hosts") {
		header("Location: hosts.php");
		return;
	}
	# Check our search term(s) for keywords
	if(!isset($keyword)) {
		if(strlen($query) < 2) {
			$keyword = 'basic';
		} elseif(eregi("basic: ", $query)) {
			$keyword = 'basic';
			$query = substr($query, 7);
		} elseif(eregi("host: ", $query)) {
			$keyword = 'host';
			$query = substr($query, 6);
		} elseif(eregi("path: ", $query)) {
			$keyword = 'path';
			$query = substr($query, 6);
		} else {
			$keyword = '';
		}
	} elseif ($keyword != 'basic' && $keyword != 'host' && $keyword != 'path') {
		$keyword = "";
	}
	
	# Get the full query for the search log page
	if($keyword) {
		$search_with_keywords = htmlentities($keyword.': '.$query);
	} else {
		$search_with_keywords = htmlentities($query);
	}
	
	# Yell at people for using basic badly
	if(strlen($query) < 4 && $keyword == "basic") {
		$returned_stuff = "<table width=100% border=0 bgcolor=" . BS_TAB_COLOR . ">
<tr><td class=\"search\">Searched for: <b>$query</b></td>
<td align=right class=\"search\">No Results Returned</td></tr></table><br>
<div align=left><span class=search><font color=red><b>YOUR SEARCH WAS WAY TOO SHORT!</b></font><br><font color=black>You've performed a basic search for a phrase of three letters or less.  This type of search puts a huge load on the server and generally returns very few relevent results. In order to allow maximum server availability and speed, we have disabled basic searches for less than 4 letters.  Try using normal fulltext search.  If you are searching for a specific file type, please use the type dropdown instead.</font></div><br>";
		return_page("Search",$returned_stuff);
		return;
	}
	
	# Get the properly-formatted SQL based on type
	$query_sql = query_sql(mysql_escape_string($query), $keyword, $type);

	# Append our ordering method
	switch($order) {
       	case 'file':
           	if($keyword == "path")
				$query_sql .= " ORDER BY path ASC";
			else
				$query_sql .= " ORDER BY file_name ASC, h.name ASC";
           	$order_by = 'File Name';
           	break;
		case 'path':
			$query_sql .= " ORDER BY path ASC";
			if($keyword != "path")
				$query_sql .= ", file_name ASC";
			break;
       	case 'size':
           	$query_sql .= " ORDER BY file_size DESC, file_name DESC";
           	$order_by = 'File Size';
           	break;
       	default:
           	$order_by = 'Best match';
			if($keyword == "")
				$query_sql .= " ORDER BY score DESC";
			break;
   	}
	
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

	# We could always quit while we're ahead
	if($num_results == 0) {
		$query_to_search_again = clean_search($query);
		$returned_stuff = "<table width=100% border=0 bgcolor=" . BS_TAB_COLOR . ">
	<tr><td class=\"search\">Searched for: <b>$query_to_search_again</b></td>
	<td align=right class=\"search\">No Results Found</td></tr></table><br>
	<div align=left><span class=search><font color=red><b>YOUR QUERY RETURNED NO RESULTS</b></font>
	<font color=black><li> You're searching for something we simply don't have.
	<li> You're searching for a host name (You should be searching for <tt><a href=\"$PHP_SELF?search=host: $query_to_search_again\">host: $query_to_search_again</a></tt>)
	<li> You're searching for a path (You should be searching for <tt><a href=\"$PHP_SELF?search=path: $query_to_search_again\">path: $query_to_search_again</a></tt>)
	<li> You're using small words. (You should be searching for <tt><a href=\"$PHP_SELF?search=basic: $query_to_search_again\">basic: $query_to_search_again</a></tt>)<br>
	<br>Why might this have happened? " . BS_SYS_NAME . " uses a fulltext search system that looks for word relationships.  Fulltext does not take into account any words under four characters.  If your search is composed of many small words, you can perform a much more intense (and slow) search using the " . BS_SYS_NAME . " basic search algorithm.  Alternately, your search might need some extra letters.  The search engine isn't smart enough to use stemming, i.e. to realize that Foo Fighter might be similar to Foo Fighter<b>s</b>.  You might want to try appending an \"s\" or other ending letter.</font></span></div><br>";
		return_page("Search",$returned_stuff);
		return;
	}
	
	# Enter search into search_terms table (assuming it returned results)
	if (!$offset && $num_results > 2)
		update_search_terms($search_with_keywords);

	# Create the jump_to HTML
	$search_enc = urlencode($query);
	$jump_to = jump_to($offset, BS_FPP, $total_results, "search=$search_enc&keyword=$keyword&format=$format&total_results=$total_results&order=$order&type=$type");

	# Figure out where we are in the results
	$first = $offset+1;
	$last = ($offset+BS_FPP > $total_results) ? $total_results : $offset+BS_FPP;
	
	# The next loop needs to know how far to go
	$files_to_show = $last - $offset;
	
	# Give us the sort bar
	$content .= "<tr bgcolor=\"#BBBBBB\" height=\"22\">";
	if($keyword == "" || $keyword == "path")
		$content .= "<td width=\"50\" nowrap=\"nowrap\"><b><a href=\"$PHP_SELF?search=$search_enc&keyword=$keyword&format=$format&total_results=$total_results&order=best&type=$type\" class=\"sortterms\">Relevance</a></b></td>";
	$content .= "<td width=\"96%\"><b><a href=\"$PHP_SELF?search=$search_enc&keyword=$keyword&format=$format&total_results=$total_results&order=path&type=$type\" class=\"sortterms\">Path</a>&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;<a href=\"$PHP_SELF?search=$search_enc&keyword=$keyword&format=$format&total_results=$total_results&order=file&type=$type\" class=\"sortterms\">File Name</a></b></td>";
	if($format == "linux")
		$content .= "<td align=\"center\" width=\"40\" nowrap=\"nowrap\"><b>IP Address</b></td>";
	if($keyword != "path" && $format == "linux" && BS_NO_ALT == 0)
		$content .= "<td align=\"center\" width=30 nowrap=\"nowrap\"><b>Alt</b></td>";
	if($keyword != "path")
		$content .= "<td align=\"center\" width=20 nowrap=\"nowrap\"><b><a href=\"$PHP_SELF?search=$search_enc&keyword=$keyword&format=$format&total_results=$total_results&order=size&type=$type\" class=\"sortterms\">Size</a></b></td>";
	$content .= "</tr>";

	for($row_num = 0; $row_num < $files_to_show; $row_num++) {
		$file = mysql_fetch_array($results);
		$file_id = $file['fid'];
		$ip = $file['ip'];
		$name = $file['file_name'];
		$path = $file['path'];
		$score = $file['score'];
		$avg = $file['avg']*100;
		$size = round($file['file_size']/1024/1024, 2);
		
		$search_strings = split("[[:space:]]+", clean_search($query));
		$name_formatted = $name;
		# This doesn't work well with boolean operators
		for($i = 0; $i < count($search_strings); $i++) {
        	# if(substr_count($sofar, $search_strings[$i])) continue;
			# $sofar .= " " . $search_strings[$i];
			$name_formatted = eregi_replace("($search_strings[$i])", "<b>\\1</b>", $name_formatted);
		}

		$bgcolor = ($row_num % 2) ? "#DDDDDD" : "#FFFFFF";
		$negimg = ($row_num % 2) ? "neg.gif" : "neg-g.gif";
		$content .= "<tr bgcolor=\"$bgcolor\" height=\"22\">";
			if($score) {
				$pos = round($score*10,0);
				$pos = ($pos > 45) ? 45 : $pos;
				$neg = 45 - $pos;
				$content .= "<td align=\"center\" width=\"50\" height=\"22\" nowrap=\"nowrap\"><img src=\"pos.gif\" width=\"$pos\" height=\"4\" align=\"absmiddle\"><img src=\"$negimg\" width=\"$neg\" height=\"4\" align=\"absmiddle\"></td>\n";
			}


#		$returned_stuff = ereg_replace("\\\\\\\\", "smb://", $returned_stuff);
#		$returned_stuff = ereg_replace("\\\\", "/", $returned_stuff);
		$filepath = ($format == "windows") ? ereg_replace("/", "\\", $path) : "smb://" . $path;
		$namelink = ($format == "windows") ? "\\<a href=\"\\\\$filepath\\$name\">$name_formatted</a>" : "/<a href=\"$filepath/$name\">$name_formatted</a>";
		$content .= "<td>" . path2html($path, $format) . "$namelink</td>";
		if($format == "linux")
			$content .= "<td nowrap=\"nowrap\"><a href=\"smb://$ip\">$ip</a></td>";
		if($keyword != "path") {
			if($format == "linux" && BS_NO_ALT == 0) {
				if($size < BS_MAX_DL)
					$content .= "<td nowrap=\"nowrap\" align=\"center\"><a href=\"download.php?file=$file_id\">Get</a></td>";
				else
					$content .= "<td nowrap=\"nowrap\" align=\"center\">-</td>";
			}
			$content .= "<td nowrap=\"nowrap\" align=\"right\">$size MB</td>";
		}
		$content .= "</tr>";
	}	
	$content .= "</table>\n";
	$content .= "<table width=600 cellpadding=6 align=center><tr><td>";
	$content .= $jump_to ? "<table align=\"center\" width=\"350\" border=\"0\" cellspacing=\"1\" cellpadding=\"6\" bgcolor=\"" . BS_TAB_COLOR . "\" nowrap-\"nowrap\"><tr><td bgcolor=\"#DDDDDD\" align=\"center\">$jump_to</td></tr></table>" : "";

	if($offset == 0 && $keyword != "path" && $keyword != "host") {
		$path_sql = "select distinct(h.id), h.name as name, path, MATCH (path) AGAINST ('$query' IN BOOLEAN MODE) AS score FROM hosts h, files WHERE MATCH (path) AGAINST ('$query') AND h.id = host_id GROUP BY host_id ORDER BY score DESC LIMIT 6";
	
	$path_result = mysql_query($path_sql, $link_id);
		if($path_result) {
			while($prow = mysql_fetch_array($path_result)) {
				$pbold = path2html($prow['path'], $format);
            	# Right now this breaks the links
				# for($i=0; $i<count($search_strings); $i++)
            	#	$pbold = eregi_replace("($search_strings[$i])", "<b>\\1</b>", $pbold);
				$path_formatted[] = $pbold;
			}
		}
		for($i=0; $path_formatted[$i]; $i++) {
			if (!($i % 2))
				$path_html .= "</tr><tr>";
			else
				$path_html .= "<td>&nbsp;</td>\n";

			$path_html .= "<td class=\"results\" valign=top>{$path_formatted[$i]}</td>\n";
		}
	}

	$time_stop = getmicrotime();
	$time_query = $time_stop - $time_start;
		
	$returned_stuff = "<table width=100% border=0 bgcolor=" . BS_TAB_COLOR . ">
	<tr><td class=\"search\">Searched for: <b>$query</b></td>
	<td align=right class=\"search\">Results " . ($first) . " - " . ($last) . " of $total_results (<i>" . round($time_query, 3) . " seconds</i>)</td></tr></table>";
	if($path_html && $path_html != "")
		$returned_stuff .= "<div align=\"left\"><table border=0>
		<tr><td colspan=3 class=\"results\"><b>Here are some file paths that also match your search.  You may also <a href=\"$PHP_SELF?search=$search_enc&keyword=path&format=$format\" class=\"sortterms\">search by path</a>.</b></td></tr>
		$path_html</tr></table></div>";
	$returned_stuff .= "<table width=100% cellspacing=0 cellpadding=4>$content</table>";

	# It is nice to take care of this all in one place...not sure on the performance hit though.
#	if($format != "windows") {
#		$returned_stuff = ereg_replace("\\\\\\\\", "smb://", $returned_stuff);
#		$returned_stuff = ereg_replace("\\\\", "/", $returned_stuff);
#	}

	# All done!
	return_page("Search",$returned_stuff);
}

?>
