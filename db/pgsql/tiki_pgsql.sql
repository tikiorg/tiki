--$Id: tiki_pgsql.sql,v 1.1 2003-07-15 09:53:28 rossta Exp $
-- Dump of tiki_mysql.sql
-- MySQL dump 8.21
--
-- Host: localhost    Database: tiki
---------------------------------------------------------
-- Server version	3.23.49-log
--
-- Table structure for table 'galaxia_activities'
--
CREATE TABLE galaxia_activities (
  activityId integer NOT NULL default nextval('galaxia_activities_seq'),
  name varchar(80) NOT NULL default '',
  normalized_name varchar(80) NOT NULL default '',
  pId integer NOT NULL default '0',
  type varchar(10) check (type in ('start','end','split','switch','join','activity','standalone')) NOT NULL default 'start',
  isAutoRouted varchar(1) NOT NULL default '',
  flowNum integer NOT NULL default '0',
  isInteractive varchar(1) NOT NULL default '',
  lastModif integer NOT NULL default '0',
  description text NOT NULL,
  PRIMARY KEY  (activityId)
) ;
--
-- Table structure for table 'galaxia_activity_roles'
--
CREATE TABLE galaxia_activity_roles (
  activityId integer NOT NULL default '0',
  roleId integer NOT NULL default '0',
  PRIMARY KEY  (activityId,roleId)
) ;
--
-- Table structure for table 'galaxia_instance_activities'
--
CREATE TABLE galaxia_instance_activities (
  instanceId integer NOT NULL default '0',
  activityId integer NOT NULL default '0',
  started integer NOT NULL default '0',
  ended integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  status varchar(9) check (status in ('running','completed')) NOT NULL default 'running',
  PRIMARY KEY  (instanceId,activityId)
) ;
--
-- Table structure for table 'galaxia_instance_comments'
--
CREATE TABLE galaxia_instance_comments (
  cId integer NOT NULL default nextval('galaxia_instance_comments_seq'),
  instanceId integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  activityId integer NOT NULL default '0',
  hash varchar(32) NOT NULL default '',
  title varchar(250) NOT NULL default '',
  comment text NOT NULL,
  activity varchar(80) NOT NULL default '',
  timestamp integer NOT NULL default '0',
  PRIMARY KEY  (cId)
) ;
--
-- Table structure for table 'galaxia_instances'
--
CREATE TABLE galaxia_instances (
  instanceId integer NOT NULL default nextval('galaxia_instances_seq'),
  pId integer NOT NULL default '0',
  started integer NOT NULL default '0',
  owner varchar(200) NOT NULL default '',
  nextActivity integer NOT NULL default '0',
  nextUser varchar(200) NOT NULL default '',
  ended integer NOT NULL default '0',
  status varchar(9) check (status in ('active','exception','aborted','completed')) NOT NULL default 'active',
  properties text,
  PRIMARY KEY  (instanceId)
) ;
--
-- Table structure for table 'galaxia_processes'
--
CREATE TABLE galaxia_processes (
  pId integer NOT NULL default nextval('galaxia_processes_seq'),
  name varchar(80) NOT NULL default '',
  isValid varchar(1) NOT NULL default '',
  isActive varchar(1) NOT NULL default '',
  version varchar(12) NOT NULL default '',
  description text NOT NULL,
  lastModif integer NOT NULL default '0',
  normalized_name varchar(80) NOT NULL default '',
  PRIMARY KEY  (pId)
) ;
--
-- Table structure for table 'galaxia_roles'
--
CREATE TABLE galaxia_roles (
  roleId integer NOT NULL default nextval('galaxia_roles_seq'),
  pId integer NOT NULL default '0',
  lastModif integer NOT NULL default '0',
  name varchar(80) NOT NULL default '',
  description text NOT NULL,
  PRIMARY KEY  (roleId)
) ;
--
-- Table structure for table 'galaxia_transitions'
--
CREATE TABLE galaxia_transitions (
  pId integer NOT NULL default '0',
  actFromId integer NOT NULL default '0',
  actToId integer NOT NULL default '0',
  PRIMARY KEY  (actFromId,actToId)
) ;
--
-- Table structure for table 'galaxia_user_roles'
--
CREATE TABLE galaxia_user_roles (
  pId integer NOT NULL default '0',
  roleId integer NOT NULL default nextval('galaxia_user_roles_seq'),
  "user" varchar(200) NOT NULL default '',
  PRIMARY KEY  (roleId,"user")
) ;
--
-- Table structure for table 'galaxia_workitems'
--
CREATE TABLE galaxia_workitems (
  itemId integer NOT NULL default nextval('galaxia_workitems_seq'),
  instanceId integer NOT NULL default '0',
  orderId integer NOT NULL default '0',
  activityId integer NOT NULL default '0',
  properties text,
  started integer NOT NULL default '0',
  ended integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  PRIMARY KEY  (itemId)
) ;
--
-- Table structure for table 'messu_messages'
--
CREATE TABLE messu_messages (
  msgId integer NOT NULL default nextval('messu_messages_seq'),
  "user" varchar(200) NOT NULL default '',
  user_from varchar(200) NOT NULL default '',
  user_to text NOT NULL,
  user_cc text NOT NULL,
  user_bcc text NOT NULL,
  subject varchar(255) NOT NULL default '',
  body text NOT NULL,
  hash varchar(32) NOT NULL default '',
  datetime integer NOT NULL default '0',
  isRead varchar(1) NOT NULL default '',
  isReplied varchar(1) NOT NULL default '',
  isFlagged varchar(1) NOT NULL default '',
  priority integer NOT NULL default '0',
  PRIMARY KEY  (msgId)
) ;
--
-- Table structure for table 'tiki_actionlog'
--
CREATE TABLE tiki_actionlog (
  action varchar(255) NOT NULL default '',
  lastModif integer NOT NULL default '0',
  pageName varchar(160) NOT NULL default '',
  "user" varchar(200) NOT NULL default '',
  ip varchar(15) NOT NULL default '',
  comment varchar(200) NOT NULL default ''
) ;
--
-- Table structure for table 'tiki_articles'
--
CREATE TABLE tiki_articles (
  articleId integer NOT NULL default nextval('tiki_articles_seq'),
  title varchar(80) NOT NULL default '',
  authorName varchar(60) NOT NULL default '',
  topicId integer NOT NULL default '0',
  topicName varchar(40) NOT NULL default '',
  size integer NOT NULL default '0',
  useImage varchar(1) NOT NULL default '',
  image_name varchar(80) NOT NULL default '',
  image_type varchar(80) NOT NULL default '',
  image_size integer NOT NULL default '0',
  image_x integer NOT NULL default '0',
  image_y integer NOT NULL default '0',
  image_data text,
  publishDate integer NOT NULL default '0',
  created integer NOT NULL default '0',
  heading text NOT NULL,
  body text NOT NULL,
  hash varchar(32) NOT NULL default '',
  author varchar(200) NOT NULL default '',
  reads integer NOT NULL default '0',
  votes integer NOT NULL default '0',
  points integer NOT NULL default '0',
  type varchar(50) NOT NULL default '',
  rating decimal(4,2) NOT NULL default '0.00',
  isfloat varchar(1) NOT NULL default '',
  PRIMARY KEY  (articleId)
) ;
--
-- Table structure for table 'tiki_banners'
--
CREATE TABLE tiki_banners (
  bannerId integer NOT NULL default nextval('tiki_banners_seq'),
  client varchar(200) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  title varchar(255) NOT NULL default '',
  alt varchar(250) NOT NULL default '',
  which varchar(50) NOT NULL default '',
  imageData text,
  imageType varchar(200) NOT NULL default '',
  imageName varchar(100) NOT NULL default '',
  HTMLData text NOT NULL,
  fixedURLData varchar(255) NOT NULL default '',
  textData text NOT NULL,
  fromDate integer NOT NULL default '0',
  toDate integer NOT NULL default '0',
  useDates varchar(1) NOT NULL default '',
  mon varchar(1) NOT NULL default '',
  tue varchar(1) NOT NULL default '',
  wed varchar(1) NOT NULL default '',
  thu varchar(1) NOT NULL default '',
  fri varchar(1) NOT NULL default '',
  sat varchar(1) NOT NULL default '',
  sun varchar(1) NOT NULL default '',
  hourFrom varchar(4) NOT NULL default '',
  hourTo varchar(4) NOT NULL default '',
  created integer NOT NULL default '0',
  maxImpressions integer NOT NULL default '0',
  impressions integer NOT NULL default '0',
  clicks integer NOT NULL default '0',
  zone varchar(40) NOT NULL default '',
  PRIMARY KEY  (bannerId)
) ;
--
-- Table structure for table 'tiki_banning'
--
CREATE TABLE tiki_banning (
  banId integer NOT NULL default nextval('tiki_banning_seq'),
  mode varchar(4) check (mode in ('user','ip')) NOT NULL default 'user',
  title varchar(200) NOT NULL default '',
  ip1 varchar(3) NOT NULL default '',
  ip2 varchar(3) NOT NULL default '',
  ip3 varchar(3) NOT NULL default '',
  ip4 varchar(3) NOT NULL default '',
  "user" varchar(200) NOT NULL default '',
  date_from datetime not null default now() NOT NULL,
  date_to datetime not null default now() NOT NULL,
  use_dates varchar(1) NOT NULL default '',
  created integer NOT NULL default '0',
  message text NOT NULL,
  PRIMARY KEY  (banId)
) ;
--
-- Table structure for table 'tiki_banning_sections'
--
CREATE TABLE tiki_banning_sections (
  banId integer NOT NULL default '0',
  section varchar(100) NOT NULL default '',
  PRIMARY KEY  (banId,section)
) ;
--
-- Table structure for table 'tiki_blog_activity'
--
CREATE TABLE tiki_blog_activity (
  blogId integer NOT NULL default '0',
  day integer NOT NULL default '0',
  posts integer NOT NULL default '0',
  PRIMARY KEY  (blogId,day)
) ;
--
-- Table structure for table 'tiki_blog_posts'
--
CREATE TABLE tiki_blog_posts (
  postId integer NOT NULL default nextval('tiki_blog_posts_seq'),
  blogId integer NOT NULL default '0',
  data text NOT NULL,
  created integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  trackbacks_to text NOT NULL,
  trackbacks_from text NOT NULL,
  title varchar(80) NOT NULL default '',
  PRIMARY KEY  (postId)
) ;
--
-- Table structure for table 'tiki_blog_posts_images'
--
CREATE TABLE tiki_blog_posts_images (
  imgId integer NOT NULL default nextval('tiki_blog_posts_images_seq'),
  postId integer NOT NULL default '0',
  filename varchar(80) NOT NULL default '',
  filetype varchar(80) NOT NULL default '',
  filesize integer NOT NULL default '0',
  data text,
  PRIMARY KEY  (imgId)
) ;
--
-- Table structure for table 'tiki_blogs'
--
CREATE TABLE tiki_blogs (
  blogId integer NOT NULL default nextval('tiki_blogs_seq'),
  created integer NOT NULL default '0',
  lastModif integer NOT NULL default '0',
  title varchar(200) NOT NULL default '',
  description text NOT NULL,
  "user" varchar(200) NOT NULL default '',
  "public" varchar(1) NOT NULL default '',
  posts integer NOT NULL default '0',
  maxPosts integer NOT NULL default '0',
  hits integer NOT NULL default '0',
  activity decimal(4,2) NOT NULL default '0.00',
  heading text NOT NULL,
  use_find varchar(1) NOT NULL default '',
  use_title varchar(1) NOT NULL default '',
  add_date varchar(1) NOT NULL default '',
  add_poster varchar(1) NOT NULL default '',
  allow_comments varchar(1) NOT NULL default '',
  PRIMARY KEY  (blogId)
) ;
--
-- Table structure for table 'tiki_calendar_categories'
--
CREATE TABLE tiki_calendar_categories (
  calcatId integer NOT NULL default nextval('tiki_calendar_categories_seq'),
  calendarId integer NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  PRIMARY KEY  (calcatId)
  ,UNIQUE (calendarId,name)) ;
