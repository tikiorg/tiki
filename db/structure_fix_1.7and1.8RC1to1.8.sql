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


#############
# *****DO NOT**** execute this command more than once!
#
# If you do, you will loose your existing structure data.
#############

# cannot add additional primary key if one already exists!
alter table tiki_pages drop primary key;
# add a page_id column
alter table tiki_pages add column page_id int(14) not null auto_increment primary key first;
# add a key back in for pageName.
alter table tiki_pages add unique (pageName);
# add a search key for description
alter table tiki_pages add fulltext (description);

# Save the old structures!
rename table tiki_structures to original_tiki_structures;

# Temporary structures table. No parent_id.
CREATE TABLE temp_tiki_structures (
  page_ref_id int(14) NOT NULL auto_increment,
  parent_id int(14) DEFAULT NULL,
  page_id int(14) NOT NULL,
  page_alias varchar(240) NOT NULL default '',
  pos int(4) default NULL,
  PRIMARY KEY  (page_ref_id),
  INDEX (page_id)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

#Copy structure heads (parent == '')
insert into temp_tiki_structures(page_id, page_alias, pos) select 
tp1.page_id,
ts.page_alias,
ts.pos
from original_tiki_structures AS ts, tiki_pages AS tp1
where ts.page=tp1.pageName AND ts.parent='';

#Copy child nodes
#Cannot enter parent_id until table is populated, use non-null dummy
insert into temp_tiki_structures(parent_id, page_id, page_alias, pos) select 
tp1.page_id,
tp1.page_id,
ts.page_alias,
ts.pos
from original_tiki_structures AS ts, tiki_pages AS tp1, tiki_pages AS tp2
where ts.page=tp1.pageName AND ts.parent=tp2.pageName;

#create a temporary table to hold parent/page relationship
CREATE TABLE temp_parents (
  parent_id int(14) NOT NULL,
  page_id int(14) NOT NULL
) TYPE=MyISAM;

# Populate the temporary parents table. 
insert into temp_parents(page_id, parent_id) select 
ts_page.page_id, ts_parent.page_ref_id
from original_tiki_structures AS ts_old, 
   temp_tiki_structures AS ts_page, 
   temp_tiki_structures AS ts_parent,
   tiki_pages AS tp_page, 
   tiki_pages AS tp_parent
where ts_old.page=tp_page.pageName AND 
      ts_old.parent=tp_parent.pageName AND
      tp_page.page_id=ts_page.page_id AND 
      tp_parent.page_id=ts_parent.page_id;

# New structures table.
CREATE TABLE tiki_structures (
  page_ref_id int(14) NOT NULL auto_increment,
  parent_id int(14) default NULL,
  page_id int(14) NOT NULL,
  page_alias varchar(240) NOT NULL default '',
  pos int(4) default NULL,
  PRIMARY KEY  (page_ref_id),
  INDEX (page_id, parent_id)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

#Copy structure heads (parent == '')
insert into tiki_structures(page_id, page_alias, pos) select 
ts.page_id,
ts.page_alias,
ts.pos
from temp_tiki_structures AS ts
where ts.parent_id IS NULL;

#Copy the rest of the structure elements aross(parent_id == non null) 
insert into tiki_structures(parent_id, page_id, page_alias, pos) select 
tp.parent_id,
ts.page_id,
ts.page_alias,
ts.pos
from temp_tiki_structures AS ts, temp_parents AS tp
where ts.page_id=tp.page_id;

UPDATE tiki_structures AS ts, temp_parents AS tp 
SET ts.parent_id=tp.parent_id 
WHERE ts.page_id=tp.page_id;

DROP TABLE original_tiki_structures;
DROP TABLE temp_tiki_structures;
DROP TABLE temp_parents;

