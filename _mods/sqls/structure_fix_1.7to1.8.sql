DROP TABLE IF EXISTS original_tiki_pages;

rename table tiki_pages to original_tiki_pages;

CREATE TABLE tiki_pages (
  page_id int(14) NOT NULL auto_increment,
  pageName varchar(160) NOT NULL default '',
  hits int(8) default NULL,
  data text,
  description varchar(200) default NULL,
  lastModif int(14) default NULL,
  comment varchar(200) default NULL,
  version int(8) NOT NULL default '0',
  user varchar(200) default NULL,
  ip varchar(15) default NULL,
  flag char(1) default NULL,
  points int(8) default NULL,
  votes int(8) default NULL,
  cache text,
  wiki_cache int(10) default 0,
  cache_timestamp int(14) default NULL,
  pageRank decimal(4,3) default NULL,
  creator varchar(200) default NULL,
  page_size int(10) unsigned default 0,
  PRIMARY KEY  (page_id),
  UNIQUE KEY pageName (pageName),
  KEY data (data(255)),
  KEY pageRank (pageRank),
  FULLTEXT KEY ft (pageName,description,data)
) TYPE=MyISAM AUTO_INCREMENT=1;



insert into tiki_pages(pageName, hits, data, description, lastModif,
  comment, version, user, ip, flag, points, votes, cache, wiki_cache,
  cache_timestamp, pageRank, creator, page_size)
    select pageName, hits, data, description, lastModif, comment,
      version, user, ip, flag, points, votes, cache, wiki_cache,
      cache_timestamp, pageRank, creator, page_size from original_tiki_pages;

DROP TABLE original_tiki_pages;

DROP TABLE IF EXISTS original_tiki_structures;

rename table tiki_structures to original_tiki_structures;

CREATE TABLE tiki_structures (
  page_ref_id int(14) NOT NULL auto_increment,
  parent_id int(14) default NULL,
  page_id int(14) NOT NULL,
  page_alias varchar(240) NOT NULL default '',
  pos int(4) default NULL,
  PRIMARY KEY  (page_ref_id),
  KEY pidpaid (page_id,parent_id)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

insert into tiki_structures(page_ref_id,page_id, pos) select 
  tp1.page_id,tp1.page_id,
  ts.pos 
    from original_tiki_structures ts, tiki_pages tp1 
      where ts.page=tp1.pageName AND ts.parent='';

insert into tiki_structures(page_ref_id,parent_id, page_id, pos) select 
  tp1.page_id,tp2.page_id,
  tp1.page_id,
  ts.pos 
    from original_tiki_structures  ts, tiki_pages  tp1, tiki_pages  tp2 
      where ts.page=tp1.pageName AND ts.parent=tp2.pageName;

DROP TABLE original_tiki_structures;

