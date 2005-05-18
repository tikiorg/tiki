-- 
-- Created by SQL::Translator::Producer::SQLite
-- Created on Sun Aug 17 01:02:33 2003
-- 
--
-- Table: galaxia_activities
--
DROP TABLE galaxia_activities;
CREATE TABLE galaxia_activities (
  activityId INTEGER PRIMARY KEY NOT NULL,
  name varchar(80) DEFAULT NULL,
  normalized_name varchar(80) DEFAULT NULL,
  pId int(14) NOT NULL DEFAULT '0',
  type enum(10) DEFAULT NULL,
  isAutoRouted char(1) DEFAULT NULL,
  flowNum int(10) DEFAULT NULL,
  isInteractive char(1) DEFAULT NULL,
  lastModif int(14) DEFAULT NULL,
  description text
);

--
-- Table: galaxia_activity_roles
--
DROP TABLE galaxia_activity_roles;
CREATE TABLE galaxia_activity_roles (
  activityId int(14) NOT NULL DEFAULT '0',
  roleId int(14) NOT NULL DEFAULT '0'
);

--
-- Table: galaxia_instance_activities
--
DROP TABLE galaxia_instance_activities;
CREATE TABLE galaxia_instance_activities (
  instanceId int(14) NOT NULL DEFAULT '0',
  activityId int(14) NOT NULL DEFAULT '0',
  started int(14) NOT NULL DEFAULT '0',
  ended int(14) NOT NULL DEFAULT '0',
  user varchar(200) DEFAULT NULL,
  status enum(9) DEFAULT NULL
);

--
-- Table: galaxia_instance_comments
--
DROP TABLE galaxia_instance_comments;
CREATE TABLE galaxia_instance_comments (
  cId INTEGER PRIMARY KEY NOT NULL,
  instanceId int(14) NOT NULL DEFAULT '0',
  user varchar(200) DEFAULT NULL,
  activityId int(14) DEFAULT NULL,
  hash varchar(32) DEFAULT NULL,
  title varchar(250) DEFAULT NULL,
  comment text,
  activity varchar(80) DEFAULT NULL,
  timestamp int(14) DEFAULT NULL
);

--
-- Table: galaxia_instances
--
DROP TABLE galaxia_instances;
CREATE TABLE galaxia_instances (
  instanceId INTEGER PRIMARY KEY NOT NULL,
  pId int(14) NOT NULL DEFAULT '0',
  started int(14) DEFAULT NULL,
  owner varchar(200) DEFAULT NULL,
  nextActivity int(14) DEFAULT NULL,
  nextUser varchar(200) DEFAULT NULL,
  ended int(14) DEFAULT NULL,
  status enum(9) DEFAULT NULL,
  properties longblob
);

--
-- Table: galaxia_processes
--
DROP TABLE galaxia_processes;
CREATE TABLE galaxia_processes (
  pId INTEGER PRIMARY KEY NOT NULL,
  name varchar(80) DEFAULT NULL,
  isValid char(1) DEFAULT NULL,
  isActive char(1) DEFAULT NULL,
  version varchar(12) DEFAULT NULL,
  description text,
  lastModif int(14) DEFAULT NULL,
  normalized_name varchar(80) DEFAULT NULL
);

--
-- Table: galaxia_roles
--
DROP TABLE galaxia_roles;
CREATE TABLE galaxia_roles (
  roleId INTEGER PRIMARY KEY NOT NULL,
  pId int(14) NOT NULL DEFAULT '0',
  lastModif int(14) DEFAULT NULL,
  name varchar(80) DEFAULT NULL,
  description text
);

--
-- Table: galaxia_transitions
--
DROP TABLE galaxia_transitions;
CREATE TABLE galaxia_transitions (
  pId int(14) NOT NULL DEFAULT '0',
  actFromId int(14) NOT NULL DEFAULT '0',
  actToId int(14) NOT NULL DEFAULT '0'
);

--
-- Table: galaxia_user_roles
--
DROP TABLE galaxia_user_roles;
CREATE TABLE galaxia_user_roles (
  pId int(14) NOT NULL DEFAULT '0',
  roleId INTEGER PRIMARY KEY NOT NULL,
  user varchar(200) NOT NULL DEFAULT ''
);

