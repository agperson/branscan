<?
include("../includes/display.inc.php");
$sys_name = BS_SYS_NAME;

global $org_name, $contact_email;
$content = print_title("Report Network Abuse");
$content .= "<p>$sys_name is a legitimate search service meant to allow
students at $org_name University to quickly find files on their fellow
students' computers.  Any activity that violates national, state, and/or local laws is strictly prohibited on this system.
<p>$sys_name is content-agnostic, meaning that the system neither knows
nor cares what files it is scanning.  There is no known way of
differentiated between copyrighted files and non-copyrighted ones,
explicit or obscene files and non-obscene, illegal or legal.  $sys_name simply acts as a catalogue.
<p>We at $sys_name strive to take quick action in responding to any allegations of abuse.  We will immediately remove from our index any files reported to us as illegally listed.  We will not, and cannot, force a student to stop sharing such a file.  That is the responsibility of the individual computer host.
<p>Note that <b>while we will remove a file or a host from our index, the automated searching system that $sys_name uses WILL re-catalogue the offending host or file on its next scan if the actual file or host is not removed from the network</b>.  We encourage you to contact the owner of the file to resolve the problem as quickly and effectively as possible.
<p>If you see a file that you believe is in violation of these rules, please email <a href=\"mailto:" . BS_CONTACT . "\">" . BS_CONTACT . "</a>.
<p>You should immediately report the offending host and file to the University's DMCA agent, at 
<a href=\"mailto:copyright@brandeis.edu\">copyright@brandeis.edu</a>.  This will ensure that the file is removed promptly and is not re-indexed by $sys_name.";

return_page("Other",$content);
?>
