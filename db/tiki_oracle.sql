-- 
-- Created by SQL::Translator::Producer::Oracle
-- Created on Sun Aug 17 00:56:05 2003
-- 
-- We assume that default NLS_DATE_FORMAT has been changed
-- but we set it here anyway to be self-consistent.
ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS';

--
-- Table: galaxia_activities
--

DROP TABLE galaxia_activities;
CREATE TABLE galaxia_activities (
  activityId number(14) CONSTRAINT nn_activityId NOT NULL,
  name varchar2(80) DEFAULT NULL,
  normalized_name varchar2(80) DEFAULT NULL,
  pId number(14) DEFAULT '0' CONSTRAINT nn_pId NOT NULL,
  type varchar2(10) DEFAULT NULL CHECK (type IN ('start', 'end', 'split', 'switch', 'join', 'activity', 'standalone')),
  isAutoRouted char(1) DEFAULT NULL,
  flowNum number(10) DEFAULT NULL,
  isInteractive char(1) DEFAULT NULL,
  lastModif number(14) DEFAULT NULL,
  description clob,
  CONSTRAINT pk_galaxia_activities PRIMARY KEY (activityId)
);

CREATE SEQUENCE sq_galaxia_activities_activity;
CREATE OR REPLACE TRIGGER ai_galaxia_activities_activity
BEFORE INSERT ON galaxia_activities
FOR EACH ROW WHEN (
 new.activityId IS NULL OR new.activityId = 0
)
BEGIN
 SELECT sq_galaxia_activities_activity.nextval
 INTO :new.activityId
 FROM dual;
END;
/

--
-- Table: galaxia_activity_roles
--

DROP TABLE galaxia_activity_roles;
CREATE TABLE galaxia_activity_roles (
  activityId number(14) DEFAULT '0' CONSTRAINT nn_activityId02 NOT NULL,
  roleId number(14) DEFAULT '0' CONSTRAINT nn_roleId NOT NULL,
  CONSTRAINT pk_galaxia_activity_roles PRIMARY KEY (activityId, roleId)
);

--
-- Table: galaxia_instance_activities
--

DROP TABLE galaxia_instance_activities;
CREATE TABLE galaxia_instance_activities (
  instanceId number(14) DEFAULT '0' CONSTRAINT nn_instanceId NOT NULL,
  activityId number(14) DEFAULT '0' CONSTRAINT nn_activityId03 NOT NULL,
  started number(14) DEFAULT '0' CONSTRAINT nn_started NOT NULL,
  ended number(14) DEFAULT '0' CONSTRAINT nn_ended NOT NULL,
  user_ varchar2(200) DEFAULT NULL,
  status varchar2(9) DEFAULT NULL CHECK (status IN ('running', 'completed')),
  CONSTRAINT pk_galaxia_instance_activities PRIMARY KEY (instanceId, activityId)
);

--
-- Table: galaxia_instance_comments
--

DROP TABLE galaxia_instance_comments;
CREATE TABLE galaxia_instance_comments (
  cId number(14) CONSTRAINT nn_cId NOT NULL,
  instanceId number(14) DEFAULT '0' CONSTRAINT nn_instanceId02 NOT NULL,
  user_ varchar2(200) DEFAULT NULL,
  activityId number(14) DEFAULT NULL,
  hash varchar2(32) DEFAULT NULL,
  title varchar2(250) DEFAULT NULL,
  comment_ clob,
  activity varchar2(80) DEFAULT NULL,
  timestamp number(14) DEFAULT NULL,
  CONSTRAINT pk_galaxia_instance_comments PRIMARY KEY (cId)
);

CREATE SEQUENCE sq_galaxia_instance_comments_c;
CREATE OR REPLACE TRIGGER ai_galaxia_instance_comments_c
BEFORE INSERT ON galaxia_instance_comments
FOR EACH ROW WHEN (
 new.cId IS NULL OR new.cId = 0
)
BEGIN
 SELECT sq_galaxia_instance_comments_c.nextval
 INTO :new.cId
 FROM dual;
END;
/

--
-- Table: galaxia_instances
--

DROP TABLE galaxia_instances;
CREATE TABLE galaxia_instances (
  instanceId number(14) CONSTRAINT nn_instanceId03 NOT NULL,
  pId number(14) DEFAULT '0' CONSTRAINT nn_pId02 NOT NULL,
  started number(14) DEFAULT NULL,
  owner varchar2(200) DEFAULT NULL,
  nextActivity number(14) DEFAULT NULL,
  nextUser varchar2(200) DEFAULT NULL,
  ended number(14) DEFAULT NULL,
  status varchar2(9) DEFAULT NULL CHECK (status IN ('active', 'exception', 'aborted', 'completed')),
  properties blob,
  CONSTRAINT pk_galaxia_instances PRIMARY KEY (instanceId)
);

CREATE SEQUENCE sq_galaxia_instances_instanceI;
CREATE OR REPLACE TRIGGER ai_galaxia_instances_instanceI
BEFORE INSERT ON galaxia_instances
FOR EACH ROW WHEN (
 new.instanceId IS NULL OR new.instanceId = 0
)
BEGIN
 SELECT sq_galaxia_instances_instanceI.nextval
 INTO :new.instanceId
 FROM dual;
END;
/

--
-- Table: galaxia_processes
--

DROP TABLE galaxia_processes;
CREATE TABLE galaxia_processes (
  pId number(14) CONSTRAINT nn_pId03 NOT NULL,
  name varchar2(80) DEFAULT NULL,
  isValid char(1) DEFAULT NULL,
  isActive char(1) DEFAULT NULL,
  version varchar2(12) DEFAULT NULL,
  description clob,
  lastModif number(14) DEFAULT NULL,
  normalized_name varchar2(80) DEFAULT NULL,
  CONSTRAINT pk_galaxia_processes PRIMARY KEY (pId)
);

CREATE SEQUENCE sq_galaxia_processes_pId;
CREATE OR REPLACE TRIGGER ai_galaxia_processes_pId
BEFORE INSERT ON galaxia_processes
FOR EACH ROW WHEN (
 new.pId IS NULL OR new.pId = 0
)
BEGIN
 SELECT sq_galaxia_processes_pId.nextval
 INTO :new.pId
 FROM dual;
END;
/

--
-- Table: galaxia_roles
--

DROP TABLE galaxia_roles;
CREATE TABLE galaxia_roles (
  roleId number(14) CONSTRAINT nn_roleId02 NOT NULL,
  pId number(14) DEFAULT '0' CONSTRAINT nn_pId04 NOT NULL,
  lastModif number(14) DEFAULT NULL,
  name varchar2(80) DEFAULT NULL,
  description clob,
  CONSTRAINT pk_galaxia_roles PRIMARY KEY (roleId)
);

CREATE SEQUENCE sq_galaxia_roles_roleId;
CREATE OR REPLACE TRIGGER ai_galaxia_roles_roleId
BEFORE INSERT ON galaxia_roles
FOR EACH ROW WHEN (
 new.roleId IS NULL OR new.roleId = 0
)
BEGIN
 SELECT sq_galaxia_roles_roleId.nextval
 INTO :new.roleId
 FROM dual;
END;
/

--
-- Table: galaxia_transitions
--

DROP TABLE galaxia_transitions;
CREATE TABLE galaxia_transitions (
  pId number(14) DEFAULT '0' CONSTRAINT nn_pId05 NOT NULL,
  actFromId number(14) DEFAULT '0' CONSTRAINT nn_actFromId NOT NULL,
  actToId number(14) DEFAULT '0' CONSTRAINT nn_actToId NOT NULL,
  CONSTRAINT pk_galaxia_transitions PRIMARY KEY (actFromId, actToId)
);

--
-- Table: galaxia_user_roles
--

DROP TABLE galaxia_user_roles;
CREATE TABLE galaxia_user_roles (
  pId number(14) DEFAULT '0' CONSTRAINT nn_pId06 NOT NULL,
  roleId number(14) CONSTRAINT nn_roleId03 NOT NULL,
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_ NOT NULL,
  CONSTRAINT pk_galaxia_user_roles PRIMARY KEY (roleId, user_)
);

CREATE SEQUENCE sq_galaxia_user_roles_roleId;
CREATE OR REPLACE TRIGGER ai_galaxia_user_roles_roleId
BEFORE INSERT ON galaxia_user_roles
FOR EACH ROW WHEN (
 new.roleId IS NULL OR new.roleId = 0
)
BEGIN
 SELECT sq_galaxia_user_roles_roleId.nextval
 INTO :new.roleId
 FROM dual;
END;
/

--
-- Table: galaxia_workitems
--

DROP TABLE galaxia_workitems;
CREATE TABLE galaxia_workitems (
  itemId number(14) CONSTRAINT nn_itemId NOT NULL,
  instanceId number(14) DEFAULT '0' CONSTRAINT nn_instanceId04 NOT NULL,
  orderId number(14) DEFAULT '0' CONSTRAINT nn_orderId NOT NULL,
  activityId number(14) DEFAULT '0' CONSTRAINT nn_activityId04 NOT NULL,
  properties blob,
  started number(14) DEFAULT NULL,
  ended number(14) DEFAULT NULL,
  user_ varchar2(200) DEFAULT NULL,
  CONSTRAINT pk_galaxia_workitems PRIMARY KEY (itemId)
);

CREATE SEQUENCE sq_galaxia_workitems_itemId;
CREATE OR REPLACE TRIGGER ai_galaxia_workitems_itemId
BEFORE INSERT ON galaxia_workitems
FOR EACH ROW WHEN (
 new.itemId IS NULL OR new.itemId = 0
)
BEGIN
 SELECT sq_galaxia_workitems_itemId.nextval
 INTO :new.itemId
 FROM dual;
END;
/

--
-- Table: messu_messages
--

DROP TABLE messu_messages;
CREATE TABLE messu_messages (
  msgId number(14) CONSTRAINT nn_msgId NOT NULL,
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_02 NOT NULL,
  user_from varchar2(200) DEFAULT '' CONSTRAINT nn_user_from NOT NULL,
  user_to clob,
  user_cc clob,
  user_bcc clob,
  subject varchar2(255) DEFAULT NULL,
  body clob,
  hash varchar2(32) DEFAULT NULL,
  date_ number(14) DEFAULT NULL,
  isRead char(1) DEFAULT NULL,
  isReplied char(1) DEFAULT NULL,
  isFlagged char(1) DEFAULT NULL,
  priority number(2) DEFAULT NULL,
  CONSTRAINT pk_messu_messages PRIMARY KEY (msgId)
);

CREATE SEQUENCE sq_messu_messages_msgId;
CREATE OR REPLACE TRIGGER ai_messu_messages_msgId
BEFORE INSERT ON messu_messages
FOR EACH ROW WHEN (
 new.msgId IS NULL OR new.msgId = 0
)
BEGIN
 SELECT sq_messu_messages_msgId.nextval
 INTO :new.msgId
 FROM dual;
END;
/

--
-- Table: tiki_actionlog
--

DROP TABLE tiki_actionlog;
CREATE TABLE tiki_actionlog (
  action varchar2(255) DEFAULT '' CONSTRAINT nn_action NOT NULL,
  lastModif number(14) DEFAULT NULL,
  pageName varchar2(200) DEFAULT NULL,
  user_ varchar2(200) DEFAULT NULL,
  ip varchar2(15) DEFAULT NULL,
  comment_ varchar2(200) DEFAULT NULL
);

--
-- Table: tiki_articles
--

DROP TABLE tiki_articles;
CREATE TABLE tiki_articles (
  articleId number(8) CONSTRAINT nn_articleId NOT NULL,
  title varchar2(80) DEFAULT NULL,
  authorName varchar2(60) DEFAULT NULL,
  topicId number(14) DEFAULT NULL,
  topicName varchar2(40) DEFAULT NULL,
  size_ number(12) DEFAULT NULL,
  useImage char(1) DEFAULT NULL,
  image_name varchar2(80) DEFAULT NULL,
  image_type varchar2(80) DEFAULT NULL,
  image_size number(14) DEFAULT NULL,
  image_x number(4) DEFAULT NULL,
  image_y number(4) DEFAULT NULL,
  image_data blob,
  publishDate number(14) DEFAULT NULL,
  created number(14) DEFAULT NULL,
  heading clob,
  body clob,
  hash varchar2(32) DEFAULT NULL,
  author varchar2(200) DEFAULT NULL,
  reads number(14) DEFAULT NULL,
  votes number(8) DEFAULT NULL,
  points number(14) DEFAULT NULL,
  type varchar2(50) DEFAULT NULL,
  rating number(3, 2) DEFAULT NULL,
  isfloat char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_articles PRIMARY KEY (articleId)
);

CREATE SEQUENCE sq_tiki_articles_articleId;
CREATE OR REPLACE TRIGGER ai_tiki_articles_articleId
BEFORE INSERT ON tiki_articles
FOR EACH ROW WHEN (
 new.articleId IS NULL OR new.articleId = 0
)
BEGIN
 SELECT sq_tiki_articles_articleId.nextval
 INTO :new.articleId
 FROM dual;
END;
/

CREATE INDEX title_tiki_articles on tiki_articles (title);

CREATE INDEX heading_tiki_articles on tiki_articles (heading);

CREATE INDEX body_tiki_articles on tiki_articles (body);

CREATE INDEX reads_tiki_articles on tiki_articles (reads);

--
-- Table: tiki_banners
--

DROP TABLE tiki_banners;
CREATE TABLE tiki_banners (
  bannerId number(12) CONSTRAINT nn_bannerId NOT NULL,
  client varchar2(200) DEFAULT '' CONSTRAINT nn_client NOT NULL,
  url varchar2(255) DEFAULT NULL,
  title varchar2(255) DEFAULT NULL,
  alt varchar2(250) DEFAULT NULL,
  which varchar2(50) DEFAULT NULL,
  imageData blob,
  imageType varchar2(200) DEFAULT NULL,
  imageName varchar2(100) DEFAULT NULL,
  HTMLData clob,
  fixedURLData varchar2(255) DEFAULT NULL,
  textData clob,
  fromDate number(14) DEFAULT NULL,
  toDate number(14) DEFAULT NULL,
  useDates char(1) DEFAULT NULL,
  mon char(1) DEFAULT NULL,
  tue char(1) DEFAULT NULL,
  wed char(1) DEFAULT NULL,
  thu char(1) DEFAULT NULL,
  fri char(1) DEFAULT NULL,
  sat char(1) DEFAULT NULL,
  sun char(1) DEFAULT NULL,
  hourFrom varchar2(4) DEFAULT NULL,
  hourTo varchar2(4) DEFAULT NULL,
  created number(14) DEFAULT NULL,
  maxImpressions number(8) DEFAULT NULL,
  impressions number(8) DEFAULT NULL,
  clicks number(8) DEFAULT NULL,
  zone varchar2(40) DEFAULT NULL,
  CONSTRAINT pk_tiki_banners PRIMARY KEY (bannerId)
);

CREATE SEQUENCE sq_tiki_banners_bannerId;
CREATE OR REPLACE TRIGGER ai_tiki_banners_bannerId
BEFORE INSERT ON tiki_banners
FOR EACH ROW WHEN (
 new.bannerId IS NULL OR new.bannerId = 0
)
BEGIN
 SELECT sq_tiki_banners_bannerId.nextval
 INTO :new.bannerId
 FROM dual;
END;
/

--
-- Table: tiki_banning
--

DROP TABLE tiki_banning;
CREATE TABLE tiki_banning (
  banId number(12) CONSTRAINT nn_banId NOT NULL,
  mode_ varchar2(4) DEFAULT NULL CHECK (mode_ IN ('user', 'ip')),
  title varchar2(200) DEFAULT NULL,
  ip1 char(3) DEFAULT NULL,
  ip2 char(3) DEFAULT NULL,
  ip3 char(3) DEFAULT NULL,
  ip4 char(3) DEFAULT NULL,
  user_ varchar2(200) DEFAULT NULL,
  date_from date CONSTRAINT nn_date_from NOT NULL,
  date_to date CONSTRAINT nn_date_to NOT NULL,
  use_dates char(1) DEFAULT NULL,
  created number(14) DEFAULT NULL,
  message clob,
  CONSTRAINT pk_tiki_banning PRIMARY KEY (banId)
);

CREATE SEQUENCE sq_tiki_banning_banId;
CREATE OR REPLACE TRIGGER ai_tiki_banning_banId
BEFORE INSERT ON tiki_banning
FOR EACH ROW WHEN (
 new.banId IS NULL OR new.banId = 0
)
BEGIN
 SELECT sq_tiki_banning_banId.nextval
 INTO :new.banId
 FROM dual;
END;
/

CREATE OR REPLACE TRIGGER ts_tiki_banning_date_from
BEFORE INSERT OR UPDATE ON tiki_banning
FOR EACH ROW WHEN (new.date_from IS NULL)
BEGIN 
 SELECT sysdate INTO :new.date_from FROM dual;
END;
/

CREATE OR REPLACE TRIGGER ts_tiki_banning_date_to
BEFORE INSERT OR UPDATE ON tiki_banning
FOR EACH ROW WHEN (new.date_to IS NULL)
BEGIN 
 SELECT sysdate INTO :new.date_to FROM dual;
END;
/

--
-- Table: tiki_banning_sections
--

DROP TABLE tiki_banning_sections;
CREATE TABLE tiki_banning_sections (
  banId number(12) DEFAULT '0' CONSTRAINT nn_banId02 NOT NULL,
  section varchar2(100) DEFAULT '' CONSTRAINT nn_section NOT NULL,
  CONSTRAINT pk_tiki_banning_sections PRIMARY KEY (banId, section)
);

--
-- Table: tiki_blog_activity
--