--
-- Table structure for table 'tiki_calendar_items'
--
CREATE TABLE tiki_calendar_items (
  calitemId integer NOT NULL default nextval('tiki_calendar_items_seq'),
  calendarId integer NOT NULL default '0',
  start integer NOT NULL default '0',
  "end" integer NOT NULL default '0',
  locationId integer NOT NULL default '0',
  categoryId integer NOT NULL default '0',
  priority varchar(1) check (priority in ('1','2','3','4','5','6','7','8','9')) NOT NULL default '1',
  status varchar(1) check (status in ('0','1','2')) NOT NULL default '0',
  url varchar(255) NOT NULL default '',
  lang varchar(2) NOT NULL default 'en',
  name varchar(255) NOT NULL default '',
  description text,
  "user" varchar(40) NOT NULL default '',
  created integer NOT NULL default '0',
  lastmodif integer NOT NULL default '0',
  PRIMARY KEY  (calitemId)
) ;
--
-- Table structure for table 'tiki_calendar_locations'
--
CREATE TABLE tiki_calendar_locations (
  callocId integer NOT NULL default nextval('tiki_calendar_locations_seq'),
  calendarId integer NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  description text,
  PRIMARY KEY  (callocId)
  ,UNIQUE (calendarId,name)) ;
--
-- Table structure for table 'tiki_calendar_roles'
--
CREATE TABLE tiki_calendar_roles (
  calitemId integer NOT NULL default '0',
  username varchar(40) NOT NULL default '',
  role varchar(1) check (role in ('0','1','2','3','6')) NOT NULL default '0',
  PRIMARY KEY  (calitemId,username,role)) ;
