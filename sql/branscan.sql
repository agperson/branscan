CREATE TABLE files (
  id int(10) unsigned NOT NULL auto_increment,
  host_id int(6) NOT NULL default '0',
  path text NOT NULL,
  file_name varchar(255) NOT NULL default '',
  file_size int(11) NOT NULL default '0',
  file_type char(3) default NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY id (id),
  KEY host_id (host_id),
  FULLTEXT KEY file_name (file_name),
  FULLTEXT KEY path (path),
  KEY file_type (file_type)
) TYPE=MyISAM;

CREATE TABLE hosts (
  id int(6) unsigned NOT NULL auto_increment,
  name varchar(40) default NULL,
  ip varchar(15) default NULL,
  lastscan datetime NOT NULL default '2002-02-02 02:02:02',
  files int(6) default NULL,
  size bigint(20) default NULL,
  isup int(1) default '0',
  KEY id (id)
) TYPE=InnoDB;

CREATE TABLE search_terms (
  term varchar(255) default NULL,
  hits int(10) default NULL,
  last datetime default NULL,
  first datetime default NULL
) TYPE=InnoDB;

CREATE TABLE users (
  ip varchar(15) NOT NULL default '',
  firstvisit datetime default NULL,
  lastvisit datetime default NULL,
  hits_today int(4) default NULL,
  hits_total int(10) default NULL
) TYPE=InnoDB;