DROP TABLE tiki_blog_activity;
CREATE TABLE tiki_blog_activity (
  blogId number(8) DEFAULT '0' CONSTRAINT nn_blogId NOT NULL,
  day number(14) DEFAULT '0' CONSTRAINT nn_day NOT NULL,
  posts number(8) DEFAULT NULL,
  CONSTRAINT pk_tiki_blog_activity PRIMARY KEY (blogId, day)
);

--
-- Table: tiki_blog_posts
--

DROP TABLE tiki_blog_posts;
CREATE TABLE tiki_blog_posts (
  postId number(8) CONSTRAINT nn_postId NOT NULL,
  blogId number(8) DEFAULT '0' CONSTRAINT nn_blogId02 NOT NULL,
  data clob,
  created number(14) DEFAULT NULL,
  user_ varchar2(200) DEFAULT NULL,
  trackbacks_to clob,
  trackbacks_from clob,
  title varchar2(80) DEFAULT NULL,
  CONSTRAINT pk_tiki_blog_posts PRIMARY KEY (postId)
);

CREATE SEQUENCE sq_tiki_blog_posts_postId;
CREATE OR REPLACE TRIGGER ai_tiki_blog_posts_postId
BEFORE INSERT ON tiki_blog_posts
FOR EACH ROW WHEN (
 new.postId IS NULL OR new.postId = 0
)
BEGIN
 SELECT sq_tiki_blog_posts_postId.nextval
 INTO :new.postId
 FROM dual;
END;
/

CREATE INDEX data_tiki_blog_posts on tiki_blog_posts (data);

CREATE INDEX blogId_tiki_blog_posts on tiki_blog_posts (blogId);

CREATE INDEX created_tiki_blog_posts on tiki_blog_posts (created);

--
-- Table: tiki_blog_posts_images
--

DROP TABLE tiki_blog_posts_images;
CREATE TABLE tiki_blog_posts_images (
  imgId number(14) CONSTRAINT nn_imgId NOT NULL,
  postId number(14) DEFAULT '0' CONSTRAINT nn_postId02 NOT NULL,
  filename varchar2(80) DEFAULT NULL,
  filetype varchar2(80) DEFAULT NULL,
  filesize number(14) DEFAULT NULL,
  data blob,
  CONSTRAINT pk_tiki_blog_posts_images PRIMARY KEY (imgId)
);

CREATE SEQUENCE sq_tiki_blog_posts_images_imgI;
CREATE OR REPLACE TRIGGER ai_tiki_blog_posts_images_imgI
BEFORE INSERT ON tiki_blog_posts_images
FOR EACH ROW WHEN (
 new.imgId IS NULL OR new.imgId = 0
)
BEGIN
 SELECT sq_tiki_blog_posts_images_imgI.nextval
 INTO :new.imgId
 FROM dual;
END;
/

--
-- Table: tiki_blogs
--

DROP TABLE tiki_blogs;
CREATE TABLE tiki_blogs (
  blogId number(8) CONSTRAINT nn_blogId03 NOT NULL,
  created number(14) DEFAULT NULL,
  lastModif number(14) DEFAULT NULL,
  title varchar2(200) DEFAULT NULL,
  description clob,
  user_ varchar2(200) DEFAULT NULL,
  public_ char(1) DEFAULT NULL,
  posts number(8) DEFAULT NULL,
  maxPosts number(8) DEFAULT NULL,
  hits number(8) DEFAULT NULL,
  activity number(4, 2) DEFAULT NULL,
  heading clob,
  use_find char(1) DEFAULT NULL,
  use_title char(1) DEFAULT NULL,
  add_date char(1) DEFAULT NULL,
  add_poster char(1) DEFAULT NULL,
  allow_comments char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_blogs PRIMARY KEY (blogId)
);

CREATE SEQUENCE sq_tiki_blogs_blogId;
CREATE OR REPLACE TRIGGER ai_tiki_blogs_blogId
BEFORE INSERT ON tiki_blogs
FOR EACH ROW WHEN (
 new.blogId IS NULL OR new.blogId = 0
)
BEGIN
 SELECT sq_tiki_blogs_blogId.nextval
 INTO :new.blogId
 FROM dual;
END;
/

CREATE INDEX title_tiki_blogs on tiki_blogs (title);

CREATE INDEX description_tiki_blogs on tiki_blogs (description);

CREATE INDEX hits_tiki_blogs on tiki_blogs (hits);

--
-- Table: tiki_calendar_categories
--

DROP TABLE tiki_calendar_categories;
CREATE TABLE tiki_calendar_categories (
  calcatId number(11) CONSTRAINT nn_calcatId NOT NULL,
  calendarId number(14) DEFAULT '0' CONSTRAINT nn_calendarId NOT NULL,
  name varchar2(255) DEFAULT '' CONSTRAINT nn_name NOT NULL,
  CONSTRAINT pk_tiki_calendar_categories PRIMARY KEY (calcatId),
  CONSTRAINT catname UNIQUE (calendarId, name)
);

CREATE SEQUENCE sq_tiki_calendar_categories_ca;
CREATE OR REPLACE TRIGGER ai_tiki_calendar_categories_ca
BEFORE INSERT ON tiki_calendar_categories
FOR EACH ROW WHEN (
 new.calcatId IS NULL OR new.calcatId = 0
)
BEGIN
 SELECT sq_tiki_calendar_categories_ca.nextval
 INTO :new.calcatId
 FROM dual;
END;
/

--
-- Table: tiki_calendar_items
--

DROP TABLE tiki_calendar_items;
CREATE TABLE tiki_calendar_items (
  calitemId number(14) CONSTRAINT nn_calitemId NOT NULL,
  calendarId number(14) DEFAULT '0' CONSTRAINT nn_calendarId02 NOT NULL,
  start_ number(14) DEFAULT '0' CONSTRAINT nn_start_ NOT NULL,
  end number(14) DEFAULT '0' CONSTRAINT nn_end NOT NULL,
  locationId number(14) DEFAULT NULL,
  categoryId number(14) DEFAULT NULL,
  priority varchar2(1) DEFAULT '1' CONSTRAINT nn_priority NOT NULL CHECK (priority IN ('1', '2', '3', '4', '5', '6', '7', '8', '9')),
  status varchar2(1) DEFAULT '0' CONSTRAINT nn_status NOT NULL CHECK (status IN ('0', '1', '2')),
  url varchar2(255) DEFAULT NULL,
  lang char(2) DEFAULT 'en' CONSTRAINT nn_lang NOT NULL,
  name varchar2(255) DEFAULT '' CONSTRAINT nn_name02 NOT NULL,
  description blob,
  user_ varchar2(40) DEFAULT NULL,
  created number(14) DEFAULT '0' CONSTRAINT nn_created NOT NULL,
  lastmodif number(14) DEFAULT '0' CONSTRAINT nn_lastmodif NOT NULL,
  CONSTRAINT pk_tiki_calendar_items PRIMARY KEY (calitemId)
);

CREATE SEQUENCE sq_tiki_calendar_items_calitem;
CREATE OR REPLACE TRIGGER ai_tiki_calendar_items_calitem
BEFORE INSERT ON tiki_calendar_items
FOR EACH ROW WHEN (
 new.calitemId IS NULL OR new.calitemId = 0
)
BEGIN
 SELECT sq_tiki_calendar_items_calitem.nextval
 INTO :new.calitemId
 FROM dual;
END;
/

CREATE INDEX calendarId_tiki_calendar_items on tiki_calendar_items (calendarId);

--
-- Table: tiki_calendar_locations
--

DROP TABLE tiki_calendar_locations;
CREATE TABLE tiki_calendar_locations (
  callocId number(14) CONSTRAINT nn_callocId NOT NULL,
  calendarId number(14) DEFAULT '0' CONSTRAINT nn_calendarId03 NOT NULL,
  name varchar2(255) DEFAULT '' CONSTRAINT nn_name03 NOT NULL,
  description blob,
  CONSTRAINT pk_tiki_calendar_locations PRIMARY KEY (callocId),
  CONSTRAINT locname UNIQUE (calendarId, name)
);

CREATE SEQUENCE sq_tiki_calendar_locations_cal;
CREATE OR REPLACE TRIGGER ai_tiki_calendar_locations_cal
BEFORE INSERT ON tiki_calendar_locations
FOR EACH ROW WHEN (
 new.callocId IS NULL OR new.callocId = 0
)
BEGIN
 SELECT sq_tiki_calendar_locations_cal.nextval
 INTO :new.callocId
 FROM dual;
END;
/

--
-- Table: tiki_calendar_roles
--

DROP TABLE tiki_calendar_roles;
CREATE TABLE tiki_calendar_roles (
  calitemId number(14) DEFAULT '0' CONSTRAINT nn_calitemId02 NOT NULL,
  username varchar2(40) DEFAULT '' CONSTRAINT nn_username NOT NULL,
  role varchar2(1) DEFAULT '0' CONSTRAINT nn_role NOT NULL CHECK (role IN ('0', '1', '2', '3', '6')),
  CONSTRAINT pk_tiki_calendar_roles PRIMARY KEY (calitemId, username, role)
);

--
-- Table: tiki_calendars
--

DROP TABLE tiki_calendars;
CREATE TABLE tiki_calendars (
  calendarId number(14) CONSTRAINT nn_calendarId04 NOT NULL,
  name varchar2(80) DEFAULT '' CONSTRAINT nn_name04 NOT NULL,
  description varchar2(255) DEFAULT NULL,
  user_ varchar2(40) DEFAULT '' CONSTRAINT nn_user_03 NOT NULL,
  customlocations varchar2(1) DEFAULT 'n' CONSTRAINT nn_customlocations NOT NULL CHECK (customlocations IN ('n', 'y')),
  customcategories varchar2(1) DEFAULT 'n' CONSTRAINT nn_customcategories NOT NULL CHECK (customcategories IN ('n', 'y')),
  customlanguages varchar2(1) DEFAULT 'n' CONSTRAINT nn_customlanguages NOT NULL CHECK (customlanguages IN ('n', 'y')),
  custompriorities varchar2(1) DEFAULT 'n' CONSTRAINT nn_custompriorities NOT NULL CHECK (custompriorities IN ('n', 'y')),
  customparticipants varchar2(1) DEFAULT 'n' CONSTRAINT nn_customparticipants NOT NULL CHECK (customparticipants IN ('n', 'y')),
  created number(14) DEFAULT '0' CONSTRAINT nn_created02 NOT NULL,
  lastmodif number(14) DEFAULT '0' CONSTRAINT nn_lastmodif02 NOT NULL,
  CONSTRAINT pk_tiki_calendars PRIMARY KEY (calendarId)
);

CREATE SEQUENCE sq_tiki_calendars_calendarId;
CREATE OR REPLACE TRIGGER ai_tiki_calendars_calendarId
BEFORE INSERT ON tiki_calendars
FOR EACH ROW WHEN (
 new.calendarId IS NULL OR new.calendarId = 0
)
BEGIN
 SELECT sq_tiki_calendars_calendarId.nextval
 INTO :new.calendarId
 FROM dual;
END;
/

--
-- Table: tiki_categories
--

DROP TABLE tiki_categories;
CREATE TABLE tiki_categories (
  categId number(12) CONSTRAINT nn_categId NOT NULL,
  name varchar2(100) DEFAULT NULL,
  description varchar2(250) DEFAULT NULL,
  parentId number(12) DEFAULT NULL,
  hits number(8) DEFAULT NULL,
  CONSTRAINT pk_tiki_categories PRIMARY KEY (categId)
);

CREATE SEQUENCE sq_tiki_categories_categId;
CREATE OR REPLACE TRIGGER ai_tiki_categories_categId
BEFORE INSERT ON tiki_categories
FOR EACH ROW WHEN (
 new.categId IS NULL OR new.categId = 0
)
BEGIN
 SELECT sq_tiki_categories_categId.nextval
 INTO :new.categId
 FROM dual;
END;
/

--
-- Table: tiki_categorized_objects
--

DROP TABLE tiki_categorized_objects;
CREATE TABLE tiki_categorized_objects (
  catObjectId number(12) CONSTRAINT nn_catObjectId NOT NULL,
  type varchar2(50) DEFAULT NULL,
  objId varchar2(255) DEFAULT NULL,
  description clob,
  created number(14) DEFAULT NULL,
  name varchar2(200) DEFAULT NULL,
  href varchar2(200) DEFAULT NULL,
  hits number(8) DEFAULT NULL,
  CONSTRAINT pk_tiki_categorized_objects PRIMARY KEY (catObjectId)
);

CREATE SEQUENCE sq_tiki_categorized_objects_ca;
CREATE OR REPLACE TRIGGER ai_tiki_categorized_objects_ca
BEFORE INSERT ON tiki_categorized_objects
FOR EACH ROW WHEN (
 new.catObjectId IS NULL OR new.catObjectId = 0
)
BEGIN
 SELECT sq_tiki_categorized_objects_ca.nextval
 INTO :new.catObjectId
 FROM dual;
END;
/

--
-- Table: tiki_category_objects
--

DROP TABLE tiki_category_objects;
CREATE TABLE tiki_category_objects (
  catObjectId number(12) DEFAULT '0' CONSTRAINT nn_catObjectId02 NOT NULL,
  categId number(12) DEFAULT '0' CONSTRAINT nn_categId02 NOT NULL,
  CONSTRAINT pk_tiki_category_objects PRIMARY KEY (catObjectId, categId)
);

--
-- Table: tiki_category_sites
--

DROP TABLE tiki_category_sites;
CREATE TABLE tiki_category_sites (
  categId number(10) DEFAULT '0' CONSTRAINT nn_categId03 NOT NULL,
  siteId number(14) DEFAULT '0' CONSTRAINT nn_siteId NOT NULL,
  CONSTRAINT pk_tiki_category_sites PRIMARY KEY (categId, siteId)
);

--
-- Table: tiki_chart_items
--

DROP TABLE tiki_chart_items;
CREATE TABLE tiki_chart_items (
  itemId number(14) CONSTRAINT nn_itemId02 NOT NULL,
  title varchar2(250) DEFAULT NULL,
  description clob,
  chartId number(14) DEFAULT '0' CONSTRAINT nn_chartId NOT NULL,
  created number(14) DEFAULT NULL,
  URL varchar2(250) DEFAULT NULL,
  votes number(14) DEFAULT NULL,
  points number(14) DEFAULT NULL,
  average number(4, 2) DEFAULT NULL,
  CONSTRAINT pk_tiki_chart_items PRIMARY KEY (itemId)
);

CREATE SEQUENCE sq_tiki_chart_items_itemId;
CREATE OR REPLACE TRIGGER ai_tiki_chart_items_itemId
BEFORE INSERT ON tiki_chart_items
FOR EACH ROW WHEN (
 new.itemId IS NULL OR new.itemId = 0
)
BEGIN
 SELECT sq_tiki_chart_items_itemId.nextval
 INTO :new.itemId
 FROM dual;
END;
/

--
-- Table: tiki_charts
--

DROP TABLE tiki_charts;
CREATE TABLE tiki_charts (
  chartId number(14) CONSTRAINT nn_chartId02 NOT NULL,
  title varchar2(250) DEFAULT NULL,
  description clob,
  hits number(14) DEFAULT NULL,
  singleItemVotes char(1) DEFAULT NULL,
  singleChartVotes char(1) DEFAULT NULL,
  suggestions char(1) DEFAULT NULL,
  autoValidate char(1) DEFAULT NULL,
  topN number(6) DEFAULT NULL,
  maxVoteValue number(4) DEFAULT NULL,
  frequency number(14) DEFAULT NULL,
  showAverage char(1) DEFAULT NULL,
  isActive char(1) DEFAULT NULL,
  showVotes char(1) DEFAULT NULL,
  useCookies char(1) DEFAULT NULL,
  lastChart number(14) DEFAULT NULL,
  voteAgainAfter number(14) DEFAULT NULL,
  created number(14) DEFAULT NULL,
  hist number(12) DEFAULT NULL,
  CONSTRAINT pk_tiki_charts PRIMARY KEY (chartId)
);

CREATE SEQUENCE sq_tiki_charts_chartId;
CREATE OR REPLACE TRIGGER ai_tiki_charts_chartId
BEFORE INSERT ON tiki_charts
FOR EACH ROW WHEN (
 new.chartId IS NULL OR new.chartId = 0
)
BEGIN
 SELECT sq_tiki_charts_chartId.nextval
 INTO :new.chartId
 FROM dual;
END;
/

--
-- Table: tiki_charts_rankings
--

DROP TABLE tiki_charts_rankings;
CREATE TABLE tiki_charts_rankings (
  chartId number(14) DEFAULT '0' CONSTRAINT nn_chartId03 NOT NULL,
  itemId number(14) DEFAULT '0' CONSTRAINT nn_itemId03 NOT NULL,
  position number(14) DEFAULT '0' CONSTRAINT nn_position NOT NULL,
  timestamp number(14) DEFAULT '0' CONSTRAINT nn_timestamp NOT NULL,
  lastPosition number(14) DEFAULT '0' CONSTRAINT nn_lastPosition NOT NULL,
  period number(14) DEFAULT '0' CONSTRAINT nn_period NOT NULL,
  rvotes number(14) DEFAULT '0' CONSTRAINT nn_rvotes NOT NULL,
  raverage number(4, 2) DEFAULT '0.00' CONSTRAINT nn_raverage NOT NULL,
  CONSTRAINT pk_tiki_charts_rankings PRIMARY KEY (chartId, itemId, period)
);

--
-- Table: tiki_charts_votes
--

DROP TABLE tiki_charts_votes;
CREATE TABLE tiki_charts_votes (
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_04 NOT NULL,
  itemId number(14) DEFAULT '0' CONSTRAINT nn_itemId04 NOT NULL,
  timestamp number(14) DEFAULT NULL,
  chartId number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_charts_votes PRIMARY KEY (user_, itemId)
);

--
-- Table: tiki_chat_channels
--

