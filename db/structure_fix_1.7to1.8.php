<?php
#########
# This script converts the tiki_structures table to use 
# page_id instead of pagename.
# It also adds a page_id column to the tiki_pages table.
#########

#########
# You must have applied tiki_1.7to1.8.sql or have checked out
# REL-1-8-RC1 before running the following commands:
#########
# $ mysql -f dbname <structure_fix_1.7to1.8notRC1.sql
#
# where dbname is the name of your tiki database.
#
# For example, if your tiki database is named tiki (not a bad
# choice), type:
#
# $ mysql -f tiki <structure_fix_1.7to1.8notRC1.sql


chdir("..");
include "tiki-setup.php";

$database = new TikiDB($dbTiki);

$query = "DROP TABLE IF EXISTS original_tiki_pages";
$result = $database->query($query);
$query = "rename table tiki_pages to original_tiki_pages";
$result = $database->query($query);

# cannot add additional primary key if one already exists!
$query  = "CREATE TABLE tiki_pages (";
$query .= "page_id int(14) NOT NULL auto_increment,";
$query .= "pageName varchar(160) NOT NULL default '',";
$query .= "hits int(8) default NULL,";
$query .= "data text,";
$query .= "description varchar(200) default NULL,";
$query .= "lastModif int(14) default NULL,";
$query .= "comment varchar(200) default NULL,";
$query .= "version int(8) NOT NULL default '0',";
$query .= "user varchar(200) default NULL,";
$query .= "ip varchar(15) default NULL,";
$query .= "flag char(1) default NULL,";
$query .= "points int(8) default NULL,";
$query .= "votes int(8) default NULL,";
$query .= "cache text,";
$query .= "wiki_cache int(10) default 0,";
$query .= "cache_timestamp int(14) default NULL,";
$query .= "pageRank decimal(4,3) default NULL,";
$query .= "creator varchar(200) default NULL,";
$query .= "page_size int(10) unsigned default 0,";
$query .= "PRIMARY KEY  (page_id),";
$query .= "UNIQUE KEY pageName (pageName),";
$query .= "KEY data (data(255)),";
$query .= "KEY pageRank (pageRank),";
$query .= "FULLTEXT KEY ft (pageName,description,data)";
$query .= ") TYPE=MyISAM AUTO_INCREMENT=1";
$result = $database->query($query);

$query = "insert into tiki_pages(pageName, hits, data, description, lastModif, ";
$query .= "comment, version, user, ip, flag, points, votes, cache, wiki_cache, ";
$query .= "cache_timestamp, pageRank, creator, page_size) ";
$query .= "select pageName, hits, data, description, lastModif, comment, ";
$query .= "version, user, ip, flag, points, votes, cache, wiki_cache, ";
$query .= "cache_timestamp, pageRank, creator, page_size from original_tiki_pages";
$result = $database->query($query);

$query = "DROP TABLE original_tiki_pages";
$result = $database->query($query);


# Save the old structures!
$query = "DROP TABLE IF EXISTS original_tiki_structures";
$result = $database->query($query);
$query = "rename table tiki_structures to original_tiki_structures";
$result = $database->query($query);

# Temporary structures table. No parent_id.
$query  = "CREATE TABLE temp_tiki_structures (";
$query .= "page_ref_id int(14) NOT NULL auto_increment, ";
$query .= "parent_id int(14) DEFAULT NULL, ";
$query .= "page_id int(14) NOT NULL, ";
$query .= "page_alias varchar(240) NOT NULL default '', ";
$query .= "pos int(4) default NULL, ";
$query .= "PRIMARY KEY  (page_ref_id), ";
$query .= "INDEX (page_id) ";
$query .= ") TYPE=MyISAM AUTO_INCREMENT=1";
$result = $database->query($query);

#Copy structure heads (parent == '')
$query  = "insert into temp_tiki_structures(page_id, pos) select ";
$query  .= "tp1.page_id,";
$query  .= "ts.pos ";
$query  .= "from original_tiki_structures ts, tiki_pages tp1 ";
$query  .= "where ts.page=tp1.pageName AND ts.parent=''";
$result = $database->query($query);