--
-- Table: galaxia_workitems
--
DROP TABLE galaxia_workitems;
CREATE TABLE galaxia_workitems (
  itemId INTEGER PRIMARY KEY NOT NULL,
  instanceId int(14) NOT NULL DEFAULT '0',
  orderId int(14) NOT NULL DEFAULT '0',
  activityId int(14) NOT NULL DEFAULT '0',
  properties longblob,
  started int(14) DEFAULT NULL,
  ended int(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL
);

--
-- Table: messu_messages
--
DROP TABLE messu_messages;
CREATE TABLE messu_messages (
  msgId INTEGER PRIMARY KEY NOT NULL,
  user varchar(200) NOT NULL DEFAULT '',
  user_from varchar(200) NOT NULL DEFAULT '',
  user_to text,
  user_cc text,
  user_bcc text,
  subject varchar(255) DEFAULT NULL,
  body text,
  hash varchar(32) DEFAULT NULL,
  date int(14) DEFAULT NULL,
  isRead char(1) DEFAULT NULL,
  isReplied char(1) DEFAULT NULL,
  isFlagged char(1) DEFAULT NULL,
  priority int(2) DEFAULT NULL
);

--
-- Table: tiki_actionlog
--
DROP TABLE tiki_actionlog;
CREATE TABLE tiki_actionlog (
  action varchar(255) NOT NULL DEFAULT '',
  lastModif int(14) DEFAULT NULL,
  pageName varchar(200) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  ip varchar(15) DEFAULT NULL,
  comment varchar(200) DEFAULT NULL
);

--
-- Table: tiki_articles
--
DROP TABLE tiki_articles;
CREATE TABLE tiki_articles (
  articleId INTEGER PRIMARY KEY NOT NULL,
  title varchar(80) DEFAULT NULL,
  authorName varchar(60) DEFAULT NULL,
  topicId int(14) DEFAULT NULL,
  topicName varchar(40) DEFAULT NULL,
  size int(12) DEFAULT NULL,
  useImage char(1) DEFAULT NULL,
  image_name varchar(80) DEFAULT NULL,
  image_type varchar(80) DEFAULT NULL,
  image_size int(14) DEFAULT NULL,
  image_x int(4) DEFAULT NULL,
  image_y int(4) DEFAULT NULL,
  image_data longblob,
  publishDate int(14) DEFAULT NULL,
  created int(14) DEFAULT NULL,
  heading text,
  body text,
  hash varchar(32) DEFAULT NULL,
  author varchar(200) DEFAULT NULL,
  reads int(14) DEFAULT NULL,
  votes int(8) DEFAULT NULL,
  points int(14) DEFAULT NULL,
  type varchar(50) DEFAULT NULL,
  rating decimal(3,2) DEFAULT NULL,
  isfloat char(1) DEFAULT NULL
);
CREATE INDEX title_tiki_articles on tiki_articles (title);
CREATE INDEX heading_tiki_articles on tiki_articles (heading);
CREATE INDEX body_tiki_articles on tiki_articles (body);
CREATE INDEX reads_tiki_articles on tiki_articles (reads);
CREATE INDEX ft_tiki_articles on tiki_articles (title, heading, body);

--
-- Table: tiki_banners
--
DROP TABLE tiki_banners;
CREATE TABLE tiki_banners (
  bannerId INTEGER PRIMARY KEY NOT NULL,
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
  fromDate int(14) DEFAULT NULL,
  toDate int(14) DEFAULT NULL,
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
  created int(14) DEFAULT NULL,
  maxImpressions int(8) DEFAULT NULL,
  impressions int(8) DEFAULT NULL,
  clicks int(8) DEFAULT NULL,
  zone varchar(40) DEFAULT NULL
);

--
-- Table: tiki_banning
--
DROP TABLE tiki_banning;
CREATE TABLE tiki_banning (
  banId INTEGER PRIMARY KEY NOT NULL,
  mode enum(4) DEFAULT NULL,
  title varchar(200) DEFAULT NULL,
  ip1 char(3) DEFAULT NULL,
  ip2 char(3) DEFAULT NULL,
  ip3 char(3) DEFAULT NULL,
  ip4 char(3) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  date_from timestamp(14) NOT NULL,
  date_to timestamp(14) NOT NULL,
  use_dates char(1) DEFAULT NULL,
  created int(14) DEFAULT NULL,
  message text
);

--
-- Table: tiki_banning_sections
--
DROP TABLE tiki_banning_sections;
CREATE TABLE tiki_banning_sections (
  banId int(12) NOT NULL DEFAULT '0',
  section varchar(100) NOT NULL DEFAULT ''
);

--
-- Table: tiki_blog_activity
--
DROP TABLE tiki_blog_activity;
CREATE TABLE tiki_blog_activity (
  blogId int(8) NOT NULL DEFAULT '0',
  day int(14) NOT NULL DEFAULT '0',
  posts int(8) DEFAULT NULL
);

--
-- Table: tiki_blog_posts
--
DROP TABLE tiki_blog_posts;
CREATE TABLE tiki_blog_posts (
  postId INTEGER PRIMARY KEY NOT NULL,
  blogId int(8) NOT NULL DEFAULT '0',
  data text,
  created int(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  trackbacks_to text,
  trackbacks_from text,
  title varchar(80) DEFAULT NULL
);
CREATE INDEX data_tiki_blog_posts on tiki_blog_posts (data);
CREATE INDEX blogId_tiki_blog_posts on tiki_blog_posts (blogId);
CREATE INDEX created_tiki_blog_posts on tiki_blog_posts (created);
CREATE INDEX ft_tiki_blog_posts on tiki_blog_posts (data);

--
-- Table: tiki_blog_posts_images
--
DROP TABLE tiki_blog_posts_images;
CREATE TABLE tiki_blog_posts_images (
  imgId INTEGER PRIMARY KEY NOT NULL,
  postId int(14) NOT NULL DEFAULT '0',
  filename varchar(80) DEFAULT NULL,
  filetype varchar(80) DEFAULT NULL,
  filesize int(14) DEFAULT NULL,
  data longblob
);

--
-- Table: tiki_blogs
--
DROP TABLE tiki_blogs;
CREATE TABLE tiki_blogs (
  blogId INTEGER PRIMARY KEY NOT NULL,
  created int(14) DEFAULT NULL,
  lastModif int(14) DEFAULT NULL,
  title varchar(200) DEFAULT NULL,
  description text,
  user varchar(200) DEFAULT NULL,
  public char(1) DEFAULT NULL,
  posts int(8) DEFAULT NULL,
  maxPosts int(8) DEFAULT NULL,
  hits int(8) DEFAULT NULL,
  activity decimal(4,2) DEFAULT NULL,
  heading text,
  use_find char(1) DEFAULT NULL,
  use_title char(1) DEFAULT NULL,
  add_date char(1) DEFAULT NULL,
  add_poster char(1) DEFAULT NULL,
  allow_comments char(1) DEFAULT NULL
);
CREATE INDEX title_tiki_blogs on tiki_blogs (title);
CREATE INDEX description_tiki_blogs on tiki_blogs (description);
CREATE INDEX hits_tiki_blogs on tiki_blogs (hits);
CREATE INDEX ft_tiki_blogs on tiki_blogs (title, description);

--
-- Table: tiki_calendar_categories
--
DROP TABLE tiki_calendar_categories;
CREATE TABLE tiki_calendar_categories (
  calcatId INTEGER PRIMARY KEY NOT NULL,
  calendarId int(14) NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL DEFAULT ''
);
CREATE UNIQUE INDEX catname_tiki_calendar_categori on tiki_calendar_categories (calendarId, name(16));

--
-- Table: tiki_calendar_items
--
DROP TABLE tiki_calendar_items;
CREATE TABLE tiki_calendar_items (
  calitemId INTEGER PRIMARY KEY NOT NULL,
  calendarId int(14) NOT NULL DEFAULT '0',
  start int(14) NOT NULL DEFAULT '0',
  end int(14) NOT NULL DEFAULT '0',
  locationId int(14) DEFAULT NULL,
  categoryId int(14) DEFAULT NULL,
  priority enum(1) NOT NULL DEFAULT '1',
  status enum(1) NOT NULL DEFAULT '0',
  url varchar(255) DEFAULT NULL,
  lang char(2) NOT NULL DEFAULT 'en',
  name varchar(255) NOT NULL DEFAULT '',
  description blob,
  user varchar(40) DEFAULT NULL,
  created int(14) NOT NULL DEFAULT '0',
  lastmodif int(14) NOT NULL DEFAULT '0'
);
CREATE INDEX calendarId_tiki_calendar_items on tiki_calendar_items (calendarId);

--
-- Table: tiki_calendar_locations
--
DROP TABLE tiki_calendar_locations;
CREATE TABLE tiki_calendar_locations (
  callocId INTEGER PRIMARY KEY NOT NULL,
  calendarId int(14) NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL DEFAULT '',
  description blob
);
CREATE UNIQUE INDEX locname_tiki_calendar_location on tiki_calendar_locations (calendarId, name(16));

--
-- Table: tiki_calendar_roles
--
DROP TABLE tiki_calendar_roles;
CREATE TABLE tiki_calendar_roles (
  calitemId int(14) NOT NULL DEFAULT '0',
  username varchar(40) NOT NULL DEFAULT '',
  role enum(1) NOT NULL DEFAULT '0'
);

--
-- Table: tiki_calendars
--
DROP TABLE tiki_calendars;
CREATE TABLE tiki_calendars (
  calendarId INTEGER PRIMARY KEY NOT NULL,
  name varchar(80) NOT NULL DEFAULT '',
  description varchar(255) DEFAULT NULL,
  user varchar(40) NOT NULL DEFAULT '',
  customlocations enum(1) NOT NULL DEFAULT 'n',
  customcategories enum(1) NOT NULL DEFAULT 'n',
  customlanguages enum(1) NOT NULL DEFAULT 'n',
  custompriorities enum(1) NOT NULL DEFAULT 'n',
  customparticipants enum(1) NOT NULL DEFAULT 'n',
  created int(14) NOT NULL DEFAULT '0',
  lastmodif int(14) NOT NULL DEFAULT '0'
);

--
-- Table: tiki_categories
--
DROP TABLE tiki_categories;
CREATE TABLE tiki_categories (
  categId INTEGER PRIMARY KEY NOT NULL,
  name varchar(100) DEFAULT NULL,
  description varchar(250) DEFAULT NULL,
  parentId int(12) DEFAULT NULL,
  hits int(8) DEFAULT NULL
);

--
-- Table: tiki_categorized_objects
--
DROP TABLE tiki_categorized_objects;
CREATE TABLE tiki_categorized_objects (
  catObjectId INTEGER PRIMARY KEY NOT NULL,
  type varchar(50) DEFAULT NULL,
  objId varchar(255) DEFAULT NULL,
  description text,
  created int(14) DEFAULT NULL,
  name varchar(200) DEFAULT NULL,
  href varchar(200) DEFAULT NULL,
  hits int(8) DEFAULT NULL
);

--
-- Table: tiki_category_objects
--
DROP TABLE tiki_category_objects;
CREATE TABLE tiki_category_objects (
  catObjectId int(12) NOT NULL DEFAULT '0',
  categId int(12) NOT NULL DEFAULT '0'
);

--
-- Table: tiki_category_sites
--
DROP TABLE tiki_category_sites;
CREATE TABLE tiki_category_sites (
  categId int(10) NOT NULL DEFAULT '0',
  siteId int(14) NOT NULL DEFAULT '0'
);

--
-- Table: tiki_chart_items
--
DROP TABLE tiki_chart_items;
CREATE TABLE tiki_chart_items (
  itemId INTEGER PRIMARY KEY NOT NULL,
  title varchar(250) DEFAULT NULL,
  description text,
  chartId int(14) NOT NULL DEFAULT '0',
  created int(14) DEFAULT NULL,
  URL varchar(250) DEFAULT NULL,
  votes int(14) DEFAULT NULL,
  points int(14) DEFAULT NULL,
  average decimal(4,2) DEFAULT NULL
);

--
-- Table: tiki_charts
--
DROP TABLE tiki_charts;
CREATE TABLE tiki_charts (
  chartId INTEGER PRIMARY KEY NOT NULL,
  title varchar(250) DEFAULT NULL,
  description text,
  hits int(14) DEFAULT NULL,
  singleItemVotes char(1) DEFAULT NULL,
  singleChartVotes char(1) DEFAULT NULL,
  suggestions char(1) DEFAULT NULL,
  autoValidate char(1) DEFAULT NULL,
  topN int(6) DEFAULT NULL,
  maxVoteValue int(4) DEFAULT NULL,
  frequency int(14) DEFAULT NULL,
  showAverage char(1) DEFAULT NULL,
  isActive char(1) DEFAULT NULL,
  showVotes char(1) DEFAULT NULL,
  useCookies char(1) DEFAULT NULL,
  lastChart int(14) DEFAULT NULL,
  voteAgainAfter int(14) DEFAULT NULL,
  created int(14) DEFAULT NULL,
  hist int(12) DEFAULT NULL
);

--
-- Table: tiki_charts_rankings
--
DROP TABLE tiki_charts_rankings;
CREATE TABLE tiki_charts_rankings (
  chartId int(14) NOT NULL DEFAULT '0',
  itemId int(14) NOT NULL DEFAULT '0',
  position int(14) NOT NULL DEFAULT '0',
  timestamp int(14) NOT NULL DEFAULT '0',
  lastPosition int(14) NOT NULL DEFAULT '0',
  period int(14) NOT NULL DEFAULT '0',
  rvotes int(14) NOT NULL DEFAULT '0',
  raverage decimal(4,2) NOT NULL DEFAULT '0.00'
);

--
-- Table: tiki_charts_votes
--
DROP TABLE tiki_charts_votes;
CREATE TABLE tiki_charts_votes (
  user varchar(200) NOT NULL DEFAULT '',
  itemId int(14) NOT NULL DEFAULT '0',
  timestamp int(14) DEFAULT NULL,
  chartId int(14) DEFAULT NULL
);

--
-- Table: tiki_chat_channels
--
DROP TABLE tiki_chat_channels;
CREATE TABLE tiki_chat_channels (
  channelId INTEGER PRIMARY KEY NOT NULL,
  name varchar(30) DEFAULT NULL,
  description varchar(250) DEFAULT NULL,
  max_users int(8) DEFAULT NULL,
  mode char(1) DEFAULT NULL,
  moderator varchar(200) DEFAULT NULL,
  active char(1) DEFAULT NULL,
  refresh int(6) DEFAULT NULL
);

--
-- Table: tiki_chat_messages
--
DROP TABLE tiki_chat_messages;
CREATE TABLE tiki_chat_messages (
  messageId INTEGER PRIMARY KEY NOT NULL,
  channelId int(8) NOT NULL DEFAULT '0',
  data varchar(255) DEFAULT NULL,
  poster varchar(200) NOT NULL DEFAULT 'anonymous',
  timestamp int(14) DEFAULT NULL
);

--
-- Table: tiki_chat_users
--
DROP TABLE tiki_chat_users;
CREATE TABLE tiki_chat_users (
  nickname varchar(200) NOT NULL DEFAULT '',
  channelId int(8) NOT NULL DEFAULT '0',
  timestamp int(14) DEFAULT NULL
);

--
-- Table: tiki_comments
--
DROP TABLE tiki_comments;
CREATE TABLE tiki_comments (
  threadId INTEGER PRIMARY KEY NOT NULL,
  object varchar(32) NOT NULL DEFAULT '',
  parentId int(14) DEFAULT NULL,
  userName varchar(200) DEFAULT NULL,
  commentDate int(14) DEFAULT NULL,
  hits int(8) DEFAULT NULL,
  type char(1) DEFAULT NULL,
  points decimal(8,2) DEFAULT NULL,
  votes int(8) DEFAULT NULL,
  average decimal(8,4) DEFAULT NULL,
  title varchar(100) DEFAULT NULL,
  data text,
  hash varchar(32) DEFAULT NULL,
  user_ip varchar(15) DEFAULT NULL,
  summary varchar(240) DEFAULT NULL,
  message_id varchar(250) default NULL,
  in_reply_to varchar(250) default NULL,
  smiley varchar(80) DEFAULT NULL
);
CREATE INDEX title_tiki_comments on tiki_comments (title);
CREATE INDEX data_tiki_comments on tiki_comments (data);
CREATE INDEX object_tiki_comments on tiki_comments (object);
CREATE INDEX hits_tiki_comments on tiki_comments (hits);
CREATE INDEX tc_pi_tiki_comments on tiki_comments (parentId);
CREATE INDEX ft_tiki_comments on tiki_comments (title, data);

--
-- Table: tiki_content
--
DROP TABLE tiki_content;
CREATE TABLE tiki_content (
  contentId INTEGER PRIMARY KEY NOT NULL,
  description text
);

--
-- Table: tiki_content_templates
--
DROP TABLE tiki_content_templates;
CREATE TABLE tiki_content_templates (
  templateId INTEGER PRIMARY KEY NOT NULL,
  content longblob,
  name varchar(200) DEFAULT NULL,
  created int(14) DEFAULT NULL
);

--
-- Table: tiki_content_templates_sections
--
DROP TABLE tiki_content_templates_sections;
CREATE TABLE tiki_content_templates_sections (
  templateId int(10) NOT NULL DEFAULT '0',
  section varchar(250) NOT NULL DEFAULT ''
);

--
-- Table: tiki_cookies
--
DROP TABLE tiki_cookies;
CREATE TABLE tiki_cookies (
  cookieId INTEGER PRIMARY KEY NOT NULL,
  cookie varchar(255) DEFAULT NULL
);

--
-- Table: tiki_copyrights
--
DROP TABLE tiki_copyrights;
CREATE TABLE tiki_copyrights (
  copyrightId INTEGER PRIMARY KEY NOT NULL,
  page varchar(200) DEFAULT NULL,
  title varchar(200) DEFAULT NULL,
  year int(11) DEFAULT NULL,
  authors varchar(200) DEFAULT NULL,
  copyright_order int(11) DEFAULT NULL,
  userName varchar(200) DEFAULT NULL
);

--
-- Table: tiki_directory_categories
--
DROP TABLE tiki_directory_categories;
CREATE TABLE tiki_directory_categories (
  categId INTEGER PRIMARY KEY NOT NULL,
  parent int(10) DEFAULT NULL,
  name varchar(240) DEFAULT NULL,
  description text,
  childrenType char(1) DEFAULT NULL,
  sites int(10) DEFAULT NULL,
  viewableChildren int(4) DEFAULT NULL,
  allowSites char(1) DEFAULT NULL,
  showCount char(1) DEFAULT NULL,
  editorGroup varchar(200) DEFAULT NULL,
  hits int(12) DEFAULT NULL
);

--
-- Table: tiki_directory_search
--
DROP TABLE tiki_directory_search;
CREATE TABLE tiki_directory_search (
  term varchar(250) NOT NULL DEFAULT '',
  hits int(14) DEFAULT NULL
);

--
-- Table: tiki_directory_sites
--
DROP TABLE tiki_directory_sites;
CREATE TABLE tiki_directory_sites (
  siteId INTEGER PRIMARY KEY NOT NULL,
  name varchar(240) DEFAULT NULL,
  description text,
  url varchar(255) DEFAULT NULL,
  country varchar(255) DEFAULT NULL,
  hits int(12) DEFAULT NULL,
  isValid char(1) DEFAULT NULL,
  created int(14) DEFAULT NULL,
  lastModif int(14) DEFAULT NULL,
  cache longblob,
  cache_timestamp int(14) DEFAULT NULL
);
CREATE INDEX ft_tiki_directory_sites on tiki_directory_sites (name, description);

--
-- Table: tiki_drawings
--
DROP TABLE tiki_drawings;
CREATE TABLE tiki_drawings (
  drawId INTEGER PRIMARY KEY NOT NULL,
  version int(8) DEFAULT NULL,
  name varchar(250) DEFAULT NULL,
  filename_draw varchar(250) DEFAULT NULL,
  filename_pad varchar(250) DEFAULT NULL,
  timestamp int(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL
);

--
-- Table: tiki_dsn
--
DROP TABLE tiki_dsn;
CREATE TABLE tiki_dsn (
  dsnId INTEGER PRIMARY KEY NOT NULL,
  name varchar(200) NOT NULL DEFAULT '',
  dsn varchar(255) DEFAULT NULL
);

--
-- Table: tiki_eph
--
DROP TABLE tiki_eph;
CREATE TABLE tiki_eph (
  ephId INTEGER PRIMARY KEY NOT NULL,
  title varchar(250) DEFAULT NULL,
  isFile char(1) DEFAULT NULL,
  filename varchar(250) DEFAULT NULL,
  filetype varchar(250) DEFAULT NULL,
  filesize varchar(250) DEFAULT NULL,
  data longblob,
  textdata longblob,
  publish int(14) DEFAULT NULL,
  hits int(10) DEFAULT NULL
);

--
-- Table: tiki_extwiki
--
DROP TABLE tiki_extwiki;
CREATE TABLE tiki_extwiki (
  extwikiId INTEGER PRIMARY KEY NOT NULL,
  name varchar(200) NOT NULL DEFAULT '',
  extwiki varchar(255) DEFAULT NULL
);

--
-- Table: tiki_faq_questions
--
DROP TABLE tiki_faq_questions;
CREATE TABLE tiki_faq_questions (
  questionId INTEGER PRIMARY KEY NOT NULL,
  faqId int(10) DEFAULT NULL,
  position int(4) DEFAULT NULL,
  question text,
  answer text
);
CREATE INDEX faqId_tiki_faq_questions on tiki_faq_questions (faqId);
CREATE INDEX question_tiki_faq_questions on tiki_faq_questions (question);
CREATE INDEX answer_tiki_faq_questions on tiki_faq_questions (answer);
CREATE INDEX ft_tiki_faq_questions on tiki_faq_questions (question, answer);

--
-- Table: tiki_faqs
--
DROP TABLE tiki_faqs;
CREATE TABLE tiki_faqs (
  faqId INTEGER PRIMARY KEY NOT NULL,
  title varchar(200) DEFAULT NULL,
  description text,
  created int(14) DEFAULT NULL,
  questions int(5) DEFAULT NULL,
  hits int(8) DEFAULT NULL,
  canSuggest char(1) DEFAULT NULL
);
CREATE INDEX title_tiki_faqs on tiki_faqs (title);
CREATE INDEX description_tiki_faqs on tiki_faqs (description);
CREATE INDEX hits_tiki_faqs on tiki_faqs (hits);
CREATE INDEX ft_tiki_faqs on tiki_faqs (title, description);

--
-- Table: tiki_featured_links
--
DROP TABLE tiki_featured_links;
CREATE TABLE tiki_featured_links (
  url varchar(200) NOT NULL DEFAULT '',
  title varchar(200) DEFAULT NULL,
  description text,
  hits int(8) DEFAULT NULL,
  position int(6) DEFAULT NULL,
  type char(1) DEFAULT NULL
);

--
-- Table: tiki_file_galleries
--
DROP TABLE tiki_file_galleries;
CREATE TABLE tiki_file_galleries (
  galleryId INTEGER PRIMARY KEY NOT NULL,
  name varchar(80) NOT NULL DEFAULT '',
  description text,
  created int(14) DEFAULT NULL,
  visible char(1) DEFAULT NULL,
  lastModif int(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  hits int(14) DEFAULT NULL,
  votes int(8) DEFAULT NULL,
  points decimal(8,2) DEFAULT NULL,
  maxRows int(10) DEFAULT NULL,
  public char(1) DEFAULT NULL,
  show_id char(1) DEFAULT NULL,
  show_icon char(1) DEFAULT NULL,
  show_name char(1) DEFAULT NULL,
  show_size char(1) DEFAULT NULL,
  show_description char(1) DEFAULT NULL,
  max_desc int(8) DEFAULT NULL,
  show_created char(1) DEFAULT NULL,
  show_dl char(1) DEFAULT NULL
);

--
-- Table: tiki_files
--
DROP TABLE tiki_files;
CREATE TABLE tiki_files (
  fileId INTEGER PRIMARY KEY NOT NULL,
  galleryId int(14) NOT NULL DEFAULT '0',
  name varchar(200) NOT NULL DEFAULT '',
  description text,
  created int(14) DEFAULT NULL,
  filename varchar(80) DEFAULT NULL,
  filesize int(14) DEFAULT NULL,
  filetype varchar(250) DEFAULT NULL,
  data longblob,
  user varchar(200) DEFAULT NULL,
  downloads int(14) DEFAULT NULL,
  votes int(8) DEFAULT NULL,
  points decimal(8,2) DEFAULT NULL,
  path varchar(255) DEFAULT NULL,
  reference_url varchar(250) DEFAULT NULL,
  is_reference char(1) DEFAULT NULL,
  hash varchar(32) DEFAULT NULL
);
CREATE INDEX name_tiki_files on tiki_files (name);
CREATE INDEX description_tiki_files on tiki_files (description);
CREATE INDEX downloads_tiki_files on tiki_files (downloads);
CREATE INDEX ft_tiki_files on tiki_files (name, description);

--
-- Table: tiki_forum_attachments
--
DROP TABLE tiki_forum_attachments;
CREATE TABLE tiki_forum_attachments (
  attId INTEGER PRIMARY KEY NOT NULL,
  threadId int(14) NOT NULL DEFAULT '0',
  qId int(14) NOT NULL DEFAULT '0',
  forumId int(14) DEFAULT NULL,
  filename varchar(250) DEFAULT NULL,
  filetype varchar(250) DEFAULT NULL,
  filesize int(12) DEFAULT NULL,
  data longblob,
  dir varchar(200) DEFAULT NULL,
  created int(14) DEFAULT NULL,
  path varchar(250) DEFAULT NULL
);

--
-- Table: tiki_forum_reads
--
DROP TABLE tiki_forum_reads;
CREATE TABLE tiki_forum_reads (
  user varchar(200) NOT NULL DEFAULT '',
  threadId int(14) NOT NULL DEFAULT '0',
  forumId int(14) DEFAULT NULL,
  timestamp int(14) DEFAULT NULL
);

--
-- Table: tiki_forums
--
DROP TABLE tiki_forums;
CREATE TABLE tiki_forums (
  forumId INTEGER PRIMARY KEY NOT NULL,
  name varchar(200) DEFAULT NULL,
  description text,
  created int(14) DEFAULT NULL,
  lastPost int(14) DEFAULT NULL,
  threads int(8) DEFAULT NULL,
  comments int(8) DEFAULT NULL,
  controlFlood char(1) DEFAULT NULL,
  floodInterval int(8) DEFAULT NULL,
  moderator varchar(200) DEFAULT NULL,
  hits int(8) DEFAULT NULL,
  mail varchar(200) DEFAULT NULL,
  useMail char(1) DEFAULT NULL,
  section varchar(200) DEFAULT NULL,
  usePruneUnreplied char(1) DEFAULT NULL,
  pruneUnrepliedAge int(8) DEFAULT NULL,
  usePruneOld char(1) DEFAULT NULL,
  pruneMaxAge int(8) DEFAULT NULL,
  topicsPerPage int(6) DEFAULT NULL,
  topicOrdering varchar(100) DEFAULT NULL,
  threadOrdering varchar(100) DEFAULT NULL,
  att varchar(80) DEFAULT NULL,
  att_store varchar(4) DEFAULT NULL,
  att_store_dir varchar(250) DEFAULT NULL,
  att_max_size int(12) DEFAULT NULL,
  ui_level char(1) DEFAULT NULL,
  forum_password varchar(32) DEFAULT NULL,
  forum_use_password char(1) DEFAULT NULL,
  moderator_group varchar(200) DEFAULT NULL,
  outbound_from varchar(250) default NULL,
  approval_type varchar(20) DEFAULT NULL,
  outbound_address varchar(250) DEFAULT NULL,
  outbound_mails_for_inbound_mails char(1) DEFAULT NULL,
  outbound_mails_reply_link char(1) DEFAULT NULL,
  inbound_pop_server varchar(250) DEFAULT NULL,
  inbound_pop_port int(4) DEFAULT NULL,
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
  vote_threads char(1) DEFAULT NULL
);

--
-- Table: tiki_forums_queue
--
DROP TABLE tiki_forums_queue;
CREATE TABLE tiki_forums_queue (
  qId INTEGER PRIMARY KEY NOT NULL,
  object varchar(32) DEFAULT NULL,
  parentId int(14) DEFAULT NULL,
  forumId int(14) DEFAULT NULL,
  timestamp int(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  title varchar(240) DEFAULT NULL,
  data text,
  type varchar(60) DEFAULT NULL,
  hash varchar(32) DEFAULT NULL,
  topic_smiley varchar(80) DEFAULT NULL,
  topic_title varchar(240) DEFAULT NULL,
  summary varchar(240) DEFAULT NULL
);

--
-- Table: tiki_forums_reported
--
DROP TABLE tiki_forums_reported;
CREATE TABLE tiki_forums_reported (
  threadId int(12) NOT NULL DEFAULT '0',
  forumId int(12) NOT NULL DEFAULT '0',
  parentId int(12) NOT NULL DEFAULT '0',
  user varchar(200) DEFAULT NULL,
  timestamp int(14) DEFAULT NULL,
  reason varchar(250) DEFAULT NULL
);

--
-- Table: tiki_galleries
--
DROP TABLE tiki_galleries;
CREATE TABLE tiki_galleries (
  galleryId INTEGER PRIMARY KEY NOT NULL,
  name varchar(80) NOT NULL DEFAULT '',
  description text,
  created int(14) DEFAULT NULL,
  lastModif int(14) DEFAULT NULL,
  visible char(1) DEFAULT NULL,
  theme varchar(60) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  hits int(14) DEFAULT NULL,
  maxRows int(10) DEFAULT NULL,
  rowImages int(10) DEFAULT NULL,
  thumbSizeX int(10) DEFAULT NULL,
  thumbSizeY int(10) DEFAULT NULL,
  public char(1) DEFAULT NULL
);
CREATE INDEX name_tiki_galleries on tiki_galleries (name);
CREATE INDEX description_tiki_galleries on tiki_galleries (description);
CREATE INDEX hits_tiki_galleries on tiki_galleries (hits);
CREATE INDEX ft_tiki_galleries on tiki_galleries (name, description);

--
-- Table: tiki_galleries_scales
--
DROP TABLE tiki_galleries_scales;
CREATE TABLE tiki_galleries_scales (
  galleryId int(14) NOT NULL DEFAULT '0',
  xsize int(11) NOT NULL DEFAULT '0',
  ysize int(11) NOT NULL DEFAULT '0'
);

--
-- Table: tiki_games
--
DROP TABLE tiki_games;
CREATE TABLE tiki_games (
  gameName varchar(200) NOT NULL DEFAULT '',
  hits int(8) DEFAULT NULL,
  votes int(8) DEFAULT NULL,
  points int(8) DEFAULT NULL
);

--
-- Table: tiki_group_inclusion
--
DROP TABLE tiki_group_inclusion;
CREATE TABLE tiki_group_inclusion (
  groupName varchar(30) NOT NULL DEFAULT '',
  includeGroup varchar(30) NOT NULL DEFAULT ''
);

--
-- Table: tiki_history
--
DROP TABLE tiki_history;
CREATE TABLE tiki_history (
  pageName varchar(160) NOT NULL DEFAULT '',
  version int(8) NOT NULL DEFAULT '0',
  lastModif int(14) DEFAULT NULL,
  description varchar(200) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  ip varchar(15) DEFAULT NULL,
  comment varchar(200) DEFAULT NULL,
  data longblob
);

--
-- Table: tiki_hotwords
--
DROP TABLE tiki_hotwords;
CREATE TABLE tiki_hotwords (
  word varchar(40) NOT NULL DEFAULT '',
  url varchar(255) NOT NULL DEFAULT ''
);

--
-- Table: tiki_html_pages
--
DROP TABLE tiki_html_pages;
CREATE TABLE tiki_html_pages (
  pageName varchar(200) NOT NULL DEFAULT '',
  content longblob,
  refresh int(10) DEFAULT NULL,
  type char(1) DEFAULT NULL,
  created int(14) DEFAULT NULL
);

--
-- Table: tiki_html_pages_dynamic_zones
--
DROP TABLE tiki_html_pages_dynamic_zones;
CREATE TABLE tiki_html_pages_dynamic_zones (
  pageName varchar(40) NOT NULL DEFAULT '',
  zone varchar(80) NOT NULL DEFAULT '',
  type char(2) DEFAULT NULL,
  content text
);

--
-- Table: tiki_images
--
DROP TABLE tiki_images;
CREATE TABLE tiki_images (
  imageId INTEGER PRIMARY KEY NOT NULL,
  galleryId int(14) NOT NULL DEFAULT '0',
  name varchar(200) NOT NULL DEFAULT '',
  description text,
  created int(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  hits int(14) DEFAULT NULL,
  path varchar(255) DEFAULT NULL
);
CREATE INDEX name_tiki_images on tiki_images (name);
CREATE INDEX description_tiki_images on tiki_images (description);
CREATE INDEX hits_tiki_images on tiki_images (hits);
CREATE INDEX ti_gId_tiki_images on tiki_images (galleryId);
CREATE INDEX ti_cr_tiki_images on tiki_images (created);
CREATE INDEX ti_us_tiki_images on tiki_images (user);
CREATE INDEX ft_tiki_images on tiki_images (name, description);

--
-- Table: tiki_images_data
--
DROP TABLE tiki_images_data;
CREATE TABLE tiki_images_data (
  imageId int(14) NOT NULL DEFAULT '0',
  xsize int(8) NOT NULL DEFAULT '0',
  ysize int(8) NOT NULL DEFAULT '0',
  type char(1) NOT NULL DEFAULT '',
  filesize int(14) DEFAULT NULL,
  filetype varchar(80) DEFAULT NULL,
  filename varchar(80) DEFAULT NULL,
  data longblob
);
CREATE INDEX t_i_d_it_tiki_images_data on tiki_images_data (imageId, type);

--
-- Table: tiki_language
--
DROP TABLE tiki_language;
CREATE TABLE tiki_language (
  source tinyblob NOT NULL,
  lang char(2) NOT NULL DEFAULT '',
  tran tinyblob
);

--
-- Table: tiki_languages
--
DROP TABLE tiki_languages;
CREATE TABLE tiki_languages (
  lang char(2) NOT NULL DEFAULT '',
  language varchar(255) DEFAULT NULL
);

--
-- Table: tiki_link_cache
--
DROP TABLE tiki_link_cache;
CREATE TABLE tiki_link_cache (
  cacheId INTEGER PRIMARY KEY NOT NULL,
  url varchar(250) DEFAULT NULL,
  data longblob,
  refresh int(14) DEFAULT NULL
);

--
-- Table: tiki_links
--
DROP TABLE tiki_links;
CREATE TABLE tiki_links (
  fromPage varchar(160) NOT NULL DEFAULT '',
  toPage varchar(160) NOT NULL DEFAULT ''
);

--
-- Table: tiki_live_support_events
--
DROP TABLE tiki_live_support_events;
CREATE TABLE tiki_live_support_events (
  eventId INTEGER PRIMARY KEY NOT NULL,
  reqId varchar(32) NOT NULL DEFAULT '',
  type varchar(40) DEFAULT NULL,
  seqId int(14) DEFAULT NULL,
  senderId varchar(32) DEFAULT NULL,
  data text,
  timestamp int(14) DEFAULT NULL
);

--
-- Table: tiki_live_support_message_comments
--
DROP TABLE tiki_live_support_message_comments;
CREATE TABLE tiki_live_support_message_comments (
  cId INTEGER PRIMARY KEY NOT NULL,
  msgId int(12) DEFAULT NULL,
  data text,
  timestamp int(14) DEFAULT NULL
);

--
-- Table: tiki_live_support_messages
--
DROP TABLE tiki_live_support_messages;
CREATE TABLE tiki_live_support_messages (
  msgId INTEGER PRIMARY KEY NOT NULL,
  data text,
  timestamp int(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  username varchar(200) DEFAULT NULL,
  priority int(2) DEFAULT NULL,
  status char(1) DEFAULT NULL,
  assigned_to varchar(200) DEFAULT NULL,
  resolution varchar(100) DEFAULT NULL,
  title varchar(200) DEFAULT NULL,
  module int(4) DEFAULT NULL,
  email varchar(250) DEFAULT NULL
);

--
-- Table: tiki_live_support_modules
--
DROP TABLE tiki_live_support_modules;
CREATE TABLE tiki_live_support_modules (
  modId INTEGER PRIMARY KEY NOT NULL,
  name varchar(90) DEFAULT NULL
);

--
-- Table: tiki_live_support_operators
--
DROP TABLE tiki_live_support_operators;
CREATE TABLE tiki_live_support_operators (
  user varchar(200) NOT NULL DEFAULT '',
  accepted_requests int(10) DEFAULT NULL,
  status varchar(20) DEFAULT NULL,
  longest_chat int(10) DEFAULT NULL,
  shortest_chat int(10) DEFAULT NULL,
  average_chat int(10) DEFAULT NULL,
  last_chat int(14) DEFAULT NULL,
  time_online int(10) DEFAULT NULL,
  votes int(10) DEFAULT NULL,
  points int(10) DEFAULT NULL,
  status_since int(14) DEFAULT NULL
);

--
-- Table: tiki_live_support_requests
--
DROP TABLE tiki_live_support_requests;
CREATE TABLE tiki_live_support_requests (
  reqId varchar(32) NOT NULL DEFAULT '',
  user varchar(200) DEFAULT NULL,
  tiki_user varchar(200) DEFAULT NULL,
  email varchar(200) DEFAULT NULL,
  operator varchar(200) DEFAULT NULL,
  operator_id varchar(32) DEFAULT NULL,
  user_id varchar(32) DEFAULT NULL,
  reason text,
  req_timestamp int(14) DEFAULT NULL,
  timestamp int(14) DEFAULT NULL,
  status varchar(40) DEFAULT NULL,
  resolution varchar(40) DEFAULT NULL,
  chat_started int(14) DEFAULT NULL,
  chat_ended int(14) DEFAULT NULL
);

--
-- Table: tiki_mail_events
--
DROP TABLE tiki_mail_events;
CREATE TABLE tiki_mail_events (
  event varchar(200) DEFAULT NULL,
  object varchar(200) DEFAULT NULL,
  email varchar(200) DEFAULT NULL
);

--
-- Table: tiki_mailin_accounts
--
DROP TABLE tiki_mailin_accounts;
CREATE TABLE tiki_mailin_accounts (
  accountId INTEGER PRIMARY KEY NOT NULL,
  user varchar(200) NOT NULL DEFAULT '',
  account varchar(50) NOT NULL DEFAULT '',
  pop varchar(255) DEFAULT NULL,
  port int(4) DEFAULT NULL,
  username varchar(100) DEFAULT NULL,
  pass varchar(100) DEFAULT NULL,
  active char(1) DEFAULT NULL,
  type varchar(40) DEFAULT NULL,
  smtp varchar(255) DEFAULT NULL,
  useAuth char(1) DEFAULT NULL,
  smtpPort int(4) DEFAULT NULL
);

--
-- Table: tiki_menu_languages
--
DROP TABLE tiki_menu_languages;
CREATE TABLE tiki_menu_languages (
  menuId INTEGER PRIMARY KEY NOT NULL,
  language char(2) NOT NULL DEFAULT ''
);

--
-- Table: tiki_menu_options
--
DROP TABLE tiki_menu_options;
CREATE TABLE tiki_menu_options (
  optionId INTEGER PRIMARY KEY NOT NULL,
  menuId int(8) DEFAULT NULL,
  type char(1) DEFAULT NULL,
  name varchar(200) DEFAULT NULL,
  url varchar(255) DEFAULT NULL,
  position int(4) DEFAULT NULL
);

--
-- Table: tiki_menus
--
DROP TABLE tiki_menus;
CREATE TABLE tiki_menus (
  menuId INTEGER PRIMARY KEY NOT NULL,
  name varchar(200) NOT NULL DEFAULT '',
  description text,
  type char(1) DEFAULT NULL
);

--
-- Table: tiki_minical_events
--
DROP TABLE tiki_minical_events;
CREATE TABLE tiki_minical_events (
  user varchar(200) DEFAULT NULL,
  eventId INTEGER PRIMARY KEY NOT NULL,
  title varchar(250) DEFAULT NULL,
  description text,
  start int(14) DEFAULT NULL,
  end int(14) DEFAULT NULL,
  security char(1) DEFAULT NULL,
  duration int(3) DEFAULT NULL,
  topicId int(12) DEFAULT NULL,
  reminded char(1) DEFAULT NULL
);

--
-- Table: tiki_minical_topics
--
DROP TABLE tiki_minical_topics;
CREATE TABLE tiki_minical_topics (
  user varchar(200) DEFAULT NULL,
  topicId INTEGER PRIMARY KEY NOT NULL,
  name varchar(250) DEFAULT NULL,
  filename varchar(200) DEFAULT NULL,
  filetype varchar(200) DEFAULT NULL,
  filesize varchar(200) DEFAULT NULL,
  data longblob,
  path varchar(250) DEFAULT NULL,
  isIcon char(1) DEFAULT NULL
);

--
-- Table: tiki_modules
--
DROP TABLE tiki_modules;
CREATE TABLE tiki_modules (
  name varchar(200) NOT NULL DEFAULT '',
  position char(1) DEFAULT NULL,
  ord int(4) DEFAULT NULL,
  type char(1) DEFAULT NULL,
  title varchar(40) DEFAULT NULL,
  cache_time int(14) DEFAULT NULL,
  rows int(4) DEFAULT NULL,
  params varchar(255) DEFAULT NULL,
  groups text
);

--
-- Table: tiki_newsletter_subscriptions
--
DROP TABLE tiki_newsletter_subscriptions;
CREATE TABLE tiki_newsletter_subscriptions (
  nlId int(12) NOT NULL DEFAULT '0',
  email varchar(255) NOT NULL DEFAULT '',
  code varchar(32) DEFAULT NULL,
  valid char(1) DEFAULT NULL,
  subscribed int(14) DEFAULT NULL
);

--
-- Table: tiki_newsletters
--
DROP TABLE tiki_newsletters;
CREATE TABLE tiki_newsletters (
  nlId INTEGER PRIMARY KEY NOT NULL,
  name varchar(200) DEFAULT NULL,
  description text,
  created int(14) DEFAULT NULL,
  lastSent int(14) DEFAULT NULL,
  editions int(10) DEFAULT NULL,
  users int(10) DEFAULT NULL,
  allowAnySub char(1) DEFAULT NULL,
  frequency int(14) DEFAULT NULL
);

--
-- Table: tiki_newsreader_marks
--
DROP TABLE tiki_newsreader_marks;
CREATE TABLE tiki_newsreader_marks (
  user varchar(200) NOT NULL DEFAULT '',
  serverId int(12) NOT NULL DEFAULT '0',
  groupName varchar(255) NOT NULL DEFAULT '',
  timestamp int(14) NOT NULL DEFAULT '0'
);

--
-- Table: tiki_newsreader_servers
--
DROP TABLE tiki_newsreader_servers;
CREATE TABLE tiki_newsreader_servers (
  user varchar(200) NOT NULL DEFAULT '',
  serverId INTEGER PRIMARY KEY NOT NULL,
  server varchar(250) DEFAULT NULL,
  port int(4) DEFAULT NULL,
  username varchar(200) DEFAULT NULL,
  password varchar(200) DEFAULT NULL
);

--
-- Table: tiki_page_footnotes
--
DROP TABLE tiki_page_footnotes;
CREATE TABLE tiki_page_footnotes (
  user varchar(200) NOT NULL DEFAULT '',
  pageName varchar(250) NOT NULL DEFAULT '',
  data text
);

--
-- Table: tiki_pages
--
DROP TABLE tiki_pages;
CREATE TABLE tiki_pages (
  pageName varchar(160) NOT NULL DEFAULT '',
  hits int(8) DEFAULT NULL,
  data text,
  description varchar(200) DEFAULT NULL,
  lastModif int(14) DEFAULT NULL,
  comment varchar(200) DEFAULT NULL,
  version int(8) NOT NULL DEFAULT '0',
  user varchar(200) DEFAULT NULL,
  ip varchar(15) DEFAULT NULL,
  flag char(1) DEFAULT NULL,
  points int(8) DEFAULT NULL,
  votes int(8) DEFAULT NULL,
  cache text,
  cache_timestamp int(14) DEFAULT NULL,
  pageRank decimal(4,3) DEFAULT NULL,
  creator varchar(200) DEFAULT NULL
);
CREATE INDEX data_tiki_pages on tiki_pages (data);
CREATE INDEX pageRank_tiki_pages on tiki_pages (pageRank);
CREATE INDEX ft_tiki_pages on tiki_pages (pageName, data);

--
-- Table: tiki_pageviews
--
DROP TABLE tiki_pageviews;
CREATE TABLE tiki_pageviews (
  day int(14) NOT NULL DEFAULT '0',
  pageviews int(14) DEFAULT NULL
);

--
-- Table: tiki_poll_options
--
DROP TABLE tiki_poll_options;
CREATE TABLE tiki_poll_options (
  pollId int(8) NOT NULL DEFAULT '0',
  optionId INTEGER PRIMARY KEY NOT NULL,
  title varchar(200) DEFAULT NULL,
  votes int(8) DEFAULT NULL
);

--
-- Table: tiki_polls
--
DROP TABLE tiki_polls;
CREATE TABLE tiki_polls (
  pollId INTEGER PRIMARY KEY NOT NULL,
  title varchar(200) DEFAULT NULL,
  votes int(8) DEFAULT NULL,
  active char(1) DEFAULT NULL,
  publishDate int(14) DEFAULT NULL
);

--
-- Table: tiki_preferences
--
DROP TABLE tiki_preferences;
CREATE TABLE tiki_preferences (
  name varchar(40) NOT NULL DEFAULT '',
  value varchar(250) DEFAULT NULL
);

--
-- Table: tiki_private_messages
--
DROP TABLE tiki_private_messages;
CREATE TABLE tiki_private_messages (
  messageId INTEGER PRIMARY KEY NOT NULL,
  toNickname varchar(200) NOT NULL DEFAULT '',
  data varchar(255) DEFAULT NULL,
  poster varchar(200) NOT NULL DEFAULT 'anonymous',
  timestamp int(14) DEFAULT NULL
);

--
-- Table: tiki_programmed_content
--
DROP TABLE tiki_programmed_content;
CREATE TABLE tiki_programmed_content (
  pId INTEGER PRIMARY KEY NOT NULL,
  contentId int(8) NOT NULL DEFAULT '0',
  publishDate int(14) NOT NULL DEFAULT '0',
  data text
);

--
-- Table: tiki_quiz_question_options
--
DROP TABLE tiki_quiz_question_options;
CREATE TABLE tiki_quiz_question_options (
  optionId INTEGER PRIMARY KEY NOT NULL,
  questionId int(10) DEFAULT NULL,
  optionText text,
  points int(4) DEFAULT NULL
);

--
-- Table: tiki_quiz_questions
--
DROP TABLE tiki_quiz_questions;
CREATE TABLE tiki_quiz_questions (
  questionId INTEGER PRIMARY KEY NOT NULL,
  quizId int(10) DEFAULT NULL,
  question text,
  position int(4) DEFAULT NULL,
  type char(1) DEFAULT NULL,
  maxPoints int(4) DEFAULT NULL
);

--
-- Table: tiki_quiz_results
--
DROP TABLE tiki_quiz_results;
CREATE TABLE tiki_quiz_results (
  resultId INTEGER PRIMARY KEY NOT NULL,
  quizId int(10) DEFAULT NULL,
  fromPoints int(4) DEFAULT NULL,
  toPoints int(4) DEFAULT NULL,
  answer text
);

--
-- Table: tiki_quiz_stats
--
DROP TABLE tiki_quiz_stats;
CREATE TABLE tiki_quiz_stats (
  quizId int(10) NOT NULL DEFAULT '0',
  questionId int(10) NOT NULL DEFAULT '0',
  optionId int(10) NOT NULL DEFAULT '0',
  votes int(10) DEFAULT NULL
);

--
-- Table: tiki_quiz_stats_sum
--
DROP TABLE tiki_quiz_stats_sum;
CREATE TABLE tiki_quiz_stats_sum (
  quizId int(10) NOT NULL DEFAULT '0',
  quizName varchar(255) DEFAULT NULL,
  timesTaken int(10) DEFAULT NULL,
  avgpoints decimal(5,2) DEFAULT NULL,
  avgavg decimal(5,2) DEFAULT NULL,
  avgtime decimal(5,2) DEFAULT NULL
);

--
-- Table: tiki_quizzes
--
DROP TABLE tiki_quizzes;
CREATE TABLE tiki_quizzes (
  quizId INTEGER PRIMARY KEY NOT NULL,
  name varchar(255) DEFAULT NULL,
  description text,
  canRepeat char(1) DEFAULT NULL,
  storeResults char(1) DEFAULT NULL,
  questionsPerPage int(4) DEFAULT NULL,
  timeLimited char(1) DEFAULT NULL,
  timeLimit int(14) DEFAULT NULL,
  created int(14) DEFAULT NULL,
  taken int(10) DEFAULT NULL
);

--
-- Table: tiki_received_articles
--
DROP TABLE tiki_received_articles;
CREATE TABLE tiki_received_articles (
  receivedArticleId INTEGER PRIMARY KEY NOT NULL,
  receivedFromSite varchar(200) DEFAULT NULL,
  receivedFromUser varchar(200) DEFAULT NULL,
  receivedDate int(14) DEFAULT NULL,
  title varchar(80) DEFAULT NULL,
  authorName varchar(60) DEFAULT NULL,
  size int(12) DEFAULT NULL,
  useImage char(1) DEFAULT NULL,
  image_name varchar(80) DEFAULT NULL,
  image_type varchar(80) DEFAULT NULL,
  image_size int(14) DEFAULT NULL,
  image_x int(4) DEFAULT NULL,
  image_y int(4) DEFAULT NULL,
  image_data longblob,
  publishDate int(14) DEFAULT NULL,
  created int(14) DEFAULT NULL,
  heading text,
  body longblob,
  hash varchar(32) DEFAULT NULL,
  author varchar(200) DEFAULT NULL,
  type varchar(50) DEFAULT NULL,
  rating decimal(3,2) DEFAULT NULL
);

--
-- Table: tiki_received_pages
--
DROP TABLE tiki_received_pages;
CREATE TABLE tiki_received_pages (
  receivedPageId INTEGER PRIMARY KEY NOT NULL,
  pageName varchar(160) NOT NULL DEFAULT '',
  data longblob,
  description varchar(200) DEFAULT NULL,
  comment varchar(200) DEFAULT NULL,
  receivedFromSite varchar(200) DEFAULT NULL,
  receivedFromUser varchar(200) DEFAULT NULL,
  receivedDate int(14) DEFAULT NULL
);

--
-- Table: tiki_referer_stats
--
DROP TABLE tiki_referer_stats;
CREATE TABLE tiki_referer_stats (
  referer varchar(50) NOT NULL DEFAULT '',
  hits int(10) DEFAULT NULL,
  last int(14) DEFAULT NULL
);

--
-- Table: tiki_related_categories
--
DROP TABLE tiki_related_categories;
CREATE TABLE tiki_related_categories (
  categId int(10) NOT NULL DEFAULT '0',
  relatedTo int(10) NOT NULL DEFAULT '0'
);

--
-- Table: tiki_rss_modules
--
DROP TABLE tiki_rss_modules;
CREATE TABLE tiki_rss_modules (
  rssId INTEGER PRIMARY KEY NOT NULL,
  name varchar(30) NOT NULL DEFAULT '',
  description text,
  url varchar(255) NOT NULL DEFAULT '',
  refresh int(8) DEFAULT NULL,
  lastUpdated int(14) DEFAULT NULL,
  content longblob
);

--
-- Table: tiki_search_stats
--
DROP TABLE tiki_search_stats;
CREATE TABLE tiki_search_stats (
  term varchar(50) NOT NULL DEFAULT '',
  hits int(10) DEFAULT NULL
);

--
-- Table: tiki_semaphores
--
DROP TABLE tiki_semaphores;
CREATE TABLE tiki_semaphores (
  semName varchar(250) NOT NULL DEFAULT '',
  user varchar(200) DEFAULT NULL,
  timestamp int(14) DEFAULT NULL
);

--
-- Table: tiki_sent_newsletters
--
DROP TABLE tiki_sent_newsletters;
CREATE TABLE tiki_sent_newsletters (
  editionId INTEGER PRIMARY KEY NOT NULL,
  nlId int(12) NOT NULL DEFAULT '0',
  users int(10) DEFAULT NULL,
  sent int(14) DEFAULT NULL,
  subject varchar(200) DEFAULT NULL,
  data longblob
);

--
-- Table: tiki_sessions
--
DROP TABLE tiki_sessions;
CREATE TABLE tiki_sessions (
  sessionId varchar(32) NOT NULL DEFAULT '',
  user varchar(200) DEFAULT NULL,
  timestamp int(14) DEFAULT NULL
);

--
-- Table: tiki_shoutbox
--
DROP TABLE tiki_shoutbox;
CREATE TABLE tiki_shoutbox (
  msgId INTEGER PRIMARY KEY NOT NULL,
  message varchar(255) DEFAULT NULL,
  timestamp int(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  hash varchar(32) DEFAULT NULL
);

--
-- Table: tiki_structures
--
DROP TABLE tiki_structures;
CREATE TABLE tiki_structures (
  page varchar(240) NOT NULL DEFAULT '',
  parent varchar(240) NOT NULL DEFAULT '',
  pos int(4) DEFAULT NULL
);

--
-- Table: tiki_submissions
--
DROP TABLE tiki_submissions;
CREATE TABLE tiki_submissions (
  subId INTEGER PRIMARY KEY NOT NULL,
  title varchar(80) DEFAULT NULL,
  authorName varchar(60) DEFAULT NULL,
  topicId int(14) DEFAULT NULL,
  topicName varchar(40) DEFAULT NULL,
  size int(12) DEFAULT NULL,
  useImage char(1) DEFAULT NULL,
  image_name varchar(80) DEFAULT NULL,
  image_type varchar(80) DEFAULT NULL,
  image_size int(14) DEFAULT NULL,
  image_x int(4) DEFAULT NULL,
  image_y int(4) DEFAULT NULL,
  image_data longblob,
  publishDate int(14) DEFAULT NULL,
  created int(14) DEFAULT NULL,
  heading text,
  body text,
  hash varchar(32) DEFAULT NULL,
  author varchar(200) DEFAULT NULL,
  reads int(14) DEFAULT NULL,
  votes int(8) DEFAULT NULL,
  points int(14) DEFAULT NULL,
  type varchar(50) DEFAULT NULL,
  rating decimal(3,2) DEFAULT NULL,
  isfloat char(1) DEFAULT NULL
);

--
-- Table: tiki_suggested_faq_questions
--
DROP TABLE tiki_suggested_faq_questions;
CREATE TABLE tiki_suggested_faq_questions (
  sfqId INTEGER PRIMARY KEY NOT NULL,
  faqId int(10) NOT NULL DEFAULT '0',
  question text,
  answer text,
  created int(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL
);

--
-- Table: tiki_survey_question_options
--
DROP TABLE tiki_survey_question_options;
CREATE TABLE tiki_survey_question_options (
  optionId INTEGER PRIMARY KEY NOT NULL,
  questionId int(12) NOT NULL DEFAULT '0',
  qoption text,
  votes int(10) DEFAULT NULL
);

--
-- Table: tiki_survey_questions
--
DROP TABLE tiki_survey_questions;
CREATE TABLE tiki_survey_questions (
  questionId INTEGER PRIMARY KEY NOT NULL,
  surveyId int(12) NOT NULL DEFAULT '0',
  question text,
  options text,
  type char(1) DEFAULT NULL,
  position int(5) DEFAULT NULL,
  votes int(10) DEFAULT NULL,
  value int(10) DEFAULT NULL,
  average decimal(4,2) DEFAULT NULL
);

--
-- Table: tiki_surveys
--
DROP TABLE tiki_surveys;
CREATE TABLE tiki_surveys (
  surveyId INTEGER PRIMARY KEY NOT NULL,
  name varchar(200) DEFAULT NULL,
  description text,
  taken int(10) DEFAULT NULL,
  lastTaken int(14) DEFAULT NULL,
  created int(14) DEFAULT NULL,
  status char(1) DEFAULT NULL
);

--
-- Table: tiki_tags
--
DROP TABLE tiki_tags;
CREATE TABLE tiki_tags (
  tagName varchar(80) NOT NULL DEFAULT '',
  pageName varchar(160) NOT NULL DEFAULT '',
  hits int(8) DEFAULT NULL,
  description varchar(200) DEFAULT NULL,
  data longblob,
  lastModif int(14) DEFAULT NULL,
  comment varchar(200) DEFAULT NULL,
  version int(8) NOT NULL DEFAULT '0',
  user varchar(200) DEFAULT NULL,
  ip varchar(15) DEFAULT NULL,
  flag char(1) DEFAULT NULL
);

--
-- Table: tiki_theme_control_categs
--
DROP TABLE tiki_theme_control_categs;
CREATE TABLE tiki_theme_control_categs (
  categId int(12) NOT NULL DEFAULT '0',
  theme varchar(250) NOT NULL DEFAULT ''
);

--
-- Table: tiki_theme_control_objects
--
DROP TABLE tiki_theme_control_objects;
CREATE TABLE tiki_theme_control_objects (
  objId varchar(250) NOT NULL DEFAULT '',
  type varchar(250) NOT NULL DEFAULT '',
  name varchar(250) NOT NULL DEFAULT '',
  theme varchar(250) NOT NULL DEFAULT ''
);

--
-- Table: tiki_theme_control_sections
--
DROP TABLE tiki_theme_control_sections;
CREATE TABLE tiki_theme_control_sections (
  section varchar(250) NOT NULL DEFAULT '',
  theme varchar(250) NOT NULL DEFAULT ''
);

--
-- Table: tiki_topics
--
DROP TABLE tiki_topics;
CREATE TABLE tiki_topics (
  topicId INTEGER PRIMARY KEY NOT NULL,
  name varchar(40) DEFAULT NULL,
  image_name varchar(80) DEFAULT NULL,
  image_type varchar(80) DEFAULT NULL,
  image_size int(14) DEFAULT NULL,
  image_data longblob,
  active char(1) DEFAULT NULL,
  created int(14) DEFAULT NULL
);

--
-- Table: tiki_tracker_fields
--
DROP TABLE tiki_tracker_fields;
CREATE TABLE tiki_tracker_fields (
  fieldId INTEGER PRIMARY KEY NOT NULL,
  trackerId int(12) NOT NULL DEFAULT '0',
  name varchar(80) DEFAULT NULL,
  options text,
  type char(1) DEFAULT NULL,
  isMain char(1) DEFAULT NULL,
  isTblVisible char(1) DEFAULT NULL
);

--
-- Table: tiki_tracker_item_attachments
--
DROP TABLE tiki_tracker_item_attachments;
CREATE TABLE tiki_tracker_item_attachments (
  attId INTEGER PRIMARY KEY NOT NULL,
  itemId varchar(40) NOT NULL DEFAULT '',
  filename varchar(80) DEFAULT NULL,
  filetype varchar(80) DEFAULT NULL,
  filesize int(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  data longblob,
  path varchar(255) DEFAULT NULL,
  downloads int(10) DEFAULT NULL,
  created int(14) DEFAULT NULL,
  comment varchar(250) DEFAULT NULL
);

--
-- Table: tiki_tracker_item_comments
--
DROP TABLE tiki_tracker_item_comments;
CREATE TABLE tiki_tracker_item_comments (
  commentId INTEGER PRIMARY KEY NOT NULL,
  itemId int(12) NOT NULL DEFAULT '0',
  user varchar(200) DEFAULT NULL,
  data text,
  title varchar(200) DEFAULT NULL,
  posted int(14) DEFAULT NULL
);

--
-- Table: tiki_tracker_item_fields
--
DROP TABLE tiki_tracker_item_fields;
CREATE TABLE tiki_tracker_item_fields (
  itemId int(12) NOT NULL DEFAULT '0',
  fieldId int(12) NOT NULL DEFAULT '0',
  value text
);

--
-- Table: tiki_tracker_items
--
DROP TABLE tiki_tracker_items;
CREATE TABLE tiki_tracker_items (
  itemId INTEGER PRIMARY KEY NOT NULL,
  trackerId int(12) NOT NULL DEFAULT '0',
  created int(14) DEFAULT NULL,
  status char(1) DEFAULT NULL,
  lastModif int(14) DEFAULT NULL
);

--
-- Table: tiki_trackers
--
DROP TABLE tiki_trackers;
CREATE TABLE tiki_trackers (
  trackerId INTEGER PRIMARY KEY NOT NULL,
  name varchar(80) DEFAULT NULL,
  description text,
  created int(14) DEFAULT NULL,
  lastModif int(14) DEFAULT NULL,
  showCreated char(1) DEFAULT NULL,
  showStatus char(1) DEFAULT NULL,
  showLastModif char(1) DEFAULT NULL,
  useComments char(1) DEFAULT NULL,
  useAttachments char(1) DEFAULT NULL,
  items int(10) DEFAULT NULL
);

--
-- Table: tiki_untranslated
--
DROP TABLE tiki_untranslated;
CREATE TABLE tiki_untranslated (
  id int NOT NULL,
  source tinyblob NOT NULL,
  lang char(2) NOT NULL DEFAULT ''
);
CREATE INDEX id_2_tiki_untranslated on tiki_untranslated (id);
CREATE UNIQUE INDEX id_tiki_untranslated on tiki_untranslated (id);

--
-- Table: tiki_user_answers
--
DROP TABLE tiki_user_answers;
CREATE TABLE tiki_user_answers (
  userResultId int(10) NOT NULL DEFAULT '0',
  quizId int(10) NOT NULL DEFAULT '0',
  questionId int(10) NOT NULL DEFAULT '0',
  optionId int(10) NOT NULL DEFAULT '0'
);

--
-- Table: tiki_user_assigned_modules
--
DROP TABLE tiki_user_assigned_modules;
CREATE TABLE tiki_user_assigned_modules (
  name varchar(200) NOT NULL DEFAULT '',
  position char(1) DEFAULT NULL,
  ord int(4) DEFAULT NULL,
  type char(1) DEFAULT NULL,
  title varchar(40) DEFAULT NULL,
  cache_time int(14) DEFAULT NULL,
  rows int(4) DEFAULT NULL,
  groups text,
  params varchar(250) DEFAULT NULL,
  user varchar(200) NOT NULL DEFAULT ''
);

--
-- Table: tiki_user_bookmarks_folders
--
DROP TABLE tiki_user_bookmarks_folders;
CREATE TABLE tiki_user_bookmarks_folders (
  folderId INTEGER PRIMARY KEY NOT NULL,
  parentId int(12) DEFAULT NULL,
  user varchar(200) NOT NULL DEFAULT '',
  name varchar(30) DEFAULT NULL
);

--
-- Table: tiki_user_bookmarks_urls
--
DROP TABLE tiki_user_bookmarks_urls;
CREATE TABLE tiki_user_bookmarks_urls (
  urlId INTEGER PRIMARY KEY NOT NULL,
  name varchar(30) DEFAULT NULL,
  url varchar(250) DEFAULT NULL,
  data longblob,
  lastUpdated int(14) DEFAULT NULL,
  folderId int(12) NOT NULL DEFAULT '0',
  user varchar(200) NOT NULL DEFAULT ''
);

--
-- Table: tiki_user_mail_accounts
--
DROP TABLE tiki_user_mail_accounts;
CREATE TABLE tiki_user_mail_accounts (
  accountId INTEGER PRIMARY KEY NOT NULL,
  user varchar(200) NOT NULL DEFAULT '',
  account varchar(50) NOT NULL DEFAULT '',
  pop varchar(255) DEFAULT NULL,
  current char(1) DEFAULT NULL,
  port int(4) DEFAULT NULL,
  username varchar(100) DEFAULT NULL,
  pass varchar(100) DEFAULT NULL,
  msgs int(4) DEFAULT NULL,
  smtp varchar(255) DEFAULT NULL,
  useAuth char(1) DEFAULT NULL,
  smtpPort int(4) DEFAULT NULL
);

--
-- Table: tiki_user_menus
--
DROP TABLE tiki_user_menus;
CREATE TABLE tiki_user_menus (
  user varchar(200) NOT NULL DEFAULT '',
  menuId INTEGER PRIMARY KEY NOT NULL,
  url varchar(250) DEFAULT NULL,
  name varchar(40) DEFAULT NULL,
  position int(4) DEFAULT NULL,
  mode char(1) DEFAULT NULL
);

--
-- Table: tiki_user_modules
--
DROP TABLE tiki_user_modules;
CREATE TABLE tiki_user_modules (
  name varchar(200) NOT NULL DEFAULT '',
  title varchar(40) DEFAULT NULL,
  data longblob
);

--
-- Table: tiki_user_notes
--
DROP TABLE tiki_user_notes;
CREATE TABLE tiki_user_notes (
  user varchar(200) NOT NULL DEFAULT '',
  noteId INTEGER PRIMARY KEY NOT NULL,
  created int(14) DEFAULT NULL,
  name varchar(255) DEFAULT NULL,
  lastModif int(14) DEFAULT NULL,
  data text,
  size int(14) DEFAULT NULL,
  parse_mode varchar(20) DEFAULT NULL
);

--
-- Table: tiki_user_postings
--
DROP TABLE tiki_user_postings;
CREATE TABLE tiki_user_postings (
  user varchar(200) NOT NULL DEFAULT '',
  posts int(12) DEFAULT NULL,
  last int(14) DEFAULT NULL,
  first int(14) DEFAULT NULL,
  level int(8) DEFAULT NULL
);

--
-- Table: tiki_user_preferences
--
DROP TABLE tiki_user_preferences;
CREATE TABLE tiki_user_preferences (
  user varchar(200) NOT NULL DEFAULT '',
  prefName varchar(40) NOT NULL DEFAULT '',
  value varchar(250) DEFAULT NULL
);

--
-- Table: tiki_user_quizzes
--
DROP TABLE tiki_user_quizzes;
CREATE TABLE tiki_user_quizzes (
  user varchar(100) DEFAULT NULL,
  quizId int(10) DEFAULT NULL,
  timestamp int(14) DEFAULT NULL,
  timeTaken int(14) DEFAULT NULL,
  points int(12) DEFAULT NULL,
  maxPoints int(12) DEFAULT NULL,
  resultId int(10) DEFAULT NULL,
  userResultId INTEGER PRIMARY KEY NOT NULL
);

--
-- Table: tiki_user_taken_quizzes
--
DROP TABLE tiki_user_taken_quizzes;
CREATE TABLE tiki_user_taken_quizzes (
  user varchar(200) NOT NULL DEFAULT '',
  quizId varchar(255) NOT NULL DEFAULT ''
);

--
-- Table: tiki_user_tasks
--
DROP TABLE tiki_user_tasks;
CREATE TABLE tiki_user_tasks (
  user varchar(200) DEFAULT NULL,
  taskId INTEGER PRIMARY KEY NOT NULL,
  title varchar(250) DEFAULT NULL,
  description text,
  date int(14) DEFAULT NULL,
  status char(1) DEFAULT NULL,
  priority int(2) DEFAULT NULL,
  completed int(14) DEFAULT NULL,
  percentage int(4) DEFAULT NULL
);

--
-- Table: tiki_user_votings
--
DROP TABLE tiki_user_votings;
CREATE TABLE tiki_user_votings (
  user varchar(200) NOT NULL DEFAULT '',
  id varchar(255) NOT NULL DEFAULT ''
);

--
-- Table: tiki_user_watches
--
DROP TABLE tiki_user_watches;
CREATE TABLE tiki_user_watches (
  user varchar(200) NOT NULL DEFAULT '',
  event varchar(40) NOT NULL DEFAULT '',
  object varchar(200) NOT NULL DEFAULT '',
  hash varchar(32) DEFAULT NULL,
  title varchar(250) DEFAULT NULL,
  type varchar(200) DEFAULT NULL,
  url varchar(250) DEFAULT NULL,
  email varchar(200) DEFAULT NULL
);

--
-- Table: tiki_userfiles
--
DROP TABLE tiki_userfiles;
CREATE TABLE tiki_userfiles (
  user varchar(200) NOT NULL DEFAULT '',
  fileId INTEGER PRIMARY KEY NOT NULL,
  name varchar(200) DEFAULT NULL,
  filename varchar(200) DEFAULT NULL,
  filetype varchar(200) DEFAULT NULL,
  filesize varchar(200) DEFAULT NULL,
  data longblob,
  hits int(8) DEFAULT NULL,
  isFile char(1) DEFAULT NULL,
  path varchar(255) DEFAULT NULL,
  created int(14) DEFAULT NULL
);

--
-- Table: tiki_userpoints
--
DROP TABLE tiki_userpoints;
CREATE TABLE tiki_userpoints (
  user varchar(200) DEFAULT NULL,
  points decimal(8,2) DEFAULT NULL,
  voted int(8) DEFAULT NULL
);

--
-- Table: tiki_users
--
DROP TABLE tiki_users;
CREATE TABLE tiki_users (
  user varchar(200) NOT NULL DEFAULT '',
  password varchar(40) DEFAULT NULL,
  email varchar(200) DEFAULT NULL,
  lastLogin int(14) DEFAULT NULL
);

--
-- Table: tiki_webmail_contacts
--
DROP TABLE tiki_webmail_contacts;
CREATE TABLE tiki_webmail_contacts (
  contactId INTEGER PRIMARY KEY NOT NULL,
  firstName varchar(80) DEFAULT NULL,
  lastName varchar(80) DEFAULT NULL,
  email varchar(250) DEFAULT NULL,
  nickname varchar(200) DEFAULT NULL,
  user varchar(200) NOT NULL DEFAULT ''
);

--
-- Table: tiki_webmail_messages
--
DROP TABLE tiki_webmail_messages;
CREATE TABLE tiki_webmail_messages (
  accountId int(12) NOT NULL DEFAULT '0',
  mailId varchar(255) NOT NULL DEFAULT '',
  user varchar(200) NOT NULL DEFAULT '',
  isRead char(1) DEFAULT NULL,
  isReplied char(1) DEFAULT NULL,
  isFlagged char(1) DEFAULT NULL
);

--
-- Table: tiki_wiki_attachments
--
DROP TABLE tiki_wiki_attachments;
CREATE TABLE tiki_wiki_attachments (
  attId INTEGER PRIMARY KEY NOT NULL,
  page varchar(200) NOT NULL DEFAULT '',
  filename varchar(80) DEFAULT NULL,
  filetype varchar(80) DEFAULT NULL,
  filesize int(14) DEFAULT NULL,
  user varchar(200) DEFAULT NULL,
  data longblob,
  path varchar(255) DEFAULT NULL,
  downloads int(10) DEFAULT NULL,
  created int(14) DEFAULT NULL,
  comment varchar(250) DEFAULT NULL
);

--
-- Table: tiki_zones
--
DROP TABLE tiki_zones;
CREATE TABLE tiki_zones (
  zone varchar(40) NOT NULL DEFAULT ''
);

--
-- Table: users_grouppermissions
--
DROP TABLE users_grouppermissions;
CREATE TABLE users_grouppermissions (
  groupName varchar(30) NOT NULL DEFAULT '',
  permName varchar(30) NOT NULL DEFAULT '',
  value char(1) NOT NULL DEFAULT ''
);

--
-- Table: users_groups
--
DROP TABLE users_groups;
CREATE TABLE users_groups (
  groupName varchar(30) NOT NULL DEFAULT '',
  groupDesc varchar(255) DEFAULT NULL
);

--
-- Table: users_objectpermissions
--
DROP TABLE users_objectpermissions;
CREATE TABLE users_objectpermissions (
  groupName varchar(30) NOT NULL DEFAULT '',
  permName varchar(30) NOT NULL DEFAULT '',
  objectType varchar(20) NOT NULL DEFAULT '',
  objectId varchar(32) NOT NULL DEFAULT ''
);

--
-- Table: users_permissions
--
DROP TABLE users_permissions;
CREATE TABLE users_permissions (
  permName varchar(30) NOT NULL DEFAULT '',
  permDesc varchar(250) DEFAULT NULL,
  level varchar(80) DEFAULT NULL,
  type varchar(20) DEFAULT NULL
);

--
-- Table: users_usergroups
--
DROP TABLE users_usergroups;
CREATE TABLE users_usergroups (
  userId int(8) NOT NULL DEFAULT '0',
  groupName varchar(30) NOT NULL DEFAULT ''
);

--
-- Table: users_users
--
DROP TABLE users_users;
CREATE TABLE users_users (
  userId INTEGER PRIMARY KEY NOT NULL,
  email varchar(200) DEFAULT NULL,
  login varchar(40) NOT NULL DEFAULT '',
  password varchar(30) NOT NULL DEFAULT '',
  provpass varchar(30) DEFAULT NULL,
  realname varchar(80) DEFAULT NULL,
  homePage varchar(200) DEFAULT NULL,
  lastLogin int(14) DEFAULT NULL,
  currentLogin int(14) DEFAULT NULL,
  registrationDate int(14) DEFAULT NULL,
  challenge varchar(32) DEFAULT NULL,
  pass_due int(14) DEFAULT NULL,
  hash varchar(32) DEFAULT NULL,
  created int(14) DEFAULT NULL,
  country varchar(80) DEFAULT NULL,
  avatarName varchar(80) DEFAULT NULL,
  avatarSize int(14) DEFAULT NULL,
  avatarFileType varchar(250) DEFAULT NULL,
  avatarData longblob,
  avatarLibName varchar(200) DEFAULT NULL,
  avatarType char(1) DEFAULT NULL
);