DROP TABLE tiki_chat_channels;
CREATE TABLE tiki_chat_channels (
  channelId number(8) CONSTRAINT nn_channelId NOT NULL,
  name varchar2(30) DEFAULT NULL,
  description varchar2(250) DEFAULT NULL,
  max_users number(8) DEFAULT NULL,
  mode_ char(1) DEFAULT NULL,
  moderator varchar2(200) DEFAULT NULL,
  active char(1) DEFAULT NULL,
  refresh number(6) DEFAULT NULL,
  CONSTRAINT pk_tiki_chat_channels PRIMARY KEY (channelId)
);

CREATE SEQUENCE sq_tiki_chat_channels_channelI;
CREATE OR REPLACE TRIGGER ai_tiki_chat_channels_channelI
BEFORE INSERT ON tiki_chat_channels
FOR EACH ROW WHEN (
 new.channelId IS NULL OR new.channelId = 0
)
BEGIN
 SELECT sq_tiki_chat_channels_channelI.nextval
 INTO :new.channelId
 FROM dual;
END;
/

--
-- Table: tiki_chat_messages
--

DROP TABLE tiki_chat_messages;
CREATE TABLE tiki_chat_messages (
  messageId number(8) CONSTRAINT nn_messageId NOT NULL,
  channelId number(8) DEFAULT '0' CONSTRAINT nn_channelId02 NOT NULL,
  data varchar2(255) DEFAULT NULL,
  poster varchar2(200) DEFAULT 'anonymous' CONSTRAINT nn_poster NOT NULL,
  timestamp number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_chat_messages PRIMARY KEY (messageId)
);

CREATE SEQUENCE sq_tiki_chat_messages_messageI;
CREATE OR REPLACE TRIGGER ai_tiki_chat_messages_messageI
BEFORE INSERT ON tiki_chat_messages
FOR EACH ROW WHEN (
 new.messageId IS NULL OR new.messageId = 0
)
BEGIN
 SELECT sq_tiki_chat_messages_messageI.nextval
 INTO :new.messageId
 FROM dual;
END;
/

--
-- Table: tiki_chat_users
--

DROP TABLE tiki_chat_users;
CREATE TABLE tiki_chat_users (
  nickname varchar2(200) DEFAULT '' CONSTRAINT nn_nickname NOT NULL,
  channelId number(8) DEFAULT '0' CONSTRAINT nn_channelId03 NOT NULL,
  timestamp number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_chat_users PRIMARY KEY (nickname, channelId)
);

--
-- Table: tiki_comments
--

DROP TABLE tiki_comments;
CREATE TABLE tiki_comments (
  threadId number(14) CONSTRAINT nn_threadId NOT NULL,
  object varchar2(32) DEFAULT '' CONSTRAINT nn_object NOT NULL,
  parentId number(14) DEFAULT NULL,
  userName varchar2(200) DEFAULT NULL,
  commentDate number(14) DEFAULT NULL,
  hits number(8) DEFAULT NULL,
  type char(1) DEFAULT NULL,
  points number(8, 2) DEFAULT NULL,
  votes number(8) DEFAULT NULL,
  average number(8, 4) DEFAULT NULL,
  title varchar2(100) DEFAULT NULL,
  data clob,
  hash varchar2(32) DEFAULT NULL,
  user_ip varchar2(15) DEFAULT NULL,
  summary varchar2(240) DEFAULT NULL,
  smiley varchar2(80) DEFAULT NULL,
  message_id varchar(250) default NULL,
  in_reply_to varchar(250) default NULL,
  CONSTRAINT pk_tiki_comments PRIMARY KEY (threadId)
);

CREATE SEQUENCE sq_tiki_comments_threadId;
CREATE OR REPLACE TRIGGER ai_tiki_comments_threadId
BEFORE INSERT ON tiki_comments
FOR EACH ROW WHEN (
 new.threadId IS NULL OR new.threadId = 0
)
BEGIN
 SELECT sq_tiki_comments_threadId.nextval
 INTO :new.threadId
 FROM dual;
END;
/

CREATE INDEX title_tiki_comments on tiki_comments (title);

CREATE INDEX data_tiki_comments on tiki_comments (data);

CREATE INDEX object_tiki_comments on tiki_comments (object);

CREATE INDEX hits_tiki_comments on tiki_comments (hits);

CREATE INDEX tc_pi_tiki_comments on tiki_comments (parentId);

--
-- Table: tiki_content
--

DROP TABLE tiki_content;
CREATE TABLE tiki_content (
  contentId number(8) CONSTRAINT nn_contentId NOT NULL,
  description clob,
  CONSTRAINT pk_tiki_content PRIMARY KEY (contentId)
);

CREATE SEQUENCE sq_tiki_content_contentId;
CREATE OR REPLACE TRIGGER ai_tiki_content_contentId
BEFORE INSERT ON tiki_content
FOR EACH ROW WHEN (
 new.contentId IS NULL OR new.contentId = 0
)
BEGIN
 SELECT sq_tiki_content_contentId.nextval
 INTO :new.contentId
 FROM dual;
END;
/

--
-- Table: tiki_content_templates
--

DROP TABLE tiki_content_templates;
CREATE TABLE tiki_content_templates (
  templateId number(10) CONSTRAINT nn_templateId NOT NULL,
  content blob,
  name varchar2(200) DEFAULT NULL,
  created number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_content_templates PRIMARY KEY (templateId)
);

CREATE SEQUENCE sq_tiki_content_templates_temp;
CREATE OR REPLACE TRIGGER ai_tiki_content_templates_temp
BEFORE INSERT ON tiki_content_templates
FOR EACH ROW WHEN (
 new.templateId IS NULL OR new.templateId = 0
)
BEGIN
 SELECT sq_tiki_content_templates_temp.nextval
 INTO :new.templateId
 FROM dual;
END;
/

--
-- Table: tiki_content_templates_section
--

DROP TABLE tiki_content_templates_section;
CREATE TABLE tiki_content_templates_section (
  templateId number(10) DEFAULT '0' CONSTRAINT nn_templateId02 NOT NULL,
  section varchar2(250) DEFAULT '' CONSTRAINT nn_section02 NOT NULL,
  CONSTRAINT pk_tiki_content_templates_sect PRIMARY KEY (templateId, section)
);

--
-- Table: tiki_cookies
--

DROP TABLE tiki_cookies;
CREATE TABLE tiki_cookies (
  cookieId number(10) CONSTRAINT nn_cookieId NOT NULL,
  cookie varchar2(255) DEFAULT NULL,
  CONSTRAINT pk_tiki_cookies PRIMARY KEY (cookieId)
);

CREATE SEQUENCE sq_tiki_cookies_cookieId;
CREATE OR REPLACE TRIGGER ai_tiki_cookies_cookieId
BEFORE INSERT ON tiki_cookies
FOR EACH ROW WHEN (
 new.cookieId IS NULL OR new.cookieId = 0
)
BEGIN
 SELECT sq_tiki_cookies_cookieId.nextval
 INTO :new.cookieId
 FROM dual;
END;
/

--
-- Table: tiki_copyrights
--

DROP TABLE tiki_copyrights;
CREATE TABLE tiki_copyrights (
  copyrightId number(12) CONSTRAINT nn_copyrightId NOT NULL,
  page varchar2(200) DEFAULT NULL,
  title varchar2(200) DEFAULT NULL,
  year number(11) DEFAULT NULL,
  authors varchar2(200) DEFAULT NULL,
  copyright_order number(11) DEFAULT NULL,
  userName varchar2(200) DEFAULT NULL,
  CONSTRAINT pk_tiki_copyrights PRIMARY KEY (copyrightId)
);

CREATE SEQUENCE sq_tiki_copyrights_copyrightId;
CREATE OR REPLACE TRIGGER ai_tiki_copyrights_copyrightId
BEFORE INSERT ON tiki_copyrights
FOR EACH ROW WHEN (
 new.copyrightId IS NULL OR new.copyrightId = 0
)
BEGIN
 SELECT sq_tiki_copyrights_copyrightId.nextval
 INTO :new.copyrightId
 FROM dual;
END;
/

--
-- Table: tiki_directory_categories
--

DROP TABLE tiki_directory_categories;
CREATE TABLE tiki_directory_categories (
  categId number(10) CONSTRAINT nn_categId04 NOT NULL,
  parent number(10) DEFAULT NULL,
  name varchar2(240) DEFAULT NULL,
  description clob,
  childrenType char(1) DEFAULT NULL,
  sites number(10) DEFAULT NULL,
  viewableChildren number(4) DEFAULT NULL,
  allowSites char(1) DEFAULT NULL,
  showCount char(1) DEFAULT NULL,
  editorGroup varchar2(200) DEFAULT NULL,
  hits number(12) DEFAULT NULL,
  CONSTRAINT pk_tiki_directory_categories PRIMARY KEY (categId)
);

CREATE SEQUENCE sq_tiki_directory_categories_c;
CREATE OR REPLACE TRIGGER ai_tiki_directory_categories_c
BEFORE INSERT ON tiki_directory_categories
FOR EACH ROW WHEN (
 new.categId IS NULL OR new.categId = 0
)
BEGIN
 SELECT sq_tiki_directory_categories_c.nextval
 INTO :new.categId
 FROM dual;
END;
/

--
-- Table: tiki_directory_search
--

DROP TABLE tiki_directory_search;
CREATE TABLE tiki_directory_search (
  term varchar2(250) DEFAULT '' CONSTRAINT nn_term NOT NULL,
  hits number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_directory_search PRIMARY KEY (term)
);

--
-- Table: tiki_directory_sites
--

DROP TABLE tiki_directory_sites;
CREATE TABLE tiki_directory_sites (
  siteId number(14) CONSTRAINT nn_siteId02 NOT NULL,
  name varchar2(240) DEFAULT NULL,
  description clob,
  url varchar2(255) DEFAULT NULL,
  country varchar2(255) DEFAULT NULL,
  hits number(12) DEFAULT NULL,
  isValid char(1) DEFAULT NULL,
  created number(14) DEFAULT NULL,
  lastModif number(14) DEFAULT NULL,
  cache blob,
  cache_timestamp number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_directory_sites PRIMARY KEY (siteId)
);

CREATE SEQUENCE sq_tiki_directory_sites_siteId;
CREATE OR REPLACE TRIGGER ai_tiki_directory_sites_siteId
BEFORE INSERT ON tiki_directory_sites
FOR EACH ROW WHEN (
 new.siteId IS NULL OR new.siteId = 0
)
BEGIN
 SELECT sq_tiki_directory_sites_siteId.nextval
 INTO :new.siteId
 FROM dual;
END;
/

--
-- Table: tiki_drawings
--

DROP TABLE tiki_drawings;
CREATE TABLE tiki_drawings (
  drawId number(12) CONSTRAINT nn_drawId NOT NULL,
  version number(8) DEFAULT NULL,
  name varchar2(250) DEFAULT NULL,
  filename_draw varchar2(250) DEFAULT NULL,
  filename_pad varchar2(250) DEFAULT NULL,
  timestamp number(14) DEFAULT NULL,
  user_ varchar2(200) DEFAULT NULL,
  CONSTRAINT pk_tiki_drawings PRIMARY KEY (drawId)
);

CREATE SEQUENCE sq_tiki_drawings_drawId;
CREATE OR REPLACE TRIGGER ai_tiki_drawings_drawId
BEFORE INSERT ON tiki_drawings
FOR EACH ROW WHEN (
 new.drawId IS NULL OR new.drawId = 0
)
BEGIN
 SELECT sq_tiki_drawings_drawId.nextval
 INTO :new.drawId
 FROM dual;
END;
/

--
-- Table: tiki_dsn
--

DROP TABLE tiki_dsn;
CREATE TABLE tiki_dsn (
  dsnId number(12) CONSTRAINT nn_dsnId NOT NULL,
  name varchar2(200) DEFAULT '' CONSTRAINT nn_name05 NOT NULL,
  dsn varchar2(255) DEFAULT NULL,
  CONSTRAINT pk_tiki_dsn PRIMARY KEY (dsnId)
);

CREATE SEQUENCE sq_tiki_dsn_dsnId;
CREATE OR REPLACE TRIGGER ai_tiki_dsn_dsnId
BEFORE INSERT ON tiki_dsn
FOR EACH ROW WHEN (
 new.dsnId IS NULL OR new.dsnId = 0
)
BEGIN
 SELECT sq_tiki_dsn_dsnId.nextval
 INTO :new.dsnId
 FROM dual;
END;
/

--
-- Table: tiki_eph
--

DROP TABLE tiki_eph;
CREATE TABLE tiki_eph (
  ephId number(12) CONSTRAINT nn_ephId NOT NULL,
  title varchar2(250) DEFAULT NULL,
  isFile char(1) DEFAULT NULL,
  filename varchar2(250) DEFAULT NULL,
  filetype varchar2(250) DEFAULT NULL,
  filesize varchar2(250) DEFAULT NULL,
  data blob,
  textdata blob,
  publish number(14) DEFAULT NULL,
  hits number(10) DEFAULT NULL,
  CONSTRAINT pk_tiki_eph PRIMARY KEY (ephId)
);

CREATE SEQUENCE sq_tiki_eph_ephId;
CREATE OR REPLACE TRIGGER ai_tiki_eph_ephId
BEFORE INSERT ON tiki_eph
FOR EACH ROW WHEN (
 new.ephId IS NULL OR new.ephId = 0
)
BEGIN
 SELECT sq_tiki_eph_ephId.nextval
 INTO :new.ephId
 FROM dual;
END;
/

--
-- Table: tiki_extwiki
--

DROP TABLE tiki_extwiki;
CREATE TABLE tiki_extwiki (
  extwikiId number(12) CONSTRAINT nn_extwikiId NOT NULL,
  name varchar2(200) DEFAULT '' CONSTRAINT nn_name06 NOT NULL,
  extwiki varchar2(255) DEFAULT NULL,
  CONSTRAINT pk_tiki_extwiki PRIMARY KEY (extwikiId)
);

CREATE SEQUENCE sq_tiki_extwiki_extwikiId;
CREATE OR REPLACE TRIGGER ai_tiki_extwiki_extwikiId
BEFORE INSERT ON tiki_extwiki
FOR EACH ROW WHEN (
 new.extwikiId IS NULL OR new.extwikiId = 0
)
BEGIN
 SELECT sq_tiki_extwiki_extwikiId.nextval
 INTO :new.extwikiId
 FROM dual;
END;
/

--
-- Table: tiki_faq_questions
--

DROP TABLE tiki_faq_questions;
CREATE TABLE tiki_faq_questions (
  questionId number(10) CONSTRAINT nn_questionId NOT NULL,
  faqId number(10) DEFAULT NULL,
  position number(4) DEFAULT NULL,
  question clob,
  answer clob,
  CONSTRAINT pk_tiki_faq_questions PRIMARY KEY (questionId)
);

CREATE SEQUENCE sq_tiki_faq_questions_question;
CREATE OR REPLACE TRIGGER ai_tiki_faq_questions_question
BEFORE INSERT ON tiki_faq_questions
FOR EACH ROW WHEN (
 new.questionId IS NULL OR new.questionId = 0
)
BEGIN
 SELECT sq_tiki_faq_questions_question.nextval
 INTO :new.questionId
 FROM dual;
END;
/

CREATE INDEX faqId_tiki_faq_questions on tiki_faq_questions (faqId);

CREATE INDEX question_tiki_faq_questions on tiki_faq_questions (question);

CREATE INDEX answer_tiki_faq_questions on tiki_faq_questions (answer);

--
-- Table: tiki_faqs
--

DROP TABLE tiki_faqs;
CREATE TABLE tiki_faqs (
  faqId number(10) CONSTRAINT nn_faqId NOT NULL,
  title varchar2(200) DEFAULT NULL,
  description clob,
  created number(14) DEFAULT NULL,
  questions number(5) DEFAULT NULL,
  hits number(8) DEFAULT NULL,
  canSuggest char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_faqs PRIMARY KEY (faqId)
);

CREATE SEQUENCE sq_tiki_faqs_faqId;
CREATE OR REPLACE TRIGGER ai_tiki_faqs_faqId
BEFORE INSERT ON tiki_faqs
FOR EACH ROW WHEN (
 new.faqId IS NULL OR new.faqId = 0
)
BEGIN
 SELECT sq_tiki_faqs_faqId.nextval
 INTO :new.faqId
 FROM dual;
END;
/

CREATE INDEX title_tiki_faqs on tiki_faqs (title);

CREATE INDEX description_tiki_faqs on tiki_faqs (description);

CREATE INDEX hits_tiki_faqs on tiki_faqs (hits);

--
-- Table: tiki_featured_links
--

DROP TABLE tiki_featured_links;
CREATE TABLE tiki_featured_links (
  url varchar2(200) DEFAULT '' CONSTRAINT nn_url NOT NULL,
  title varchar2(200) DEFAULT NULL,
  description clob,
  hits number(8) DEFAULT NULL,
  position number(6) DEFAULT NULL,
  type char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_featured_links PRIMARY KEY (url)
);

--
-- Table: tiki_file_galleries
--

DROP TABLE tiki_file_galleries;
CREATE TABLE tiki_file_galleries (
  galleryId number(14) CONSTRAINT nn_galleryId NOT NULL,
  name varchar2(80) DEFAULT '' CONSTRAINT nn_name07 NOT NULL,
  description clob,
  created number(14) DEFAULT NULL,
  visible char(1) DEFAULT NULL,
  lastModif number(14) DEFAULT NULL,
  user_ varchar2(200) DEFAULT NULL,
  hits number(14) DEFAULT NULL,
  votes number(8) DEFAULT NULL,
  points number(8, 2) DEFAULT NULL,
  maxRows number(10) DEFAULT NULL,
  public_ char(1) DEFAULT NULL,
  show_id char(1) DEFAULT NULL,
  show_icon char(1) DEFAULT NULL,
  show_name char(1) DEFAULT NULL,
  show_size char(1) DEFAULT NULL,
  show_description char(1) DEFAULT NULL,
  max_desc number(8) DEFAULT NULL,
  show_created char(1) DEFAULT NULL,
  show_dl char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_file_galleries PRIMARY KEY (galleryId)
);