--
-- Table structure for table 'tiki_calendars'
--
CREATE TABLE tiki_calendars (
  calendarId integer NOT NULL default nextval('tiki_calendars_seq'),
  name varchar(80) NOT NULL default '',
  description varchar(255) NOT NULL default '',
  "user" varchar(40) NOT NULL default '',
  customlocations varchar(1) check (customlocations in ('n','y')) NOT NULL default 'n',
  customcategories varchar(1) check (customcategories in ('n','y')) NOT NULL default 'n',
  customlanguages varchar(1) check (customlanguages in ('n','y')) NOT NULL default 'n',
  custompriorities varchar(1) check (custompriorities in ('n','y')) NOT NULL default 'n',
  customparticipants varchar(1) check (customparticipants in ('n','y')) NOT NULL default 'n',
  created integer NOT NULL default '0',
  lastmodif integer NOT NULL default '0',
  PRIMARY KEY  (calendarId)
) ;
--
-- Table structure for table 'tiki_categories'
--
CREATE TABLE tiki_categories (
  categId integer NOT NULL default nextval('tiki_categories_seq'),
  name varchar(100) NOT NULL default '',
  description varchar(250) NOT NULL default '',
  parentId integer NOT NULL default '0',
  hits integer NOT NULL default '0',
  PRIMARY KEY  (categId)
) ;
--
-- Table structure for table 'tiki_categorized_objects'
--
CREATE TABLE tiki_categorized_objects (
  catObjectId integer NOT NULL default nextval('tiki_categorized_objects_seq'),
  type varchar(50) NOT NULL default '',
  objId varchar(255) NOT NULL default '',
  description text NOT NULL,
  created integer NOT NULL default '0',
  name varchar(200) NOT NULL default '',
  href varchar(200) NOT NULL default '',
  hits integer NOT NULL default '0',
  PRIMARY KEY  (catObjectId)
) ;
--
-- Table structure for table 'tiki_category_objects'
--
CREATE TABLE tiki_category_objects (
  catObjectId integer NOT NULL default '0',
  categId integer NOT NULL default '0',
  PRIMARY KEY  (catObjectId,categId)
) ;
--
-- Table structure for table 'tiki_category_sites'
--
CREATE TABLE tiki_category_sites (
  categId integer NOT NULL default '0',
  siteId integer NOT NULL default '0',
  PRIMARY KEY  (categId,siteId)
) ;
--
-- Table structure for table 'tiki_chart_items'
--
CREATE TABLE tiki_chart_items (
  itemId integer NOT NULL default nextval('tiki_chart_items_seq'),
  title varchar(250) NOT NULL default '',
  description text NOT NULL,
  chartId integer NOT NULL default '0',
  created integer NOT NULL default '0',
  URL varchar(250) NOT NULL default '',
  votes integer NOT NULL default '0',
  points integer NOT NULL default '0',
  average decimal(4,2) NOT NULL default '0.00',
  PRIMARY KEY  (itemId)
) ;
--
-- Table structure for table 'tiki_charts'
--
CREATE TABLE tiki_charts (
  chartId integer NOT NULL default nextval('tiki_charts_seq'),
  title varchar(250) NOT NULL default '',
  description text NOT NULL,
  hits integer NOT NULL default '0',
  singleItemVotes varchar(1) NOT NULL default '',
  singleChartVotes varchar(1) NOT NULL default '',
  suggestions varchar(1) NOT NULL default '',
  autoValidate varchar(1) NOT NULL default '',
  topN integer NOT NULL default '0',
  maxVoteValue integer NOT NULL default '0',
  frequency integer NOT NULL default '0',
  showAverage varchar(1) NOT NULL default '',
  isActive varchar(1) NOT NULL default '',
  showVotes varchar(1) NOT NULL default '',
  useCookies varchar(1) NOT NULL default '',
  lastChart integer NOT NULL default '0',
  voteAgainAfter integer NOT NULL default '0',
  created integer NOT NULL default '0',
  hist integer NOT NULL default '0',
  PRIMARY KEY  (chartId)
) ;
--
-- Table structure for table 'tiki_charts_rankings'
--
CREATE TABLE tiki_charts_rankings (
  chartId integer NOT NULL default '0',
  itemId integer NOT NULL default '0',
  position integer NOT NULL default '0',
  timestamp integer NOT NULL default '0',
  lastPosition integer NOT NULL default '0',
  period integer NOT NULL default '0',
  rvotes integer NOT NULL default '0',
  raverage decimal(4,2) NOT NULL default '0.00',
  PRIMARY KEY  (chartId,itemId,period)
) ;
--
-- Table structure for table 'tiki_charts_votes'
--
CREATE TABLE tiki_charts_votes (
  "user" varchar(200) NOT NULL default '',
  itemId integer NOT NULL default '0',
  timestamp integer NOT NULL default '0',
  chartId integer NOT NULL default '0',
  PRIMARY KEY  ("user",itemId)
) ;
--
-- Table structure for table 'tiki_chat_channels'
--
CREATE TABLE tiki_chat_channels (
  channelId integer NOT NULL default nextval('tiki_chat_channels_seq'),
  name varchar(30) NOT NULL default '',
  description varchar(250) NOT NULL default '',
  max_users integer NOT NULL default '0',
  mode varchar(1) NOT NULL default '',
  moderator varchar(200) NOT NULL default '',
  active varchar(1) NOT NULL default '',
  refresh integer NOT NULL default '0',
  PRIMARY KEY  (channelId)
) ;
--
-- Table structure for table 'tiki_chat_messages'
--
CREATE TABLE tiki_chat_messages (
  messageId integer NOT NULL default nextval('tiki_chat_messages_seq'),
  channelId integer NOT NULL default '0',
  data varchar(255) NOT NULL default '',
  poster varchar(200) NOT NULL default 'anonymous',
  timestamp integer NOT NULL default '0',
  PRIMARY KEY  (messageId)
) ;
--
-- Table structure for table 'tiki_chat_users'
--
CREATE TABLE tiki_chat_users (
  nickname varchar(200) NOT NULL default '',
  channelId integer NOT NULL default '0',
  timestamp integer NOT NULL default '0',
  PRIMARY KEY  (nickname,channelId)
) ;
--
-- Table structure for table 'tiki_comments'
--
CREATE TABLE tiki_comments (
  threadId integer NOT NULL default nextval('tiki_comments_seq'),
  object varchar(32) NOT NULL default '',
  parentId integer NOT NULL default '0',
  userName varchar(200) NOT NULL default '',
  commentDate integer NOT NULL default '0',
  hits integer NOT NULL default '0',
  type varchar(1) NOT NULL default '',
  points decimal(8,2) NOT NULL default '0.00',
  votes integer NOT NULL default '0',
  average decimal(8,4) NOT NULL default '0.0000',
  title varchar(100) NOT NULL default '',
  data text NOT NULL,
  hash varchar(32) NOT NULL default '',
  summary varchar(240) NOT NULL default '',
  smiley varchar(80) NOT NULL default '',
  user_ip varchar(15) NOT NULL default '',
  PRIMARY KEY  (threadId)
) ;
--
-- Table structure for table 'tiki_content'
--
CREATE TABLE tiki_content (
  contentId integer NOT NULL default nextval('tiki_content_seq'),
  description text NOT NULL,
  PRIMARY KEY  (contentId)
) ;
--
-- Table structure for table 'tiki_content_templates'
--
CREATE TABLE tiki_content_templates (
  templateId integer NOT NULL default nextval('tiki_content_templates_seq'),
  content text,
  name varchar(200) NOT NULL default '',
  created integer NOT NULL default '0',
  PRIMARY KEY  (templateId)
) ;
--
-- Table structure for table 'tiki_content_templates_sections'
--
CREATE TABLE tiki_content_templates_sections (
  templateId integer NOT NULL default '0',
  section varchar(250) NOT NULL default '',
  PRIMARY KEY  (templateId,section)
) ;
--
-- Table structure for table 'tiki_cookies'
--
CREATE TABLE tiki_cookies (
  cookieId integer NOT NULL default nextval('tiki_cookies_seq'),
  cookie varchar(255) NOT NULL default '',
  PRIMARY KEY  (cookieId)
) ;
--
-- Table structure for table 'tiki_copyrights'
--
CREATE TABLE tiki_copyrights (
  copyrightId integer NOT NULL default nextval('tiki_copyrights_seq'),
  page varchar(200) NOT NULL default '',
  title varchar(200) NOT NULL default '',
  year integer NOT NULL default '0',
  authors varchar(200) NOT NULL default '',
  copyright_order integer NOT NULL default '0',
  userName varchar(200) NOT NULL default '',
  PRIMARY KEY  (copyrightId)
) ;
--
-- Table structure for table 'tiki_directory_categories'
--
CREATE TABLE tiki_directory_categories (
  categId integer NOT NULL default nextval('tiki_directory_categories_seq'),
  parent integer NOT NULL default '0',
  name varchar(240) NOT NULL default '',
  description text NOT NULL,
  childrenType varchar(1) NOT NULL default '',
  sites integer NOT NULL default '0',
  viewableChildren integer NOT NULL default '0',
  allowSites varchar(1) NOT NULL default '',
  showCount varchar(1) NOT NULL default '',
  editorGroup varchar(200) NOT NULL default '',
  hits integer NOT NULL default '0',
  PRIMARY KEY  (categId)
) ;
--
-- Table structure for table 'tiki_directory_search'
--
CREATE TABLE tiki_directory_search (
  term varchar(250) NOT NULL default '',
  hits integer NOT NULL default '0',
  PRIMARY KEY  (term)
) ;
--
-- Table structure for table 'tiki_directory_sites'
--
CREATE TABLE tiki_directory_sites (
  siteId integer NOT NULL default nextval('tiki_directory_sites_seq'),
  name varchar(240) NOT NULL default '',
  description text NOT NULL,
  url varchar(255) NOT NULL default '',
  country varchar(255) NOT NULL default '',
  hits integer NOT NULL default '0',
  isValid varchar(1) NOT NULL default '',
  created integer NOT NULL default '0',
  lastModif integer NOT NULL default '0',
  cache text,
  cache_timestamp integer NOT NULL default '0',
  PRIMARY KEY  (siteId)
) ;
--
-- Table structure for table 'tiki_drawings'
--
CREATE TABLE tiki_drawings (
  drawId integer NOT NULL default nextval('tiki_drawings_seq'),
  version integer NOT NULL default '0',
  name varchar(250) NOT NULL default '',
  filename_draw varchar(250) NOT NULL default '',
  filename_pad varchar(250) NOT NULL default '',
  timestamp integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  PRIMARY KEY  (drawId)
) ;
--
-- Table structure for table 'tiki_dsn'
--
CREATE TABLE tiki_dsn (
  dsnId integer NOT NULL default nextval('tiki_dsn_seq'),
  name varchar(20) NOT NULL default '',
  dsn varchar(255) NOT NULL default '',
  PRIMARY KEY  (dsnId)
) ;
--
-- Table structure for table 'tiki_eph'
--
CREATE TABLE tiki_eph (
  ephId integer NOT NULL default nextval('tiki_eph_seq'),
  title varchar(250) NOT NULL default '',
  isFile varchar(1) NOT NULL default '',
  filename varchar(250) NOT NULL default '',
  filetype varchar(250) NOT NULL default '',
  filesize varchar(250) NOT NULL default '',
  data text,
  textdata text,
  publish integer NOT NULL default '0',
  hits integer NOT NULL default '0',
  PRIMARY KEY  (ephId)
) ;
--
-- Table structure for table 'tiki_extwiki'
--
CREATE TABLE tiki_extwiki (
  extwikiId integer NOT NULL default nextval('tiki_extwiki_seq'),
  name varchar(20) NOT NULL default '',
  extwiki varchar(255) NOT NULL default '',
  PRIMARY KEY  (extwikiId)
) ;
--
-- Table structure for table 'tiki_faq_questions'
--
CREATE TABLE tiki_faq_questions (
  questionId integer NOT NULL default nextval('tiki_faq_questions_seq'),
  faqId integer NOT NULL default '0',
  position integer NOT NULL default '0',
  question text NOT NULL,
  answer text NOT NULL,
  PRIMARY KEY  (questionId)
) ;
--
-- Table structure for table 'tiki_faqs'
--
CREATE TABLE tiki_faqs (
  faqId integer NOT NULL default nextval('tiki_faqs_seq'),
  title varchar(200) NOT NULL default '',
  description text NOT NULL,
  created integer NOT NULL default '0',
  questions integer NOT NULL default '0',
  hits integer NOT NULL default '0',
  canSuggest varchar(1) NOT NULL default '',
  PRIMARY KEY  (faqId)
) ;
--
-- Table structure for table 'tiki_featured_links'
--
CREATE TABLE tiki_featured_links (
  url varchar(200) NOT NULL default '',
  title varchar(40) NOT NULL default '',
  description text NOT NULL,
  hits integer NOT NULL default '0',
  position integer NOT NULL default '0',
  type varchar(1) NOT NULL default '',
  PRIMARY KEY  (url)
) ;
--
-- Table structure for table 'tiki_file_galleries'
--
CREATE TABLE tiki_file_galleries (
  galleryId integer NOT NULL default nextval('tiki_file_galleries_seq'),
  name varchar(80) NOT NULL default '',
  description text NOT NULL,
  created integer NOT NULL default '0',
  visible varchar(1) NOT NULL default '',
  lastModif integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  hits integer NOT NULL default '0',
  votes integer NOT NULL default '0',
  points decimal(8,2) NOT NULL default '0.00',
  maxRows integer NOT NULL default '0',
  "public" varchar(1) NOT NULL default '',
  show_id varchar(1) NOT NULL default '',
  show_icon varchar(1) NOT NULL default '',
  show_name varchar(1) NOT NULL default '',
  show_size varchar(1) NOT NULL default '',
  show_description varchar(1) NOT NULL default '',
  max_desc integer NOT NULL default '0',
  show_created varchar(1) NOT NULL default '',
  show_dl varchar(1) NOT NULL default '',
  PRIMARY KEY  (galleryId)
) ;
--
-- Table structure for table 'tiki_files'
--
CREATE TABLE tiki_files (
  fileId integer NOT NULL default nextval('tiki_files_seq'),
  galleryId integer NOT NULL default '0',
  name varchar(40) NOT NULL default '',
  description text NOT NULL,
  created integer NOT NULL default '0',
  filename varchar(80) NOT NULL default '',
  filesize integer NOT NULL default '0',
  filetype varchar(250) NOT NULL default '',
  data text,
  "user" varchar(200) NOT NULL default '',
  downloads integer NOT NULL default '0',
  votes integer NOT NULL default '0',
  points decimal(8,2) NOT NULL default '0.00',
  path varchar(255) NOT NULL default '',
  hash varchar(32) NOT NULL default '',
  reference_url varchar(250) NOT NULL default '',
  is_reference varchar(1) NOT NULL default '',
  PRIMARY KEY  (fileId)
) ;
--
-- Table structure for table 'tiki_forum_attachments'
--
CREATE TABLE tiki_forum_attachments (
  attId integer NOT NULL default nextval('tiki_forum_attachments_seq'),
  threadId integer NOT NULL default '0',
  qId integer NOT NULL default '0',
  forumId integer NOT NULL default '0',
  filename varchar(250) NOT NULL default '',
  filetype varchar(250) NOT NULL default '',
  filesize integer NOT NULL default '0',
  data text,
  dir varchar(200) NOT NULL default '',
  created integer NOT NULL default '0',
  path varchar(250) NOT NULL default '',
  PRIMARY KEY  (attId)
) ;
--
-- Table structure for table 'tiki_forum_reads'
--
CREATE TABLE tiki_forum_reads (
  "user" varchar(200) NOT NULL default '',
  threadId integer NOT NULL default '0',
  forumId integer NOT NULL default '0',
  timestamp integer NOT NULL default '0',
  PRIMARY KEY  ("user",threadId)
) ;
--
-- Table structure for table 'tiki_forums'
--
CREATE TABLE tiki_forums (
  forumId integer NOT NULL default nextval('tiki_forums_seq'),
  name varchar(200) NOT NULL default '',
  description text NOT NULL,
  created integer NOT NULL default '0',
  lastPost integer NOT NULL default '0',
  threads integer NOT NULL default '0',
  comments integer NOT NULL default '0',
  controlFlood varchar(1) NOT NULL default '',
  floodInterval integer NOT NULL default '0',
  moderator varchar(200) NOT NULL default '',
  hits integer NOT NULL default '0',
  mail varchar(200) NOT NULL default '',
  useMail varchar(1) NOT NULL default '',
  usePruneUnreplied varchar(1) NOT NULL default '',
  pruneUnrepliedAge integer NOT NULL default '0',
  usePruneOld varchar(1) NOT NULL default '',
  pruneMaxAge integer NOT NULL default '0',
  topicsPerPage integer NOT NULL default '0',
  topicOrdering varchar(100) NOT NULL default '',
  threadOrdering varchar(100) NOT NULL default '',
  section varchar(200) NOT NULL default '',
  topics_list_replies varchar(1) NOT NULL default '',
  topics_list_reads varchar(1) NOT NULL default '',
  topics_list_pts varchar(1) NOT NULL default '',
  topics_list_lastpost varchar(1) NOT NULL default '',
  topics_list_author varchar(1) NOT NULL default '',
  vote_threads varchar(1) NOT NULL default '',
  moderator_group varchar(200) NOT NULL default '',
  approval_type varchar(20) NOT NULL default '',
  outbound_address varchar(1) NOT NULL default '',
  inbound_address varchar(1) NOT NULL default '',
  topic_smileys varchar(1) NOT NULL default '',
  ui_avatar varchar(1) NOT NULL default '',
  ui_flag varchar(1) NOT NULL default '',
  ui_posts varchar(1) NOT NULL default '',
  ui_email varchar(1) NOT NULL default '',
  ui_online varchar(1) NOT NULL default '',
  topic_summary varchar(1) NOT NULL default '',
  show_description varchar(1) NOT NULL default '',
  att varchar(80) NOT NULL default '',
  att_store varchar(4) NOT NULL default '',
  att_store_dir varchar(250) NOT NULL default '',
  att_max_size integer NOT NULL default '0',
  ui_level varchar(1) NOT NULL default '',
  forum_password varchar(32) NOT NULL default '',
  forum_use_password varchar(1) NOT NULL default '',
  inbound_pop_server varchar(250) NOT NULL default '',
  inbound_pop_port integer NOT NULL default '0',
  inbound_pop_user varchar(200) NOT NULL default '',
  inbound_pop_password varchar(80) NOT NULL default '',
  PRIMARY KEY  (forumId)
) ;
--
-- Table structure for table 'tiki_forums_queue'
--
CREATE TABLE tiki_forums_queue (
  qId integer NOT NULL default nextval('tiki_forums_queue_seq'),
  object varchar(32) NOT NULL default '',
  parentId integer NOT NULL default '0',
  forumId integer NOT NULL default '0',
  timestamp integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  title varchar(240) NOT NULL default '',
  data text NOT NULL,
  type varchar(60) NOT NULL default '',
  hash varchar(32) NOT NULL default '',
  topic_smiley varchar(80) NOT NULL default '',
  topic_title varchar(240) NOT NULL default '',
  summary varchar(240) NOT NULL default '',
  PRIMARY KEY  (qId)
) ;
--
-- Table structure for table 'tiki_forums_reported'
--
CREATE TABLE tiki_forums_reported (
  threadId integer NOT NULL default '0',
  forumId integer NOT NULL default '0',
  parentId integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  timestamp integer NOT NULL default '0',
  reason varchar(250) NOT NULL default '',
  PRIMARY KEY  (threadId)
) ;
--
-- Table structure for table 'tiki_galleries'
--
CREATE TABLE tiki_galleries (
  galleryId integer NOT NULL default nextval('tiki_galleries_seq'),
  name varchar(80) NOT NULL default '',
  description text NOT NULL,
  created integer NOT NULL default '0',
  lastModif integer NOT NULL default '0',
  visible varchar(1) NOT NULL default '',
  theme varchar(60) NOT NULL default '',
  "user" varchar(200) NOT NULL default '',
  hits integer NOT NULL default '0',
  maxRows integer NOT NULL default '0',
  rowImages integer NOT NULL default '0',
  thumbSizeX integer NOT NULL default '0',
  thumbSizeY integer NOT NULL default '0',
  "public" varchar(1) NOT NULL default '',
  PRIMARY KEY  (galleryId)
) ;
--
-- Table structure for table 'tiki_galleries_scales'
--
CREATE TABLE tiki_galleries_scales (
  galleryId integer NOT NULL default '0',
  xsize integer NOT NULL default '0',
  ysize integer NOT NULL default '0',
  PRIMARY KEY  (galleryId,xsize,ysize)
) ;
--
-- Table structure for table 'tiki_games'
--
CREATE TABLE tiki_games (
  gameName varchar(200) NOT NULL default '',
  hits integer NOT NULL default '0',
  votes integer NOT NULL default '0',
  points integer NOT NULL default '0',
  PRIMARY KEY  (gameName)
) ;
--
-- Table structure for table 'tiki_group_inclusion'
--
CREATE TABLE tiki_group_inclusion (
  groupName varchar(30) NOT NULL default '',
  includeGroup varchar(30) NOT NULL default '',
  PRIMARY KEY  (groupName,includeGroup)
) ;
--
-- Table structure for table 'tiki_history'
--
CREATE TABLE tiki_history (
  pageName varchar(160) NOT NULL default '',
  version integer NOT NULL default '0',
  lastModif integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  ip varchar(15) NOT NULL default '',
  comment varchar(200) NOT NULL default '',
  data text,
  description varchar(200) NOT NULL default '',
  PRIMARY KEY  (pageName,version)
) ;
--
-- Table structure for table 'tiki_hotwords'
--
CREATE TABLE tiki_hotwords (
  word varchar(40) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  PRIMARY KEY  (word)
) ;
--
-- Table structure for table 'tiki_html_pages'
--
CREATE TABLE tiki_html_pages (
  pageName varchar(40) NOT NULL default '',
  content text,
  refresh integer NOT NULL default '0',
  type varchar(1) NOT NULL default '',
  created integer NOT NULL default '0',
  PRIMARY KEY  (pageName)
) ;
--
-- Table structure for table 'tiki_html_pages_dynamic_zones'
--
CREATE TABLE tiki_html_pages_dynamic_zones (
  pageName varchar(40) NOT NULL default '',
  zone varchar(80) NOT NULL default '',
  type varchar(2) NOT NULL default '',
  content text NOT NULL,
  PRIMARY KEY  (pageName,zone)
) ;
--
-- Table structure for table 'tiki_images'
--
CREATE TABLE tiki_images (
  imageId integer NOT NULL default nextval('tiki_images_seq'),
  galleryId integer NOT NULL default '0',
  name varchar(40) NOT NULL default '',
  description text NOT NULL,
  created integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  hits integer NOT NULL default '0',
  path varchar(255) NOT NULL default '',
  PRIMARY KEY  (imageId)
) ;
--
-- Table structure for table 'tiki_images_data'
--
CREATE TABLE tiki_images_data (
  imageId integer NOT NULL default '0',
  xsize integer NOT NULL default '0',
  ysize integer NOT NULL default '0',
  type varchar(1) NOT NULL default '',
  filesize integer NOT NULL default '0',
  filetype varchar(80) NOT NULL default '',
  filename varchar(80) NOT NULL default '',
  data text,
  PRIMARY KEY  (imageId,xsize,ysize,type)
) ;
--
-- Table structure for table 'tiki_images_old'
--
CREATE TABLE tiki_images_old (
  imageId integer NOT NULL default nextval('tiki_images_old_seq'),
  galleryId integer NOT NULL default '0',
  name varchar(40) NOT NULL default '',
  description text NOT NULL,
  created integer NOT NULL default '0',
  filename varchar(80) NOT NULL default '',
  filetype varchar(80) NOT NULL default '',
  filesize integer NOT NULL default '0',
  data text,
  xsize integer NOT NULL default '0',
  ysize integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  t_data text,
  t_type varchar(30) NOT NULL default '',
  hits integer NOT NULL default '0',
  path varchar(255) NOT NULL default '',
  PRIMARY KEY  (imageId)
) ;
--
-- Table structure for table 'tiki_language'
--
CREATE TABLE tiki_language (
  source text NOT NULL,
  lang varchar(2) NOT NULL default '',
  tran text,
  PRIMARY KEY  (source,lang)) ;
