-- 
-- Created by SQL::Translator::Producer::MySQL
-- Created on Sun Aug 17 01:09:07 2003
-- 
SET foreign_key_checks=0;

--
-- Table: galaxia_activities
--
DROP TABLE IF EXISTS galaxia_activities;
CREATE TABLE galaxia_activities (
  activityId integer(14) NOT NULL auto_increment,
  name varchar(80) DEFAULT NULL,
  normalized_name varchar(80) DEFAULT NULL,
  pId integer(14) NOT NULL DEFAULT '0',
  type enum('start','end','split','switch','join','activity','standalone') DEFAULT NULL,
  isAutoRouted char(1) DEFAULT NULL,
  flowNum integer(10) DEFAULT NULL,
  isInteractive char(1) DEFAULT NULL,
  lastModif integer(14) DEFAULT NULL,
  description text,
  PRIMARY KEY (activityId)
);

--
-- Table: galaxia_activity_roles
--
DROP TABLE IF EXISTS galaxia_activity_roles;
CREATE TABLE galaxia_activity_roles (
  activityId integer(14) NOT NULL DEFAULT '0',
  roleId integer(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (activityId, roleId)
);

--
-- Table: galaxia_instance_activities
--
DROP TABLE IF EXISTS galaxia_instance_activities;
CREATE TABLE galaxia_instance_activities (
  instanceId integer(14) NOT NULL DEFAULT '0',
  activityId integer(14) NOT NULL DEFAULT '0',
  started integer(14) NOT NULL DEFAULT '0',
  ended integer(14) NOT NULL DEFAULT '0',
  user varchar(200) DEFAULT NULL,
  status enum('running','completed') DEFAULT NULL,
  PRIMARY KEY (instanceId, activityId)
);

--
-- Table: galaxia_instance_comments
--
DROP TABLE IF EXISTS galaxia_instance_comments;
CREATE TABLE galaxia_instance_comments (
  cId integer(14) NOT NULL auto_increment,
  instanceId integer(14) NOT NULL DEFAULT '0',
  user varchar(200) DEFAULT NULL,
  activityId integer(14) DEFAULT NULL,
  hash varchar(32) DEFAULT NULL,
  title varchar(250) DEFAULT NULL,
  comment text,
  activity varchar(80) DEFAULT NULL,
  timestamp integer(14) DEFAULT NULL,
  PRIMARY KEY (cId)
);

--
-- Table: galaxia_instances
--
DROP TABLE IF EXISTS galaxia_instances;
CREATE TABLE galaxia_instances (
  instanceId integer(14) NOT NULL auto_increment,
  pId integer(14) NOT NULL DEFAULT '0',
  started integer(14) DEFAULT NULL,
  owner varchar(200) DEFAULT NULL,
  nextActivity integer(14) DEFAULT NULL,
  nextUser varchar(200) DEFAULT NULL,
  ended integer(14) DEFAULT NULL,
  status enum('active','exception','aborted','completed') DEFAULT NULL,
  properties longblob,
  PRIMARY KEY (instanceId)
);

--
-- Table: galaxia_processes
--
DROP TABLE IF EXISTS galaxia_processes;
CREATE TABLE galaxia_processes (
  pId integer(14) NOT NULL auto_increment,
  name varchar(80) DEFAULT NULL,
  isValid char(1) DEFAULT NULL,
  isActive char(1) DEFAULT NULL,
  version varchar(12) DEFAULT NULL,
  description text,
  lastModif integer(14) DEFAULT NULL,
  normalized_name varchar(80) DEFAULT NULL,
  PRIMARY KEY (pId)
);

--
-- Table: galaxia_roles
--
DROP TABLE IF EXISTS galaxia_roles;
CREATE TABLE galaxia_roles (
  roleId integer(14) NOT NULL auto_increment,
  pId integer(14) NOT NULL DEFAULT '0',
  lastModif integer(14) DEFAULT NULL,
  name varchar(80) DEFAULT NULL,
  description text,
  PRIMARY KEY (roleId)
);

--
-- Table: galaxia_transitions
--
DROP TABLE IF EXISTS galaxia_transitions;
CREATE TABLE galaxia_transitions (
  pId integer(14) NOT NULL DEFAULT '0',
  actFromId integer(14) NOT NULL DEFAULT '0',
  actToId integer(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (actFromId, actToId)
);

--
-- Table: galaxia_user_roles
--
DROP TABLE IF EXISTS galaxia_user_roles;
CREATE TABLE galaxia_user_roles (
  pId integer(14) NOT NULL DEFAULT '0',
  roleId integer(14) NOT NULL auto_increment,
  user varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (roleId, user)
);

--
-- Table: galaxia_workitems
--
DROP TABLE IF EXISTS galaxia_workitems;
CREATE TABLE galaxia_workitems (
  itemId integer(14) NOT NULL auto_increment,
  instanceId integer(14) NOT NULL DEFAULT '0',
  orderId integer(14) NOT NULL DEFAULT '0',
  activityId integer(14) NOT NULL DEFAULT '0',
  properties longblob,
  started integer(14) DEFAULT NULL,
  ended integer(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  PRIMARY KEY (itemId)
);

--
-- Table: messu_messages
--
DROP TABLE IF EXISTS messu_messages;
CREATE TABLE messu_messages (
  msgId integer(14) NOT NULL auto_increment,
  user varchar(200) NOT NULL DEFAULT '',
  user_from varchar(200) NOT NULL DEFAULT '',
  user_to text,
  user_cc text,
  user_bcc text,
  subject varchar(255) DEFAULT NULL,
  body text,
  hash varchar(32) DEFAULT NULL,
  date integer(14) DEFAULT NULL,
  isRead char(1) DEFAULT NULL,
  isReplied char(1) DEFAULT NULL,
  isFlagged char(1) DEFAULT NULL,
  priority integer(2) DEFAULT NULL,
  PRIMARY KEY (msgId)
);

--
-- Table: tiki_actionlog
--
DROP TABLE IF EXISTS tiki_actionlog;
CREATE TABLE tiki_actionlog (
  action varchar(255) NOT NULL DEFAULT '',
  lastModif integer(14) DEFAULT NULL,
  pageName varchar(200) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  ip varchar(15) DEFAULT NULL,
  comment varchar(200) DEFAULT NULL
);

--
-- Table: tiki_articles
--
DROP TABLE IF EXISTS tiki_articles;
CREATE TABLE tiki_articles (
  articleId integer(8) NOT NULL auto_increment,
  title varchar(80) DEFAULT NULL,
  authorName varchar(60) DEFAULT NULL,
  topicId integer(14) DEFAULT NULL,
  topicName varchar(40) DEFAULT NULL,
  size integer(12) DEFAULT NULL,
  useImage char(1) DEFAULT NULL,
  image_name varchar(80) DEFAULT NULL,
  image_type varchar(80) DEFAULT NULL,
  image_size integer(14) DEFAULT NULL,
  image_x integer(4) DEFAULT NULL,
  image_y integer(4) DEFAULT NULL,
  image_data longblob,
  publishDate integer(14) DEFAULT NULL,
  created integer(14) DEFAULT NULL,
  heading text,
  body text,
  hash varchar(32) DEFAULT NULL,
  author varchar(200) DEFAULT NULL,
  reads integer(14) DEFAULT NULL,
  votes integer(8) DEFAULT NULL,
  points integer(14) DEFAULT NULL,
  type varchar(50) DEFAULT NULL,
  rating decimal(3, 2) DEFAULT NULL,
  isfloat char(1) DEFAULT NULL,
  INDEX title (title),
  INDEX heading (heading(255)),
  INDEX body (body(255)),
  INDEX reads (reads),
  FULLTEXT ft (title, heading, body),
  PRIMARY KEY (articleId)
);

--
-- Table: tiki_banners
--
DROP TABLE IF EXISTS tiki_banners;
CREATE TABLE tiki_banners (
  bannerId integer(12) NOT NULL auto_increment,
  client varchar(200) NOT NULL DEFAULT '',
  url varchar(255) DEFAULT NULL,
  title varchar(255) DEFAULT NULL,
  alt varchar(250) DEFAULT NULL,
  which varchar(50) DEFAULT NULL,
  imageData longblob,
  imageType varchar(200) DEFAULT NULL,
  imageName varchar(100) DEFAULT NULL,
  HTMLData text,
  fixedURLData varchar(255) DEFAULT NULL,
  textData text,
  fromDate integer(14) DEFAULT NULL,
  toDate integer(14) DEFAULT NULL,
  useDates char(1) DEFAULT NULL,
  mon char(1) DEFAULT NULL,
  tue char(1) DEFAULT NULL,
  wed char(1) DEFAULT NULL,
  thu char(1) DEFAULT NULL,
  fri char(1) DEFAULT NULL,
  sat char(1) DEFAULT NULL,
  sun char(1) DEFAULT NULL,
  hourFrom varchar(4) DEFAULT NULL,
  hourTo varchar(4) DEFAULT NULL,
  created integer(14) DEFAULT NULL,
  maxImpressions integer(8) DEFAULT NULL,
  impressions integer(8) DEFAULT NULL,
  clicks integer(8) DEFAULT NULL,
  zone varchar(40) DEFAULT NULL,
  PRIMARY KEY (bannerId)
);

--
-- Table: tiki_banning
--
DROP TABLE IF EXISTS tiki_banning;
CREATE TABLE tiki_banning (
  banId integer(12) NOT NULL auto_increment,
  mode enum('user','ip') DEFAULT NULL,
  title varchar(200) DEFAULT NULL,
  ip1 char(3) DEFAULT NULL,
  ip2 char(3) DEFAULT NULL,
  ip3 char(3) DEFAULT NULL,
  ip4 char(3) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  date_from timestamp(14) NOT NULL,
  date_to timestamp(14) NOT NULL,
  use_dates char(1) DEFAULT NULL,
  created integer(14) DEFAULT NULL,
  message text,
  PRIMARY KEY (banId)
);

--
-- Table: tiki_banning_sections
--
DROP TABLE IF EXISTS tiki_banning_sections;
CREATE TABLE tiki_banning_sections (
  banId integer(12) NOT NULL DEFAULT '0',
  section varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (banId, section)
);

--
-- Table: tiki_blog_activity
--
DROP TABLE IF EXISTS tiki_blog_activity;
CREATE TABLE tiki_blog_activity (
  blogId integer(8) NOT NULL DEFAULT '0',
  day integer(14) NOT NULL DEFAULT '0',
  posts integer(8) DEFAULT NULL,
  PRIMARY KEY (blogId, day)
);

--
-- Table: tiki_blog_posts
--
DROP TABLE IF EXISTS tiki_blog_posts;
CREATE TABLE tiki_blog_posts (
  postId integer(8) NOT NULL auto_increment,
  blogId integer(8) NOT NULL DEFAULT '0',
  data text,
  created integer(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  trackbacks_to text,
  trackbacks_from text,
  title varchar(80) DEFAULT NULL,
  INDEX data (data(255)),
  INDEX blogId (blogId),
  INDEX created (created),
  FULLTEXT ft (data),
  PRIMARY KEY (postId)
);

--
-- Table: tiki_blog_posts_images
--
DROP TABLE IF EXISTS tiki_blog_posts_images;
CREATE TABLE tiki_blog_posts_images (
  imgId integer(14) NOT NULL auto_increment,
  postId integer(14) NOT NULL DEFAULT '0',
  filename varchar(80) DEFAULT NULL,
  filetype varchar(80) DEFAULT NULL,
  filesize integer(14) DEFAULT NULL,
  data longblob,
  PRIMARY KEY (imgId)
);

--
-- Table: tiki_blogs
--
DROP TABLE IF EXISTS tiki_blogs;
CREATE TABLE tiki_blogs (
  blogId integer(8) NOT NULL auto_increment,
  created integer(14) DEFAULT NULL,
  lastModif integer(14) DEFAULT NULL,
  title varchar(200) DEFAULT NULL,
  description text,
  user varchar(200) DEFAULT NULL,
  public char(1) DEFAULT NULL,
  posts integer(8) DEFAULT NULL,
  maxPosts integer(8) DEFAULT NULL,
  hits integer(8) DEFAULT NULL,
  activity decimal(4, 2) DEFAULT NULL,
  heading text,
  use_find char(1) DEFAULT NULL,
  use_title char(1) DEFAULT NULL,
  add_date char(1) DEFAULT NULL,
  add_poster char(1) DEFAULT NULL,
  allow_comments char(1) DEFAULT NULL,
  INDEX title (title),
  INDEX description (description(255)),
  INDEX hits (hits),
  FULLTEXT ft (title, description),
  PRIMARY KEY (blogId)
);

--
-- Table: tiki_calendar_categories
--
DROP TABLE IF EXISTS tiki_calendar_categories;
CREATE TABLE tiki_calendar_categories (
  calcatId integer(11) NOT NULL auto_increment,
  calendarId integer(14) NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (calcatId),
  UNIQUE (calendarId, name(16))
);

--
-- Table: tiki_calendar_items
--
DROP TABLE IF EXISTS tiki_calendar_items;
CREATE TABLE tiki_calendar_items (
  calitemId integer(14) NOT NULL auto_increment,
  calendarId integer(14) NOT NULL DEFAULT '0',
  start integer(14) NOT NULL DEFAULT '0',
  end integer(14) NOT NULL DEFAULT '0',
  locationId integer(14) DEFAULT NULL,
  categoryId integer(14) DEFAULT NULL,
  priority enum('1','2','3','4','5','6','7','8','9') NOT NULL DEFAULT '1',
  status enum('0','1','2') NOT NULL DEFAULT '0',
  url varchar(255) DEFAULT NULL,
  lang char(2) NOT NULL DEFAULT 'en',
  name varchar(255) NOT NULL DEFAULT '',
  description blob,
  user varchar(40) DEFAULT NULL,
  created integer(14) NOT NULL DEFAULT '0',
  lastmodif integer(14) NOT NULL DEFAULT '0',
  INDEX calendarId (calendarId),
  PRIMARY KEY (calitemId)
);

--
-- Table: tiki_calendar_locations
--
DROP TABLE IF EXISTS tiki_calendar_locations;
CREATE TABLE tiki_calendar_locations (
  callocId integer(14) NOT NULL auto_increment,
  calendarId integer(14) NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL DEFAULT '',
  description blob,
  PRIMARY KEY (callocId),
  UNIQUE (calendarId, name(16))
);

--
-- Table: tiki_calendar_roles
--
DROP TABLE IF EXISTS tiki_calendar_roles;
CREATE TABLE tiki_calendar_roles (
  calitemId integer(14) NOT NULL DEFAULT '0',
  username varchar(40) NOT NULL DEFAULT '',
  role enum('0','1','2','3','6') NOT NULL DEFAULT '0',
  PRIMARY KEY (calitemId, username(16), role)
);

--
-- Table: tiki_calendars
--
DROP TABLE IF EXISTS tiki_calendars;
CREATE TABLE tiki_calendars (
  calendarId integer(14) NOT NULL auto_increment,
  name varchar(80) NOT NULL DEFAULT '',
  description varchar(255) DEFAULT NULL,
  user varchar(40) NOT NULL DEFAULT '',
  customlocations enum('n','y') NOT NULL DEFAULT 'n',
  customcategories enum('n','y') NOT NULL DEFAULT 'n',
  customlanguages enum('n','y') NOT NULL DEFAULT 'n',
  custompriorities enum('n','y') NOT NULL DEFAULT 'n',
  customparticipants enum('n','y') NOT NULL DEFAULT 'n',
  created integer(14) NOT NULL DEFAULT '0',
  lastmodif integer(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (calendarId)
);

--
-- Table: tiki_categories
--
DROP TABLE IF EXISTS tiki_categories;
CREATE TABLE tiki_categories (
  categId integer(12) NOT NULL auto_increment,
  name varchar(100) DEFAULT NULL,
  description varchar(250) DEFAULT NULL,
  parentId integer(12) DEFAULT NULL,
  hits integer(8) DEFAULT NULL,
  PRIMARY KEY (categId)
);

--
-- Table: tiki_categorized_objects
--
DROP TABLE IF EXISTS tiki_categorized_objects;
CREATE TABLE tiki_categorized_objects (
  catObjectId integer(12) NOT NULL auto_increment,
  type varchar(50) DEFAULT NULL,
  objId varchar(255) DEFAULT NULL,
  description text,
  created integer(14) DEFAULT NULL,
  name varchar(200) DEFAULT NULL,
  href varchar(200) DEFAULT NULL,
  hits integer(8) DEFAULT NULL,
  PRIMARY KEY (catObjectId)
);

--
-- Table: tiki_category_objects
--
DROP TABLE IF EXISTS tiki_category_objects;
CREATE TABLE tiki_category_objects (
  catObjectId integer(12) NOT NULL DEFAULT '0',
  categId integer(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (catObjectId, categId)
);

--
-- Table: tiki_category_sites
--
DROP TABLE IF EXISTS tiki_category_sites;
CREATE TABLE tiki_category_sites (
  categId integer(10) NOT NULL DEFAULT '0',
  siteId integer(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (categId, siteId)
);

--
-- Table: tiki_chart_items
--
DROP TABLE IF EXISTS tiki_chart_items;
CREATE TABLE tiki_chart_items (
  itemId integer(14) NOT NULL auto_increment,
  title varchar(250) DEFAULT NULL,
  description text,
  chartId integer(14) NOT NULL DEFAULT '0',
  created integer(14) DEFAULT NULL,
  URL varchar(250) DEFAULT NULL,
  votes integer(14) DEFAULT NULL,
  points integer(14) DEFAULT NULL,
  average decimal(4, 2) DEFAULT NULL,
  PRIMARY KEY (itemId)
);

--
-- Table: tiki_charts
--
DROP TABLE IF EXISTS tiki_charts;
CREATE TABLE tiki_charts (
  chartId integer(14) NOT NULL auto_increment,
  title varchar(250) DEFAULT NULL,
  description text,
  hits integer(14) DEFAULT NULL,
  singleItemVotes char(1) DEFAULT NULL,
  singleChartVotes char(1) DEFAULT NULL,
  suggestions char(1) DEFAULT NULL,
  autoValidate char(1) DEFAULT NULL,
  topN integer(6) DEFAULT NULL,
  maxVoteValue integer(4) DEFAULT NULL,
  frequency integer(14) DEFAULT NULL,
  showAverage char(1) DEFAULT NULL,
  isActive char(1) DEFAULT NULL,
  showVotes char(1) DEFAULT NULL,
  useCookies char(1) DEFAULT NULL,
  lastChart integer(14) DEFAULT NULL,
  voteAgainAfter integer(14) DEFAULT NULL,
  created integer(14) DEFAULT NULL,
  hist integer(12) DEFAULT NULL,
  PRIMARY KEY (chartId)
);

--
-- Table: tiki_charts_rankings
--
DROP TABLE IF EXISTS tiki_charts_rankings;
CREATE TABLE tiki_charts_rankings (
  chartId integer(14) NOT NULL DEFAULT '0',
  itemId integer(14) NOT NULL DEFAULT '0',
  position integer(14) NOT NULL DEFAULT '0',
  timestamp integer(14) NOT NULL DEFAULT '0',
  lastPosition integer(14) NOT NULL DEFAULT '0',
  period integer(14) NOT NULL DEFAULT '0',
  rvotes integer(14) NOT NULL DEFAULT '0',
  raverage decimal(4, 2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (chartId, itemId, period)
);

--
-- Table: tiki_charts_votes
--
DROP TABLE IF EXISTS tiki_charts_votes;
CREATE TABLE tiki_charts_votes (
  user varchar(200) NOT NULL DEFAULT '',
  itemId integer(14) NOT NULL DEFAULT '0',
  timestamp integer(14) DEFAULT NULL,
  chartId integer(14) DEFAULT NULL,
  PRIMARY KEY (user, itemId)
);

--
-- Table: tiki_chat_channels
--
DROP TABLE IF EXISTS tiki_chat_channels;
CREATE TABLE tiki_chat_channels (
  channelId integer(8) NOT NULL auto_increment,
  name varchar(30) DEFAULT NULL,
  description varchar(250) DEFAULT NULL,
  max_users integer(8) DEFAULT NULL,
  mode char(1) DEFAULT NULL,
  moderator varchar(200) DEFAULT NULL,
  active char(1) DEFAULT NULL,
  refresh integer(6) DEFAULT NULL,
  PRIMARY KEY (channelId)
);

--
-- Table: tiki_chat_messages
--
DROP TABLE IF EXISTS tiki_chat_messages;
CREATE TABLE tiki_chat_messages (
  messageId integer(8) NOT NULL auto_increment,
  channelId integer(8) NOT NULL DEFAULT '0',
  data varchar(255) DEFAULT NULL,
  poster varchar(200) NOT NULL DEFAULT 'anonymous',
  timestamp integer(14) DEFAULT NULL,
  PRIMARY KEY (messageId)
);

--
-- Table: tiki_chat_users
--
DROP TABLE IF EXISTS tiki_chat_users;
CREATE TABLE tiki_chat_users (
  nickname varchar(200) NOT NULL DEFAULT '',
  channelId integer(8) NOT NULL DEFAULT '0',
  timestamp integer(14) DEFAULT NULL,
  PRIMARY KEY (nickname, channelId)
);

--
-- Table: tiki_comments
--
DROP TABLE IF EXISTS tiki_comments;
CREATE TABLE tiki_comments (
  threadId integer(14) NOT NULL auto_increment,
  object varchar(32) NOT NULL DEFAULT '',
  parentId integer(14) DEFAULT NULL,
  userName varchar(200) DEFAULT NULL,
  commentDate integer(14) DEFAULT NULL,
  hits integer(8) DEFAULT NULL,
  type char(1) DEFAULT NULL,
  points decimal(8, 2) DEFAULT NULL,
  votes integer(8) DEFAULT NULL,
  average decimal(8, 4) DEFAULT NULL,
  title varchar(100) DEFAULT NULL,
  data text,
  hash varchar(32) DEFAULT NULL,
  user_ip varchar(15) DEFAULT NULL,
  summary varchar(240) DEFAULT NULL,
  smiley varchar(80) DEFAULT NULL,
  message_id varchar(250) default NULL,
  in_reply_to varchar(250) default NULL,
  INDEX title (title),
  INDEX data (data(255)),
  INDEX object (object),
  INDEX hits (hits),
  INDEX tc_pi (parentId),
  FULLTEXT ft (title, data),
  PRIMARY KEY (threadId)
);

--
-- Table: tiki_content
--
DROP TABLE IF EXISTS tiki_content;
CREATE TABLE tiki_content (
  contentId integer(8) NOT NULL auto_increment,
  description text,
  PRIMARY KEY (contentId)
);

--
-- Table: tiki_content_templates
--
DROP TABLE IF EXISTS tiki_content_templates;
CREATE TABLE tiki_content_templates (
  templateId integer(10) NOT NULL auto_increment,
  content longblob,
  name varchar(200) DEFAULT NULL,
  created integer(14) DEFAULT NULL,
  PRIMARY KEY (templateId)
);

--
-- Table: tiki_content_templates_sections
--
DROP TABLE IF EXISTS tiki_content_templates_sections;
CREATE TABLE tiki_content_templates_sections (
  templateId integer(10) NOT NULL DEFAULT '0',
  section varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (templateId, section)
);

--
-- Table: tiki_cookies
--
DROP TABLE IF EXISTS tiki_cookies;
CREATE TABLE tiki_cookies (
  cookieId integer(10) NOT NULL auto_increment,
  cookie varchar(255) DEFAULT NULL,
  PRIMARY KEY (cookieId)
);

--
-- Table: tiki_copyrights
--
DROP TABLE IF EXISTS tiki_copyrights;
CREATE TABLE tiki_copyrights (
  copyrightId integer(12) NOT NULL auto_increment,
  page varchar(200) DEFAULT NULL,
  title varchar(200) DEFAULT NULL,
  year integer(11) DEFAULT NULL,
  authors varchar(200) DEFAULT NULL,
  copyright_order integer(11) DEFAULT NULL,
  userName varchar(200) DEFAULT NULL,
  PRIMARY KEY (copyrightId)
);

--
-- Table: tiki_directory_categories
--
DROP TABLE IF EXISTS tiki_directory_categories;
CREATE TABLE tiki_directory_categories (
  categId integer(10) NOT NULL auto_increment,
  parent integer(10) DEFAULT NULL,
  name varchar(240) DEFAULT NULL,
  description text,
  childrenType char(1) DEFAULT NULL,
  sites integer(10) DEFAULT NULL,
  viewableChildren integer(4) DEFAULT NULL,
  allowSites char(1) DEFAULT NULL,
  showCount char(1) DEFAULT NULL,
  editorGroup varchar(200) DEFAULT NULL,
  hits integer(12) DEFAULT NULL,
  PRIMARY KEY (categId)
);

--
-- Table: tiki_directory_search
--
DROP TABLE IF EXISTS tiki_directory_search;
CREATE TABLE tiki_directory_search (
  term varchar(250) NOT NULL DEFAULT '',
  hits integer(14) DEFAULT NULL,
  PRIMARY KEY (term)
);

--
-- Table: tiki_directory_sites
--
DROP TABLE IF EXISTS tiki_directory_sites;
CREATE TABLE tiki_directory_sites (
  siteId integer(14) NOT NULL auto_increment,
  name varchar(240) DEFAULT NULL,
  description text,
  url varchar(255) DEFAULT NULL,
  country varchar(255) DEFAULT NULL,
  hits integer(12) DEFAULT NULL,
  isValid char(1) DEFAULT NULL,
  created integer(14) DEFAULT NULL,
  lastModif integer(14) DEFAULT NULL,
  cache longblob,
  cache_timestamp integer(14) DEFAULT NULL,
  FULLTEXT ft (name, description),
  PRIMARY KEY (siteId)
);

--
-- Table: tiki_drawings
--
DROP TABLE IF EXISTS tiki_drawings;
CREATE TABLE tiki_drawings (
  drawId integer(12) NOT NULL auto_increment,
  version integer(8) DEFAULT NULL,
  name varchar(250) DEFAULT NULL,
  filename_draw varchar(250) DEFAULT NULL,
  filename_pad varchar(250) DEFAULT NULL,
  timestamp integer(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  PRIMARY KEY (drawId)
);

--
-- Table: tiki_dsn
--
DROP TABLE IF EXISTS tiki_dsn;
CREATE TABLE tiki_dsn (
  dsnId integer(12) NOT NULL auto_increment,
  name varchar(200) NOT NULL DEFAULT '',
  dsn varchar(255) DEFAULT NULL,
  PRIMARY KEY (dsnId)
);

--
-- Table: tiki_eph
--
DROP TABLE IF EXISTS tiki_eph;
CREATE TABLE tiki_eph (
  ephId integer(12) NOT NULL auto_increment,
  title varchar(250) DEFAULT NULL,
  isFile char(1) DEFAULT NULL,
  filename varchar(250) DEFAULT NULL,
  filetype varchar(250) DEFAULT NULL,
  filesize varchar(250) DEFAULT NULL,
  data longblob,
  textdata longblob,
  publish integer(14) DEFAULT NULL,
  hits integer(10) DEFAULT NULL,
  PRIMARY KEY (ephId)
);

--
-- Table: tiki_extwiki
--
DROP TABLE IF EXISTS tiki_extwiki;
CREATE TABLE tiki_extwiki (
  extwikiId integer(12) NOT NULL auto_increment,
  name varchar(200) NOT NULL DEFAULT '',
  extwiki varchar(255) DEFAULT NULL,
  PRIMARY KEY (extwikiId)
);

--
-- Table: tiki_faq_questions
--
DROP TABLE IF EXISTS tiki_faq_questions;
CREATE TABLE tiki_faq_questions (
  questionId integer(10) NOT NULL auto_increment,
  faqId integer(10) DEFAULT NULL,
  position integer(4) DEFAULT NULL,
  question text,
  answer text,
  INDEX faqId (faqId),
  INDEX question (question(255)),
  INDEX answer (answer(255)),
  FULLTEXT ft (question, answer),
  PRIMARY KEY (questionId)
);

--
-- Table: tiki_faqs
--
DROP TABLE IF EXISTS tiki_faqs;
CREATE TABLE tiki_faqs (
  faqId integer(10) NOT NULL auto_increment,
  title varchar(200) DEFAULT NULL,
  description text,
  created integer(14) DEFAULT NULL,
  questions integer(5) DEFAULT NULL,
  hits integer(8) DEFAULT NULL,
  canSuggest char(1) DEFAULT NULL,
  INDEX title (title),
  INDEX description (description(255)),
  INDEX hits (hits),
  FULLTEXT ft (title, description),
  PRIMARY KEY (faqId)
);

--
-- Table: tiki_featured_links
--
DROP TABLE IF EXISTS tiki_featured_links;
CREATE TABLE tiki_featured_links (
  url varchar(200) NOT NULL DEFAULT '',
  title varchar(200) DEFAULT NULL,
  description text,
  hits integer(8) DEFAULT NULL,
  position integer(6) DEFAULT NULL,
  type char(1) DEFAULT NULL,
  PRIMARY KEY (url)
);

--
-- Table: tiki_file_galleries
--
DROP TABLE IF EXISTS tiki_file_galleries;
CREATE TABLE tiki_file_galleries (
  galleryId integer(14) NOT NULL auto_increment,
  name varchar(80) NOT NULL DEFAULT '',
  description text,
  created integer(14) DEFAULT NULL,
  visible char(1) DEFAULT NULL,
  lastModif integer(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  hits integer(14) DEFAULT NULL,
  votes integer(8) DEFAULT NULL,
  points decimal(8, 2) DEFAULT NULL,
  maxRows integer(10) DEFAULT NULL,
  public char(1) DEFAULT NULL,
  show_id char(1) DEFAULT NULL,
  show_icon char(1) DEFAULT NULL,
  show_name char(1) DEFAULT NULL,
  show_size char(1) DEFAULT NULL,
  show_description char(1) DEFAULT NULL,
  max_desc integer(8) DEFAULT NULL,
  show_created char(1) DEFAULT NULL,
  show_dl char(1) DEFAULT NULL,
  PRIMARY KEY (galleryId)
);

--
-- Table: tiki_files
--
DROP TABLE IF EXISTS tiki_files;
CREATE TABLE tiki_files (
  fileId integer(14) NOT NULL auto_increment,
  galleryId integer(14) NOT NULL DEFAULT '0',
  name varchar(200) NOT NULL DEFAULT '',
  description text,
  created integer(14) DEFAULT NULL,
  filename varchar(80) DEFAULT NULL,
  filesize integer(14) DEFAULT NULL,
  filetype varchar(250) DEFAULT NULL,
  data longblob,
  user varchar(200) DEFAULT NULL,
  downloads integer(14) DEFAULT NULL,
  votes integer(8) DEFAULT NULL,
  points decimal(8, 2) DEFAULT NULL,
  path varchar(255) DEFAULT NULL,
  reference_url varchar(250) DEFAULT NULL,
  is_reference char(1) DEFAULT NULL,
  hash varchar(32) DEFAULT NULL,
  INDEX name (name),
  INDEX description (description(255)),
  INDEX downloads (downloads),
  FULLTEXT ft (name, description),
  PRIMARY KEY (fileId)
);

--
-- Table: tiki_forum_attachments
--
DROP TABLE IF EXISTS tiki_forum_attachments;
CREATE TABLE tiki_forum_attachments (
  attId integer(14) NOT NULL auto_increment,
  threadId integer(14) NOT NULL DEFAULT '0',
  qId integer(14) NOT NULL DEFAULT '0',
  forumId integer(14) DEFAULT NULL,
  filename varchar(250) DEFAULT NULL,
  filetype varchar(250) DEFAULT NULL,
  filesize integer(12) DEFAULT NULL,
  data longblob,
  dir varchar(200) DEFAULT NULL,
  created integer(14) DEFAULT NULL,
  path varchar(250) DEFAULT NULL,
  PRIMARY KEY (attId)
);

--
-- Table: tiki_forum_reads
--
DROP TABLE IF EXISTS tiki_forum_reads;
CREATE TABLE tiki_forum_reads (
  user varchar(200) NOT NULL DEFAULT '',
  threadId integer(14) NOT NULL DEFAULT '0',
  forumId integer(14) DEFAULT NULL,
  timestamp integer(14) DEFAULT NULL,
  PRIMARY KEY (user, threadId)
);

--
-- Table: tiki_forums
--
DROP TABLE IF EXISTS tiki_forums;
CREATE TABLE tiki_forums (
  forumId integer(8) NOT NULL auto_increment,
  name varchar(200) DEFAULT NULL,
  description text,
  created integer(14) DEFAULT NULL,
  lastPost integer(14) DEFAULT NULL,
  threads integer(8) DEFAULT NULL,
  comments integer(8) DEFAULT NULL,
  controlFlood char(1) DEFAULT NULL,
  floodInterval integer(8) DEFAULT NULL,
  moderator varchar(200) DEFAULT NULL,
  hits integer(8) DEFAULT NULL,
  mail varchar(200) DEFAULT NULL,
  useMail char(1) DEFAULT NULL,
  section varchar(200) DEFAULT NULL,
  usePruneUnreplied char(1) DEFAULT NULL,
  pruneUnrepliedAge integer(8) DEFAULT NULL,
  usePruneOld char(1) DEFAULT NULL,
  pruneMaxAge integer(8) DEFAULT NULL,
  topicsPerPage integer(6) DEFAULT NULL,
  topicOrdering varchar(100) DEFAULT NULL,
  threadOrdering varchar(100) DEFAULT NULL,
  att varchar(80) DEFAULT NULL,
  att_store varchar(4) DEFAULT NULL,
  att_store_dir varchar(250) DEFAULT NULL,
  att_max_size integer(12) DEFAULT NULL,
  ui_level char(1) DEFAULT NULL,
  forum_password varchar(32) DEFAULT NULL,
  forum_use_password char(1) DEFAULT NULL,
  moderator_group varchar(200) DEFAULT NULL,
  approval_type varchar(20) DEFAULT NULL,
  outbound_address varchar(250) DEFAULT NULL,
  outbound_mails_for_inbound_mails char(1) DEFAULT NULL,
  outbound_mails_reply_link char(1) DEFAULT NULL,
  outbound_from varchar(250) default NULL,
  inbound_pop_server varchar(250) DEFAULT NULL,
  inbound_pop_port integer(4) DEFAULT NULL,
  inbound_pop_user varchar(200) DEFAULT NULL,
  inbound_pop_password varchar(80) DEFAULT NULL,
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
  PRIMARY KEY (forumId)
);

--
-- Table: tiki_forums_queue
--
DROP TABLE IF EXISTS tiki_forums_queue;
CREATE TABLE tiki_forums_queue (
  qId integer(14) NOT NULL auto_increment,
  object varchar(32) DEFAULT NULL,
  parentId integer(14) DEFAULT NULL,
  forumId integer(14) DEFAULT NULL,
  timestamp integer(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  title varchar(240) DEFAULT NULL,
  data text,
  type varchar(60) DEFAULT NULL,
  hash varchar(32) DEFAULT NULL,
  topic_smiley varchar(80) DEFAULT NULL,
  topic_title varchar(240) DEFAULT NULL,
  summary varchar(240) DEFAULT NULL,
  PRIMARY KEY (qId)
);

--
-- Table: tiki_forums_reported
--
DROP TABLE IF EXISTS tiki_forums_reported;
CREATE TABLE tiki_forums_reported (
  threadId integer(12) NOT NULL DEFAULT '0',
  forumId integer(12) NOT NULL DEFAULT '0',
  parentId integer(12) NOT NULL DEFAULT '0',
  user varchar(200) DEFAULT NULL,
  timestamp integer(14) DEFAULT NULL,
  reason varchar(250) DEFAULT NULL,
  PRIMARY KEY (threadId)
);

--
-- Table: tiki_galleries
--
DROP TABLE IF EXISTS tiki_galleries;
CREATE TABLE tiki_galleries (
  galleryId integer(14) NOT NULL auto_increment,
  name varchar(80) NOT NULL DEFAULT '',
  description text,
  created integer(14) DEFAULT NULL,
  lastModif integer(14) DEFAULT NULL,
  visible char(1) DEFAULT NULL,
  theme varchar(60) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  hits integer(14) DEFAULT NULL,
  maxRows integer(10) DEFAULT NULL,
  rowImages integer(10) DEFAULT NULL,
  thumbSizeX integer(10) DEFAULT NULL,
  thumbSizeY integer(10) DEFAULT NULL,
  public char(1) DEFAULT NULL,
  INDEX name (name),
  INDEX description (description(255)),
  INDEX hits (hits),
  FULLTEXT ft (name, description),
  PRIMARY KEY (galleryId)
);

--
-- Table: tiki_galleries_scales
--
DROP TABLE IF EXISTS tiki_galleries_scales;
CREATE TABLE tiki_galleries_scales (
  galleryId integer(14) NOT NULL DEFAULT '0',
  xsize integer(11) NOT NULL DEFAULT '0',
  ysize integer(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (galleryId, xsize, ysize)
);

--
-- Table: tiki_games
--
DROP TABLE IF EXISTS tiki_games;
CREATE TABLE tiki_games (
  gameName varchar(200) NOT NULL DEFAULT '',
  hits integer(8) DEFAULT NULL,
  votes integer(8) DEFAULT NULL,
  points integer(8) DEFAULT NULL,
  PRIMARY KEY (gameName)
);

--
-- Table: tiki_group_inclusion
--
DROP TABLE IF EXISTS tiki_group_inclusion;
CREATE TABLE tiki_group_inclusion (
  groupName varchar(30) NOT NULL DEFAULT '',
  includeGroup varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (groupName, includeGroup)
);

--
-- Table: tiki_history
--
DROP TABLE IF EXISTS tiki_history;
CREATE TABLE tiki_history (
  pageName varchar(160) NOT NULL DEFAULT '',
  version integer(8) NOT NULL DEFAULT '0',
  version_minor integer(8) NOT NULL DEFAULT '0',
  lastModif integer(14) DEFAULT NULL,
  description varchar(200) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  ip varchar(15) DEFAULT NULL,
  comment varchar(200) DEFAULT NULL,
  data longblob,
  PRIMARY KEY (pageName, version)
);

--
-- Table: tiki_hotwords
--
DROP TABLE IF EXISTS tiki_hotwords;
CREATE TABLE tiki_hotwords (
  word varchar(40) NOT NULL DEFAULT '',
  url varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (word)
);

--
-- Table: tiki_html_pages
--
DROP TABLE IF EXISTS tiki_html_pages;
CREATE TABLE tiki_html_pages (
  pageName varchar(200) NOT NULL DEFAULT '',
  content longblob,
  refresh integer(10) DEFAULT NULL,
  type char(1) DEFAULT NULL,
  created integer(14) DEFAULT NULL,
  PRIMARY KEY (pageName)
);

--
-- Table: tiki_html_pages_dynamic_zones
--
DROP TABLE IF EXISTS tiki_html_pages_dynamic_zones;
CREATE TABLE tiki_html_pages_dynamic_zones (
  pageName varchar(40) NOT NULL DEFAULT '',
  zone varchar(80) NOT NULL DEFAULT '',
  type char(2) DEFAULT NULL,
  content text,
  PRIMARY KEY (pageName, zone)
);

--
-- Table: tiki_images
--
DROP TABLE IF EXISTS tiki_images;
CREATE TABLE tiki_images (
  imageId integer(14) NOT NULL auto_increment,
  galleryId integer(14) NOT NULL DEFAULT '0',
  name varchar(200) NOT NULL DEFAULT '',
  description text,
  created integer(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  hits integer(14) DEFAULT NULL,
  path varchar(255) DEFAULT NULL,
  INDEX name (name),
  INDEX description (description(255)),
  INDEX hits (hits),
  INDEX ti_gId (galleryId),
  INDEX ti_cr (created),
  INDEX ti_us (user),
  FULLTEXT ft (name, description),
  PRIMARY KEY (imageId)
);

--
-- Table: tiki_images_data
--
DROP TABLE IF EXISTS tiki_images_data;
CREATE TABLE tiki_images_data (
  imageId integer(14) NOT NULL DEFAULT '0',
  xsize integer(8) NOT NULL DEFAULT '0',
  ysize integer(8) NOT NULL DEFAULT '0',
  type char(1) NOT NULL DEFAULT '',
  filesize integer(14) DEFAULT NULL,
  filetype varchar(80) DEFAULT NULL,
  filename varchar(80) DEFAULT NULL,
  data longblob,
  INDEX t_i_d_it (imageId, type),
  PRIMARY KEY (imageId, xsize, ysize, type)
);

--
-- Table: tiki_language
--
DROP TABLE IF EXISTS tiki_language;
CREATE TABLE tiki_language (
  source tinyblob NOT NULL,
  lang char(2) NOT NULL DEFAULT '',
  tran tinyblob,
  PRIMARY KEY (source(255), lang)
);

--
-- Table: tiki_languages
--
DROP TABLE IF EXISTS tiki_languages;
CREATE TABLE tiki_languages (
  lang char(2) NOT NULL DEFAULT '',
  language varchar(255) DEFAULT NULL,
  PRIMARY KEY (lang)
);

--
-- Table: tiki_link_cache
--
DROP TABLE IF EXISTS tiki_link_cache;
CREATE TABLE tiki_link_cache (
  cacheId integer(14) NOT NULL auto_increment,
  url varchar(250) DEFAULT NULL,
  data longblob,
  refresh integer(14) DEFAULT NULL,
  PRIMARY KEY (cacheId)
);

--
-- Table: tiki_links
--
DROP TABLE IF EXISTS tiki_links;
CREATE TABLE tiki_links (
  fromPage varchar(160) NOT NULL DEFAULT '',
  toPage varchar(160) NOT NULL DEFAULT '',
  PRIMARY KEY (fromPage, toPage)
);

--
-- Table: tiki_live_support_events
--
DROP TABLE IF EXISTS tiki_live_support_events;
CREATE TABLE tiki_live_support_events (
  eventId integer(14) NOT NULL auto_increment,
  reqId varchar(32) NOT NULL DEFAULT '',
  type varchar(40) DEFAULT NULL,
  seqId integer(14) DEFAULT NULL,
  senderId varchar(32) DEFAULT NULL,
  data text,
  timestamp integer(14) DEFAULT NULL,
  PRIMARY KEY (eventId)
);

--
-- Table: tiki_live_support_message_comments
--
DROP TABLE IF EXISTS tiki_live_support_message_comments;
CREATE TABLE tiki_live_support_message_comments (
  cId integer(12) NOT NULL auto_increment,
  msgId integer(12) DEFAULT NULL,
  data text,
  timestamp integer(14) DEFAULT NULL,
  PRIMARY KEY (cId)
);

--
-- Table: tiki_live_support_messages
--
DROP TABLE IF EXISTS tiki_live_support_messages;
CREATE TABLE tiki_live_support_messages (
  msgId integer(12) NOT NULL auto_increment,
  data text,
  timestamp integer(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  username varchar(200) DEFAULT NULL,
  priority integer(2) DEFAULT NULL,
  status char(1) DEFAULT NULL,
  assigned_to varchar(200) DEFAULT NULL,
  resolution varchar(100) DEFAULT NULL,
  title varchar(200) DEFAULT NULL,
  module integer(4) DEFAULT NULL,
  email varchar(250) DEFAULT NULL,
  PRIMARY KEY (msgId)
);

--
-- Table: tiki_live_support_modules
--
DROP TABLE IF EXISTS tiki_live_support_modules;
CREATE TABLE tiki_live_support_modules (
  modId integer(4) NOT NULL auto_increment,
  name varchar(90) DEFAULT NULL,
  PRIMARY KEY (modId)
);

--
-- Table: tiki_live_support_operators
--
DROP TABLE IF EXISTS tiki_live_support_operators;
CREATE TABLE tiki_live_support_operators (
  user varchar(200) NOT NULL DEFAULT '',
  accepted_requests integer(10) DEFAULT NULL,
  status varchar(20) DEFAULT NULL,
  longest_chat integer(10) DEFAULT NULL,
  shortest_chat integer(10) DEFAULT NULL,
  average_chat integer(10) DEFAULT NULL,
  last_chat integer(14) DEFAULT NULL,
  time_online integer(10) DEFAULT NULL,
  votes integer(10) DEFAULT NULL,
  points integer(10) DEFAULT NULL,
  status_since integer(14) DEFAULT NULL,
  PRIMARY KEY (user)
);

--
-- Table: tiki_live_support_requests
--
DROP TABLE IF EXISTS tiki_live_support_requests;
CREATE TABLE tiki_live_support_requests (
  reqId varchar(32) NOT NULL DEFAULT '',
  user varchar(200) DEFAULT NULL,
  tiki_user varchar(200) DEFAULT NULL,
  email varchar(200) DEFAULT NULL,
  operator varchar(200) DEFAULT NULL,
  operator_id varchar(32) DEFAULT NULL,
  user_id varchar(32) DEFAULT NULL,
  reason text,
  req_timestamp integer(14) DEFAULT NULL,
  timestamp integer(14) DEFAULT NULL,
  status varchar(40) DEFAULT NULL,
  resolution varchar(40) DEFAULT NULL,
  chat_started integer(14) DEFAULT NULL,
  chat_ended integer(14) DEFAULT NULL,
  PRIMARY KEY (reqId)
);

--
-- Table: tiki_mail_events
--
DROP TABLE IF EXISTS tiki_mail_events;
CREATE TABLE tiki_mail_events (
  event varchar(200) DEFAULT NULL,
  object varchar(200) DEFAULT NULL,
  email varchar(200) DEFAULT NULL
);

--
-- Table: tiki_mailin_accounts
--
DROP TABLE IF EXISTS tiki_mailin_accounts;
CREATE TABLE tiki_mailin_accounts (
  accountId integer(12) NOT NULL auto_increment,
  user varchar(200) NOT NULL DEFAULT '',
  account varchar(50) NOT NULL DEFAULT '',
  pop varchar(255) DEFAULT NULL,
  port integer(4) DEFAULT NULL,
  username varchar(100) DEFAULT NULL,
  pass varchar(100) DEFAULT NULL,
  active char(1) DEFAULT NULL,
  type varchar(40) DEFAULT NULL,
  smtp varchar(255) DEFAULT NULL,
  useAuth char(1) DEFAULT NULL,
  smtpPort integer(4) DEFAULT NULL,
  PRIMARY KEY (accountId)
);

--
-- Table: tiki_menu_languages
--
DROP TABLE IF EXISTS tiki_menu_languages;
CREATE TABLE tiki_menu_languages (
  menuId integer(8) NOT NULL auto_increment,
  language char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (menuId, language)
);

--
-- Table: tiki_menu_options
--
DROP TABLE IF EXISTS tiki_menu_options;
CREATE TABLE tiki_menu_options (
  optionId integer(8) NOT NULL auto_increment,
  menuId integer(8) DEFAULT NULL,
  type char(1) DEFAULT NULL,
  name varchar(200) DEFAULT NULL,
  url varchar(255) DEFAULT NULL,
  position integer(4) DEFAULT NULL,
  PRIMARY KEY (optionId)
);

--
-- Table: tiki_menus
--
DROP TABLE IF EXISTS tiki_menus;
CREATE TABLE tiki_menus (
  menuId integer(8) NOT NULL auto_increment,
  name varchar(200) NOT NULL DEFAULT '',
  description text,
  type char(1) DEFAULT NULL,
  PRIMARY KEY (menuId)
);

--
-- Table: tiki_minical_events
--
DROP TABLE IF EXISTS tiki_minical_events;
CREATE TABLE tiki_minical_events (
  user varchar(200) DEFAULT NULL,
  eventId integer(12) NOT NULL auto_increment,
  title varchar(250) DEFAULT NULL,
  description text,
  start integer(14) DEFAULT NULL,
  end integer(14) DEFAULT NULL,
  security char(1) DEFAULT NULL,
  duration integer(3) DEFAULT NULL,
  topicId integer(12) DEFAULT NULL,
  reminded char(1) DEFAULT NULL,
  PRIMARY KEY (eventId)
);

--
-- Table: tiki_minical_topics
--
DROP TABLE IF EXISTS tiki_minical_topics;
CREATE TABLE tiki_minical_topics (
  user varchar(200) DEFAULT NULL,
  topicId integer(12) NOT NULL auto_increment,
  name varchar(250) DEFAULT NULL,
  filename varchar(200) DEFAULT NULL,
  filetype varchar(200) DEFAULT NULL,
  filesize varchar(200) DEFAULT NULL,
  data longblob,
  path varchar(250) DEFAULT NULL,
  isIcon char(1) DEFAULT NULL,
  PRIMARY KEY (topicId)
);

--
-- Table: tiki_modules
--
DROP TABLE IF EXISTS tiki_modules;
CREATE TABLE tiki_modules (
  name varchar(200) NOT NULL DEFAULT '',
  position char(1) DEFAULT NULL,
  ord integer(4) DEFAULT NULL,
  type char(1) DEFAULT NULL,
  title varchar(40) DEFAULT NULL,
  cache_time integer(14) DEFAULT NULL,
  rows integer(4) DEFAULT NULL,
  params varchar(255) DEFAULT NULL,
  groups text,
  PRIMARY KEY (name)
);

--
-- Table: tiki_newsletter_subscriptions
--
DROP TABLE IF EXISTS tiki_newsletter_subscriptions;
CREATE TABLE tiki_newsletter_subscriptions (
  nlId integer(12) NOT NULL DEFAULT '0',
  email varchar(255) NOT NULL DEFAULT '',
  code varchar(32) DEFAULT NULL,
  valid char(1) DEFAULT NULL,
  subscribed integer(14) DEFAULT NULL,
  PRIMARY KEY (nlId, email)
);

--
-- Table: tiki_newsletters
--
DROP TABLE IF EXISTS tiki_newsletters;
CREATE TABLE tiki_newsletters (
  nlId integer(12) NOT NULL auto_increment,
  name varchar(200) DEFAULT NULL,
  description text,
  created integer(14) DEFAULT NULL,
  lastSent integer(14) DEFAULT NULL,
  editions integer(10) DEFAULT NULL,
  users integer(10) DEFAULT NULL,
  allowAnySub char(1) DEFAULT NULL,
  frequency integer(14) DEFAULT NULL,
  PRIMARY KEY (nlId)
);

--
-- Table: tiki_newsreader_marks
--
DROP TABLE IF EXISTS tiki_newsreader_marks;
CREATE TABLE tiki_newsreader_marks (
  user varchar(200) NOT NULL DEFAULT '',
  serverId integer(12) NOT NULL DEFAULT '0',
  groupName varchar(255) NOT NULL DEFAULT '',
  timestamp integer(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (user, serverId, groupName)
);

--
-- Table: tiki_newsreader_servers
--
DROP TABLE IF EXISTS tiki_newsreader_servers;
CREATE TABLE tiki_newsreader_servers (
  user varchar(200) NOT NULL DEFAULT '',
  serverId integer(12) NOT NULL auto_increment,
  server varchar(250) DEFAULT NULL,
  port integer(4) DEFAULT NULL,
  username varchar(200) DEFAULT NULL,
  password varchar(200) DEFAULT NULL,
  PRIMARY KEY (serverId)
);

--
-- Table: tiki_page_footnotes
--
DROP TABLE IF EXISTS tiki_page_footnotes;
CREATE TABLE tiki_page_footnotes (
  user varchar(200) NOT NULL DEFAULT '',
  pageName varchar(250) NOT NULL DEFAULT '',
  data text,
  PRIMARY KEY (user, pageName)
);

--
-- Table: tiki_pages
--
DROP TABLE IF EXISTS tiki_pages;
CREATE TABLE tiki_pages (
  pageName varchar(160) NOT NULL DEFAULT '',
  hits integer(8) DEFAULT NULL,
  data text,
  description varchar(200) DEFAULT NULL,
  lastModif integer(14) DEFAULT NULL,
  comment varchar(200) DEFAULT NULL,
  version integer(8) NOT NULL DEFAULT '0',
  user varchar(200) DEFAULT NULL,
  ip varchar(15) DEFAULT NULL,
  flag char(1) DEFAULT NULL,
  points integer(8) DEFAULT NULL,
  votes integer(8) DEFAULT NULL,
  cache text,
  cache_timestamp integer(14) DEFAULT NULL,
  pageRank decimal(4, 3) DEFAULT NULL,
  creator varchar(200) DEFAULT NULL,
  INDEX data (data(255)),
  INDEX pageRank (pageRank),
  FULLTEXT ft (pageName, data),
  PRIMARY KEY (pageName)
);

--
-- Table: tiki_pageviews
--
DROP TABLE IF EXISTS tiki_pageviews;
CREATE TABLE tiki_pageviews (
  day integer(14) NOT NULL DEFAULT '0',
  pageviews integer(14) DEFAULT NULL,
  PRIMARY KEY (day)
);

--
-- Table: tiki_poll_options
--
DROP TABLE IF EXISTS tiki_poll_options;
CREATE TABLE tiki_poll_options (
  pollId integer(8) NOT NULL DEFAULT '0',
  optionId integer(8) NOT NULL auto_increment,
  title varchar(200) DEFAULT NULL,
  votes integer(8) DEFAULT NULL,
  PRIMARY KEY (optionId)
);

--
-- Table: tiki_polls
--
DROP TABLE IF EXISTS tiki_polls;
CREATE TABLE tiki_polls (
  pollId integer(8) NOT NULL auto_increment,
  title varchar(200) DEFAULT NULL,
  votes integer(8) DEFAULT NULL,
  active char(1) DEFAULT NULL,
  publishDate integer(14) DEFAULT NULL,
  PRIMARY KEY (pollId)
);

--
-- Table: tiki_preferences
--
DROP TABLE IF EXISTS tiki_preferences;
CREATE TABLE tiki_preferences (
  name varchar(40) NOT NULL DEFAULT '',
  value varchar(250) DEFAULT NULL,
  PRIMARY KEY (name)
);

--
-- Table: tiki_private_messages
--
DROP TABLE IF EXISTS tiki_private_messages;
CREATE TABLE tiki_private_messages (
  messageId integer(8) NOT NULL auto_increment,
  toNickname varchar(200) NOT NULL DEFAULT '',
  data varchar(255) DEFAULT NULL,
  poster varchar(200) NOT NULL DEFAULT 'anonymous',
  timestamp integer(14) DEFAULT NULL,
  PRIMARY KEY (messageId)
);

--
-- Table: tiki_programmed_content
--
DROP TABLE IF EXISTS tiki_programmed_content;
CREATE TABLE tiki_programmed_content (
  pId integer(8) NOT NULL auto_increment,
  contentId integer(8) NOT NULL DEFAULT '0',
  publishDate integer(14) NOT NULL DEFAULT '0',
  data text,
  PRIMARY KEY (pId)
);

--
-- Table: tiki_quiz_question_options
--
DROP TABLE IF EXISTS tiki_quiz_question_options;
CREATE TABLE tiki_quiz_question_options (
  optionId integer(10) NOT NULL auto_increment,
  questionId integer(10) DEFAULT NULL,
  optionText text,
  points integer(4) DEFAULT NULL,
  PRIMARY KEY (optionId)
);

--
-- Table: tiki_quiz_questions
--
DROP TABLE IF EXISTS tiki_quiz_questions;
CREATE TABLE tiki_quiz_questions (
  questionId integer(10) NOT NULL auto_increment,
  quizId integer(10) DEFAULT NULL,
  question text,
  position integer(4) DEFAULT NULL,
  type char(1) DEFAULT NULL,
  maxPoints integer(4) DEFAULT NULL,
  PRIMARY KEY (questionId)
);

--
-- Table: tiki_quiz_results
--
DROP TABLE IF EXISTS tiki_quiz_results;
CREATE TABLE tiki_quiz_results (
  resultId integer(10) NOT NULL auto_increment,
  quizId integer(10) DEFAULT NULL,
  fromPoints integer(4) DEFAULT NULL,
  toPoints integer(4) DEFAULT NULL,
  answer text,
  PRIMARY KEY (resultId)
);

--
-- Table: tiki_quiz_stats
--
DROP TABLE IF EXISTS tiki_quiz_stats;
CREATE TABLE tiki_quiz_stats (
  quizId integer(10) NOT NULL DEFAULT '0',
  questionId integer(10) NOT NULL DEFAULT '0',
  optionId integer(10) NOT NULL DEFAULT '0',
  votes integer(10) DEFAULT NULL,
  PRIMARY KEY (quizId, questionId, optionId)
);

--
-- Table: tiki_quiz_stats_sum
--
DROP TABLE IF EXISTS tiki_quiz_stats_sum;
CREATE TABLE tiki_quiz_stats_sum (
  quizId integer(10) NOT NULL DEFAULT '0',
  quizName varchar(255) DEFAULT NULL,
  timesTaken integer(10) DEFAULT NULL,
  avgpoints decimal(5, 2) DEFAULT NULL,
  avgavg decimal(5, 2) DEFAULT NULL,
  avgtime decimal(5, 2) DEFAULT NULL,
  PRIMARY KEY (quizId)
);

--
-- Table: tiki_quizzes
--
DROP TABLE IF EXISTS tiki_quizzes;
CREATE TABLE tiki_quizzes (
  quizId integer(10) NOT NULL auto_increment,
  name varchar(255) DEFAULT NULL,
  description text,
  canRepeat char(1) DEFAULT NULL,
  storeResults char(1) DEFAULT NULL,
  questionsPerPage integer(4) DEFAULT NULL,
  timeLimited char(1) DEFAULT NULL,
  timeLimit integer(14) DEFAULT NULL,
  created integer(14) DEFAULT NULL,
  taken integer(10) DEFAULT NULL,
  PRIMARY KEY (quizId)
);

--
-- Table: tiki_received_articles
--
DROP TABLE IF EXISTS tiki_received_articles;
CREATE TABLE tiki_received_articles (
  receivedArticleId integer(14) NOT NULL auto_increment,
  receivedFromSite varchar(200) DEFAULT NULL,
  receivedFromUser varchar(200) DEFAULT NULL,
  receivedDate integer(14) DEFAULT NULL,
  title varchar(80) DEFAULT NULL,
  authorName varchar(60) DEFAULT NULL,
  size integer(12) DEFAULT NULL,
  useImage char(1) DEFAULT NULL,
  image_name varchar(80) DEFAULT NULL,
  image_type varchar(80) DEFAULT NULL,
  image_size integer(14) DEFAULT NULL,
  image_x integer(4) DEFAULT NULL,
  image_y integer(4) DEFAULT NULL,
  image_data longblob,
  publishDate integer(14) DEFAULT NULL,
  created integer(14) DEFAULT NULL,
  heading text,
  body longblob,
  hash varchar(32) DEFAULT NULL,
  author varchar(200) DEFAULT NULL,
  type varchar(50) DEFAULT NULL,
  rating decimal(3, 2) DEFAULT NULL,
  PRIMARY KEY (receivedArticleId)
);

--
-- Table: tiki_received_pages
--
DROP TABLE IF EXISTS tiki_received_pages;
CREATE TABLE tiki_received_pages (
  receivedPageId integer(14) NOT NULL auto_increment,
  pageName varchar(160) NOT NULL DEFAULT '',
  data longblob,
  description varchar(200) DEFAULT NULL,
  comment varchar(200) DEFAULT NULL,
  receivedFromSite varchar(200) DEFAULT NULL,
  receivedFromUser varchar(200) DEFAULT NULL,
  receivedDate integer(14) DEFAULT NULL,
  PRIMARY KEY (receivedPageId)
);

--
-- Table: tiki_referer_stats
--
DROP TABLE IF EXISTS tiki_referer_stats;
CREATE TABLE tiki_referer_stats (
  referer varchar(50) NOT NULL DEFAULT '',
  hits integer(10) DEFAULT NULL,
  last integer(14) DEFAULT NULL,
  PRIMARY KEY (referer)
);

--
-- Table: tiki_related_categories
--
DROP TABLE IF EXISTS tiki_related_categories;
CREATE TABLE tiki_related_categories (
  categId integer(10) NOT NULL DEFAULT '0',
  relatedTo integer(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (categId, relatedTo)
);

--
-- Table: tiki_rss_modules
--
DROP TABLE IF EXISTS tiki_rss_modules;
CREATE TABLE tiki_rss_modules (
  rssId integer(8) NOT NULL auto_increment,
  name varchar(30) NOT NULL DEFAULT '',
  description text,
  url varchar(255) NOT NULL DEFAULT '',
  refresh integer(8) DEFAULT NULL,
  lastUpdated integer(14) DEFAULT NULL,
  content longblob,
  PRIMARY KEY (rssId)
);

--
-- Table: tiki_search_stats
--
DROP TABLE IF EXISTS tiki_search_stats;
CREATE TABLE tiki_search_stats (
  term varchar(50) NOT NULL DEFAULT '',
  hits integer(10) DEFAULT NULL,
  PRIMARY KEY (term)
);

--
-- Table: tiki_semaphores
--
DROP TABLE IF EXISTS tiki_semaphores;
CREATE TABLE tiki_semaphores (
  semName varchar(250) NOT NULL DEFAULT '',
  user varchar(200) DEFAULT NULL,
  timestamp integer(14) DEFAULT NULL,
  PRIMARY KEY (semName)
);

--
-- Table: tiki_sent_newsletters
--
DROP TABLE IF EXISTS tiki_sent_newsletters;
CREATE TABLE tiki_sent_newsletters (
  editionId integer(12) NOT NULL auto_increment,
  nlId integer(12) NOT NULL DEFAULT '0',
  users integer(10) DEFAULT NULL,
  sent integer(14) DEFAULT NULL,
  subject varchar(200) DEFAULT NULL,
  data longblob,
  PRIMARY KEY (editionId)
);

--
-- Table: tiki_sessions
--
DROP TABLE IF EXISTS tiki_sessions;
CREATE TABLE tiki_sessions (
  sessionId varchar(32) NOT NULL DEFAULT '',
  user varchar(200) DEFAULT NULL,
  timestamp integer(14) DEFAULT NULL,
  PRIMARY KEY (sessionId)
);

--
-- Table: tiki_shoutbox
--
DROP TABLE IF EXISTS tiki_shoutbox;
CREATE TABLE tiki_shoutbox (
  msgId integer(10) NOT NULL auto_increment,
  message varchar(255) DEFAULT NULL,
  timestamp integer(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  hash varchar(32) DEFAULT NULL,
  PRIMARY KEY (msgId)
);

--
-- Table: tiki_structures
--
DROP TABLE IF EXISTS tiki_structures;
CREATE TABLE tiki_structures (
  page varchar(240) NOT NULL DEFAULT '',
  parent varchar(240) NOT NULL DEFAULT '',
  pos integer(4) DEFAULT NULL,
  PRIMARY KEY (page, parent)
);

--
-- Table: tiki_submissions
--
DROP TABLE IF EXISTS tiki_submissions;
CREATE TABLE tiki_submissions (
  subId integer(8) NOT NULL auto_increment,
  title varchar(80) DEFAULT NULL,
  authorName varchar(60) DEFAULT NULL,
  topicId integer(14) DEFAULT NULL,
  topicName varchar(40) DEFAULT NULL,
  size integer(12) DEFAULT NULL,
  useImage char(1) DEFAULT NULL,
  image_name varchar(80) DEFAULT NULL,
  image_type varchar(80) DEFAULT NULL,
  image_size integer(14) DEFAULT NULL,
  image_x integer(4) DEFAULT NULL,
  image_y integer(4) DEFAULT NULL,
  image_data longblob,
  publishDate integer(14) DEFAULT NULL,
  created integer(14) DEFAULT NULL,
  heading text,
  body text,
  hash varchar(32) DEFAULT NULL,
  author varchar(200) DEFAULT NULL,
  reads integer(14) DEFAULT NULL,
  votes integer(8) DEFAULT NULL,
  points integer(14) DEFAULT NULL,
  type varchar(50) DEFAULT NULL,
  rating decimal(3, 2) DEFAULT NULL,
  isfloat char(1) DEFAULT NULL,
  PRIMARY KEY (subId)
);

--
-- Table: tiki_suggested_faq_questions
--
DROP TABLE IF EXISTS tiki_suggested_faq_questions;
CREATE TABLE tiki_suggested_faq_questions (
  sfqId integer(10) NOT NULL auto_increment,
  faqId integer(10) NOT NULL DEFAULT '0',
  question text,
  answer text,
  created integer(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  PRIMARY KEY (sfqId)
);

--
-- Table: tiki_survey_question_options
--
DROP TABLE IF EXISTS tiki_survey_question_options;
CREATE TABLE tiki_survey_question_options (
  optionId integer(12) NOT NULL auto_increment,
  questionId integer(12) NOT NULL DEFAULT '0',
  qoption text,
  votes integer(10) DEFAULT NULL,
  PRIMARY KEY (optionId)
);

--
-- Table: tiki_survey_questions
--
DROP TABLE IF EXISTS tiki_survey_questions;
CREATE TABLE tiki_survey_questions (
  questionId integer(12) NOT NULL auto_increment,
  surveyId integer(12) NOT NULL DEFAULT '0',
  question text,
  options text,
  type char(1) DEFAULT NULL,
  position integer(5) DEFAULT NULL,
  votes integer(10) DEFAULT NULL,
  value integer(10) DEFAULT NULL,
  average decimal(4, 2) DEFAULT NULL,
  PRIMARY KEY (questionId)
);

--
-- Table: tiki_surveys
--
DROP TABLE IF EXISTS tiki_surveys;
CREATE TABLE tiki_surveys (
  surveyId integer(12) NOT NULL auto_increment,
  name varchar(200) DEFAULT NULL,
  description text,
  taken integer(10) DEFAULT NULL,
  lastTaken integer(14) DEFAULT NULL,
  created integer(14) DEFAULT NULL,
  status char(1) DEFAULT NULL,
  PRIMARY KEY (surveyId)
);

--
-- Table: tiki_tags
--
DROP TABLE IF EXISTS tiki_tags;
CREATE TABLE tiki_tags (
  tagName varchar(80) NOT NULL DEFAULT '',
  pageName varchar(160) NOT NULL DEFAULT '',
  hits integer(8) DEFAULT NULL,
  description varchar(200) DEFAULT NULL,
  data longblob,
  lastModif integer(14) DEFAULT NULL,
  comment varchar(200) DEFAULT NULL,
  version integer(8) NOT NULL DEFAULT '0',
  user varchar(200) DEFAULT NULL,
  ip varchar(15) DEFAULT NULL,
  flag char(1) DEFAULT NULL,
  PRIMARY KEY (tagName, pageName)
);

--
-- Table: tiki_theme_control_categs
--
DROP TABLE IF EXISTS tiki_theme_control_categs;
CREATE TABLE tiki_theme_control_categs (
  categId integer(12) NOT NULL DEFAULT '0',
  theme varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (categId)
);

--
-- Table: tiki_theme_control_objects
--
DROP TABLE IF EXISTS tiki_theme_control_objects;
CREATE TABLE tiki_theme_control_objects (
  objId varchar(250) NOT NULL DEFAULT '',
  type varchar(250) NOT NULL DEFAULT '',
  name varchar(250) NOT NULL DEFAULT '',
  theme varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (objId)
);

--
-- Table: tiki_theme_control_sections
--
DROP TABLE IF EXISTS tiki_theme_control_sections;
CREATE TABLE tiki_theme_control_sections (
  section varchar(250) NOT NULL DEFAULT '',
  theme varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (section)
);

--
-- Table: tiki_topics
--
DROP TABLE IF EXISTS tiki_topics;
CREATE TABLE tiki_topics (
  topicId integer(14) NOT NULL auto_increment,
  name varchar(40) DEFAULT NULL,
  image_name varchar(80) DEFAULT NULL,
  image_type varchar(80) DEFAULT NULL,
  image_size integer(14) DEFAULT NULL,
  image_data longblob,
  active char(1) DEFAULT NULL,
  created integer(14) DEFAULT NULL,
  PRIMARY KEY (topicId)
);

--
-- Table: tiki_tracker_fields
--
DROP TABLE IF EXISTS tiki_tracker_fields;
CREATE TABLE tiki_tracker_fields (
  fieldId integer(12) NOT NULL auto_increment,
  trackerId integer(12) NOT NULL DEFAULT '0',
  name varchar(80) DEFAULT NULL,
  options text,
  type char(1) DEFAULT NULL,
  isMain char(1) DEFAULT NULL,
  isTblVisible char(1) DEFAULT NULL,
  PRIMARY KEY (fieldId)
);

--
-- Table: tiki_tracker_item_attachments
--
DROP TABLE IF EXISTS tiki_tracker_item_attachments;
CREATE TABLE tiki_tracker_item_attachments (
  attId integer(12) NOT NULL auto_increment,
  itemId varchar(40) NOT NULL DEFAULT '',
  filename varchar(80) DEFAULT NULL,
  filetype varchar(80) DEFAULT NULL,
  filesize integer(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  data longblob,
  path varchar(255) DEFAULT NULL,
  downloads integer(10) DEFAULT NULL,
  created integer(14) DEFAULT NULL,
  comment varchar(250) DEFAULT NULL,
  PRIMARY KEY (attId)
);

--
-- Table: tiki_tracker_item_comments
--
DROP TABLE IF EXISTS tiki_tracker_item_comments;
CREATE TABLE tiki_tracker_item_comments (
  commentId integer(12) NOT NULL auto_increment,
  itemId integer(12) NOT NULL DEFAULT '0',
  user varchar(200) DEFAULT NULL,
  data text,
  title varchar(200) DEFAULT NULL,
  posted integer(14) DEFAULT NULL,
  PRIMARY KEY (commentId)
);

--
-- Table: tiki_tracker_item_fields
--
DROP TABLE IF EXISTS tiki_tracker_item_fields;
CREATE TABLE tiki_tracker_item_fields (
  itemId integer(12) NOT NULL DEFAULT '0',
  fieldId integer(12) NOT NULL DEFAULT '0',
  value text,
  PRIMARY KEY (itemId, fieldId)
);

--
-- Table: tiki_tracker_items
--
DROP TABLE IF EXISTS tiki_tracker_items;
CREATE TABLE tiki_tracker_items (
  itemId integer(12) NOT NULL auto_increment,
  trackerId integer(12) NOT NULL DEFAULT '0',
  created integer(14) DEFAULT NULL,
  status char(1) DEFAULT NULL,
  lastModif integer(14) DEFAULT NULL,
  PRIMARY KEY (itemId)
);

--
-- Table: tiki_trackers
--
DROP TABLE IF EXISTS tiki_trackers;
CREATE TABLE tiki_trackers (
  trackerId integer(12) NOT NULL auto_increment,
  name varchar(80) DEFAULT NULL,
  description text,
  created integer(14) DEFAULT NULL,
  lastModif integer(14) DEFAULT NULL,
  showCreated char(1) DEFAULT NULL,
  showStatus char(1) DEFAULT NULL,
  showLastModif char(1) DEFAULT NULL,
  useComments char(1) DEFAULT NULL,
  useAttachments char(1) DEFAULT NULL,
  items integer(10) DEFAULT NULL,
  PRIMARY KEY (trackerId)
);

--
-- Table: tiki_untranslated
--
DROP TABLE IF EXISTS tiki_untranslated;
CREATE TABLE tiki_untranslated (
  id integer(14) NOT NULL auto_increment,
  source tinyblob NOT NULL,
  lang char(2) NOT NULL DEFAULT '',
  INDEX id_2 (id),
  PRIMARY KEY (source(255), lang),
  UNIQUE (id)
);

--
-- Table: tiki_user_answers
--
DROP TABLE IF EXISTS tiki_user_answers;
CREATE TABLE tiki_user_answers (
  userResultId integer(10) NOT NULL DEFAULT '0',
  quizId integer(10) NOT NULL DEFAULT '0',
  questionId integer(10) NOT NULL DEFAULT '0',
  optionId integer(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (userResultId, quizId, questionId, optionId)
);

--
-- Table: tiki_user_assigned_modules
--
DROP TABLE IF EXISTS tiki_user_assigned_modules;
CREATE TABLE tiki_user_assigned_modules (
  name varchar(200) NOT NULL DEFAULT '',
  position char(1) DEFAULT NULL,
  ord integer(4) DEFAULT NULL,
  type char(1) DEFAULT NULL,
  title varchar(40) DEFAULT NULL,
  cache_time integer(14) DEFAULT NULL,
  rows integer(4) DEFAULT NULL,
  groups text,
  params varchar(250) DEFAULT NULL,
  user varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (name, user)
);

--
-- Table: tiki_user_bookmarks_folders
--
DROP TABLE IF EXISTS tiki_user_bookmarks_folders;
CREATE TABLE tiki_user_bookmarks_folders (
  folderId integer(12) NOT NULL auto_increment,
  parentId integer(12) DEFAULT NULL,
  user varchar(200) NOT NULL DEFAULT '',
  name varchar(30) DEFAULT NULL,
  PRIMARY KEY (user, folderId)
);

--
-- Table: tiki_user_bookmarks_urls
--
DROP TABLE IF EXISTS tiki_user_bookmarks_urls;
CREATE TABLE tiki_user_bookmarks_urls (
  urlId integer(12) NOT NULL auto_increment,
  name varchar(30) DEFAULT NULL,
  url varchar(250) DEFAULT NULL,
  data longblob,
  lastUpdated integer(14) DEFAULT NULL,
  folderId integer(12) NOT NULL DEFAULT '0',
  user varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (urlId)
);

--
-- Table: tiki_user_mail_accounts
--
DROP TABLE IF EXISTS tiki_user_mail_accounts;
CREATE TABLE tiki_user_mail_accounts (
  accountId integer(12) NOT NULL auto_increment,
  user varchar(200) NOT NULL DEFAULT '',
  account varchar(50) NOT NULL DEFAULT '',
  pop varchar(255) DEFAULT NULL,
  current char(1) DEFAULT NULL,
  port integer(4) DEFAULT NULL,
  username varchar(100) DEFAULT NULL,
  pass varchar(100) DEFAULT NULL,
  msgs integer(4) DEFAULT NULL,
  smtp varchar(255) DEFAULT NULL,
  useAuth char(1) DEFAULT NULL,
  smtpPort integer(4) DEFAULT NULL,
  PRIMARY KEY (accountId)
);

--
-- Table: tiki_user_menus
--
DROP TABLE IF EXISTS tiki_user_menus;
CREATE TABLE tiki_user_menus (
  user varchar(200) NOT NULL DEFAULT '',
  menuId integer(12) NOT NULL auto_increment,
  url varchar(250) DEFAULT NULL,
  name varchar(40) DEFAULT NULL,
  position integer(4) DEFAULT NULL,
  mode char(1) DEFAULT NULL,
  PRIMARY KEY (menuId)
);

--
-- Table: tiki_user_modules
--
DROP TABLE IF EXISTS tiki_user_modules;
CREATE TABLE tiki_user_modules (
  name varchar(200) NOT NULL DEFAULT '',
  title varchar(40) DEFAULT NULL,
  data longblob,
  PRIMARY KEY (name)
);

--
-- Table: tiki_user_notes
--
DROP TABLE IF EXISTS tiki_user_notes;
CREATE TABLE tiki_user_notes (
  user varchar(200) NOT NULL DEFAULT '',
  noteId integer(12) NOT NULL auto_increment,
  created integer(14) DEFAULT NULL,
  name varchar(255) DEFAULT NULL,
  lastModif integer(14) DEFAULT NULL,
  data text,
  size integer(14) DEFAULT NULL,
  parse_mode varchar(20) DEFAULT NULL,
  PRIMARY KEY (noteId)
);

--
-- Table: tiki_user_postings
--
DROP TABLE IF EXISTS tiki_user_postings;
CREATE TABLE tiki_user_postings (
  user varchar(200) NOT NULL DEFAULT '',
  posts integer(12) DEFAULT NULL,
  last integer(14) DEFAULT NULL,
  first integer(14) DEFAULT NULL,
  level integer(8) DEFAULT NULL,
  PRIMARY KEY (user)
);

--
-- Table: tiki_user_preferences
--
DROP TABLE IF EXISTS tiki_user_preferences;
CREATE TABLE tiki_user_preferences (
  user varchar(200) NOT NULL DEFAULT '',
  prefName varchar(40) NOT NULL DEFAULT '',
  value varchar(250) DEFAULT NULL,
  PRIMARY KEY (user, prefName)
);

--
-- Table: tiki_user_quizzes
--
DROP TABLE IF EXISTS tiki_user_quizzes;
CREATE TABLE tiki_user_quizzes (
  user varchar(100) DEFAULT NULL,
  quizId integer(10) DEFAULT NULL,
  timestamp integer(14) DEFAULT NULL,
  timeTaken integer(14) DEFAULT NULL,
  points integer(12) DEFAULT NULL,
  maxPoints integer(12) DEFAULT NULL,
  resultId integer(10) DEFAULT NULL,
  userResultId integer(10) NOT NULL auto_increment,
  PRIMARY KEY (userResultId)
);

--
-- Table: tiki_user_taken_quizzes
--
DROP TABLE IF EXISTS tiki_user_taken_quizzes;
CREATE TABLE tiki_user_taken_quizzes (
  user varchar(200) NOT NULL DEFAULT '',
  quizId varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (user, quizId)
);

--
-- Table: tiki_user_tasks
--
DROP TABLE IF EXISTS tiki_user_tasks;
CREATE TABLE tiki_user_tasks (
  user varchar(200) DEFAULT NULL,
  taskId integer(14) NOT NULL auto_increment,
  title varchar(250) DEFAULT NULL,
  description text,
  date integer(14) DEFAULT NULL,
  status char(1) DEFAULT NULL,
  priority integer(2) DEFAULT NULL,
  completed integer(14) DEFAULT NULL,
  percentage integer(4) DEFAULT NULL,
  PRIMARY KEY (taskId)
);

--
-- Table: tiki_user_votings
--
DROP TABLE IF EXISTS tiki_user_votings;
CREATE TABLE tiki_user_votings (
  user varchar(200) NOT NULL DEFAULT '',
  id varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (user, id)
);

--
-- Table: tiki_user_watches
--
DROP TABLE IF EXISTS tiki_user_watches;
CREATE TABLE tiki_user_watches (
  user varchar(200) NOT NULL DEFAULT '',
  event varchar(40) NOT NULL DEFAULT '',
  object varchar(200) NOT NULL DEFAULT '',
  hash varchar(32) DEFAULT NULL,
  title varchar(250) DEFAULT NULL,
  type varchar(200) DEFAULT NULL,
  url varchar(250) DEFAULT NULL,
  email varchar(200) DEFAULT NULL,
  PRIMARY KEY (user, event, object)
);

--
-- Table: tiki_userfiles
--
DROP TABLE IF EXISTS tiki_userfiles;
CREATE TABLE tiki_userfiles (
  user varchar(200) NOT NULL DEFAULT '',
  fileId integer(12) NOT NULL auto_increment,
  name varchar(200) DEFAULT NULL,
  filename varchar(200) DEFAULT NULL,
  filetype varchar(200) DEFAULT NULL,
  filesize varchar(200) DEFAULT NULL,
  data longblob,
  hits integer(8) DEFAULT NULL,
  isFile char(1) DEFAULT NULL,
  path varchar(255) DEFAULT NULL,
  created integer(14) DEFAULT NULL,
  PRIMARY KEY (fileId)
);

--
-- Table: tiki_userpoints
--
DROP TABLE IF EXISTS tiki_userpoints;
CREATE TABLE tiki_userpoints (
  user varchar(200) DEFAULT NULL,
  points decimal(8, 2) DEFAULT NULL,
  voted integer(8) DEFAULT NULL
);

--
-- Table: tiki_users
--
DROP TABLE IF EXISTS tiki_users;
CREATE TABLE tiki_users (
  user varchar(200) NOT NULL DEFAULT '',
  password varchar(40) DEFAULT NULL,
  email varchar(200) DEFAULT NULL,
  lastLogin integer(14) DEFAULT NULL,
  PRIMARY KEY (user)
);

--
-- Table: tiki_webmail_contacts
--
DROP TABLE IF EXISTS tiki_webmail_contacts;
CREATE TABLE tiki_webmail_contacts (
  contactId integer(12) NOT NULL auto_increment,
  firstName varchar(80) DEFAULT NULL,
  lastName varchar(80) DEFAULT NULL,
  email varchar(250) DEFAULT NULL,
  nickname varchar(200) DEFAULT NULL,
  user varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (contactId)
);

--
-- Table: tiki_webmail_messages
--
DROP TABLE IF EXISTS tiki_webmail_messages;
CREATE TABLE tiki_webmail_messages (
  accountId integer(12) NOT NULL DEFAULT '0',
  mailId varchar(255) NOT NULL DEFAULT '',
  user varchar(200) NOT NULL DEFAULT '',
  isRead char(1) DEFAULT NULL,
  isReplied char(1) DEFAULT NULL,
  isFlagged char(1) DEFAULT NULL,
  PRIMARY KEY (accountId, mailId)
);

--
-- Table: tiki_wiki_attachments
--
DROP TABLE IF EXISTS tiki_wiki_attachments;
CREATE TABLE tiki_wiki_attachments (
  attId integer(12) NOT NULL auto_increment,
  page varchar(200) NOT NULL DEFAULT '',
  filename varchar(80) DEFAULT NULL,
  filetype varchar(80) DEFAULT NULL,
  filesize integer(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  data longblob,
  path varchar(255) DEFAULT NULL,
  downloads integer(10) DEFAULT NULL,
  created integer(14) DEFAULT NULL,
  comment varchar(250) DEFAULT NULL,
  PRIMARY KEY (attId)
);

--
-- Table: tiki_zones
--
DROP TABLE IF EXISTS tiki_zones;
CREATE TABLE tiki_zones (
  zone varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (zone)
);

--
-- Table: users_grouppermissions
--
DROP TABLE IF EXISTS users_grouppermissions;
CREATE TABLE users_grouppermissions (
  groupName varchar(30) NOT NULL DEFAULT '',
  permName varchar(30) NOT NULL DEFAULT '',
  value char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (groupName, permName)
);

--
-- Table: users_groups
--
DROP TABLE IF EXISTS users_groups;
CREATE TABLE users_groups (
  groupName varchar(30) NOT NULL DEFAULT '',
  groupDesc varchar(255) DEFAULT NULL,
  PRIMARY KEY (groupName)
);

--
-- Table: users_objectpermissions
--
DROP TABLE IF EXISTS users_objectpermissions;
CREATE TABLE users_objectpermissions (
  groupName varchar(30) NOT NULL DEFAULT '',
  permName varchar(30) NOT NULL DEFAULT '',
  objectType varchar(20) NOT NULL DEFAULT '',
  objectId varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (objectId, groupName, permName)
);

--
-- Table: users_permissions
--
DROP TABLE IF EXISTS users_permissions;
CREATE TABLE users_permissions (
  permName varchar(30) NOT NULL DEFAULT '',
  permDesc varchar(250) DEFAULT NULL,
  level varchar(80) DEFAULT NULL,
  type varchar(20) DEFAULT NULL,
  PRIMARY KEY (permName)
);

--
-- Table: users_usergroups
--
DROP TABLE IF EXISTS users_usergroups;
CREATE TABLE users_usergroups (
  userId integer(8) NOT NULL DEFAULT '0',
  groupName varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (userId, groupName)
);

--
-- Table: users_users
--
DROP TABLE IF EXISTS users_users;
CREATE TABLE users_users (
  userId integer(8) NOT NULL auto_increment,
  email varchar(200) DEFAULT NULL,
  login varchar(40) NOT NULL DEFAULT '',
  password varchar(30) NOT NULL DEFAULT '',
  provpass varchar(30) DEFAULT NULL,
  realname varchar(80) DEFAULT NULL,
  homePage varchar(200) DEFAULT NULL,
  lastLogin integer(14) DEFAULT NULL,
  currentLogin integer(14) DEFAULT NULL,
  registrationDate integer(14) DEFAULT NULL,
  challenge varchar(32) DEFAULT NULL,
  pass_due integer(14) DEFAULT NULL,
  hash varchar(32) DEFAULT NULL,
  created integer(14) DEFAULT NULL,
  country varchar(80) DEFAULT NULL,
  avatarName varchar(80) DEFAULT NULL,
  avatarSize integer(14) DEFAULT NULL,
  avatarFileType varchar(250) DEFAULT NULL,
  avatarData longblob,
  avatarLibName varchar(200) DEFAULT NULL,
  avatarType char(1) DEFAULT NULL,
  PRIMARY KEY (userId)
);

--
-- Table: tiki_download
--
DROP TABLE IF EXISTS tiki_download;
CREATE TABLE tiki_download (
  id int(11) NOT NULL auto_increment,
  object varchar(255) NOT NULL default '',
  userId int(8) NOT NULL default '0',
  type varchar(20) NOT NULL default '',
  date int(14) NOT NULL default '0',
  IP varchar(50) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY object (object,userId,type),
  KEY userId (userId),
  KEY type (type),
  KEY date (date)
);