CREATE SEQUENCE sq_tiki_file_galleries_gallery;
CREATE OR REPLACE TRIGGER ai_tiki_file_galleries_gallery
BEFORE INSERT ON tiki_file_galleries
FOR EACH ROW WHEN (
 new.galleryId IS NULL OR new.galleryId = 0
)
BEGIN
 SELECT sq_tiki_file_galleries_gallery.nextval
 INTO :new.galleryId
 FROM dual;
END;
/

--
-- Table: tiki_files
--

DROP TABLE tiki_files;
CREATE TABLE tiki_files (
  fileId number(14) CONSTRAINT nn_fileId NOT NULL,
  galleryId number(14) DEFAULT '0' CONSTRAINT nn_galleryId02 NOT NULL,
  name varchar2(200) DEFAULT '' CONSTRAINT nn_name08 NOT NULL,
  description clob,
  created number(14) DEFAULT NULL,
  filename varchar2(80) DEFAULT NULL,
  filesize number(14) DEFAULT NULL,
  filetype varchar2(250) DEFAULT NULL,
  data blob,
  user_ varchar2(200) DEFAULT NULL,
  downloads number(14) DEFAULT NULL,
  votes number(8) DEFAULT NULL,
  points number(8, 2) DEFAULT NULL,
  path varchar2(255) DEFAULT NULL,
  reference_url varchar2(250) DEFAULT NULL,
  is_reference char(1) DEFAULT NULL,
  hash varchar2(32) DEFAULT NULL,
  CONSTRAINT pk_tiki_files PRIMARY KEY (fileId)
);

CREATE SEQUENCE sq_tiki_files_fileId;
CREATE OR REPLACE TRIGGER ai_tiki_files_fileId
BEFORE INSERT ON tiki_files
FOR EACH ROW WHEN (
 new.fileId IS NULL OR new.fileId = 0
)
BEGIN
 SELECT sq_tiki_files_fileId.nextval
 INTO :new.fileId
 FROM dual;
END;
/

CREATE INDEX name_tiki_files on tiki_files (name);

CREATE INDEX description_tiki_files on tiki_files (description);

CREATE INDEX downloads_tiki_files on tiki_files (downloads);

--
-- Table: tiki_forum_attachments
--

DROP TABLE tiki_forum_attachments;
CREATE TABLE tiki_forum_attachments (
  attId number(14) CONSTRAINT nn_attId NOT NULL,
  threadId number(14) DEFAULT '0' CONSTRAINT nn_threadId02 NOT NULL,
  qId number(14) DEFAULT '0' CONSTRAINT nn_qId NOT NULL,
  forumId number(14) DEFAULT NULL,
  filename varchar2(250) DEFAULT NULL,
  filetype varchar2(250) DEFAULT NULL,
  filesize number(12) DEFAULT NULL,
  data blob,
  dir varchar2(200) DEFAULT NULL,
  created number(14) DEFAULT NULL,
  path varchar2(250) DEFAULT NULL,
  CONSTRAINT pk_tiki_forum_attachments PRIMARY KEY (attId)
);

CREATE SEQUENCE sq_tiki_forum_attachments_attI;
CREATE OR REPLACE TRIGGER ai_tiki_forum_attachments_attI
BEFORE INSERT ON tiki_forum_attachments
FOR EACH ROW WHEN (
 new.attId IS NULL OR new.attId = 0
)
BEGIN
 SELECT sq_tiki_forum_attachments_attI.nextval
 INTO :new.attId
 FROM dual;
END;
/

--
-- Table: tiki_forum_reads
--

DROP TABLE tiki_forum_reads;
CREATE TABLE tiki_forum_reads (
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_05 NOT NULL,
  threadId number(14) DEFAULT '0' CONSTRAINT nn_threadId03 NOT NULL,
  forumId number(14) DEFAULT NULL,
  timestamp number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_forum_reads PRIMARY KEY (user_, threadId)
);

--
-- Table: tiki_forums
--

DROP TABLE tiki_forums;
CREATE TABLE tiki_forums (
  forumId number(8) CONSTRAINT nn_forumId NOT NULL,
  name varchar2(200) DEFAULT NULL,
  description clob,
  created number(14) DEFAULT NULL,
  lastPost number(14) DEFAULT NULL,
  threads number(8) DEFAULT NULL,
  comments number(8) DEFAULT NULL,
  controlFlood char(1) DEFAULT NULL,
  floodInterval number(8) DEFAULT NULL,
  moderator varchar2(200) DEFAULT NULL,
  hits number(8) DEFAULT NULL,
  mail varchar2(200) DEFAULT NULL,
  useMail char(1) DEFAULT NULL,
  section varchar2(200) DEFAULT NULL,
  usePruneUnreplied char(1) DEFAULT NULL,
  pruneUnrepliedAge number(8) DEFAULT NULL,
  usePruneOld char(1) DEFAULT NULL,
  pruneMaxAge number(8) DEFAULT NULL,
  topicsPerPage number(6) DEFAULT NULL,
  topicOrdering varchar2(100) DEFAULT NULL,
  threadOrdering varchar2(100) DEFAULT NULL,
  att varchar2(80) DEFAULT NULL,
  att_store varchar2(4) DEFAULT NULL,
  att_store_dir varchar2(250) DEFAULT NULL,
  att_max_size number(12) DEFAULT NULL,
  ui_level char(1) DEFAULT NULL,
  forum_password varchar2(32) DEFAULT NULL,
  forum_use_password char(1) DEFAULT NULL,
  moderator_group varchar2(200) DEFAULT NULL,
  approval_type varchar2(20) DEFAULT NULL,
  outbound_address varchar2(250) DEFAULT NULL,
  outbound_from varchar(250) default NULL,
  inbound_pop_server varchar2(250) DEFAULT NULL,
  inbound_pop_port number(4) DEFAULT NULL,
  inbound_pop_user varchar2(200) DEFAULT NULL,
  inbound_pop_password varchar2(80) DEFAULT NULL,
  topic_smileys char(1) DEFAULT NULL,
  ui_avatar char(1) DEFAULT NULL,
  ui_flag char(1) DEFAULT NULL,
  ui_posts char(1) DEFAULT NULL,
  ui_email char(1) DEFAULT NULL,
  ui_online char(1) DEFAULT NULL,
  topic_summary char(1) DEFAULT NULL,
  show_description char(1) DEFAULT NULL,
  topics_list_replies char(1) DEFAULT NULL,
  topics_list_reads char(1) DEFAULT NULL,
  topics_list_pts char(1) DEFAULT NULL,
  topics_list_lastpost char(1) DEFAULT NULL,
  topics_list_author char(1) DEFAULT NULL,
  vote_threads char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_forums PRIMARY KEY (forumId)
);

CREATE SEQUENCE sq_tiki_forums_forumId;
CREATE OR REPLACE TRIGGER ai_tiki_forums_forumId
BEFORE INSERT ON tiki_forums
FOR EACH ROW WHEN (
 new.forumId IS NULL OR new.forumId = 0
)
BEGIN
 SELECT sq_tiki_forums_forumId.nextval
 INTO :new.forumId
 FROM dual;
END;
/

--
-- Table: tiki_forums_queue
--

DROP TABLE tiki_forums_queue;
CREATE TABLE tiki_forums_queue (
  qId number(14) CONSTRAINT nn_qId02 NOT NULL,
  object varchar2(32) DEFAULT NULL,
  parentId number(14) DEFAULT NULL,
  forumId number(14) DEFAULT NULL,
  timestamp number(14) DEFAULT NULL,
  user_ varchar2(200) DEFAULT NULL,
  title varchar2(240) DEFAULT NULL,
  data clob,
  type varchar2(60) DEFAULT NULL,
  hash varchar2(32) DEFAULT NULL,
  topic_smiley varchar2(80) DEFAULT NULL,
  topic_title varchar2(240) DEFAULT NULL,
  summary varchar2(240) DEFAULT NULL,
  CONSTRAINT pk_tiki_forums_queue PRIMARY KEY (qId)
);

CREATE SEQUENCE sq_tiki_forums_queue_qId;
CREATE OR REPLACE TRIGGER ai_tiki_forums_queue_qId
BEFORE INSERT ON tiki_forums_queue
FOR EACH ROW WHEN (
 new.qId IS NULL OR new.qId = 0
)
BEGIN
 SELECT sq_tiki_forums_queue_qId.nextval
 INTO :new.qId
 FROM dual;
END;
/

--
-- Table: tiki_forums_reported
--

DROP TABLE tiki_forums_reported;
CREATE TABLE tiki_forums_reported (
  threadId number(12) DEFAULT '0' CONSTRAINT nn_threadId04 NOT NULL,
  forumId number(12) DEFAULT '0' CONSTRAINT nn_forumId02 NOT NULL,
  parentId number(12) DEFAULT '0' CONSTRAINT nn_parentId NOT NULL,
  user_ varchar2(200) DEFAULT NULL,
  timestamp number(14) DEFAULT NULL,
  reason varchar2(250) DEFAULT NULL,
  CONSTRAINT pk_tiki_forums_reported PRIMARY KEY (threadId)
);

--
-- Table: tiki_galleries
--

DROP TABLE tiki_galleries;
CREATE TABLE tiki_galleries (
  galleryId number(14) CONSTRAINT nn_galleryId03 NOT NULL,
  name varchar2(80) DEFAULT '' CONSTRAINT nn_name09 NOT NULL,
  description clob,
  created number(14) DEFAULT NULL,
  lastModif number(14) DEFAULT NULL,
  visible char(1) DEFAULT NULL,
  theme varchar2(60) DEFAULT NULL,
  user_ varchar2(200) DEFAULT NULL,
  hits number(14) DEFAULT NULL,
  maxRows number(10) DEFAULT NULL,
  rowImages number(10) DEFAULT NULL,
  thumbSizeX number(10) DEFAULT NULL,
  thumbSizeY number(10) DEFAULT NULL,
  public_ char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_galleries PRIMARY KEY (galleryId)
);

CREATE SEQUENCE sq_tiki_galleries_galleryId;
CREATE OR REPLACE TRIGGER ai_tiki_galleries_galleryId
BEFORE INSERT ON tiki_galleries
FOR EACH ROW WHEN (
 new.galleryId IS NULL OR new.galleryId = 0
)
BEGIN
 SELECT sq_tiki_galleries_galleryId.nextval
 INTO :new.galleryId
 FROM dual;
END;
/

CREATE INDEX name_tiki_galleries on tiki_galleries (name);

CREATE INDEX description_tiki_galleries on tiki_galleries (description);

CREATE INDEX hits_tiki_galleries on tiki_galleries (hits);

--
-- Table: tiki_galleries_scales
--

DROP TABLE tiki_galleries_scales;
CREATE TABLE tiki_galleries_scales (
  galleryId number(14) DEFAULT '0' CONSTRAINT nn_galleryId04 NOT NULL,
  xsize number(11) DEFAULT '0' CONSTRAINT nn_xsize NOT NULL,
  ysize number(11) DEFAULT '0' CONSTRAINT nn_ysize NOT NULL,
  CONSTRAINT pk_tiki_galleries_scales PRIMARY KEY (galleryId, xsize, ysize)
);

--
-- Table: tiki_games
--

DROP TABLE tiki_games;
CREATE TABLE tiki_games (
  gameName varchar2(200) DEFAULT '' CONSTRAINT nn_gameName NOT NULL,
  hits number(8) DEFAULT NULL,
  votes number(8) DEFAULT NULL,
  points number(8) DEFAULT NULL,
  CONSTRAINT pk_tiki_games PRIMARY KEY (gameName)
);

--
-- Table: tiki_group_inclusion
--

DROP TABLE tiki_group_inclusion;
CREATE TABLE tiki_group_inclusion (
  groupName varchar2(30) DEFAULT '' CONSTRAINT nn_groupName NOT NULL,
  includeGroup varchar2(30) DEFAULT '' CONSTRAINT nn_includeGroup NOT NULL,
  CONSTRAINT pk_tiki_group_inclusion PRIMARY KEY (groupName, includeGroup)
);

--
-- Table: tiki_history
--

DROP TABLE tiki_history;
CREATE TABLE tiki_history (
  pageName varchar2(160) DEFAULT '' CONSTRAINT nn_pageName NOT NULL,
  version number(8) DEFAULT '0' CONSTRAINT nn_version NOT NULL,
  lastModif number(14) DEFAULT NULL,
  description varchar2(200) DEFAULT NULL,
  user_ varchar2(200) DEFAULT NULL,
  ip varchar2(15) DEFAULT NULL,
  comment_ varchar2(200) DEFAULT NULL,
  data blob,
  CONSTRAINT pk_tiki_history PRIMARY KEY (pageName, version)
);

--
-- Table: tiki_hotwords
--

DROP TABLE tiki_hotwords;
CREATE TABLE tiki_hotwords (
  word varchar2(40) DEFAULT '' CONSTRAINT nn_word NOT NULL,
  url varchar2(255) DEFAULT '' CONSTRAINT nn_url02 NOT NULL,
  CONSTRAINT pk_tiki_hotwords PRIMARY KEY (word)
);

--
-- Table: tiki_html_pages
--

DROP TABLE tiki_html_pages;
CREATE TABLE tiki_html_pages (
  pageName varchar2(200) DEFAULT '' CONSTRAINT nn_pageName02 NOT NULL,
  content blob,
  refresh number(10) DEFAULT NULL,
  type char(1) DEFAULT NULL,
  created number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_html_pages PRIMARY KEY (pageName)
);

--
-- Table: tiki_html_pages_dynamic_zones
--

DROP TABLE tiki_html_pages_dynamic_zones;
CREATE TABLE tiki_html_pages_dynamic_zones (
  pageName varchar2(40) DEFAULT '' CONSTRAINT nn_pageName03 NOT NULL,
  zone varchar2(80) DEFAULT '' CONSTRAINT nn_zone NOT NULL,
  type char(2) DEFAULT NULL,
  content clob,
  CONSTRAINT pk_tiki_html_pages_dynamic_zon PRIMARY KEY (pageName, zone)
);

--
-- Table: tiki_images
--

DROP TABLE tiki_images;
CREATE TABLE tiki_images (
  imageId number(14) CONSTRAINT nn_imageId NOT NULL,
  galleryId number(14) DEFAULT '0' CONSTRAINT nn_galleryId05 NOT NULL,
  name varchar2(200) DEFAULT '' CONSTRAINT nn_name10 NOT NULL,
  description clob,
  created number(14) DEFAULT NULL,
  user_ varchar2(200) DEFAULT NULL,
  hits number(14) DEFAULT NULL,
  path varchar2(255) DEFAULT NULL,
  CONSTRAINT pk_tiki_images PRIMARY KEY (imageId)
);

CREATE SEQUENCE sq_tiki_images_imageId;
CREATE OR REPLACE TRIGGER ai_tiki_images_imageId
BEFORE INSERT ON tiki_images
FOR EACH ROW WHEN (
 new.imageId IS NULL OR new.imageId = 0
)
BEGIN
 SELECT sq_tiki_images_imageId.nextval
 INTO :new.imageId
 FROM dual;
END;
/

CREATE INDEX name_tiki_images on tiki_images (name);

CREATE INDEX description_tiki_images on tiki_images (description);

CREATE INDEX hits_tiki_images on tiki_images (hits);

CREATE INDEX ti_gId_tiki_images on tiki_images (galleryId);

CREATE INDEX ti_cr_tiki_images on tiki_images (created);

CREATE INDEX ti_us_tiki_images on tiki_images (user_);

--
-- Table: tiki_images_data
--

DROP TABLE tiki_images_data;
CREATE TABLE tiki_images_data (
  imageId number(14) DEFAULT '0' CONSTRAINT nn_imageId02 NOT NULL,
  xsize number(8) DEFAULT '0' CONSTRAINT nn_xsize02 NOT NULL,
  ysize number(8) DEFAULT '0' CONSTRAINT nn_ysize02 NOT NULL,
  type char(1) DEFAULT '' CONSTRAINT nn_type NOT NULL,
  filesize number(14) DEFAULT NULL,
  filetype varchar2(80) DEFAULT NULL,
  filename varchar2(80) DEFAULT NULL,
  data blob,
  CONSTRAINT pk_tiki_images_data PRIMARY KEY (imageId, xsize, ysize, type)
);

CREATE INDEX t_i_d_it_tiki_images_data on tiki_images_data (imageId, type);

--
-- Table: tiki_language
--

DROP TABLE tiki_language;
CREATE TABLE tiki_language (
  source blob CONSTRAINT nn_source NOT NULL,
  lang char(2) DEFAULT '' CONSTRAINT nn_lang02 NOT NULL,
  tran blob,
  CONSTRAINT pk_tiki_language PRIMARY KEY (source, lang)
);

--
-- Table: tiki_languages
--

DROP TABLE tiki_languages;
CREATE TABLE tiki_languages (
  lang char(2) DEFAULT '' CONSTRAINT nn_lang03 NOT NULL,
  language varchar2(255) DEFAULT NULL,
  CONSTRAINT pk_tiki_languages PRIMARY KEY (lang)
);

--
-- Table: tiki_link_cache
--

DROP TABLE tiki_link_cache;
CREATE TABLE tiki_link_cache (
  cacheId number(14) CONSTRAINT nn_cacheId NOT NULL,
  url varchar2(250) DEFAULT NULL,
  data blob,
  refresh number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_link_cache PRIMARY KEY (cacheId)
);

CREATE SEQUENCE sq_tiki_link_cache_cacheId;
CREATE OR REPLACE TRIGGER ai_tiki_link_cache_cacheId
BEFORE INSERT ON tiki_link_cache
FOR EACH ROW WHEN (
 new.cacheId IS NULL OR new.cacheId = 0
)
BEGIN
 SELECT sq_tiki_link_cache_cacheId.nextval
 INTO :new.cacheId
 FROM dual;
