<?
define("BS_TITLE",    "BranScan");            # Web page title
define("BS_LOGO",   "branscan.gif");          # Logo image
define("BS_ORG_NAME", "School");              # Short name of school or organization
define("BS_SYS_NAME", "SchoolScan");          # Name of this search system
define("BS_CONTACT",  "user@domain.nic");     # Contact person's email address
define("BS_TAB_COLOR",  "#000033");           # Color for selected tabs (unselected are gray)
define("BS_BASE_PATH",  "/var/www/branscan"); # Path where this script is located
define("BS_MYSQL_HOST", "localhost");         # MySQL Hostname (usually should be localhost)
define("BS_MYSQL_USER", "");                  # MySQL Username
define("BS_MYSQL_PASS", "");                  # MySQL Password
define("BS_MYSQL_DB", "");                    # MySQL Database
define("BS_BASE_IP",  "192.168");             # Range of IPs to scan
define("BS_SCAN_HOURS", "12");                # Time before rescanning a computer (+-2)
define("BS_MAX_PROC", "5");                   # Maximum crawlers to run simultaneously
define("BS_MAX_DEPTH",  "6");                 # Maximum depth to scan (to avoid infinite recursion
define("BS_FPP",    "50");                    # Number of files to display per results page
define("BS_MAX_DL",   "100");                 # Max download size in MB for alternate download link
define("BS_NO_ALT",   "0");                   # Disable alternate download link
define("BS_SHARE_NAG",  "1");                 # Nag users if they aren't sharing (0 for off)
define("BS_VERSION",  "2.0");                 # Version number.  Best not to change this
define("BS_NO_SEARCH",  "0");                 # Disable search functionality (0 for off)
define("BS_MYSQL_4",  "1");                   # Uses MySQL 4 Boolean Mode search

# The types array is used to categorize files.  Feel free to modify as you wish.
# If a file extension is not one of these, the file will not be indexed.  Extensions can be 2 or 3 characters.
$types = array(
    'Video' => array('divx', 'dvx', 'mov', 'mpg', 'mpe', 'avi', 'wmv', 'rm', 'ram', 'vfw', 'swf', 'asf', 'mp4', 'mpeg', 'qt'),
  'Audio' => array('mp3', 'snd', 'asx', 'wav', 'mid', 'wma', 'mp2', 'ra', 'ram', 'ogg'),
  'Documents' => array('txt', 'rtf', 'doc', 'htm', 'jpg', 'bmp', 'gif', 'png', 'psd', 'pdf', 'swf', 'ps', 'tex', 'xls', 'ppt'),
  'Programs' => array('exe', 'app', 'pkg'),
  'Compressed' => array('zip', 'bin', 'sit', 'dmg', 'iso', 'bz2', 'gz', 'tar', 'hqx', 'tgz')
);

# Copyright notice appears at the bottom of every page.  It can also be a liablity statement
define("BS_TEXT_COPYRIGHT", "Please do not steal files.");

# Virus alert appears on the homepage above the search box
define("BS_TEXT_VIRUS_ALERT", "");

# Announcements appears on the homepage under its own banner if it is set.
define("BS_TEXT_ANNOUNCEMENTS", "");

# You can ignore this
$link_id = @mysql_connect(BS_MYSQL_HOST, BS_MYSQL_USER, BS_MYSQL_PASS)
  or die("Could not connect to MySQL server.");
mysql_select_db(BS_MYSQL_DB, $link_id)
  or die("Could not connect to MySQL database.");
?>