#Copy child nodes
#Cannot enter parent_id until table is populated, use non-null dummy
$query  = "insert into temp_tiki_structures(parent_id, page_id, pos) select ";
$query  .= "tp1.page_id,";
$query  .= "tp1.page_id,";
$query  .= "ts.pos ";
$query  .= "from original_tiki_structures  ts, tiki_pages  tp1, tiki_pages  tp2 ";
$query  .= "where ts.page=tp1.pageName AND ts.parent=tp2.pageName";
$result = $database->query($query);

#create a temporary table to hold parent/page relationship
$query  = "CREATE TABLE temp_parents (";
$query  .= "parent_id int(14) NOT NULL,";
$query  .= "page_id int(14) NOT NULL";
$query  .= ") TYPE=MyISAM";
$result = $database->query($query);

# Populate the temporary parents table. 
$query  = "insert into temp_parents(page_id, parent_id) select ";
$query  .= "ts_page.page_id, ts_parent.page_ref_id ";
$query  .= "from original_tiki_structures  ts_old, ";
$query  .= "   temp_tiki_structures  ts_page, ";
$query  .= "   temp_tiki_structures  ts_parent,";
$query  .= "   tiki_pages  tp_page, ";
$query  .= "   tiki_pages  tp_parent ";
$query  .= "where ts_old.page=tp_page.pageName AND ";
$query  .= "      ts_old.parent=tp_parent.pageName AND ";
$query  .= "      tp_page.page_id=ts_page.page_id AND ";
$query  .= "      tp_parent.page_id=ts_parent.page_id";
$result = $database->query($query);

# New structures table.
$query  = "CREATE TABLE tiki_structures (";
$query  .= "  page_ref_id int(14) NOT NULL auto_increment,";
$query  .= "  parent_id int(14) default NULL,";
$query  .= "  page_id int(14) NOT NULL,";
$query  .= "  page_alias varchar(240) NOT NULL default '',";
$query  .= "  pos int(4) default NULL,";
$query  .= "  PRIMARY KEY  (page_ref_id),";
$query  .= "  INDEX (page_id, parent_id)";
$query  .= ") TYPE=MyISAM AUTO_INCREMENT=1 ";
$result = $database->query($query);

#Copy structure heads (parent == '')
$query  = "insert into tiki_structures(page_id, page_alias, pos) select ";
$query  .= "ts.page_id,";
$query  .= "ts.page_alias,";
$query  .= "ts.pos ";
$query  .= "from temp_tiki_structures  ts ";
$query  .= "where ts.parent_id IS NULL";
$result = $database->query($query);

#Copy the rest of the structure elements aross(parent_id == non null) 
$query  = "insert into tiki_structures(parent_id, page_id, page_alias, pos) select ";
$query  .= "tp.parent_id,";
$query  .= "ts.page_id,";
$query  .= "ts.page_alias,";
$query  .= "ts.pos ";
$query  .= "from temp_tiki_structures  ts, temp_parents  tp ";
$query  .= "where ts.page_id=tp.page_id";
$result = $database->query($query);

$query  = "select page_id, parent_id from temp_parents";
$result = $database->query($query);
$parents = array();
$pages = array();
while ($res = $result->fetchRow()) {
  $pages[] = $page_id = $res["page_id"];
	$parents[$page_id] = $res["parent_id"];
}

$query = "UPDATE tiki_structures ";
$query .= "SET parent_id=? ";
$query .= "WHERE page_id=?";
foreach ( $pages as $page_id ) {
  $result = $database->query($query, array($parents[$page_id], $page_id));
}

$query  = "DROP TABLE original_tiki_structures";
$result = $database->query($query);
$query  = "DROP TABLE temp_tiki_structures";
$result = $database->query($query);
$query  = "DROP TABLE temp_parents";
$result = $database->query($query);

print("<H1>Structures Updated</H1>");

?>