END;
/

--
-- Table: tiki_links
--

DROP TABLE tiki_links;
CREATE TABLE tiki_links (
  fromPage varchar2(160) DEFAULT '' CONSTRAINT nn_fromPage NOT NULL,
  toPage varchar2(160) DEFAULT '' CONSTRAINT nn_toPage NOT NULL,
  CONSTRAINT pk_tiki_links PRIMARY KEY (fromPage, toPage)
);

--
-- Table: tiki_live_support_events
--

DROP TABLE tiki_live_support_events;
CREATE TABLE tiki_live_support_events (
  eventId number(14) CONSTRAINT nn_eventId NOT NULL,
  reqId varchar2(32) DEFAULT '' CONSTRAINT nn_reqId NOT NULL,
  type varchar2(40) DEFAULT NULL,
  seqId number(14) DEFAULT NULL,
  senderId varchar2(32) DEFAULT NULL,
  data clob,
  timestamp number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_live_support_events PRIMARY KEY (eventId)
);

CREATE SEQUENCE sq_tiki_live_support_events_ev;
CREATE OR REPLACE TRIGGER ai_tiki_live_support_events_ev
BEFORE INSERT ON tiki_live_support_events
FOR EACH ROW WHEN (
 new.eventId IS NULL OR new.eventId = 0
)
BEGIN
 SELECT sq_tiki_live_support_events_ev.nextval
 INTO :new.eventId
 FROM dual;
END;
/

--
-- Table: tiki_live_support_message_comm
--

DROP TABLE tiki_live_support_message_comm;
CREATE TABLE tiki_live_support_message_comm (
  cId number(12) CONSTRAINT nn_cId02 NOT NULL,
  msgId number(12) DEFAULT NULL,
  data clob,
  timestamp number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_live_support_message_c PRIMARY KEY (cId)
);

CREATE SEQUENCE sq_tiki_live_support_message_c;
CREATE OR REPLACE TRIGGER ai_tiki_live_support_message_c
BEFORE INSERT ON tiki_live_support_message_comm
FOR EACH ROW WHEN (
 new.cId IS NULL OR new.cId = 0
)
BEGIN
 SELECT sq_tiki_live_support_message_c.nextval
 INTO :new.cId
 FROM dual;
END;
/

--
-- Table: tiki_live_support_messages
--

DROP TABLE tiki_live_support_messages;
CREATE TABLE tiki_live_support_messages (
  msgId number(12) CONSTRAINT nn_msgId02 NOT NULL,
  data clob,
  timestamp number(14) DEFAULT NULL,
  user_ varchar2(200) DEFAULT NULL,
  username varchar2(200) DEFAULT NULL,
  priority number(2) DEFAULT NULL,
  status char(1) DEFAULT NULL,
  assigned_to varchar2(200) DEFAULT NULL,
  resolution varchar2(100) DEFAULT NULL,
  title varchar2(200) DEFAULT NULL,
  module number(4) DEFAULT NULL,
  email varchar2(250) DEFAULT NULL,
  CONSTRAINT pk_tiki_live_support_messages PRIMARY KEY (msgId)
);

CREATE SEQUENCE sq_tiki_live_support_messages_;
CREATE OR REPLACE TRIGGER ai_tiki_live_support_messages_
BEFORE INSERT ON tiki_live_support_messages
FOR EACH ROW WHEN (
 new.msgId IS NULL OR new.msgId = 0
)
BEGIN
 SELECT sq_tiki_live_support_messages_.nextval
 INTO :new.msgId
 FROM dual;
END;
/

--
-- Table: tiki_live_support_modules
--

DROP TABLE tiki_live_support_modules;
CREATE TABLE tiki_live_support_modules (
  modId number(4) CONSTRAINT nn_modId NOT NULL,
  name varchar2(90) DEFAULT NULL,
  CONSTRAINT pk_tiki_live_support_modules PRIMARY KEY (modId)
);

CREATE SEQUENCE sq_tiki_live_support_modules_m;
CREATE OR REPLACE TRIGGER ai_tiki_live_support_modules_m
BEFORE INSERT ON tiki_live_support_modules
FOR EACH ROW WHEN (
 new.modId IS NULL OR new.modId = 0
)
BEGIN
 SELECT sq_tiki_live_support_modules_m.nextval
 INTO :new.modId
 FROM dual;
END;
/

--
-- Table: tiki_live_support_operators
--

DROP TABLE tiki_live_support_operators;
CREATE TABLE tiki_live_support_operators (
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_06 NOT NULL,
  accepted_requests number(10) DEFAULT NULL,
  status varchar2(20) DEFAULT NULL,
  longest_chat number(10) DEFAULT NULL,
  shortest_chat number(10) DEFAULT NULL,
  average_chat number(10) DEFAULT NULL,
  last_chat number(14) DEFAULT NULL,
  time_online number(10) DEFAULT NULL,
  votes number(10) DEFAULT NULL,
  points number(10) DEFAULT NULL,
  status_since number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_live_support_operators PRIMARY KEY (user_)
);

--
-- Table: tiki_live_support_requests
--

DROP TABLE tiki_live_support_requests;
CREATE TABLE tiki_live_support_requests (
  reqId varchar2(32) DEFAULT '' CONSTRAINT nn_reqId02 NOT NULL,
  user_ varchar2(200) DEFAULT NULL,
  tiki_user varchar2(200) DEFAULT NULL,
  email varchar2(200) DEFAULT NULL,
  operator varchar2(200) DEFAULT NULL,
  operator_id varchar2(32) DEFAULT NULL,
  user_id varchar2(32) DEFAULT NULL,
  reason clob,
  req_timestamp number(14) DEFAULT NULL,
  timestamp number(14) DEFAULT NULL,
  status varchar2(40) DEFAULT NULL,
  resolution varchar2(40) DEFAULT NULL,
  chat_started number(14) DEFAULT NULL,
  chat_ended number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_live_support_requests PRIMARY KEY (reqId)
);

--
-- Table: tiki_mail_events
--

DROP TABLE tiki_mail_events;
CREATE TABLE tiki_mail_events (
  event varchar2(200) DEFAULT NULL,
  object varchar2(200) DEFAULT NULL,
  email varchar2(200) DEFAULT NULL
);

--
-- Table: tiki_mailin_accounts
--

DROP TABLE tiki_mailin_accounts;
CREATE TABLE tiki_mailin_accounts (
  accountId number(12) CONSTRAINT nn_accountId NOT NULL,
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_07 NOT NULL,
  account varchar2(50) DEFAULT '' CONSTRAINT nn_account NOT NULL,
  pop varchar2(255) DEFAULT NULL,
  port number(4) DEFAULT NULL,
  username varchar2(100) DEFAULT NULL,
  pass varchar2(100) DEFAULT NULL,
  active char(1) DEFAULT NULL,
  type varchar2(40) DEFAULT NULL,
  smtp varchar2(255) DEFAULT NULL,
  useAuth char(1) DEFAULT NULL,
  smtpPort number(4) DEFAULT NULL,
  CONSTRAINT pk_tiki_mailin_accounts PRIMARY KEY (accountId)
);

CREATE SEQUENCE sq_tiki_mailin_accounts_accoun;
CREATE OR REPLACE TRIGGER ai_tiki_mailin_accounts_accoun
BEFORE INSERT ON tiki_mailin_accounts
FOR EACH ROW WHEN (
 new.accountId IS NULL OR new.accountId = 0
)
BEGIN
 SELECT sq_tiki_mailin_accounts_accoun.nextval
 INTO :new.accountId
 FROM dual;
END;
/

--
-- Table: tiki_menu_languages
--

DROP TABLE tiki_menu_languages;
CREATE TABLE tiki_menu_languages (
  menuId number(8) CONSTRAINT nn_menuId NOT NULL,
  language char(2) DEFAULT '' CONSTRAINT nn_language NOT NULL,
  CONSTRAINT pk_tiki_menu_languages PRIMARY KEY (menuId, language)
);

CREATE SEQUENCE sq_tiki_menu_languages_menuId;
CREATE OR REPLACE TRIGGER ai_tiki_menu_languages_menuId
BEFORE INSERT ON tiki_menu_languages
FOR EACH ROW WHEN (
 new.menuId IS NULL OR new.menuId = 0
)
BEGIN
 SELECT sq_tiki_menu_languages_menuId.nextval
 INTO :new.menuId
 FROM dual;
END;
/

--
-- Table: tiki_menu_options
--

DROP TABLE tiki_menu_options;
CREATE TABLE tiki_menu_options (
  optionId number(8) CONSTRAINT nn_optionId NOT NULL,
  menuId number(8) DEFAULT NULL,
  type char(1) DEFAULT NULL,
  name varchar2(200) DEFAULT NULL,
  url varchar2(255) DEFAULT NULL,
  position number(4) DEFAULT NULL,
  CONSTRAINT pk_tiki_menu_options PRIMARY KEY (optionId)
);

CREATE SEQUENCE sq_tiki_menu_options_optionId;
CREATE OR REPLACE TRIGGER ai_tiki_menu_options_optionId
BEFORE INSERT ON tiki_menu_options
FOR EACH ROW WHEN (
 new.optionId IS NULL OR new.optionId = 0
)
BEGIN
 SELECT sq_tiki_menu_options_optionId.nextval
 INTO :new.optionId
 FROM dual;
END;
/

--
-- Table: tiki_menus
--

DROP TABLE tiki_menus;
CREATE TABLE tiki_menus (
  menuId number(8) CONSTRAINT nn_menuId02 NOT NULL,
  name varchar2(200) DEFAULT '' CONSTRAINT nn_name11 NOT NULL,
  description clob,
  type char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_menus PRIMARY KEY (menuId)
);

CREATE SEQUENCE sq_tiki_menus_menuId;
CREATE OR REPLACE TRIGGER ai_tiki_menus_menuId
BEFORE INSERT ON tiki_menus
FOR EACH ROW WHEN (
 new.menuId IS NULL OR new.menuId = 0
)
BEGIN
 SELECT sq_tiki_menus_menuId.nextval
 INTO :new.menuId
 FROM dual;
END;
/

--
-- Table: tiki_minical_events
--

DROP TABLE tiki_minical_events;
CREATE TABLE tiki_minical_events (
  user_ varchar2(200) DEFAULT NULL,
  eventId number(12) CONSTRAINT nn_eventId02 NOT NULL,
  title varchar2(250) DEFAULT NULL,
  description clob,
  start_ number(14) DEFAULT NULL,
  end number(14) DEFAULT NULL,
  security char(1) DEFAULT NULL,
  duration number(3) DEFAULT NULL,
  topicId number(12) DEFAULT NULL,
  reminded char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_minical_events PRIMARY KEY (eventId)
);

CREATE SEQUENCE sq_tiki_minical_events_eventId;
CREATE OR REPLACE TRIGGER ai_tiki_minical_events_eventId
BEFORE INSERT ON tiki_minical_events
FOR EACH ROW WHEN (
 new.eventId IS NULL OR new.eventId = 0
)
BEGIN
 SELECT sq_tiki_minical_events_eventId.nextval
 INTO :new.eventId
 FROM dual;
END;
/

--
-- Table: tiki_minical_topics
--

DROP TABLE tiki_minical_topics;
CREATE TABLE tiki_minical_topics (
  user_ varchar2(200) DEFAULT NULL,
  topicId number(12) CONSTRAINT nn_topicId NOT NULL,
  name varchar2(250) DEFAULT NULL,
  filename varchar2(200) DEFAULT NULL,
  filetype varchar2(200) DEFAULT NULL,
  filesize varchar2(200) DEFAULT NULL,
  data blob,
  path varchar2(250) DEFAULT NULL,
  isIcon char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_minical_topics PRIMARY KEY (topicId)
);

CREATE SEQUENCE sq_tiki_minical_topics_topicId;
CREATE OR REPLACE TRIGGER ai_tiki_minical_topics_topicId
BEFORE INSERT ON tiki_minical_topics
FOR EACH ROW WHEN (
 new.topicId IS NULL OR new.topicId = 0
)
BEGIN
 SELECT sq_tiki_minical_topics_topicId.nextval
 INTO :new.topicId
 FROM dual;
END;
/

--
-- Table: tiki_modules
--

DROP TABLE tiki_modules;
CREATE TABLE tiki_modules (
  name varchar2(200) DEFAULT '' CONSTRAINT nn_name12 NOT NULL,
  position char(1) DEFAULT NULL,
  ord number(4) DEFAULT NULL,
  type char(1) DEFAULT NULL,
  title varchar2(40) DEFAULT NULL,
  cache_time number(14) DEFAULT NULL,
  rows_ number(4) DEFAULT NULL,
  params varchar2(255) DEFAULT NULL,
  groups clob,
  CONSTRAINT pk_tiki_modules PRIMARY KEY (name)
);

--
-- Table: tiki_newsletter_subscriptions
--

DROP TABLE tiki_newsletter_subscriptions;
CREATE TABLE tiki_newsletter_subscriptions (
  nlId number(12) DEFAULT '0' CONSTRAINT nn_nlId NOT NULL,
  email varchar2(255) DEFAULT '' CONSTRAINT nn_email NOT NULL,
  code varchar2(32) DEFAULT NULL,
  valid char(1) DEFAULT NULL,
  subscribed number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_newsletter_subscriptio PRIMARY KEY (nlId, email)
);

--
-- Table: tiki_newsletters
--

DROP TABLE tiki_newsletters;
CREATE TABLE tiki_newsletters (
  nlId number(12) CONSTRAINT nn_nlId02 NOT NULL,
  name varchar2(200) DEFAULT NULL,
  description clob,
  created number(14) DEFAULT NULL,
  lastSent number(14) DEFAULT NULL,
  editions number(10) DEFAULT NULL,
  users number(10) DEFAULT NULL,
  allowAnySub char(1) DEFAULT NULL,
  frequency number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_newsletters PRIMARY KEY (nlId)
);

CREATE SEQUENCE sq_tiki_newsletters_nlId;
CREATE OR REPLACE TRIGGER ai_tiki_newsletters_nlId
BEFORE INSERT ON tiki_newsletters
FOR EACH ROW WHEN (
 new.nlId IS NULL OR new.nlId = 0
)
BEGIN
 SELECT sq_tiki_newsletters_nlId.nextval
 INTO :new.nlId
 FROM dual;
END;
/

--
-- Table: tiki_newsreader_marks
--

DROP TABLE tiki_newsreader_marks;
CREATE TABLE tiki_newsreader_marks (
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_08 NOT NULL,
  serverId number(12) DEFAULT '0' CONSTRAINT nn_serverId NOT NULL,
  groupName varchar2(255) DEFAULT '' CONSTRAINT nn_groupName02 NOT NULL,
  timestamp number(14) DEFAULT '0' CONSTRAINT nn_timestamp02 NOT NULL,
  CONSTRAINT pk_tiki_newsreader_marks PRIMARY KEY (user_, serverId, groupName)
);

--
-- Table: tiki_newsreader_servers
--

DROP TABLE tiki_newsreader_servers;
CREATE TABLE tiki_newsreader_servers (
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_09 NOT NULL,
  serverId number(12) CONSTRAINT nn_serverId02 NOT NULL,
  server varchar2(250) DEFAULT NULL,
  port number(4) DEFAULT NULL,
  username varchar2(200) DEFAULT NULL,
  password varchar2(200) DEFAULT NULL,
  CONSTRAINT pk_tiki_newsreader_servers PRIMARY KEY (serverId)
);

CREATE SEQUENCE sq_tiki_newsreader_servers_ser;
CREATE OR REPLACE TRIGGER ai_tiki_newsreader_servers_ser
BEFORE INSERT ON tiki_newsreader_servers
FOR EACH ROW WHEN (
 new.serverId IS NULL OR new.serverId = 0
)
BEGIN
 SELECT sq_tiki_newsreader_servers_ser.nextval
 INTO :new.serverId
 FROM dual;
END;
/

--
-- Table: tiki_page_footnotes
--

DROP TABLE tiki_page_footnotes;
CREATE TABLE tiki_page_footnotes (
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_10 NOT NULL,
  pageName varchar2(250) DEFAULT '' CONSTRAINT nn_pageName04 NOT NULL,
  data clob,
  CONSTRAINT pk_tiki_page_footnotes PRIMARY KEY (user_, pageName)
);

--
-- Table: tiki_pages
--

DROP TABLE tiki_pages;
CREATE TABLE tiki_pages (
  pageName varchar2(160) DEFAULT '' CONSTRAINT nn_pageName05 NOT NULL,
  hits number(8) DEFAULT NULL,
  data clob,
  description varchar2(200) DEFAULT NULL,
  lastModif number(14) DEFAULT NULL,
  comment_ varchar2(200) DEFAULT NULL,
  version number(8) DEFAULT '0' CONSTRAINT nn_version02 NOT NULL,
  user_ varchar2(200) DEFAULT NULL,
  ip varchar2(15) DEFAULT NULL,
  flag char(1) DEFAULT NULL,
  points number(8) DEFAULT NULL,
  votes number(8) DEFAULT NULL,
  cache clob,
  cache_timestamp number(14) DEFAULT NULL,
  pageRank number(4, 3) DEFAULT NULL,
  creator varchar2(200) DEFAULT NULL,
  CONSTRAINT pk_tiki_pages PRIMARY KEY (pageName)
);

CREATE INDEX data_tiki_pages on tiki_pages (data);

CREATE INDEX pageRank_tiki_pages on tiki_pages (pageRank);

--
-- Table: tiki_pageviews
--

DROP TABLE tiki_pageviews;
CREATE TABLE tiki_pageviews (
  day number(14) DEFAULT '0' CONSTRAINT nn_day02 NOT NULL,
  pageviews number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_pageviews PRIMARY KEY (day)
);