--
-- Table structure for table 'tiki_languages'
--
CREATE TABLE tiki_languages (
  lang varchar(2) NOT NULL default '',
  language varchar(255) NOT NULL default '',
  PRIMARY KEY  (lang)
) ;
--
-- Table structure for table 'tiki_link_cache'
--
CREATE TABLE tiki_link_cache (
  cacheId integer NOT NULL default nextval('tiki_link_cache_seq'),
  url varchar(250) NOT NULL default '',
  data text,
  refresh integer NOT NULL default '0',
  PRIMARY KEY  (cacheId)
) ;
--
-- Table structure for table 'tiki_links'
--
CREATE TABLE tiki_links (
  fromPage varchar(160) NOT NULL default '',
  toPage varchar(160) NOT NULL default '',
  PRIMARY KEY  (fromPage,toPage)
) ;
--
-- Table structure for table 'tiki_live_support_events'
--
CREATE TABLE tiki_live_support_events (
  eventId integer NOT NULL default nextval('tiki_live_support_events_seq'),
  reqId varchar(32) NOT NULL default '',
  type varchar(40) NOT NULL default '',
  seqId integer NOT NULL default '0',
  senderId varchar(32) NOT NULL default '',
  data text NOT NULL,
  timestamp integer NOT NULL default '0',
  PRIMARY KEY  (eventId)
) ;
--
-- Table structure for table 'tiki_live_support_message_comments'
--
CREATE TABLE tiki_live_support_message_comments (
  cId integer NOT NULL default nextval('tiki_live_support_message_c_seq'),
  msgId integer NOT NULL default '0',
  data text NOT NULL,
  timestamp integer NOT NULL default '0',
  PRIMARY KEY  (cId)
) ;
--
-- Table structure for table 'tiki_live_support_messages'
--
CREATE TABLE tiki_live_support_messages (
  msgId integer NOT NULL default nextval('tiki_live_support_messages_seq'),
  data text NOT NULL,
  timestamp integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  username varchar(200) NOT NULL default '',
  priority integer NOT NULL default '0',
  status varchar(1) NOT NULL default '',
  assigned_to varchar(200) NOT NULL default '',
  resolution varchar(100) NOT NULL default '',
  title varchar(200) NOT NULL default '',
  module integer NOT NULL default '0',
  email varchar(250) NOT NULL default '',
  PRIMARY KEY  (msgId)
) ;
--
-- Table structure for table 'tiki_live_support_modules'
--
CREATE TABLE tiki_live_support_modules (
  modId integer NOT NULL default nextval('tiki_live_support_modules_seq'),
  name varchar(90) NOT NULL default '',
  PRIMARY KEY  (modId)
) ;
--
-- Table structure for table 'tiki_live_support_operators'
--
CREATE TABLE tiki_live_support_operators (
  "user" varchar(200) NOT NULL default '',
  accepted_requests integer NOT NULL default '0',
  status varchar(20) NOT NULL default '',
  longest_chat integer NOT NULL default '0',
  shortest_chat integer NOT NULL default '0',
  average_chat integer NOT NULL default '0',
  last_chat integer NOT NULL default '0',
  time_online integer NOT NULL default '0',
  votes integer NOT NULL default '0',
  points integer NOT NULL default '0',
  status_since integer NOT NULL default '0',
  PRIMARY KEY  ("user")
) ;
--
-- Table structure for table 'tiki_live_support_requests'
--
CREATE TABLE tiki_live_support_requests (
  reqId varchar(32) NOT NULL default '',
  "user" varchar(200) NOT NULL default '',
  tiki_user varchar(200) NOT NULL default '',
  email varchar(200) NOT NULL default '',
  operator varchar(200) NOT NULL default '',
  operator_id varchar(32) NOT NULL default '',
  user_id varchar(32) NOT NULL default '',
  reason text NOT NULL,
  req_timestamp integer NOT NULL default '0',
  timestamp integer NOT NULL default '0',
  status varchar(40) NOT NULL default '',
  resolution varchar(40) NOT NULL default '',
  chat_started integer NOT NULL default '0',
  chat_ended integer NOT NULL default '0',
  PRIMARY KEY  (reqId)
) ;
--
-- Table structure for table 'tiki_mail_events'
--
CREATE TABLE tiki_mail_events (
  event varchar(200) NOT NULL default '',
  object varchar(200) NOT NULL default '',
  email varchar(200) NOT NULL default ''
) ;
--
-- Table structure for table 'tiki_mailin_accounts'
--
CREATE TABLE tiki_mailin_accounts (
  accountId integer NOT NULL default nextval('tiki_mailin_accounts_seq'),
  "user" varchar(200) NOT NULL default '',
  account varchar(50) NOT NULL default '',
  pop varchar(255) NOT NULL default '',
  port integer NOT NULL default '0',
  username varchar(100) NOT NULL default '',
  pass varchar(100) NOT NULL default '',
  active varchar(1) NOT NULL default '',
  type varchar(40) NOT NULL default '',
  smtp varchar(255) NOT NULL default '',
  useAuth varchar(1) NOT NULL default '',
  smtpPort integer NOT NULL default '0',
  PRIMARY KEY  (accountId)
) ;
--
-- Table structure for table 'tiki_menu_languages'
--
CREATE TABLE tiki_menu_languages (
  menuId integer NOT NULL default nextval('tiki_menu_languages_seq'),
  language varchar(2) NOT NULL default '',
  PRIMARY KEY  (menuId,language)
) ;
--
-- Table structure for table 'tiki_menu_options'
--
CREATE TABLE tiki_menu_options (
  optionId integer NOT NULL default nextval('tiki_menu_options_seq'),
  menuId integer NOT NULL default '0',
  type varchar(1) NOT NULL default '',
  name varchar(20) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  position integer NOT NULL default '0',
  PRIMARY KEY  (optionId)
) ;
--
-- Table structure for table 'tiki_menus'
--
CREATE TABLE tiki_menus (
  menuId integer NOT NULL default nextval('tiki_menus_seq'),
  name varchar(20) NOT NULL default '',
  description text NOT NULL,
  type varchar(1) NOT NULL default '',
  PRIMARY KEY  (menuId)
) ;
--
-- Table structure for table 'tiki_minical_events'
--
CREATE TABLE tiki_minical_events (
  "user" varchar(200) NOT NULL default '',
  eventId integer NOT NULL default nextval('tiki_minical_events_seq'),
  title varchar(250) NOT NULL default '',
  description text NOT NULL,
  start integer NOT NULL default '0',
  "end" integer NOT NULL default '0',
  security varchar(1) NOT NULL default '',
  duration integer NOT NULL default '0',
  topicId integer NOT NULL default '0',
  reminded varchar(1) NOT NULL default '',
  PRIMARY KEY  (eventId)
) ;
--
-- Table structure for table 'tiki_minical_topics'
--
CREATE TABLE tiki_minical_topics (
  "user" varchar(200) NOT NULL default '',
  topicId integer NOT NULL default nextval('tiki_minical_topics_seq'),
  name varchar(250) NOT NULL default '',
  filename varchar(200) NOT NULL default '',
  filetype varchar(200) NOT NULL default '',
  filesize varchar(200) NOT NULL default '',
  data text,
  path varchar(250) NOT NULL default '',
  isIcon varchar(1) NOT NULL default '',
  PRIMARY KEY  (topicId)
) ;
--
-- Table structure for table 'tiki_modules'
--
CREATE TABLE tiki_modules (
  name varchar(200) NOT NULL default '',
  position varchar(1) NOT NULL default '',
  ord integer NOT NULL default '0',
  type varchar(1) NOT NULL default '',
  title varchar(40) NOT NULL default '',
  cache_time integer NOT NULL default '0',
  rows integer NOT NULL default '0',
  groups text NOT NULL,
  params varchar(250) NOT NULL default '',
  PRIMARY KEY  (name)
) ;
--
-- Table structure for table 'tiki_newsletter_subscriptions'
--
CREATE TABLE tiki_newsletter_subscriptions (
  nlId integer NOT NULL default '0',
  email varchar(255) NOT NULL default '',
  code varchar(32) NOT NULL default '',
  valid varchar(1) NOT NULL default '',
  subscribed integer NOT NULL default '0',
  PRIMARY KEY  (nlId,email)
) ;
--
-- Table structure for table 'tiki_newsletters'
--
CREATE TABLE tiki_newsletters (
  nlId integer NOT NULL default nextval('tiki_newsletters_seq'),
  name varchar(200) NOT NULL default '',
  description text NOT NULL,
  created integer NOT NULL default '0',
  lastSent integer NOT NULL default '0',
  editions integer NOT NULL default '0',
  users integer NOT NULL default '0',
  allowAnySub varchar(1) NOT NULL default '',
  frequency integer NOT NULL default '0',
  PRIMARY KEY  (nlId)
) ;
--
-- Table structure for table 'tiki_newsreader_marks'
--
CREATE TABLE tiki_newsreader_marks (
  "user" varchar(200) NOT NULL default '',
  serverId integer NOT NULL default '0',
  groupName varchar(255) NOT NULL default '',
  timestamp integer NOT NULL default '0',
  PRIMARY KEY  ("user",serverId,groupName)
) ;
--
-- Table structure for table 'tiki_newsreader_servers'
--
CREATE TABLE tiki_newsreader_servers (
  "user" varchar(200) NOT NULL default '',
  serverId integer NOT NULL default nextval('tiki_newsreader_servers_seq'),
  server varchar(250) NOT NULL default '',
  port integer NOT NULL default '0',
  username varchar(200) NOT NULL default '',
  password varchar(200) NOT NULL default '',
  PRIMARY KEY  (serverId)
) ;
--
-- Table structure for table 'tiki_page_footnotes'
--
CREATE TABLE tiki_page_footnotes (
  "user" varchar(200) NOT NULL default '',
  pageName varchar(250) NOT NULL default '',
  data text NOT NULL,
  PRIMARY KEY  ("user",pageName)
) ;
--
-- Table structure for table 'tiki_pages'
--
CREATE TABLE tiki_pages (
  pageName varchar(160) NOT NULL default '',
  hits integer NOT NULL default '0',
  data text NOT NULL,
  lastModif integer NOT NULL default '0',
  comment varchar(200) NOT NULL default '',
  version integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  ip varchar(15) NOT NULL default '',
  flag varchar(1) NOT NULL default '',
  points integer NOT NULL default '0',
  votes integer NOT NULL default '0',
  pageRank decimal(5,3) NOT NULL default '0.000',
  description varchar(200) NOT NULL default '',
  cache text,
  cache_timestamp integer NOT NULL default '0',
  creator varchar(200) NOT NULL default '',
  PRIMARY KEY  (pageName)
) ;
--
-- Table structure for table 'tiki_pageviews'
--
CREATE TABLE tiki_pageviews (
  day integer NOT NULL default '0',
  pageviews integer NOT NULL default '0',
  PRIMARY KEY  (day)
) ;
--
-- Table structure for table 'tiki_poll_options'
--
CREATE TABLE tiki_poll_options (
  pollId integer NOT NULL default '0',
  optionId integer NOT NULL default nextval('tiki_poll_options_seq'),
  title varchar(200) NOT NULL default '',
  votes integer NOT NULL default '0',
  PRIMARY KEY  (optionId)
) ;
--
-- Table structure for table 'tiki_polls'
--
CREATE TABLE tiki_polls (
  pollId integer NOT NULL default nextval('tiki_polls_seq'),
  title varchar(200) NOT NULL default '',
  votes integer NOT NULL default '0',
  active varchar(1) NOT NULL default '',
  publishDate integer NOT NULL default '0',
  PRIMARY KEY  (pollId)
) ;
--
-- Table structure for table 'tiki_preferences'
--
CREATE TABLE tiki_preferences (
  name varchar(40) NOT NULL default '',
  value varchar(250) NOT NULL default '',
  PRIMARY KEY  (name)
) ;
--
-- Table structure for table 'tiki_private_messages'
--
CREATE TABLE tiki_private_messages (
  messageId integer NOT NULL default nextval('tiki_private_messages_seq'),
  toNickname varchar(200) NOT NULL default '',
  data varchar(255) NOT NULL default '',
  poster varchar(200) NOT NULL default 'anonymous',
  timestamp integer NOT NULL default '0',
  PRIMARY KEY  (messageId)
) ;
--
-- Table structure for table 'tiki_programmed_content'
--
CREATE TABLE tiki_programmed_content (
  pId integer NOT NULL default nextval('tiki_programmed_content_seq'),
  contentId integer NOT NULL default '0',
  publishDate integer NOT NULL default '0',
  data text NOT NULL,
  PRIMARY KEY  (pId)
) ;
--
-- Table structure for table 'tiki_quiz_question_options'
--
CREATE TABLE tiki_quiz_question_options (
  optionId integer NOT NULL default nextval('tiki_quiz_question_options_seq'),
  questionId integer NOT NULL default '0',
  optionText text NOT NULL,
  points integer NOT NULL default '0',
  PRIMARY KEY  (optionId)
) ;
--
-- Table structure for table 'tiki_quiz_questions'
--
CREATE TABLE tiki_quiz_questions (
  questionId integer NOT NULL default nextval('tiki_quiz_questions_seq'),
  quizId integer NOT NULL default '0',
  question text NOT NULL,
  position integer NOT NULL default '0',
  type varchar(1) NOT NULL default '',
  maxPoints integer NOT NULL default '0',
  PRIMARY KEY  (questionId)
) ;
--
-- Table structure for table 'tiki_quiz_results'
--
CREATE TABLE tiki_quiz_results (
  resultId integer NOT NULL default nextval('tiki_quiz_results_seq'),
  quizId integer NOT NULL default '0',
  fromPoints integer NOT NULL default '0',
  toPoints integer NOT NULL default '0',
  answer text NOT NULL,
  PRIMARY KEY  (resultId)
) ;
--
-- Table structure for table 'tiki_quiz_stats'
--
CREATE TABLE tiki_quiz_stats (
  quizId integer NOT NULL default '0',
  questionId integer NOT NULL default '0',
  optionId integer NOT NULL default '0',
  votes integer NOT NULL default '0',
  PRIMARY KEY  (quizId,questionId,optionId)
) ;
--
-- Table structure for table 'tiki_quiz_stats_sum'
--
CREATE TABLE tiki_quiz_stats_sum (
  quizId integer NOT NULL default '0',
  quizName varchar(255) NOT NULL default '',
  timesTaken integer NOT NULL default '0',
  avgpoints decimal(5,2) NOT NULL default '0.00',
  avgavg decimal(5,2) NOT NULL default '0.00',
  avgtime decimal(5,2) NOT NULL default '0.00',
  PRIMARY KEY  (quizId)
) ;
--
-- Table structure for table 'tiki_quizzes'
--
CREATE TABLE tiki_quizzes (
  quizId integer NOT NULL default nextval('tiki_quizzes_seq'),
  name varchar(255) NOT NULL default '',
  description text NOT NULL,
  canRepeat varchar(1) NOT NULL default '',
  storeResults varchar(1) NOT NULL default '',
  questionsPerPage integer NOT NULL default '0',
  timeLimited varchar(1) NOT NULL default '',
  timeLimit integer NOT NULL default '0',
  created integer NOT NULL default '0',
  taken integer NOT NULL default '0',
  PRIMARY KEY  (quizId)
) ;
--
-- Table structure for table 'tiki_received_articles'
--
CREATE TABLE tiki_received_articles (
  receivedArticleId integer NOT NULL default nextval('tiki_received_articles_seq'),
  receivedFromSite varchar(200) NOT NULL default '',
  receivedFromUser varchar(200) NOT NULL default '',
  receivedDate integer NOT NULL default '0',
  title varchar(80) NOT NULL default '',
  authorName varchar(60) NOT NULL default '',
  size integer NOT NULL default '0',
  useImage varchar(1) NOT NULL default '',
  image_name varchar(80) NOT NULL default '',
  image_type varchar(80) NOT NULL default '',
  image_size integer NOT NULL default '0',
  image_x integer NOT NULL default '0',
  image_y integer NOT NULL default '0',
  image_data text,
  publishDate integer NOT NULL default '0',
  created integer NOT NULL default '0',
  heading text NOT NULL,
  body text,
  hash varchar(32) NOT NULL default '',
  author varchar(200) NOT NULL default '',
  type varchar(50) NOT NULL default '',
  rating decimal(4,2) NOT NULL default '0.00',
  PRIMARY KEY  (receivedArticleId)
) ;
--
-- Table structure for table 'tiki_received_pages'
--
CREATE TABLE tiki_received_pages (
  receivedPageId integer NOT NULL default nextval('tiki_received_pages_seq'),
  pageName varchar(160) NOT NULL default '',
  data text,
  comment varchar(200) NOT NULL default '',
  receivedFromSite varchar(200) NOT NULL default '',
  receivedFromUser varchar(200) NOT NULL default '',
  receivedDate integer NOT NULL default '0',
  description varchar(200) NOT NULL default '',
  PRIMARY KEY  (receivedPageId)
) ;
--
-- Table structure for table 'tiki_referer_stats'
--
CREATE TABLE tiki_referer_stats (
  referer varchar(50) NOT NULL default '',
  hits integer NOT NULL default '0',
  last integer NOT NULL default '0',
  PRIMARY KEY  (referer)
) ;
--
-- Table structure for table 'tiki_related_categories'
--
CREATE TABLE tiki_related_categories (
  categId integer NOT NULL default '0',
  relatedTo integer NOT NULL default '0',
  PRIMARY KEY  (categId,relatedTo)
) ;
--
-- Table structure for table 'tiki_rss_modules'
--
CREATE TABLE tiki_rss_modules (
  rssId integer NOT NULL default nextval('tiki_rss_modules_seq'),
  name varchar(30) NOT NULL default '',
  description text NOT NULL,
  url varchar(255) NOT NULL default '',
  refresh integer NOT NULL default '0',
  lastUpdated integer NOT NULL default '0',
  content text,
  PRIMARY KEY  (rssId)
) ;
--
-- Table structure for table 'tiki_search_stats'
--
CREATE TABLE tiki_search_stats (
  term varchar(50) NOT NULL default '',
  hits integer NOT NULL default '0',
  PRIMARY KEY  (term)
) ;
--
-- Table structure for table 'tiki_semaphores'
--
CREATE TABLE tiki_semaphores (
  semName varchar(30) NOT NULL default '',
  timestamp integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  PRIMARY KEY  (semName)
) ;
--
-- Table structure for table 'tiki_sent_newsletters'
--
CREATE TABLE tiki_sent_newsletters (
  editionId integer NOT NULL default nextval('tiki_sent_newsletters_seq'),
  nlId integer NOT NULL default '0',
  users integer NOT NULL default '0',
  sent integer NOT NULL default '0',
  subject varchar(200) NOT NULL default '',
  data text,
  PRIMARY KEY  (editionId)
) ;
--
-- Table structure for table 'tiki_sessions'
--
CREATE TABLE tiki_sessions (
  sessionId varchar(32) NOT NULL default '',
  timestamp integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  PRIMARY KEY  (sessionId)
) ;
--
-- Table structure for table 'tiki_shoutbox'
--
CREATE TABLE tiki_shoutbox (
  msgId integer NOT NULL default nextval('tiki_shoutbox_seq'),
  message varchar(255) NOT NULL default '',
  timestamp integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  hash varchar(32) NOT NULL default '',
  PRIMARY KEY  (msgId)
) ;
--
-- Table structure for table 'tiki_structures'
--
CREATE TABLE tiki_structures (
  page varchar(240) NOT NULL default '',
  parent varchar(240) NOT NULL default '',
  pos integer NOT NULL default '0',
  PRIMARY KEY  (page,parent)
) ;
--
-- Table structure for table 'tiki_submissions'
--
CREATE TABLE tiki_submissions (
  subId integer NOT NULL default nextval('tiki_submissions_seq'),
  title varchar(80) NOT NULL default '',
  authorName varchar(60) NOT NULL default '',
  topicId integer NOT NULL default '0',
  topicName varchar(40) NOT NULL default '',
  size integer NOT NULL default '0',
  useImage varchar(1) NOT NULL default '',
  image_name varchar(80) NOT NULL default '',
  image_type varchar(80) NOT NULL default '',
  image_size integer NOT NULL default '0',
  image_x integer NOT NULL default '0',
  image_y integer NOT NULL default '0',
  image_data text,
  publishDate integer NOT NULL default '0',
  created integer NOT NULL default '0',
  heading text NOT NULL,
  body text,
  hash varchar(32) NOT NULL default '',
  author varchar(200) NOT NULL default '',
  reads integer NOT NULL default '0',
  votes integer NOT NULL default '0',
  points integer NOT NULL default '0',
  type varchar(50) NOT NULL default '',
  rating decimal(4,2) NOT NULL default '0.00',
  isfloat varchar(1) NOT NULL default '',
  PRIMARY KEY  (subId)
) ;
--
-- Table structure for table 'tiki_suggested_faq_questions'
--
CREATE TABLE tiki_suggested_faq_questions (
  sfqId integer NOT NULL default nextval('tiki_suggested_faq_question_seq'),
  faqId integer NOT NULL default '0',
  question text NOT NULL,
  answer text NOT NULL,
  created integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  PRIMARY KEY  (sfqId)
) ;
--
-- Table structure for table 'tiki_survey_question_options'
--
CREATE TABLE tiki_survey_question_options (
  optionId integer NOT NULL default nextval('tiki_survey_question_option_seq'),
  questionId integer NOT NULL default '0',
  qoption text NOT NULL,
  votes integer NOT NULL default '0',
  PRIMARY KEY  (optionId)
) ;
--
-- Table structure for table 'tiki_survey_questions'
--
CREATE TABLE tiki_survey_questions (
  questionId integer NOT NULL default nextval('tiki_survey_questions_seq'),
  surveyId integer NOT NULL default '0',
  question text NOT NULL,
  options text NOT NULL,
  type varchar(1) NOT NULL default '',
  position integer NOT NULL default '0',
  votes integer NOT NULL default '0',
  value integer NOT NULL default '0',
  average decimal(4,2) NOT NULL default '0.00',
  PRIMARY KEY  (questionId)
) ;
--
-- Table structure for table 'tiki_surveys'
--
CREATE TABLE tiki_surveys (
  surveyId integer NOT NULL default nextval('tiki_surveys_seq'),
  name varchar(200) NOT NULL default '',
  description text NOT NULL,
  taken integer NOT NULL default '0',
  lastTaken integer NOT NULL default '0',
  created integer NOT NULL default '0',
  status varchar(1) NOT NULL default '',
  PRIMARY KEY  (surveyId)
) ;
--
-- Table structure for table 'tiki_tags'
--
CREATE TABLE tiki_tags (
  tagName varchar(80) NOT NULL default '',
  pageName varchar(160) NOT NULL default '',
  hits integer NOT NULL default '0',
  data text,
  lastModif integer NOT NULL default '0',
  comment varchar(200) NOT NULL default '',
  version integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  ip varchar(15) NOT NULL default '',
  flag varchar(1) NOT NULL default '',
  description varchar(200) NOT NULL default '',
  PRIMARY KEY  (tagName,pageName)
) ;
--
-- Table structure for table 'tiki_theme_control_categs'
--
CREATE TABLE tiki_theme_control_categs (
  categId integer NOT NULL default '0',
  theme varchar(250) NOT NULL default '',
  PRIMARY KEY  (categId)
) ;
--
-- Table structure for table 'tiki_theme_control_objects'
--
CREATE TABLE tiki_theme_control_objects (
  objId varchar(250) NOT NULL default '',
  type varchar(250) NOT NULL default '',
  name varchar(250) NOT NULL default '',
  theme varchar(250) NOT NULL default '',
  PRIMARY KEY  (objId)
) ;
--
-- Table structure for table 'tiki_theme_control_sections'
--
CREATE TABLE tiki_theme_control_sections (
  section varchar(250) NOT NULL default '',
  theme varchar(250) NOT NULL default '',
  PRIMARY KEY  (section)
) ;
--
-- Table structure for table 'tiki_topics'
--
CREATE TABLE tiki_topics (
  topicId integer NOT NULL default nextval('tiki_topics_seq'),
  name varchar(40) NOT NULL default '',
  image_name varchar(80) NOT NULL default '',
  image_type varchar(80) NOT NULL default '',
  image_size integer NOT NULL default '0',
  image_data text,
  active varchar(1) NOT NULL default '',
  created integer NOT NULL default '0',
  PRIMARY KEY  (topicId)
) ;
--
-- Table structure for table 'tiki_tracker_fields'
--
CREATE TABLE tiki_tracker_fields (
  fieldId integer NOT NULL default nextval('tiki_tracker_fields_seq'),
  trackerId integer NOT NULL default '0',
  name varchar(80) NOT NULL default '',
  options text NOT NULL,
  type varchar(1) NOT NULL default '',
  isMain varchar(1) NOT NULL default '',
  isTblVisible varchar(1) NOT NULL default '',
  PRIMARY KEY  (fieldId)
) ;
--
-- Table structure for table 'tiki_tracker_item_attachments'
--
CREATE TABLE tiki_tracker_item_attachments (
  attId integer NOT NULL default nextval('tiki_tracker_item_attachmen_seq'),
  itemId varchar(40) NOT NULL default '',
  filename varchar(80) NOT NULL default '',
  filetype varchar(80) NOT NULL default '',
  filesize integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  data text,
  path varchar(255) NOT NULL default '',
  downloads integer NOT NULL default '0',
  created integer NOT NULL default '0',
  comment varchar(250) NOT NULL default '',
  PRIMARY KEY  (attId)
) ;
--
-- Table structure for table 'tiki_tracker_item_comments'
--
CREATE TABLE tiki_tracker_item_comments (
  commentId integer NOT NULL default nextval('tiki_tracker_item_comments_seq'),
  itemId integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  data text NOT NULL,
  title varchar(200) NOT NULL default '',
  posted integer NOT NULL default '0',
  PRIMARY KEY  (commentId)
) ;
--
-- Table structure for table 'tiki_tracker_item_fields'
--
CREATE TABLE tiki_tracker_item_fields (
  itemId integer NOT NULL default '0',
  fieldId integer NOT NULL default '0',
  value text NOT NULL,
  PRIMARY KEY  (itemId,fieldId)
) ;
--
-- Table structure for table 'tiki_tracker_items'
--
CREATE TABLE tiki_tracker_items (
  itemId integer NOT NULL default nextval('tiki_tracker_items_seq'),
  trackerId integer NOT NULL default '0',
  created integer NOT NULL default '0',
  status varchar(1) NOT NULL default '',
  lastModif integer NOT NULL default '0',
  PRIMARY KEY  (itemId)
) ;
--
-- Table structure for table 'tiki_trackers'
--
CREATE TABLE tiki_trackers (
  trackerId integer NOT NULL default nextval('tiki_trackers_seq'),
  name varchar(80) NOT NULL default '',
  description text NOT NULL,
  created integer NOT NULL default '0',
  lastModif integer NOT NULL default '0',
  showCreated varchar(1) NOT NULL default '',
  showStatus varchar(1) NOT NULL default '',
  showLastModif varchar(1) NOT NULL default '',
  useComments varchar(1) NOT NULL default '',
  useAttachments varchar(1) NOT NULL default '',
  items integer NOT NULL default '0',
  PRIMARY KEY  (trackerId)
) ;
--
-- Table structure for table 'tiki_untranslated'
--
CREATE TABLE tiki_untranslated (
  id integer NOT NULL default nextval('tiki_untranslated_seq'),
  source text NOT NULL,
  lang varchar(2) NOT NULL default '',
  PRIMARY KEY  (source,lang)  ,UNIQUE (id)
) ;
--
-- Table structure for table 'tiki_user_answers'
--
CREATE TABLE tiki_user_answers (
  userResultId integer NOT NULL default '0',
  quizId integer NOT NULL default '0',
  questionId integer NOT NULL default '0',
  optionId integer NOT NULL default '0',
  PRIMARY KEY  (userResultId,quizId,questionId,optionId)
) ;
--
-- Table structure for table 'tiki_user_assigned_modules'
--
CREATE TABLE tiki_user_assigned_modules (
  name varchar(200) NOT NULL default '',
  position varchar(1) NOT NULL default '',
  ord integer NOT NULL default '0',
  type varchar(1) NOT NULL default '',
  title varchar(40) NOT NULL default '',
  cache_time integer NOT NULL default '0',
  rows integer NOT NULL default '0',
  groups text NOT NULL,
  "user" varchar(200) NOT NULL default '',
  PRIMARY KEY  (name,"user")
) ;
--
-- Table structure for table 'tiki_user_bookmarks_folders'
--
CREATE TABLE tiki_user_bookmarks_folders (
  folderId integer NOT NULL default nextval('tiki_user_bookmarks_folders_seq'),
  parentId integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  name varchar(30) NOT NULL default '',
  PRIMARY KEY  ("user",folderId)
) ;
--
-- Table structure for table 'tiki_user_bookmarks_urls'
--
CREATE TABLE tiki_user_bookmarks_urls (
  urlId integer NOT NULL default nextval('tiki_user_bookmarks_urls_seq'),
  name varchar(30) NOT NULL default '',
  url varchar(250) NOT NULL default '',
  data text,
  lastUpdated integer NOT NULL default '0',
  folderId integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  PRIMARY KEY  (urlId)
) ;
--
-- Table structure for table 'tiki_user_mail_accounts'
--
CREATE TABLE tiki_user_mail_accounts (
  accountId integer NOT NULL default nextval('tiki_user_mail_accounts_seq'),
  "user" varchar(200) NOT NULL default '',
  account varchar(50) NOT NULL default '',
  pop varchar(255) NOT NULL default '',
  current varchar(1) NOT NULL default '',
  port integer NOT NULL default '0',
  username varchar(100) NOT NULL default '',
  pass varchar(100) NOT NULL default '',
  msgs integer NOT NULL default '0',
  smtp varchar(255) NOT NULL default '',
  useAuth varchar(1) NOT NULL default '',
  smtpPort integer NOT NULL default '0',
  PRIMARY KEY  (accountId)
) ;
--
-- Table structure for table 'tiki_user_menus'
--
CREATE TABLE tiki_user_menus (
  "user" varchar(200) NOT NULL default '',
  menuId integer NOT NULL default nextval('tiki_user_menus_seq'),
  url varchar(250) NOT NULL default '',
  name varchar(40) NOT NULL default '',
  position integer NOT NULL default '0',
  mode varchar(1) NOT NULL default '',
  PRIMARY KEY  (menuId)
) ;
--
-- Table structure for table 'tiki_user_modules'
--
CREATE TABLE tiki_user_modules (
  name varchar(200) NOT NULL default '',
  title varchar(40) NOT NULL default '',
  data text,
  PRIMARY KEY  (name)
) ;
--
-- Table structure for table 'tiki_user_notes'
--
CREATE TABLE tiki_user_notes (
  "user" varchar(200) NOT NULL default '',
  noteId integer NOT NULL default nextval('tiki_user_notes_seq'),
  created integer NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  lastModif integer NOT NULL default '0',
  data text NOT NULL,
  size integer NOT NULL default '0',
  parse_mode varchar(20) NOT NULL default '',
  PRIMARY KEY  (noteId)
) ;
--
-- Table structure for table 'tiki_user_postings'
--
CREATE TABLE tiki_user_postings (
  "user" varchar(200) NOT NULL default '',
  posts integer NOT NULL default '0',
  last integer NOT NULL default '0',
  first integer NOT NULL default '0',
  level integer NOT NULL default '0',
  PRIMARY KEY  ("user")
) ;
--
-- Table structure for table 'tiki_user_preferences'
--
CREATE TABLE tiki_user_preferences (
  "user" varchar(200) NOT NULL default '',
  prefName varchar(40) NOT NULL default '',
  value varchar(250) NOT NULL default '',
  PRIMARY KEY  ("user",prefName)
) ;
--
-- Table structure for table 'tiki_user_quizzes'
--
CREATE TABLE tiki_user_quizzes (
  "user" varchar(100) NOT NULL default '',
  quizId integer NOT NULL default '0',
  timestamp integer NOT NULL default '0',
  timeTaken integer NOT NULL default '0',
  points integer NOT NULL default '0',
  maxPoints integer NOT NULL default '0',
  resultId integer NOT NULL default '0',
  userResultId integer NOT NULL default nextval('tiki_user_quizzes_seq'),
  PRIMARY KEY  (userResultId)
) ;
--
-- Table structure for table 'tiki_user_taken_quizzes'
--
CREATE TABLE tiki_user_taken_quizzes (
  "user" varchar(200) NOT NULL default '',
  quizId varchar(255) NOT NULL default '',
  PRIMARY KEY  ("user",quizId)
) ;
--
-- Table structure for table 'tiki_user_tasks'
--
CREATE TABLE tiki_user_tasks (
  "user" varchar(200) NOT NULL default '',
  taskId integer NOT NULL default nextval('tiki_user_tasks_seq'),
  title varchar(250) NOT NULL default '',
  description text NOT NULL,
  datetime integer NOT NULL default '0',
  status varchar(1) NOT NULL default '',
  priority integer NOT NULL default '0',
  completed integer NOT NULL default '0',
  percentage integer NOT NULL default '0',
  PRIMARY KEY  (taskId)
) ;
--
-- Table structure for table 'tiki_user_votings'
--
CREATE TABLE tiki_user_votings (
  "user" varchar(200) NOT NULL default '',
  id varchar(255) NOT NULL default '',
  PRIMARY KEY  ("user",id)
) ;
--
-- Table structure for table 'tiki_user_watches'
--
CREATE TABLE tiki_user_watches (
  "user" varchar(200) NOT NULL default '',
  event varchar(40) NOT NULL default '',
  object varchar(200) NOT NULL default '',
  hash varchar(32) NOT NULL default '',
  title varchar(250) NOT NULL default '',
  type varchar(200) NOT NULL default '',
  url varchar(250) NOT NULL default '',
  email varchar(200) NOT NULL default '',
  PRIMARY KEY  ("user",event,object)
) ;
--
-- Table structure for table 'tiki_userfiles'
--
CREATE TABLE tiki_userfiles (
  "user" varchar(200) NOT NULL default '',
  fileId integer NOT NULL default nextval('tiki_userfiles_seq'),
  name varchar(200) NOT NULL default '',
  filename varchar(200) NOT NULL default '',
  filetype varchar(200) NOT NULL default '',
  filesize varchar(200) NOT NULL default '',
  data text,
  hits integer NOT NULL default '0',
  isFile varchar(1) NOT NULL default '',
  path varchar(255) NOT NULL default '',
  created integer NOT NULL default '0',
  PRIMARY KEY  (fileId)
) ;
--
-- Table structure for table 'tiki_userpoints'
--
CREATE TABLE tiki_userpoints (
  "user" varchar(200) NOT NULL default '',
  points decimal(8,2) NOT NULL default '0.00',
  voted integer NOT NULL default '0'
) ;
--
-- Table structure for table 'tiki_users'
--
CREATE TABLE tiki_users (
  "user" varchar(200) NOT NULL default '',
  password varchar(40) NOT NULL default '',
  email varchar(200) NOT NULL default '',
  lastLogin integer NOT NULL default '0',
  PRIMARY KEY  ("user")
) ;
--
-- Table structure for table 'tiki_webmail_contacts'
--
CREATE TABLE tiki_webmail_contacts (
  contactId integer NOT NULL default nextval('tiki_webmail_contacts_seq'),
  firstName varchar(80) NOT NULL default '',
  lastName varchar(80) NOT NULL default '',
  email varchar(250) NOT NULL default '',
  nickname varchar(200) NOT NULL default '',
  "user" varchar(200) NOT NULL default '',
  PRIMARY KEY  (contactId)
) ;
--
-- Table structure for table 'tiki_webmail_messages'
--
CREATE TABLE tiki_webmail_messages (
  accountId integer NOT NULL default '0',
  mailId varchar(255) NOT NULL default '',
  "user" varchar(200) NOT NULL default '',
  isRead varchar(1) NOT NULL default '',
  isReplied varchar(1) NOT NULL default '',
  isFlagged varchar(1) NOT NULL default '',
  PRIMARY KEY  (accountId,mailId)
) ;
--
-- Table structure for table 'tiki_wiki_attachments'
--
CREATE TABLE tiki_wiki_attachments (
  attId integer NOT NULL default nextval('tiki_wiki_attachments_seq'),
  page varchar(40) NOT NULL default '',
  filename varchar(80) NOT NULL default '',
  filetype varchar(80) NOT NULL default '',
  filesize integer NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  data text,
  path varchar(255) NOT NULL default '',
  downloads integer NOT NULL default '0',
  created integer NOT NULL default '0',
  comment varchar(250) NOT NULL default '',
  PRIMARY KEY  (attId)
) ;
--
-- Table structure for table 'tiki_zones'
--
CREATE TABLE tiki_zones (
  zone varchar(40) NOT NULL default '',
  PRIMARY KEY  (zone)
) ;
--
-- Table structure for table 'users_grouppermissions'
--
CREATE TABLE users_grouppermissions (
  groupName varchar(30) NOT NULL default '',
  permName varchar(30) NOT NULL default '',
  value varchar(1) NOT NULL default '',
  PRIMARY KEY  (groupName,permName)
) ;
--
-- Table structure for table 'users_groups'
--
CREATE TABLE users_groups (
  groupName varchar(30) NOT NULL default '',
  groupDesc varchar(255) NOT NULL default '',
  PRIMARY KEY  (groupName)
) ;
--
-- Table structure for table 'users_objectpermissions'
--
CREATE TABLE users_objectpermissions (
  groupName varchar(30) NOT NULL default '',
  permName varchar(30) NOT NULL default '',
  objectType varchar(20) NOT NULL default '',
  objectId varchar(32) NOT NULL default '',
  PRIMARY KEY  (objectId,groupName,permName)
) ;
--
-- Table structure for table 'users_permissions'
--
CREATE TABLE users_permissions (
  permName varchar(30) NOT NULL default '',
  permDesc varchar(250) NOT NULL default '',
  type varchar(20) NOT NULL default '',
  level varchar(80) NOT NULL default '',
  PRIMARY KEY  (permName)
) ;
--
-- Table structure for table 'users_usergroups'
--
CREATE TABLE users_usergroups (
  userId integer NOT NULL default '0',
  groupName varchar(30) NOT NULL default '',
  PRIMARY KEY  (userId,groupName)
) ;
--
-- Table structure for table 'users_users'
--
CREATE TABLE users_users (
  userId integer NOT NULL default nextval('users_users_seq'),
  email varchar(200) NOT NULL default '',
  login varchar(40) NOT NULL default '',
  password varchar(30) NOT NULL default '',
  provpass varchar(30) NOT NULL default '',
  realname varchar(80) NOT NULL default '',
  homePage varchar(200) NOT NULL default '',
  lastLogin integer NOT NULL default '0',
  country varchar(80) NOT NULL default '',
  currentLogin integer NOT NULL default '0',
  registrationDate integer NOT NULL default '0',
  challenge varchar(32) NOT NULL default '',
  hash varchar(32) NOT NULL default '',
  pass_due integer NOT NULL default '0',
  created integer NOT NULL default '0',
  avatarName varchar(80) NOT NULL default '',
  avatarSize integer NOT NULL default '0',
  avatarFileType varchar(250) NOT NULL default '',
  avatarData text,
  avatarLibName varchar(200) NOT NULL default '',
  avatarType varchar(1) NOT NULL default '',
  PRIMARY KEY  (userId)
) ;

