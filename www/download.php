<?php
include("../config.inc.php");
include("../includes/display.inc.php");
$file_id = $_GET['file'];
if($file_id == "" || !is_numeric($file_id)) {
	$stuff = print_title("Download Error");
	$stuff .= "No file ID was specified or the specified ID was non-numeric.  What, are you trying to hack us?";
	return_page("Other", $stuff);
	return;
}

if($query = mysql_query("SELECT path, file_name, file_size FROM files WHERE id='$file_id'", $link_id)) {
	if(mysql_num_rows($query) == 1) {
		$info = mysql_fetch_array($query);
		if($info['file_size']/1024/1024 <= BS_MAX_DL) {
			$name = $info['file_name'];
			$size = $info['file_size'];
			$path = "smb:" . $info['path'] . "/" . $info['file_name'];
		} else {
			$stuff = print_title("Download Error");
			$stuff .= "The size of this file is larger then the allowable download size (" . BS_MAX_DL . ").  The system wouldn't give you this link intentionally, we suspect foul play...don't do it again";
			return_page("Other", $stuff);
			return;
		}
	} else {
		$stuff = print_title("Download Error");
		$stuff .= "The file ID specified was not found in our database or is not unique.  Either way, unless you were doing something bad, its probably a server problem and you might want to tell the server admin.";
		return_page("Other", $stuff);
		return;
	}
} else {
	$stuff = print_title("Download Error");
	$stuff .= "The file ID specified was not found in our database or is not unique.  Either way, unless you were doing something bad, its probably a server problem and you might want to tell the server admin.";
	return_page("Other", $stuff);
	return;
}

# Try to make sure Mozilla (easy) and IE (hard) know to dowload this file
header("Content-type: application/force-download");
header("Content-Length: $size");
header("Content-Disposition: attachment; filename=$name");
header("Cache-control: private");

$fh = smbclient_open($path);
while($str = smbclient_read($fh, 4096)) {
	echo $str;
}
smbclient_close($fh);
?>
