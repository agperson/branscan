<?
include("../includes/display.inc.php");
$sys_name = BS_SYS_NAME;

$content = print_title("Frequently Asked Questions");
$content .= "<ul>
<li> <a href=#1>What is $sys_name and how does it work?</a>
<li> <a href=#2>How can I get the best search results?</a>
<li> <a href=#3>Is $sys_name legal?</a>
<li> <a href=#4>Why are you scanning my computer?</a>
<li> <a href=#5>What if I don't want you to scan my computer?</a>
<li> <a href=#6>I added a robots.txt file and/or turned off open shares.
Why are you still scanning me?</a>
<li> <a href=#7>Can I use $sys_name to access the {$org_name} network from
off-campus?</a>
<li> <a href=#8>What hardware/software does $sys_name use?</a>
<li> <a href=#9>Where can I go for more information?</a>
</ul><hr>";
$content .= "<a name=1></a><b>What is $sys_name and how does it work?</b>
<br>$sys_name is a network file search tool.  $sys_name runs continuously searching for new computers, indexing found computers, and checking computer reliability.  $sys_name uses a database to allow for sophisticated search queries and relevency rankings.  For more information on $sys_name, visit the <a href=\"about.php\">about page</a>.<br><br>
<a name=2></a><b>How can I get the best search results?</b>
<br>Do the right type of query for your search!
<ul><li>To search for a path, use the keyword \"path: \"
<li>For a host, use \"host: \"
<li>To perform a \"basic\" search, use \"basic: \"
</ul>You can use any of these keywords by prepending them to your search terms.  You can use only one keyword at a time.  For example, to find the computer MUNCHKIN, type <tt>host: munchkin</tt>, and to find a
file like \"Chili Pepper\" type <tt>basic: chili pepper</tt> (this will 
put Red Hot Chili Pepper<b>s</b> above Chili Recipe, instead of the other way
around).
Using these keywords will help $sys_name to search more efficiently 
and display the best results.<br><br>
<a name=3></a><b>Is $sys_name legal?</b>
<br>$sys_name is provided as a free service to the " . BS_ORG_NAME . " community.  It is only accessible from on-campus.  Like the search engine <a href=\"http://www.google.com\">Google</a>, this system simply acts as a \"spider\" searching for files.  Like Google, our systemis content-agnostic, meaning that it does not and cannot understand or read the files that it is cataloguing.  Therefore, $sys_name simply acts as a search tool and does not store or distribute any files on its own.  $sys_name was created for the legitimate sharing of non-infrining works, and it has substantial non-infringing uses through its ability to allow users to find legal and freely shared files.  As ruled in the <i>Betamax</i> case before the US Supreme Court, a system with substantial non-infringing uses is not infringing.  While anyone <b>could</b> use an audio tape to copy a copyrighted CD, there are also many other uses for audio tapes, such as recording your own speech or music.  In this way, $sys_name itself is not illegal, although some files that $sys_name unknowingly indexes may violate certain laws.  We work to quickly take down any files that have been brought to our attention as in any way illegal, as detailed on our <a href=\"abuse.php\">abuse policy</a> page.<br><br>
<a name=4></a><b>Why are you scanning my computer?</b>
<br>$sys_name performs a periodic network service scan in order to determine which computers are running the SMB service.  These scans are non-invasive, occur once daily, and can be safely ignored.
<p>$sys_name conducts daily sweeps of select computers to check for new or changed files.  $sys_name connects to your computer, gets a list of shares, and then loops through each share, scanning for files.  This task is invasive but should not seriously degrade your computer's performance.  Your computer should be scanned no more than twice daily, and most scans take place in less than 20 seconds.<br><br>
<a name=5></a><b>What if I don't want you to scan my computer?</b>
<br>$sys_name only scans public (non-password protected) directories.  If you password-protect your share, it cannot be read by the public and, thus, $sys_name will not see it.  Additionally, $sys_name follows a simple version of the Robot Exclusion Protocol.  It first fetches a file called <tt>robots.txt</tt> before commencing its scan.  If this file exists at the base level of a share, $sys_name will not index that share.  Simply place an empty text file or any other file with the name <tt>robots.txt</tt> at the top of your share point in order to avoid a scan.<br><br>
<a name=6></a><b>I added a robots.txt file and/or turned off open
shares.  Why are you still scanning me?</b>
<br>$sys_name will continue to perform non-invasive daily scans on all computers within the local network.  $sys_name will continue to look for open shares on any computer with SMB enabled.  This is to check for new shares and to find shares that may no longer exist.  $sys_name will <b>never</b> attempt to read or break into password-protected directories.  $sys_name will attempt to scan any public directory but will stop immediately if it finds a robots.txt file.  These scans for open shares usually take less than three seconds and should have no impact on computer performance.<br><br>
<a name=7></a><b>Can I use $sys_name to access the {$org_name} network from
off-campus?</b>
<br>NO!  A \"local network\" is called such because it is LOCAL.  $sys_name is restricted by IP so that only users on the local network can access it.  There is no way to access $sys_name from off-campus, and using a seperate VPN or other tunneling system to access the network may violate acceptable use policies for this network.  Contact ITS before doing anything rash.  It would probably just be easier to bring your computer on campus and plug in for a few hours.<br><br>
<a name=8></a><b>What hardware/software does $sys_name use?</b>
<br>This installation of $sys_name is running on a dedicated server with dual Pentium II/300 processors.  This system, which is too old and slow to run the latest Microsoft bloatware, works just fine for this kind of service.  $sys_name is written in PHP and uses a MySQL database for storage.  It is run through Apache under the GNU/Linux operating system.<br><br>
<a name=9></a><b>Where can I go for more information?</b>
<br>For more information, contact this site's operator directly at <a
href=\"mailto:" . BS_CONTACT . "\">" . BS_CONTACT . "</a>.";
return_page("Other",$content);
?>