-- Sequences

select setval('galaxia_activities_seq', (select max(activityId) from galaxia_activities));
select setval('galaxia_instance_comments_seq', (select max(cId) from galaxia_instance_comments));
select setval('galaxia_instances_seq', (select max(instanceId) from galaxia_instances));
select setval('galaxia_processes_seq', (select max(pId) from galaxia_processes));
select setval('galaxia_roles_seq', (select max(roleId) from galaxia_roles));
select setval('galaxia_user_roles_seq', (select max(roleId) from galaxia_user_roles));
select setval('galaxia_workitems_seq', (select max(itemId) from galaxia_workitems));
select setval('messu_messages_seq', (select max(msgId) from messu_messages));
select setval('tiki_articles_seq', (select max(articleId) from tiki_articles));
select setval('tiki_banners_seq', (select max(bannerId) from tiki_banners));
select setval('tiki_banning_seq', (select max(banId) from tiki_banning));
select setval('tiki_blog_posts_seq', (select max(postId) from tiki_blog_posts));
select setval('tiki_blog_posts_images_seq', (select max(imgId) from tiki_blog_posts_images));
select setval('tiki_blogs_seq', (select max(blogId) from tiki_blogs));
select setval('tiki_calendar_categories_seq', (select max(calcatId) from tiki_calendar_categories));
select setval('tiki_calendar_items_seq', (select max(calitemId) from tiki_calendar_items));
select setval('tiki_calendar_locations_seq', (select max(callocId) from tiki_calendar_locations));
select setval('tiki_calendars_seq', (select max(calendarId) from tiki_calendars));
select setval('tiki_categories_seq', (select max(categId) from tiki_categories));
select setval('tiki_categorized_objects_seq', (select max(catObjectId) from tiki_categorized_objects));
select setval('tiki_chart_items_seq', (select max(itemId) from tiki_chart_items));
select setval('tiki_charts_seq', (select max(chartId) from tiki_charts));
select setval('tiki_chat_channels_seq', (select max(channelId) from tiki_chat_channels));
select setval('tiki_chat_messages_seq', (select max(messageId) from tiki_chat_messages));
select setval('tiki_comments_seq', (select max(threadId) from tiki_comments));
select setval('tiki_content_seq', (select max(contentId) from tiki_content));
select setval('tiki_content_templates_seq', (select max(templateId) from tiki_content_templates));
select setval('tiki_cookies_seq', (select max(cookieId) from tiki_cookies));
select setval('tiki_copyrights_seq', (select max(copyrightId) from tiki_copyrights));
select setval('tiki_directory_categories_seq', (select max(categId) from tiki_directory_categories));
select setval('tiki_directory_sites_seq', (select max(siteId) from tiki_directory_sites));
select setval('tiki_drawings_seq', (select max(drawId) from tiki_drawings));
select setval('tiki_dsn_seq', (select max(dsnId) from tiki_dsn));
select setval('tiki_eph_seq', (select max(ephId) from tiki_eph));
select setval('tiki_extwiki_seq', (select max(extwikiId) from tiki_extwiki));
select setval('tiki_faq_questions_seq', (select max(questionId) from tiki_faq_questions));
select setval('tiki_faqs_seq', (select max(faqId) from tiki_faqs));
select setval('tiki_file_galleries_seq', (select max(galleryId) from tiki_file_galleries));
select setval('tiki_files_seq', (select max(fileId) from tiki_files));
select setval('tiki_forum_attachments_seq', (select max(attId) from tiki_forum_attachments));
select setval('tiki_forums_seq', (select max(forumId) from tiki_forums));
select setval('tiki_forums_queue_seq', (select max(qId) from tiki_forums_queue));
select setval('tiki_galleries_seq', (select max(galleryId) from tiki_galleries));
select setval('tiki_images_seq', (select max(imageId) from tiki_images));
select setval('tiki_images_old_seq', (select max(imageId) from tiki_images_old));
select setval('tiki_link_cache_seq', (select max(cacheId) from tiki_link_cache));
select setval('tiki_live_support_events_seq', (select max(eventId) from tiki_live_support_events));
select setval('tiki_live_support_message_c_seq', (select max(cId) from tiki_live_support_message_comments));
select setval('tiki_live_support_messages_seq', (select max(msgId) from tiki_live_support_messages));
select setval('tiki_live_support_modules_seq', (select max(modId) from tiki_live_support_modules));
select setval('tiki_mailin_accounts_seq', (select max(accountId) from tiki_mailin_accounts));
select setval('tiki_menu_languages_seq', (select max(menuId) from tiki_menu_languages));
select setval('tiki_menu_options_seq', (select max(optionId) from tiki_menu_options));
select setval('tiki_menus_seq', (select max(menuId) from tiki_menus));
select setval('tiki_minical_events_seq', (select max(eventId) from tiki_minical_events));
select setval('tiki_minical_topics_seq', (select max(topicId) from tiki_minical_topics));
select setval('tiki_newsletters_seq', (select max(nlId) from tiki_newsletters));
select setval('tiki_newsreader_servers_seq', (select max(serverId) from tiki_newsreader_servers));
select setval('tiki_poll_options_seq', (select max(optionId) from tiki_poll_options));
select setval('tiki_polls_seq', (select max(pollId) from tiki_polls));
select setval('tiki_private_messages_seq', (select max(messageId) from tiki_private_messages));
select setval('tiki_programmed_content_seq', (select max(pId) from tiki_programmed_content));
select setval('tiki_quiz_question_options_seq', (select max(optionId) from tiki_quiz_question_options));
select setval('tiki_quiz_questions_seq', (select max(questionId) from tiki_quiz_questions));
select setval('tiki_quiz_results_seq', (select max(resultId) from tiki_quiz_results));
select setval('tiki_quizzes_seq', (select max(quizId) from tiki_quizzes));
select setval('tiki_received_articles_seq', (select max(receivedArticleId) from tiki_received_articles));
select setval('tiki_received_pages_seq', (select max(receivedPageId) from tiki_received_pages));
select setval('tiki_rss_modules_seq', (select max(rssId) from tiki_rss_modules));
select setval('tiki_sent_newsletters_seq', (select max(editionId) from tiki_sent_newsletters));
select setval('tiki_shoutbox_seq', (select max(msgId) from tiki_shoutbox));
select setval('tiki_submissions_seq', (select max(subId) from tiki_submissions));
select setval('tiki_suggested_faq_question_seq', (select max(sfqId) from tiki_suggested_faq_questions));
select setval('tiki_survey_question_option_seq', (select max(optionId) from tiki_survey_question_options));
select setval('tiki_survey_questions_seq', (select max(questionId) from tiki_survey_questions));
select setval('tiki_surveys_seq', (select max(surveyId) from tiki_surveys));
select setval('tiki_topics_seq', (select max(topicId) from tiki_topics));
select setval('tiki_tracker_fields_seq', (select max(fieldId) from tiki_tracker_fields));
select setval('tiki_tracker_item_attachmen_seq', (select max(attId) from tiki_tracker_item_attachments));
select setval('tiki_tracker_item_comments_seq', (select max(commentId) from tiki_tracker_item_comments));
select setval('tiki_tracker_items_seq', (select max(itemId) from tiki_tracker_items));
select setval('tiki_trackers_seq', (select max(trackerId) from tiki_trackers));
select setval('tiki_untranslated_seq', (select max(id) from tiki_untranslated));
select setval('tiki_user_bookmarks_folders_seq', (select max(folderId) from tiki_user_bookmarks_folders));
select setval('tiki_user_bookmarks_urls_seq', (select max(urlId) from tiki_user_bookmarks_urls));
select setval('tiki_user_mail_accounts_seq', (select max(accountId) from tiki_user_mail_accounts));
select setval('tiki_user_menus_seq', (select max(menuId) from tiki_user_menus));
select setval('tiki_user_notes_seq', (select max(noteId) from tiki_user_notes));
select setval('tiki_user_quizzes_seq', (select max(userResultId) from tiki_user_quizzes));
select setval('tiki_user_tasks_seq', (select max(taskId) from tiki_user_tasks));
select setval('tiki_userfiles_seq', (select max(fileId) from tiki_userfiles));
select setval('tiki_webmail_contacts_seq', (select max(contactId) from tiki_webmail_contacts));
select setval('tiki_wiki_attachments_seq', (select max(attId) from tiki_wiki_attachments));
select setval('users_users_seq', (select max(userId) from users_users));

