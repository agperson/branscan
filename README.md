```
o888888b.                             .d8888b.                            
888  "88b                           d88P  Y88b                            
888  .88P                           Y88b.                                 
8888888K.  888d888 8888b.  88888b.   "Y888b.    .d8888b  8888b.  88888b.  
888  "Y88b 888P"      "88b 888 "88b     "Y88b. d88P"        "88b 888 "88b 
888    888 888    .d888888 888  888       "888 888      .d888888 888  888 
888   d88P 888    888  888 888  888 Y88b  d88P Y88b.    888  888 888  888 
8888888P"  888    "Y888888 888  888  "Y8888P"   "Y8888P "Y888888 888  888 
```

Description
===========
BranScan is a SMB/CIFS network search solution written in PHP.
It requires at least MySQL 3.x and PHP 4.2 with libsmbclient compiled in.

BranScan is copyright (c) 2002-2003 Daniel Silverman <zeno@agblog.com>


Screenshots
===========
A static (non-functional) scrape of the Brandeis installation is viewable at: http://agperson.com/old/boogle


Directory Structure
===================
```
config.inc.php      All configuration options
subnets.inc.php     A list of subnets to scan
cron.tab            Sample crontab file
README              This file
INSTALL             Installation instructions
www/                Web accessible directory
sql/                MySQL schema
lib/                Scripts for scanning and crawling
```
