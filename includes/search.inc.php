<?php
include("../config.inc.php");
global $types;

# search.inc.php
# Index page search functions

# is_user_sharing()				If the remote IP matches a server in the list, returns 1
# pathhtml($path)				Returns a properly formatted and linked file path
# clean_search($query)			Formats the string for SQL
# type_sql($type)				Returns file_type SQL
# query_sql($query, $keyword)	Returns query SQL based on search type
# update_search_terms($term)	Catalogs term as appropriate

function is_user_sharing() {
	global $link_id;
	$ip = $_SERVER["REMOTE_ADDR"];
	$sql = "select hosts.id from hosts where ip='$ip'
			and files > 0 and size > 0";
	$result = mysql_query($sql, $link_id);
	if(mysql_numrows($result))
		return 1;
	return 0;
}

function path2html($path, $format)
{
	// Create an array named $paths which stores all of the paths
	$paths = split("/",$path);
	
	$levels = sizeof($paths);
	
	$seperator = "/";
	if($format == "windows")
		$link = $fileloc = $seperator = "\\";
	else
		$link = $fileloc = "smb:/";
	
	for ( $i = 0; $i < $levels; $i++) {
			$fileloc = $fileloc . $seperator . $paths[$i];
			$link =$link . $seperator . "<a href = \"$fileloc\">$paths[$i]</a>";
	}
	
	return $link;
}

function clean_search($query) {
	$search_clean = urldecode($query);
	
	# Replace invalid characters \ / * ? " < > | with whitespace
	$search_clean = ereg_replace("[\\/\*\?\"><\|\(\)]", ' ', $search_clean);
	
	# Clear up any extra whitespace we added
	$search_clean = ereg_replace("[[:space:]]+", ' ', trim($search_clean));
	
	return $search_clean;
}

function type_sql($type) {
	global $types;
	if($type == "All" || $type == "")
		return;
	$type_array = $types[$type];
	$type_sql = " AND file_type IN (";
	foreach($type_array as $onetype)
		$type_sql .= "'$onetype', ";
	$type_sql = substr($type_sql, 0, -2);
	$type_sql .= ")";
	return $type_sql;
}

function query_sql($query, $keyword, $type) {
	$type_sql = type_sql($type);
	if(BS_MYSQL_4 == "1")
		$mode = " IN BOOLEAN MODE";
	if($keyword == "basic" || $keyword == "host")
		$query = '%'.eregi_replace('[[:space:]]+', '%', $query).'%';
	switch($keyword) {
		case 'basic':
			$query_sql = "select files.id as fid, ip,
path, file_size, file_name FROM files, hosts h WHERE file_name LIKE '$query' AND isup = 1 AND h.id = host_id$type_sql";
			break;
		case 'host':
			$query_sql = "select files.id as fid, ip, path, file_size, file_name FROM files, hosts h WHERE h.name LIKE '$query' AND isup = 1 AND h.id = host_id$type_sql";
			break;
		case 'path':
			$query_sql = "select distinct(h.id), ip, path, MATCH (path) AGAINST ('$query'$mode) AS score FROM hosts h, files WHERE MATCH (path) AGAINST ('$query'$mode) AND isup = 1 AND h.id = host_id";
			break;
		default:
			$query_sql = "select files.id as fid, ip,
path, file_size, file_name, MATCH (file_name) AGAINST ('$query'$mode) AS score FROM files, hosts h WHERE MATCH (file_name) AGAINST ('$query'$mode) AND isup = 1 AND h.id = host_id$type_sql";
			break;
		}
	return $query_sql;
}
	
function update_search_terms($query) {
	global $link_id;
	$sql = "SELECT hits FROM search_terms WHERE term='$query'";
	$log_result = mysql_query($sql, $link_id);
	if($log_result)
		$log_num_rows = mysql_num_rows($log_result);
	if($log_num_rows > 0) {
		$log_row = mysql_fetch_array($log_result);
		$log_hits = $log_row['hits'];
		$log_hits++;
		$sql = "UPDATE search_terms SET hits=$log_hits, last=NOW() WHERE term='$query' AND NOW() - DATE_ADD(last, INTERVAL 1 MINUTE) > 0";
		$result = mysql_query($sql, $link_id);
	} else {
		$sql = "INSERT INTO search_terms (term, hits, last, first) VALUES('$query', '1', NOW(), NOW())";
		$result = mysql_query($sql, $link_id);
	}
}
?>