-- Indexes

CREATE INDEX tiki_articles_body ON tiki_articles (body);
CREATE INDEX tiki_articles_heading ON tiki_articles (heading);
CREATE INDEX tiki_articles_reads ON tiki_articles (reads);
CREATE INDEX tiki_articles_title ON tiki_articles (title);
CREATE INDEX tiki_blog_posts_blogId ON tiki_blog_posts (blogId);
CREATE INDEX tiki_blog_posts_created ON tiki_blog_posts (created);
CREATE INDEX tiki_blog_posts_data ON tiki_blog_posts (data);
CREATE INDEX tiki_blogs_description ON tiki_blogs (description);
CREATE INDEX tiki_blogs_hits ON tiki_blogs (hits);
CREATE INDEX tiki_blogs_title ON tiki_blogs (title);
CREATE INDEX tiki_calendar_categories_calendarId ON tiki_calendar_categories (calendarId);
CREATE INDEX tiki_calendar_categories_name ON tiki_calendar_categories (name);
CREATE INDEX tiki_calendar_items_calendarId ON tiki_calendar_items (calendarId);
CREATE INDEX tiki_calendar_locations_calendarId ON tiki_calendar_locations (calendarId);
CREATE INDEX tiki_calendar_locations_name ON tiki_calendar_locations (name);
CREATE INDEX tiki_comments_data ON tiki_comments (data);
CREATE INDEX tiki_comments_hits ON tiki_comments (hits);
CREATE INDEX tiki_comments_object ON tiki_comments (object);
CREATE INDEX tiki_comments_title ON tiki_comments (title);
CREATE INDEX tiki_directory_sites_description ON tiki_directory_sites (description);
CREATE INDEX tiki_directory_sites_name ON tiki_directory_sites (name);
CREATE INDEX tiki_faq_questions_answer ON tiki_faq_questions (answer);
CREATE INDEX tiki_faq_questions_faqId ON tiki_faq_questions (faqId);
CREATE INDEX tiki_faq_questions_question ON tiki_faq_questions (question);
CREATE INDEX tiki_faqs_description ON tiki_faqs (description);
CREATE INDEX tiki_faqs_hits ON tiki_faqs (hits);
CREATE INDEX tiki_faqs_title ON tiki_faqs (title);
CREATE INDEX tiki_files_description ON tiki_files (description);
CREATE INDEX tiki_files_downloads ON tiki_files (downloads);
CREATE INDEX tiki_files_name ON tiki_files (name);
CREATE INDEX tiki_galleries_description ON tiki_galleries (description);
CREATE INDEX tiki_galleries_hits ON tiki_galleries (hits);
CREATE INDEX tiki_galleries_name ON tiki_galleries (name);
CREATE INDEX tiki_images_created ON tiki_images (created);
CREATE INDEX tiki_images_description ON tiki_images (description);
CREATE INDEX tiki_images_galleryId ON tiki_images (galleryId);
CREATE INDEX tiki_images_hits ON tiki_images (hits);
CREATE INDEX tiki_images_name ON tiki_images (name);
CREATE INDEX tiki_images_user ON tiki_images ("user");
CREATE INDEX tiki_images_data_imageId ON tiki_images_data (imageId);
CREATE INDEX tiki_images_data_type ON tiki_images_data (type);
CREATE INDEX tiki_images_old_description ON tiki_images_old (description);
CREATE INDEX tiki_images_old_hits ON tiki_images_old (hits);
CREATE INDEX tiki_images_old_name ON tiki_images_old (name);
CREATE INDEX tiki_pages_data ON tiki_pages (data);
CREATE INDEX tiki_pages_pageName ON tiki_pages (pageName);
CREATE INDEX tiki_pages_pageRank ON tiki_pages (pageRank);
CREATE INDEX tiki_untranslated_id ON tiki_untranslated (id);

-- EOF