--
-- Table: tiki_poll_options
--

DROP TABLE tiki_poll_options;
CREATE TABLE tiki_poll_options (
  pollId number(8) DEFAULT '0' CONSTRAINT nn_pollId NOT NULL,
  optionId number(8) CONSTRAINT nn_optionId02 NOT NULL,
  title varchar2(200) DEFAULT NULL,
  votes number(8) DEFAULT NULL,
  CONSTRAINT pk_tiki_poll_options PRIMARY KEY (optionId)
);

CREATE SEQUENCE sq_tiki_poll_options_optionId;
CREATE OR REPLACE TRIGGER ai_tiki_poll_options_optionId
BEFORE INSERT ON tiki_poll_options
FOR EACH ROW WHEN (
 new.optionId IS NULL OR new.optionId = 0
)
BEGIN
 SELECT sq_tiki_poll_options_optionId.nextval
 INTO :new.optionId
 FROM dual;
END;
/

--
-- Table: tiki_polls
--

DROP TABLE tiki_polls;
CREATE TABLE tiki_polls (
  pollId number(8) CONSTRAINT nn_pollId02 NOT NULL,
  title varchar2(200) DEFAULT NULL,
  votes number(8) DEFAULT NULL,
  active char(1) DEFAULT NULL,
  publishDate number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_polls PRIMARY KEY (pollId)
);

CREATE SEQUENCE sq_tiki_polls_pollId;
CREATE OR REPLACE TRIGGER ai_tiki_polls_pollId
BEFORE INSERT ON tiki_polls
FOR EACH ROW WHEN (
 new.pollId IS NULL OR new.pollId = 0
)
BEGIN
 SELECT sq_tiki_polls_pollId.nextval
 INTO :new.pollId
 FROM dual;
END;
/

--
-- Table: tiki_preferences
--

DROP TABLE tiki_preferences;
CREATE TABLE tiki_preferences (
  name varchar2(40) DEFAULT '' CONSTRAINT nn_name13 NOT NULL,
  value varchar2(250) DEFAULT NULL,
  CONSTRAINT pk_tiki_preferences PRIMARY KEY (name)
);

--
-- Table: tiki_private_messages
--

DROP TABLE tiki_private_messages;
CREATE TABLE tiki_private_messages (
  messageId number(8) CONSTRAINT nn_messageId02 NOT NULL,
  toNickname varchar2(200) DEFAULT '' CONSTRAINT nn_toNickname NOT NULL,
  data varchar2(255) DEFAULT NULL,
  poster varchar2(200) DEFAULT 'anonymous' CONSTRAINT nn_poster02 NOT NULL,
  timestamp number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_private_messages PRIMARY KEY (messageId)
);

CREATE SEQUENCE sq_tiki_private_messages_messa;
CREATE OR REPLACE TRIGGER ai_tiki_private_messages_messa
BEFORE INSERT ON tiki_private_messages
FOR EACH ROW WHEN (
 new.messageId IS NULL OR new.messageId = 0
)
BEGIN
 SELECT sq_tiki_private_messages_messa.nextval
 INTO :new.messageId
 FROM dual;
END;
/

--
-- Table: tiki_programmed_content
--

DROP TABLE tiki_programmed_content;
CREATE TABLE tiki_programmed_content (
  pId number(8) CONSTRAINT nn_pId07 NOT NULL,
  contentId number(8) DEFAULT '0' CONSTRAINT nn_contentId02 NOT NULL,
  publishDate number(14) DEFAULT '0' CONSTRAINT nn_publishDate NOT NULL,
  data clob,
  CONSTRAINT pk_tiki_programmed_content PRIMARY KEY (pId)
);

CREATE SEQUENCE sq_tiki_programmed_content_pId;
CREATE OR REPLACE TRIGGER ai_tiki_programmed_content_pId
BEFORE INSERT ON tiki_programmed_content
FOR EACH ROW WHEN (
 new.pId IS NULL OR new.pId = 0
)
BEGIN
 SELECT sq_tiki_programmed_content_pId.nextval
 INTO :new.pId
 FROM dual;
END;
/

--
-- Table: tiki_quiz_question_options
--

DROP TABLE tiki_quiz_question_options;
CREATE TABLE tiki_quiz_question_options (
  optionId number(10) CONSTRAINT nn_optionId03 NOT NULL,
  questionId number(10) DEFAULT NULL,
  optionText clob,
  points number(4) DEFAULT NULL,
  CONSTRAINT pk_tiki_quiz_question_options PRIMARY KEY (optionId)
);

CREATE SEQUENCE sq_tiki_quiz_question_options_;
CREATE OR REPLACE TRIGGER ai_tiki_quiz_question_options_
BEFORE INSERT ON tiki_quiz_question_options
FOR EACH ROW WHEN (
 new.optionId IS NULL OR new.optionId = 0
)
BEGIN
 SELECT sq_tiki_quiz_question_options_.nextval
 INTO :new.optionId
 FROM dual;
END;
/

--
-- Table: tiki_quiz_questions
--

DROP TABLE tiki_quiz_questions;
CREATE TABLE tiki_quiz_questions (
  questionId number(10) CONSTRAINT nn_questionId02 NOT NULL,
  quizId number(10) DEFAULT NULL,
  question clob,
  position number(4) DEFAULT NULL,
  type char(1) DEFAULT NULL,
  maxPoints number(4) DEFAULT NULL,
  CONSTRAINT pk_tiki_quiz_questions PRIMARY KEY (questionId)
);

CREATE SEQUENCE sq_tiki_quiz_questions_questio;
CREATE OR REPLACE TRIGGER ai_tiki_quiz_questions_questio
BEFORE INSERT ON tiki_quiz_questions
FOR EACH ROW WHEN (
 new.questionId IS NULL OR new.questionId = 0
)
BEGIN
 SELECT sq_tiki_quiz_questions_questio.nextval
 INTO :new.questionId
 FROM dual;
END;
/

--
-- Table: tiki_quiz_results
--

DROP TABLE tiki_quiz_results;
CREATE TABLE tiki_quiz_results (
  resultId number(10) CONSTRAINT nn_resultId NOT NULL,
  quizId number(10) DEFAULT NULL,
  fromPoints number(4) DEFAULT NULL,
  toPoints number(4) DEFAULT NULL,
  answer clob,
  CONSTRAINT pk_tiki_quiz_results PRIMARY KEY (resultId)
);

CREATE SEQUENCE sq_tiki_quiz_results_resultId;
CREATE OR REPLACE TRIGGER ai_tiki_quiz_results_resultId
BEFORE INSERT ON tiki_quiz_results
FOR EACH ROW WHEN (
 new.resultId IS NULL OR new.resultId = 0
)
BEGIN
 SELECT sq_tiki_quiz_results_resultId.nextval
 INTO :new.resultId
 FROM dual;
END;
/

--
-- Table: tiki_quiz_stats
--

DROP TABLE tiki_quiz_stats;
CREATE TABLE tiki_quiz_stats (
  quizId number(10) DEFAULT '0' CONSTRAINT nn_quizId NOT NULL,
  questionId number(10) DEFAULT '0' CONSTRAINT nn_questionId03 NOT NULL,
  optionId number(10) DEFAULT '0' CONSTRAINT nn_optionId04 NOT NULL,
  votes number(10) DEFAULT NULL,
  CONSTRAINT pk_tiki_quiz_stats PRIMARY KEY (quizId, questionId, optionId)
);

--
-- Table: tiki_quiz_stats_sum
--

DROP TABLE tiki_quiz_stats_sum;
CREATE TABLE tiki_quiz_stats_sum (
  quizId number(10) DEFAULT '0' CONSTRAINT nn_quizId02 NOT NULL,
  quizName varchar2(255) DEFAULT NULL,
  timesTaken number(10) DEFAULT NULL,
  avgpoints number(5, 2) DEFAULT NULL,
  avgavg number(5, 2) DEFAULT NULL,
  avgtime number(5, 2) DEFAULT NULL,
  CONSTRAINT pk_tiki_quiz_stats_sum PRIMARY KEY (quizId)
);

--
-- Table: tiki_quizzes
--

DROP TABLE tiki_quizzes;
CREATE TABLE tiki_quizzes (
  quizId number(10) CONSTRAINT nn_quizId03 NOT NULL,
  name varchar2(255) DEFAULT NULL,
  description clob,
  canRepeat char(1) DEFAULT NULL,
  storeResults char(1) DEFAULT NULL,
  questionsPerPage number(4) DEFAULT NULL,
  timeLimited char(1) DEFAULT NULL,
  timeLimit number(14) DEFAULT NULL,
  created number(14) DEFAULT NULL,
  taken number(10) DEFAULT NULL,
  CONSTRAINT pk_tiki_quizzes PRIMARY KEY (quizId)
);

CREATE SEQUENCE sq_tiki_quizzes_quizId;
CREATE OR REPLACE TRIGGER ai_tiki_quizzes_quizId
BEFORE INSERT ON tiki_quizzes
FOR EACH ROW WHEN (
 new.quizId IS NULL OR new.quizId = 0
)
BEGIN
 SELECT sq_tiki_quizzes_quizId.nextval
 INTO :new.quizId
 FROM dual;
END;
/

--
-- Table: tiki_received_articles
--

DROP TABLE tiki_received_articles;
CREATE TABLE tiki_received_articles (
  receivedArticleId number(14) CONSTRAINT nn_receivedArticleId NOT NULL,
  receivedFromSite varchar2(200) DEFAULT NULL,
  receivedFromUser varchar2(200) DEFAULT NULL,
  receivedDate number(14) DEFAULT NULL,
  title varchar2(80) DEFAULT NULL,
  authorName varchar2(60) DEFAULT NULL,
  size_ number(12) DEFAULT NULL,
  useImage char(1) DEFAULT NULL,
  image_name varchar2(80) DEFAULT NULL,
  image_type varchar2(80) DEFAULT NULL,
  image_size number(14) DEFAULT NULL,
  image_x number(4) DEFAULT NULL,
  image_y number(4) DEFAULT NULL,
  image_data blob,
  publishDate number(14) DEFAULT NULL,
  created number(14) DEFAULT NULL,
  heading clob,
  body blob,
  hash varchar2(32) DEFAULT NULL,
  author varchar2(200) DEFAULT NULL,
  type varchar2(50) DEFAULT NULL,
  rating number(3, 2) DEFAULT NULL,
  CONSTRAINT pk_tiki_received_articles PRIMARY KEY (receivedArticleId)
);

CREATE SEQUENCE sq_tiki_received_articles_rece;
CREATE OR REPLACE TRIGGER ai_tiki_received_articles_rece
BEFORE INSERT ON tiki_received_articles
FOR EACH ROW WHEN (
 new.receivedArticleId IS NULL OR new.receivedArticleId = 0
)
BEGIN
 SELECT sq_tiki_received_articles_rece.nextval
 INTO :new.receivedArticleId
 FROM dual;
END;
/

--
-- Table: tiki_received_pages
--

DROP TABLE tiki_received_pages;
CREATE TABLE tiki_received_pages (
  receivedPageId number(14) CONSTRAINT nn_receivedPageId NOT NULL,
  pageName varchar2(160) DEFAULT '' CONSTRAINT nn_pageName06 NOT NULL,
  data blob,
  description varchar2(200) DEFAULT NULL,
  comment_ varchar2(200) DEFAULT NULL,
  receivedFromSite varchar2(200) DEFAULT NULL,
  receivedFromUser varchar2(200) DEFAULT NULL,
  receivedDate number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_received_pages PRIMARY KEY (receivedPageId)
);

CREATE SEQUENCE sq_tiki_received_pages_receive;
CREATE OR REPLACE TRIGGER ai_tiki_received_pages_receive
BEFORE INSERT ON tiki_received_pages
FOR EACH ROW WHEN (
 new.receivedPageId IS NULL OR new.receivedPageId = 0
)
BEGIN
 SELECT sq_tiki_received_pages_receive.nextval
 INTO :new.receivedPageId
 FROM dual;
END;
/

--
-- Table: tiki_referer_stats
--

DROP TABLE tiki_referer_stats;
CREATE TABLE tiki_referer_stats (
  referer varchar2(50) DEFAULT '' CONSTRAINT nn_referer NOT NULL,
  hits number(10) DEFAULT NULL,
  last number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_referer_stats PRIMARY KEY (referer)
);

--
-- Table: tiki_related_categories
--

DROP TABLE tiki_related_categories;
CREATE TABLE tiki_related_categories (
  categId number(10) DEFAULT '0' CONSTRAINT nn_categId05 NOT NULL,
  relatedTo number(10) DEFAULT '0' CONSTRAINT nn_relatedTo NOT NULL,
  CONSTRAINT pk_tiki_related_categories PRIMARY KEY (categId, relatedTo)
);

--
-- Table: tiki_rss_modules
--

DROP TABLE tiki_rss_modules;
CREATE TABLE tiki_rss_modules (
  rssId number(8) CONSTRAINT nn_rssId NOT NULL,
  name varchar2(30) DEFAULT '' CONSTRAINT nn_name14 NOT NULL,
  description clob,
  url varchar2(255) DEFAULT '' CONSTRAINT nn_url03 NOT NULL,
  refresh number(8) DEFAULT NULL,
  lastUpdated number(14) DEFAULT NULL,
  content blob,
  CONSTRAINT pk_tiki_rss_modules PRIMARY KEY (rssId)
);

CREATE SEQUENCE sq_tiki_rss_modules_rssId;
CREATE OR REPLACE TRIGGER ai_tiki_rss_modules_rssId
BEFORE INSERT ON tiki_rss_modules
FOR EACH ROW WHEN (
 new.rssId IS NULL OR new.rssId = 0
)
BEGIN
 SELECT sq_tiki_rss_modules_rssId.nextval
 INTO :new.rssId
 FROM dual;
END;
/

--
-- Table: tiki_search_stats
--

DROP TABLE tiki_search_stats;
CREATE TABLE tiki_search_stats (
  term varchar2(50) DEFAULT '' CONSTRAINT nn_term02 NOT NULL,
  hits number(10) DEFAULT NULL,
  CONSTRAINT pk_tiki_search_stats PRIMARY KEY (term)
);

--
-- Table: tiki_semaphores
--

DROP TABLE tiki_semaphores;
CREATE TABLE tiki_semaphores (
  semName varchar2(250) DEFAULT '' CONSTRAINT nn_semName NOT NULL,
  user_ varchar2(200) DEFAULT NULL,
  timestamp number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_semaphores PRIMARY KEY (semName)
);

--
-- Table: tiki_sent_newsletters
--

DROP TABLE tiki_sent_newsletters;
CREATE TABLE tiki_sent_newsletters (
  editionId number(12) CONSTRAINT nn_editionId NOT NULL,
  nlId number(12) DEFAULT '0' CONSTRAINT nn_nlId03 NOT NULL,
  users number(10) DEFAULT NULL,
  sent number(14) DEFAULT NULL,
  subject varchar2(200) DEFAULT NULL,
  data blob,
  CONSTRAINT pk_tiki_sent_newsletters PRIMARY KEY (editionId)
);

CREATE SEQUENCE sq_tiki_sent_newsletters_editi;
CREATE OR REPLACE TRIGGER ai_tiki_sent_newsletters_editi
BEFORE INSERT ON tiki_sent_newsletters
FOR EACH ROW WHEN (
 new.editionId IS NULL OR new.editionId = 0
)
BEGIN
 SELECT sq_tiki_sent_newsletters_editi.nextval
 INTO :new.editionId
 FROM dual;
END;
/

--
-- Table: tiki_sessions
--

DROP TABLE tiki_sessions;
CREATE TABLE tiki_sessions (
  sessionId varchar2(32) DEFAULT '' CONSTRAINT nn_sessionId NOT NULL,
  user_ varchar2(200) DEFAULT NULL,
  timestamp number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_sessions PRIMARY KEY (sessionId)
);

--
-- Table: tiki_shoutbox
--

DROP TABLE tiki_shoutbox;
CREATE TABLE tiki_shoutbox (
  msgId number(10) CONSTRAINT nn_msgId03 NOT NULL,
  message varchar2(255) DEFAULT NULL,
  timestamp number(14) DEFAULT NULL,
  user_ varchar2(200) DEFAULT NULL,
  hash varchar2(32) DEFAULT NULL,
  CONSTRAINT pk_tiki_shoutbox PRIMARY KEY (msgId)
);

CREATE SEQUENCE sq_tiki_shoutbox_msgId;
CREATE OR REPLACE TRIGGER ai_tiki_shoutbox_msgId
BEFORE INSERT ON tiki_shoutbox
FOR EACH ROW WHEN (
 new.msgId IS NULL OR new.msgId = 0
)
BEGIN
 SELECT sq_tiki_shoutbox_msgId.nextval
 INTO :new.msgId
 FROM dual;
END;
/

--
-- Table: tiki_structures
--

DROP TABLE tiki_structures;
CREATE TABLE tiki_structures (
  page varchar2(240) DEFAULT '' CONSTRAINT nn_page NOT NULL,
  parent varchar2(240) DEFAULT '' CONSTRAINT nn_parent NOT NULL,
  pos number(4) DEFAULT NULL,
  CONSTRAINT pk_tiki_structures PRIMARY KEY (page, parent)
);

--
-- Table: tiki_submissions
--

