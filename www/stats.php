<?
require("../includes/display.inc.php");

# Array is title, file
$stats = array(
	"Computers On Scan List" => "hosts-total.txt",
	"Computers Sharing" => "hosts-sharing.txt",
	"Total Page Views" => "visitors-total.txt",
	"Unique Visitors" => "visitors-unique.txt",
	"Total Search Queries" => "search-total.txt",
	"Unique Search Queries" => "search-unique.txt",
	"Files Currently Indexed" => "files.txt",
	"Size of Data Indexed (in MB)" => "size-mb.txt"
);

function read_stat($file) {
	$statfile = fopen("../stats/" . $file, r);
	$number = fread($statfile, 1024);
	return $number;
}

$content = print_title("Site Statistics");
$content .= "<table width=100% cellpadding=\"3\" cellspacing=\"0\" border=\"0\">";
$i = 0;
foreach($stats as $key => $value) {
	$i++;
	$color = !($i % 2) ? "#DDDDDD" : "#FFFFFF";
	$num = read_stat($value);
	$content .= "<tr bgcolor=\"$color\"><td width=\"80%\">$key</td><td width=\"20%\" align=\"right\" nobwrap=\"nowrap\"><b>$num</b></td></tr>";
}
$content .= "</table>";

return_page("Stats",$content);
?>