DROP TABLE tiki_submissions;
CREATE TABLE tiki_submissions (
  subId number(8) CONSTRAINT nn_subId NOT NULL,
  title varchar2(80) DEFAULT NULL,
  authorName varchar2(60) DEFAULT NULL,
  topicId number(14) DEFAULT NULL,
  topicName varchar2(40) DEFAULT NULL,
  size_ number(12) DEFAULT NULL,
  useImage char(1) DEFAULT NULL,
  image_name varchar2(80) DEFAULT NULL,
  image_type varchar2(80) DEFAULT NULL,
  image_size number(14) DEFAULT NULL,
  image_x number(4) DEFAULT NULL,
  image_y number(4) DEFAULT NULL,
  image_data blob,
  publishDate number(14) DEFAULT NULL,
  created number(14) DEFAULT NULL,
  heading clob,
  body clob,
  hash varchar2(32) DEFAULT NULL,
  author varchar2(200) DEFAULT NULL,
  reads number(14) DEFAULT NULL,
  votes number(8) DEFAULT NULL,
  points number(14) DEFAULT NULL,
  type varchar2(50) DEFAULT NULL,
  rating number(3, 2) DEFAULT NULL,
  isfloat char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_submissions PRIMARY KEY (subId)
);

CREATE SEQUENCE sq_tiki_submissions_subId;
CREATE OR REPLACE TRIGGER ai_tiki_submissions_subId
BEFORE INSERT ON tiki_submissions
FOR EACH ROW WHEN (
 new.subId IS NULL OR new.subId = 0
)
BEGIN
 SELECT sq_tiki_submissions_subId.nextval
 INTO :new.subId
 FROM dual;
END;
/

--
-- Table: tiki_suggested_faq_questions
--

DROP TABLE tiki_suggested_faq_questions;
CREATE TABLE tiki_suggested_faq_questions (
  sfqId number(10) CONSTRAINT nn_sfqId NOT NULL,
  faqId number(10) DEFAULT '0' CONSTRAINT nn_faqId02 NOT NULL,
  question clob,
  answer clob,
  created number(14) DEFAULT NULL,
  user_ varchar2(200) DEFAULT NULL,
  CONSTRAINT pk_tiki_suggested_faq_question PRIMARY KEY (sfqId)
);

CREATE SEQUENCE sq_tiki_suggested_faq_question;
CREATE OR REPLACE TRIGGER ai_tiki_suggested_faq_question
BEFORE INSERT ON tiki_suggested_faq_questions
FOR EACH ROW WHEN (
 new.sfqId IS NULL OR new.sfqId = 0
)
BEGIN
 SELECT sq_tiki_suggested_faq_question.nextval
 INTO :new.sfqId
 FROM dual;
END;
/

--
-- Table: tiki_survey_question_options
--

DROP TABLE tiki_survey_question_options;
CREATE TABLE tiki_survey_question_options (
  optionId number(12) CONSTRAINT nn_optionId05 NOT NULL,
  questionId number(12) DEFAULT '0' CONSTRAINT nn_questionId04 NOT NULL,
  qoption clob,
  votes number(10) DEFAULT NULL,
  CONSTRAINT pk_tiki_survey_question_option PRIMARY KEY (optionId)
);

CREATE SEQUENCE sq_tiki_survey_question_option;
CREATE OR REPLACE TRIGGER ai_tiki_survey_question_option
BEFORE INSERT ON tiki_survey_question_options
FOR EACH ROW WHEN (
 new.optionId IS NULL OR new.optionId = 0
)
BEGIN
 SELECT sq_tiki_survey_question_option.nextval
 INTO :new.optionId
 FROM dual;
END;
/

--
-- Table: tiki_survey_questions
--

DROP TABLE tiki_survey_questions;
CREATE TABLE tiki_survey_questions (
  questionId number(12) CONSTRAINT nn_questionId05 NOT NULL,
  surveyId number(12) DEFAULT '0' CONSTRAINT nn_surveyId NOT NULL,
  question clob,
  options clob,
  type char(1) DEFAULT NULL,
  position number(5) DEFAULT NULL,
  votes number(10) DEFAULT NULL,
  value number(10) DEFAULT NULL,
  average number(4, 2) DEFAULT NULL,
  CONSTRAINT pk_tiki_survey_questions PRIMARY KEY (questionId)
);

CREATE SEQUENCE sq_tiki_survey_questions_quest;
CREATE OR REPLACE TRIGGER ai_tiki_survey_questions_quest
BEFORE INSERT ON tiki_survey_questions
FOR EACH ROW WHEN (
 new.questionId IS NULL OR new.questionId = 0
)
BEGIN
 SELECT sq_tiki_survey_questions_quest.nextval
 INTO :new.questionId
 FROM dual;
END;
/

--
-- Table: tiki_surveys
--

DROP TABLE tiki_surveys;
CREATE TABLE tiki_surveys (
  surveyId number(12) CONSTRAINT nn_surveyId02 NOT NULL,
  name varchar2(200) DEFAULT NULL,
  description clob,
  taken number(10) DEFAULT NULL,
  lastTaken number(14) DEFAULT NULL,
  created number(14) DEFAULT NULL,
  status char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_surveys PRIMARY KEY (surveyId)
);

CREATE SEQUENCE sq_tiki_surveys_surveyId;
CREATE OR REPLACE TRIGGER ai_tiki_surveys_surveyId
BEFORE INSERT ON tiki_surveys
FOR EACH ROW WHEN (
 new.surveyId IS NULL OR new.surveyId = 0
)
BEGIN
 SELECT sq_tiki_surveys_surveyId.nextval
 INTO :new.surveyId
 FROM dual;
END;
/

--
-- Table: tiki_tags
--

DROP TABLE tiki_tags;
CREATE TABLE tiki_tags (
  tagName varchar2(80) DEFAULT '' CONSTRAINT nn_tagName NOT NULL,
  pageName varchar2(160) DEFAULT '' CONSTRAINT nn_pageName07 NOT NULL,
  hits number(8) DEFAULT NULL,
  description varchar2(200) DEFAULT NULL,
  data blob,
  lastModif number(14) DEFAULT NULL,
  comment_ varchar2(200) DEFAULT NULL,
  version number(8) DEFAULT '0' CONSTRAINT nn_version03 NOT NULL,
  user_ varchar2(200) DEFAULT NULL,
  ip varchar2(15) DEFAULT NULL,
  flag char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_tags PRIMARY KEY (tagName, pageName)
);

--
-- Table: tiki_theme_control_categs
--

DROP TABLE tiki_theme_control_categs;
CREATE TABLE tiki_theme_control_categs (
  categId number(12) DEFAULT '0' CONSTRAINT nn_categId06 NOT NULL,
  theme varchar2(250) DEFAULT '' CONSTRAINT nn_theme NOT NULL,
  CONSTRAINT pk_tiki_theme_control_categs PRIMARY KEY (categId)
);

--
-- Table: tiki_theme_control_objects
--

DROP TABLE tiki_theme_control_objects;
CREATE TABLE tiki_theme_control_objects (
  objId varchar2(250) DEFAULT '' CONSTRAINT nn_objId NOT NULL,
  type varchar2(250) DEFAULT '' CONSTRAINT nn_type02 NOT NULL,
  name varchar2(250) DEFAULT '' CONSTRAINT nn_name15 NOT NULL,
  theme varchar2(250) DEFAULT '' CONSTRAINT nn_theme02 NOT NULL,
  CONSTRAINT pk_tiki_theme_control_objects PRIMARY KEY (objId)
);

--
-- Table: tiki_theme_control_sections
--

DROP TABLE tiki_theme_control_sections;
CREATE TABLE tiki_theme_control_sections (
  section varchar2(250) DEFAULT '' CONSTRAINT nn_section03 NOT NULL,
  theme varchar2(250) DEFAULT '' CONSTRAINT nn_theme03 NOT NULL,
  CONSTRAINT pk_tiki_theme_control_sections PRIMARY KEY (section)
);

--
-- Table: tiki_topics
--

DROP TABLE tiki_topics;
CREATE TABLE tiki_topics (
  topicId number(14) CONSTRAINT nn_topicId02 NOT NULL,
  name varchar2(40) DEFAULT NULL,
  image_name varchar2(80) DEFAULT NULL,
  image_type varchar2(80) DEFAULT NULL,
  image_size number(14) DEFAULT NULL,
  image_data blob,
  active char(1) DEFAULT NULL,
  created number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_topics PRIMARY KEY (topicId)
);

CREATE SEQUENCE sq_tiki_topics_topicId;
CREATE OR REPLACE TRIGGER ai_tiki_topics_topicId
BEFORE INSERT ON tiki_topics
FOR EACH ROW WHEN (
 new.topicId IS NULL OR new.topicId = 0
)
BEGIN
 SELECT sq_tiki_topics_topicId.nextval
 INTO :new.topicId
 FROM dual;
END;
/

--
-- Table: tiki_tracker_fields
--

DROP TABLE tiki_tracker_fields;
CREATE TABLE tiki_tracker_fields (
  fieldId number(12) CONSTRAINT nn_fieldId NOT NULL,
  trackerId number(12) DEFAULT '0' CONSTRAINT nn_trackerId NOT NULL,
  name varchar2(80) DEFAULT NULL,
  options clob,
  type char(1) DEFAULT NULL,
  isMain char(1) DEFAULT NULL,
  isTblVisible char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_tracker_fields PRIMARY KEY (fieldId)
);

CREATE SEQUENCE sq_tiki_tracker_fields_fieldId;
CREATE OR REPLACE TRIGGER ai_tiki_tracker_fields_fieldId
BEFORE INSERT ON tiki_tracker_fields
FOR EACH ROW WHEN (
 new.fieldId IS NULL OR new.fieldId = 0
)
BEGIN
 SELECT sq_tiki_tracker_fields_fieldId.nextval
 INTO :new.fieldId
 FROM dual;
END;
/

--
-- Table: tiki_tracker_item_attachments
--

DROP TABLE tiki_tracker_item_attachments;
CREATE TABLE tiki_tracker_item_attachments (
  attId number(12) CONSTRAINT nn_attId02 NOT NULL,
  itemId varchar2(40) DEFAULT '' CONSTRAINT nn_itemId05 NOT NULL,
  filename varchar2(80) DEFAULT NULL,
  filetype varchar2(80) DEFAULT NULL,
  filesize number(14) DEFAULT NULL,
  user_ varchar2(200) DEFAULT NULL,
  data blob,
  path varchar2(255) DEFAULT NULL,
  downloads number(10) DEFAULT NULL,
  created number(14) DEFAULT NULL,
  comment_ varchar2(250) DEFAULT NULL,
  CONSTRAINT pk_tiki_tracker_item_attachmen PRIMARY KEY (attId)
);

CREATE SEQUENCE sq_tiki_tracker_item_attachmen;
CREATE OR REPLACE TRIGGER ai_tiki_tracker_item_attachmen
BEFORE INSERT ON tiki_tracker_item_attachments
FOR EACH ROW WHEN (
 new.attId IS NULL OR new.attId = 0
)
BEGIN
 SELECT sq_tiki_tracker_item_attachmen.nextval
 INTO :new.attId
 FROM dual;
END;
/

--
-- Table: tiki_tracker_item_comments
--

DROP TABLE tiki_tracker_item_comments;
CREATE TABLE tiki_tracker_item_comments (
  commentId number(12) CONSTRAINT nn_commentId NOT NULL,
  itemId number(12) DEFAULT '0' CONSTRAINT nn_itemId06 NOT NULL,
  user_ varchar2(200) DEFAULT NULL,
  data clob,
  title varchar2(200) DEFAULT NULL,
  posted number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_tracker_item_comments PRIMARY KEY (commentId)
);

CREATE SEQUENCE sq_tiki_tracker_item_comments_;
CREATE OR REPLACE TRIGGER ai_tiki_tracker_item_comments_
BEFORE INSERT ON tiki_tracker_item_comments
FOR EACH ROW WHEN (
 new.commentId IS NULL OR new.commentId = 0
)
BEGIN
 SELECT sq_tiki_tracker_item_comments_.nextval
 INTO :new.commentId
 FROM dual;
END;
/

--
-- Table: tiki_tracker_item_fields
--

DROP TABLE tiki_tracker_item_fields;
CREATE TABLE tiki_tracker_item_fields (
  itemId number(12) DEFAULT '0' CONSTRAINT nn_itemId07 NOT NULL,
  fieldId number(12) DEFAULT '0' CONSTRAINT nn_fieldId02 NOT NULL,
  value clob,
  CONSTRAINT pk_tiki_tracker_item_fields PRIMARY KEY (itemId, fieldId)
);

--
-- Table: tiki_tracker_items
--

DROP TABLE tiki_tracker_items;
CREATE TABLE tiki_tracker_items (
  itemId number(12) CONSTRAINT nn_itemId08 NOT NULL,
  trackerId number(12) DEFAULT '0' CONSTRAINT nn_trackerId02 NOT NULL,
  created number(14) DEFAULT NULL,
  status char(1) DEFAULT NULL,
  lastModif number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_tracker_items PRIMARY KEY (itemId)
);

CREATE SEQUENCE sq_tiki_tracker_items_itemId;
CREATE OR REPLACE TRIGGER ai_tiki_tracker_items_itemId
BEFORE INSERT ON tiki_tracker_items
FOR EACH ROW WHEN (
 new.itemId IS NULL OR new.itemId = 0
)
BEGIN
 SELECT sq_tiki_tracker_items_itemId.nextval
 INTO :new.itemId
 FROM dual;
END;
/

--
-- Table: tiki_trackers
--

DROP TABLE tiki_trackers;
CREATE TABLE tiki_trackers (
  trackerId number(12) CONSTRAINT nn_trackerId03 NOT NULL,
  name varchar2(80) DEFAULT NULL,
  description clob,
  created number(14) DEFAULT NULL,
  lastModif number(14) DEFAULT NULL,
  showCreated char(1) DEFAULT NULL,
  showStatus char(1) DEFAULT NULL,
  showLastModif char(1) DEFAULT NULL,
  useComments char(1) DEFAULT NULL,
  useAttachments char(1) DEFAULT NULL,
  items number(10) DEFAULT NULL,
  CONSTRAINT pk_tiki_trackers PRIMARY KEY (trackerId)
);

CREATE SEQUENCE sq_tiki_trackers_trackerId;
CREATE OR REPLACE TRIGGER ai_tiki_trackers_trackerId
BEFORE INSERT ON tiki_trackers
FOR EACH ROW WHEN (
 new.trackerId IS NULL OR new.trackerId = 0
)
BEGIN
 SELECT sq_tiki_trackers_trackerId.nextval
 INTO :new.trackerId
 FROM dual;
END;
/

--
-- Table: tiki_untranslated
--

DROP TABLE tiki_untranslated;
CREATE TABLE tiki_untranslated (
  id number(14) CONSTRAINT nn_id NOT NULL,
  source blob CONSTRAINT nn_source02 NOT NULL,
  lang char(2) DEFAULT '' CONSTRAINT nn_lang04 NOT NULL,
  CONSTRAINT pk_tiki_untranslated PRIMARY KEY (source, lang),
  CONSTRAINT id UNIQUE (id)
);

CREATE SEQUENCE sq_tiki_untranslated_id;
CREATE OR REPLACE TRIGGER ai_tiki_untranslated_id
BEFORE INSERT ON tiki_untranslated
FOR EACH ROW WHEN (
 new.id IS NULL OR new.id = 0
)
BEGIN
 SELECT sq_tiki_untranslated_id.nextval
 INTO :new.id
 FROM dual;
END;
/

CREATE INDEX id_2_tiki_untranslated on tiki_untranslated (id);

--
-- Table: tiki_user_answers
--

DROP TABLE tiki_user_answers;
CREATE TABLE tiki_user_answers (
  userResultId number(10) DEFAULT '0' CONSTRAINT nn_userResultId NOT NULL,
  quizId number(10) DEFAULT '0' CONSTRAINT nn_quizId04 NOT NULL,
  questionId number(10) DEFAULT '0' CONSTRAINT nn_questionId06 NOT NULL,
  optionId number(10) DEFAULT '0' CONSTRAINT nn_optionId06 NOT NULL,
  CONSTRAINT pk_tiki_user_answers PRIMARY KEY (userResultId, quizId, questionId, optionId)
);

--
-- Table: tiki_user_assigned_modules
--

DROP TABLE tiki_user_assigned_modules;
CREATE TABLE tiki_user_assigned_modules (
  name varchar2(200) DEFAULT '' CONSTRAINT nn_name16 NOT NULL,
  position char(1) DEFAULT NULL,
  ord number(4) DEFAULT NULL,
  type char(1) DEFAULT NULL,
  title varchar2(40) DEFAULT NULL,
  cache_time number(14) DEFAULT NULL,
  rows_ number(4) DEFAULT NULL,
  groups clob,
  params varchar2(250) DEFAULT NULL,
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_11 NOT NULL,
  CONSTRAINT pk_tiki_user_assigned_modules PRIMARY KEY (name, user_)
);

--
-- Table: tiki_user_bookmarks_folders
--

DROP TABLE tiki_user_bookmarks_folders;
CREATE TABLE tiki_user_bookmarks_folders (
  folderId number(12) CONSTRAINT nn_folderId NOT NULL,
  parentId number(12) DEFAULT NULL,
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_12 NOT NULL,
  name varchar2(30) DEFAULT NULL,
  CONSTRAINT pk_tiki_user_bookmarks_folders PRIMARY KEY (user_, folderId)
);

CREATE SEQUENCE sq_tiki_user_bookmarks_folders;
CREATE OR REPLACE TRIGGER ai_tiki_user_bookmarks_folders
BEFORE INSERT ON tiki_user_bookmarks_folders
FOR EACH ROW WHEN (
 new.folderId IS NULL OR new.folderId = 0
)
BEGIN
 SELECT sq_tiki_user_bookmarks_folders.nextval
 INTO :new.folderId
 FROM dual;
END;
/

--
-- Table: tiki_user_bookmarks_urls
--

DROP TABLE tiki_user_bookmarks_urls;
CREATE TABLE tiki_user_bookmarks_urls (
  urlId number(12) CONSTRAINT nn_urlId NOT NULL,
  name varchar2(30) DEFAULT NULL,
  url varchar2(250) DEFAULT NULL,
  data blob,
  lastUpdated number(14) DEFAULT NULL,
  folderId number(12) DEFAULT '0' CONSTRAINT nn_folderId02 NOT NULL,
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_13 NOT NULL,
  CONSTRAINT pk_tiki_user_bookmarks_urls PRIMARY KEY (urlId)
);

CREATE SEQUENCE sq_tiki_user_bookmarks_urls_ur;
CREATE OR REPLACE TRIGGER ai_tiki_user_bookmarks_urls_ur
BEFORE INSERT ON tiki_user_bookmarks_urls
FOR EACH ROW WHEN (
 new.urlId IS NULL OR new.urlId = 0
)
BEGIN
 SELECT sq_tiki_user_bookmarks_urls_ur.nextval
 INTO :new.urlId
 FROM dual;
END;
/

--
-- Table: tiki_user_mail_accounts
--

DROP TABLE tiki_user_mail_accounts;
CREATE TABLE tiki_user_mail_accounts (
  accountId number(12) CONSTRAINT nn_accountId02 NOT NULL,
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_14 NOT NULL,
  account varchar2(50) DEFAULT '' CONSTRAINT nn_account02 NOT NULL,
  pop varchar2(255) DEFAULT NULL,
  current_ char(1) DEFAULT NULL,
  port number(4) DEFAULT NULL,
  username varchar2(100) DEFAULT NULL,
  pass varchar2(100) DEFAULT NULL,
  msgs number(4) DEFAULT NULL,
  smtp varchar2(255) DEFAULT NULL,
  useAuth char(1) DEFAULT NULL,
  smtpPort number(4) DEFAULT NULL,
  CONSTRAINT pk_tiki_user_mail_accounts PRIMARY KEY (accountId)
);

CREATE SEQUENCE sq_tiki_user_mail_accounts_acc;
CREATE OR REPLACE TRIGGER ai_tiki_user_mail_accounts_acc
BEFORE INSERT ON tiki_user_mail_accounts
FOR EACH ROW WHEN (
 new.accountId IS NULL OR new.accountId = 0
)
BEGIN
 SELECT sq_tiki_user_mail_accounts_acc.nextval
 INTO :new.accountId
 FROM dual;
END;
/

--
-- Table: tiki_user_menus
--

DROP TABLE tiki_user_menus;
CREATE TABLE tiki_user_menus (
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_15 NOT NULL,
  menuId number(12) CONSTRAINT nn_menuId03 NOT NULL,
  url varchar2(250) DEFAULT NULL,
  name varchar2(40) DEFAULT NULL,
  position number(4) DEFAULT NULL,
  mode_ char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_user_menus PRIMARY KEY (menuId)
);

CREATE SEQUENCE sq_tiki_user_menus_menuId;
CREATE OR REPLACE TRIGGER ai_tiki_user_menus_menuId
BEFORE INSERT ON tiki_user_menus
FOR EACH ROW WHEN (
 new.menuId IS NULL OR new.menuId = 0
)
BEGIN
 SELECT sq_tiki_user_menus_menuId.nextval
 INTO :new.menuId
 FROM dual;
END;
/

--
-- Table: tiki_user_modules
--

DROP TABLE tiki_user_modules;
CREATE TABLE tiki_user_modules (
  name varchar2(200) DEFAULT '' CONSTRAINT nn_name17 NOT NULL,
  title varchar2(40) DEFAULT NULL,
  data blob,
  CONSTRAINT pk_tiki_user_modules PRIMARY KEY (name)
);

--
-- Table: tiki_user_notes
--

DROP TABLE tiki_user_notes;
CREATE TABLE tiki_user_notes (
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_16 NOT NULL,
  noteId number(12) CONSTRAINT nn_noteId NOT NULL,
  created number(14) DEFAULT NULL,
  name varchar2(255) DEFAULT NULL,
  lastModif number(14) DEFAULT NULL,
  data clob,
  size_ number(14) DEFAULT NULL,
  parse_mode varchar2(20) DEFAULT NULL,
  CONSTRAINT pk_tiki_user_notes PRIMARY KEY (noteId)
);

CREATE SEQUENCE sq_tiki_user_notes_noteId;
CREATE OR REPLACE TRIGGER ai_tiki_user_notes_noteId
BEFORE INSERT ON tiki_user_notes
FOR EACH ROW WHEN (
 new.noteId IS NULL OR new.noteId = 0
)
BEGIN
 SELECT sq_tiki_user_notes_noteId.nextval
 INTO :new.noteId
 FROM dual;
END;
/

--
-- Table: tiki_user_postings
--

DROP TABLE tiki_user_postings;
CREATE TABLE tiki_user_postings (
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_17 NOT NULL,
  posts number(12) DEFAULT NULL,
  last number(14) DEFAULT NULL,
  first number(14) DEFAULT NULL,
  level_ number(8) DEFAULT NULL,
  CONSTRAINT pk_tiki_user_postings PRIMARY KEY (user_)
);

--
-- Table: tiki_user_preferences
--

DROP TABLE tiki_user_preferences;
CREATE TABLE tiki_user_preferences (
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_18 NOT NULL,
  prefName varchar2(40) DEFAULT '' CONSTRAINT nn_prefName NOT NULL,
  value varchar2(250) DEFAULT NULL,
  CONSTRAINT pk_tiki_user_preferences PRIMARY KEY (user_, prefName)
);

--
-- Table: tiki_user_quizzes
--

DROP TABLE tiki_user_quizzes;
CREATE TABLE tiki_user_quizzes (
  user_ varchar2(100) DEFAULT NULL,
  quizId number(10) DEFAULT NULL,
  timestamp number(14) DEFAULT NULL,
  timeTaken number(14) DEFAULT NULL,
  points number(12) DEFAULT NULL,
  maxPoints number(12) DEFAULT NULL,
  resultId number(10) DEFAULT NULL,
  userResultId number(10) CONSTRAINT nn_userResultId02 NOT NULL,
  CONSTRAINT pk_tiki_user_quizzes PRIMARY KEY (userResultId)
);

CREATE SEQUENCE sq_tiki_user_quizzes_userResul;
CREATE OR REPLACE TRIGGER ai_tiki_user_quizzes_userResul
BEFORE INSERT ON tiki_user_quizzes
FOR EACH ROW WHEN (
 new.userResultId IS NULL OR new.userResultId = 0
)
BEGIN
 SELECT sq_tiki_user_quizzes_userResul.nextval
 INTO :new.userResultId
 FROM dual;
END;
/

--
-- Table: tiki_user_taken_quizzes
--

DROP TABLE tiki_user_taken_quizzes;
CREATE TABLE tiki_user_taken_quizzes (
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_19 NOT NULL,
  quizId varchar2(255) DEFAULT '' CONSTRAINT nn_quizId05 NOT NULL,
  CONSTRAINT pk_tiki_user_taken_quizzes PRIMARY KEY (user_, quizId)
);

--
-- Table: tiki_user_tasks
--

DROP TABLE tiki_user_tasks;
CREATE TABLE tiki_user_tasks (
  user_ varchar2(200) DEFAULT NULL,
  taskId number(14) CONSTRAINT nn_taskId NOT NULL,
  title varchar2(250) DEFAULT NULL,
  description clob,
  date_ number(14) DEFAULT NULL,
  status char(1) DEFAULT NULL,
  priority number(2) DEFAULT NULL,
  completed number(14) DEFAULT NULL,
  percentage number(4) DEFAULT NULL,
  CONSTRAINT pk_tiki_user_tasks PRIMARY KEY (taskId)
);

CREATE SEQUENCE sq_tiki_user_tasks_taskId;
CREATE OR REPLACE TRIGGER ai_tiki_user_tasks_taskId
BEFORE INSERT ON tiki_user_tasks
FOR EACH ROW WHEN (
 new.taskId IS NULL OR new.taskId = 0
)
BEGIN
 SELECT sq_tiki_user_tasks_taskId.nextval
 INTO :new.taskId
 FROM dual;
END;
/

--
-- Table: tiki_user_votings
--

DROP TABLE tiki_user_votings;
CREATE TABLE tiki_user_votings (
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_20 NOT NULL,
  id varchar2(255) DEFAULT '' CONSTRAINT nn_id02 NOT NULL,
  CONSTRAINT pk_tiki_user_votings PRIMARY KEY (user_, id)
);

--
-- Table: tiki_user_watches
--

DROP TABLE tiki_user_watches;
CREATE TABLE tiki_user_watches (
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_21 NOT NULL,
  event varchar2(40) DEFAULT '' CONSTRAINT nn_event NOT NULL,
  object varchar2(200) DEFAULT '' CONSTRAINT nn_object02 NOT NULL,
  hash varchar2(32) DEFAULT NULL,
  title varchar2(250) DEFAULT NULL,
  type varchar2(200) DEFAULT NULL,
  url varchar2(250) DEFAULT NULL,
  email varchar2(200) DEFAULT NULL,
  CONSTRAINT pk_tiki_user_watches PRIMARY KEY (user_, event, object)
);

--
-- Table: tiki_userfiles
--

DROP TABLE tiki_userfiles;
CREATE TABLE tiki_userfiles (
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_22 NOT NULL,
  fileId number(12) CONSTRAINT nn_fileId02 NOT NULL,
  name varchar2(200) DEFAULT NULL,
  filename varchar2(200) DEFAULT NULL,
  filetype varchar2(200) DEFAULT NULL,
  filesize varchar2(200) DEFAULT NULL,
  data blob,
  hits number(8) DEFAULT NULL,
  isFile char(1) DEFAULT NULL,
  path varchar2(255) DEFAULT NULL,
  created number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_userfiles PRIMARY KEY (fileId)
);

CREATE SEQUENCE sq_tiki_userfiles_fileId;
CREATE OR REPLACE TRIGGER ai_tiki_userfiles_fileId
BEFORE INSERT ON tiki_userfiles
FOR EACH ROW WHEN (
 new.fileId IS NULL OR new.fileId = 0
)
BEGIN
 SELECT sq_tiki_userfiles_fileId.nextval
 INTO :new.fileId
 FROM dual;
END;
/

--
-- Table: tiki_userpoints
--

DROP TABLE tiki_userpoints;
CREATE TABLE tiki_userpoints (
  user_ varchar2(200) DEFAULT NULL,
  points number(8, 2) DEFAULT NULL,
  voted number(8) DEFAULT NULL
);

--
-- Table: tiki_users
--

DROP TABLE tiki_users;
CREATE TABLE tiki_users (
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_23 NOT NULL,
  password varchar2(40) DEFAULT NULL,
  email varchar2(200) DEFAULT NULL,
  lastLogin number(14) DEFAULT NULL,
  CONSTRAINT pk_tiki_users PRIMARY KEY (user_)
);

--
-- Table: tiki_webmail_contacts
--

DROP TABLE tiki_webmail_contacts;
CREATE TABLE tiki_webmail_contacts (
  contactId number(12) CONSTRAINT nn_contactId NOT NULL,
  firstName varchar2(80) DEFAULT NULL,
  lastName varchar2(80) DEFAULT NULL,
  email varchar2(250) DEFAULT NULL,
  nickname varchar2(200) DEFAULT NULL,
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_24 NOT NULL,
  CONSTRAINT pk_tiki_webmail_contacts PRIMARY KEY (contactId)
);

CREATE SEQUENCE sq_tiki_webmail_contacts_conta;
CREATE OR REPLACE TRIGGER ai_tiki_webmail_contacts_conta
BEFORE INSERT ON tiki_webmail_contacts
FOR EACH ROW WHEN (
 new.contactId IS NULL OR new.contactId = 0
)
BEGIN
 SELECT sq_tiki_webmail_contacts_conta.nextval
 INTO :new.contactId
 FROM dual;
END;
/

--
-- Table: tiki_webmail_messages
--

DROP TABLE tiki_webmail_messages;
CREATE TABLE tiki_webmail_messages (
  accountId number(12) DEFAULT '0' CONSTRAINT nn_accountId03 NOT NULL,
  mailId varchar2(255) DEFAULT '' CONSTRAINT nn_mailId NOT NULL,
  user_ varchar2(200) DEFAULT '' CONSTRAINT nn_user_25 NOT NULL,
  isRead char(1) DEFAULT NULL,
  isReplied char(1) DEFAULT NULL,
  isFlagged char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_webmail_messages PRIMARY KEY (accountId, mailId)
);

--
-- Table: tiki_wiki_attachments
--

DROP TABLE tiki_wiki_attachments;
CREATE TABLE tiki_wiki_attachments (
  attId number(12) CONSTRAINT nn_attId03 NOT NULL,
  page varchar2(200) DEFAULT '' CONSTRAINT nn_page02 NOT NULL,
  filename varchar2(80) DEFAULT NULL,
  filetype varchar2(80) DEFAULT NULL,
  filesize number(14) DEFAULT NULL,
  user_ varchar2(200) DEFAULT NULL,
  data blob,
  path varchar2(255) DEFAULT NULL,
  downloads number(10) DEFAULT NULL,
  created number(14) DEFAULT NULL,
  comment_ varchar2(250) DEFAULT NULL,
  CONSTRAINT pk_tiki_wiki_attachments PRIMARY KEY (attId)
);

CREATE SEQUENCE sq_tiki_wiki_attachments_attId;
CREATE OR REPLACE TRIGGER ai_tiki_wiki_attachments_attId
BEFORE INSERT ON tiki_wiki_attachments
FOR EACH ROW WHEN (
 new.attId IS NULL OR new.attId = 0
)
BEGIN
 SELECT sq_tiki_wiki_attachments_attId.nextval
 INTO :new.attId
 FROM dual;
END;
/

--
-- Table: tiki_zones
--

DROP TABLE tiki_zones;
CREATE TABLE tiki_zones (
  zone varchar2(40) DEFAULT '' CONSTRAINT nn_zone02 NOT NULL,
  CONSTRAINT pk_tiki_zones PRIMARY KEY (zone)
);

--
-- Table: users_grouppermissions
--

DROP TABLE users_grouppermissions;
CREATE TABLE users_grouppermissions (
  groupName varchar2(30) DEFAULT '' CONSTRAINT nn_groupName03 NOT NULL,
  permName varchar2(30) DEFAULT '' CONSTRAINT nn_permName NOT NULL,
  value char(1) DEFAULT '' CONSTRAINT nn_value NOT NULL,
  CONSTRAINT pk_users_grouppermissions PRIMARY KEY (groupName, permName)
);

--
-- Table: users_groups
--

DROP TABLE users_groups;
CREATE TABLE users_groups (
  groupName varchar2(30) DEFAULT '' CONSTRAINT nn_groupName04 NOT NULL,
  groupDesc varchar2(255) DEFAULT NULL,
  CONSTRAINT pk_users_groups PRIMARY KEY (groupName)
);

--
-- Table: users_objectpermissions
--

DROP TABLE users_objectpermissions;
CREATE TABLE users_objectpermissions (
  groupName varchar2(30) DEFAULT '' CONSTRAINT nn_groupName05 NOT NULL,
  permName varchar2(30) DEFAULT '' CONSTRAINT nn_permName02 NOT NULL,
  objectType varchar2(20) DEFAULT '' CONSTRAINT nn_objectType NOT NULL,
  objectId varchar2(32) DEFAULT '' CONSTRAINT nn_objectId NOT NULL,
  CONSTRAINT pk_users_objectpermissions PRIMARY KEY (objectId, groupName, permName)
);

--
-- Table: users_permissions
--

DROP TABLE users_permissions;
CREATE TABLE users_permissions (
  permName varchar2(30) DEFAULT '' CONSTRAINT nn_permName03 NOT NULL,
  permDesc varchar2(250) DEFAULT NULL,
  level_ varchar2(80) DEFAULT NULL,
  type varchar2(20) DEFAULT NULL,
  CONSTRAINT pk_users_permissions PRIMARY KEY (permName)
);

--
-- Table: users_usergroups
--

DROP TABLE users_usergroups;
CREATE TABLE users_usergroups (
  userId number(8) DEFAULT '0' CONSTRAINT nn_userId NOT NULL,
  groupName varchar2(30) DEFAULT '' CONSTRAINT nn_groupName06 NOT NULL,
  CONSTRAINT pk_users_usergroups PRIMARY KEY (userId, groupName)
);

--
-- Table: users_users
--

DROP TABLE users_users;
CREATE TABLE users_users (
  userId number(8) CONSTRAINT nn_userId02 NOT NULL,
  email varchar2(200) DEFAULT NULL,
  login varchar2(40) DEFAULT '' CONSTRAINT nn_login NOT NULL,
  password varchar2(30) DEFAULT '' CONSTRAINT nn_password NOT NULL,
  provpass varchar2(30) DEFAULT NULL,
  realname varchar2(80) DEFAULT NULL,
  homePage varchar2(200) DEFAULT NULL,
  lastLogin number(14) DEFAULT NULL,
  currentLogin number(14) DEFAULT NULL,
  registrationDate number(14) DEFAULT NULL,
  challenge varchar2(32) DEFAULT NULL,
  pass_due number(14) DEFAULT NULL,
  hash varchar2(32) DEFAULT NULL,
  created number(14) DEFAULT NULL,
  country varchar2(80) DEFAULT NULL,
  avatarName varchar2(80) DEFAULT NULL,
  avatarSize number(14) DEFAULT NULL,
  avatarFileType varchar2(250) DEFAULT NULL,
  avatarData blob,
  avatarLibName varchar2(200) DEFAULT NULL,
  avatarType char(1) DEFAULT NULL,
  CONSTRAINT pk_users_users PRIMARY KEY (userId)
);

CREATE SEQUENCE sq_users_users_userId;
CREATE OR REPLACE TRIGGER ai_users_users_userId
BEFORE INSERT ON users_users
FOR EACH ROW WHEN (
 new.userId IS NULL OR new.userId = 0
)
BEGIN
 SELECT sq_users_users_userId.nextval
 INTO :new.userId
 FROM dual;
END;
/

