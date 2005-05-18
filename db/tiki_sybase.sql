-- 
-- Created by SQL::Translator::Producer::Sybase
-- Created on Sun Aug 17 01:05:49 2003
-- 
--
-- Table: galaxia_activities
--

DROP TABLE galaxia_activities;
CREATE TABLE galaxia_activities (
  activityId IDENTITY int(14) NOT NULL,
  name varchar(80) DEFAULT NULL NULL,
  normalized_name varchar(80) DEFAULT NULL NULL,
  pId numeric(14) DEFAULT '0' NOT NULL,
  type enumcharacter varying(10) DEFAULT NULL NULL,
  isAutoRouted char(1) DEFAULT NULL NULL,
  flowNum numeric(10) DEFAULT NULL NULL,
  isInteractive char(1) DEFAULT NULL NULL,
  lastModif numeric(14) DEFAULT NULL NULL,
  description varchar(255) NULL,
  CONSTRAINT chk_galaxia_activities_type CHECK (type IN ('start','end','split','switch','join','activity','standalone')),
  CONSTRAINT pk_galaxia_activities PRIMARY KEY (activityId)
);

--
-- Table: galaxia_activity_roles
--

DROP TABLE galaxia_activity_roles;
CREATE TABLE galaxia_activity_roles (
  activityId numeric(14) DEFAULT '0' NOT NULL,
  roleId numeric(14) DEFAULT '0' NOT NULL,
  CONSTRAINT pk_galaxia_activity_roles PRIMARY KEY (activityId, roleId)
);

--
-- Table: galaxia_instance_activities
--

DROP TABLE galaxia_instance_activities;
CREATE TABLE galaxia_instance_activities (
  instanceId numeric(14) DEFAULT '0' NOT NULL,
  activityId numeric(14) DEFAULT '0' NOT NULL,
  started numeric(14) DEFAULT '0' NOT NULL,
  ended numeric(14) DEFAULT '0' NOT NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  status enumcharacter varying(9) DEFAULT NULL NULL,
  CONSTRAINT chk_galaxia_instance_activitie CHECK (status IN ('running','completed')),
  CONSTRAINT pk_galaxia_instance_activities PRIMARY KEY (instanceId, activityId)
);

--
-- Table: galaxia_instance_comments
--

DROP TABLE galaxia_instance_comments;
CREATE TABLE galaxia_instance_comments (
  cId IDENTITY int(14) NOT NULL,
  instanceId numeric(14) DEFAULT '0' NOT NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  activityId numeric(14) DEFAULT NULL NULL,
  hash varchar(32) DEFAULT NULL NULL,
  title varchar(250) DEFAULT NULL NULL,
  comment varchar(255) NULL,
  activity varchar(80) DEFAULT NULL NULL,
  timestamp numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_galaxia_instance_comments PRIMARY KEY (cId)
);

--
-- Table: galaxia_instances
--

DROP TABLE galaxia_instances;
CREATE TABLE galaxia_instances (
  instanceId IDENTITY int(14) NOT NULL,
  pId numeric(14) DEFAULT '0' NOT NULL,
  started numeric(14) DEFAULT NULL NULL,
  owner varchar(200) DEFAULT NULL NULL,
  nextActivity numeric(14) DEFAULT NULL NULL,
  nextUser varchar(200) DEFAULT NULL NULL,
  ended numeric(14) DEFAULT NULL NULL,
  status enumcharacter varying(9) DEFAULT NULL NULL,
  properties longblob NULL,
  CONSTRAINT chk_galaxia_instances_status CHECK (status IN ('active','exception','aborted','completed')),
  CONSTRAINT pk_galaxia_instances PRIMARY KEY (instanceId)
);

--
-- Table: galaxia_processes
--

DROP TABLE galaxia_processes;
CREATE TABLE galaxia_processes (
  pId IDENTITY int(14) NOT NULL,
  name varchar(80) DEFAULT NULL NULL,
  isValid char(1) DEFAULT NULL NULL,
  isActive char(1) DEFAULT NULL NULL,
  version varchar(12) DEFAULT NULL NULL,
  description varchar(255) NULL,
  lastModif numeric(14) DEFAULT NULL NULL,
  normalized_name varchar(80) DEFAULT NULL NULL,
  CONSTRAINT pk_galaxia_processes PRIMARY KEY (pId)
);

--
-- Table: galaxia_roles
--

DROP TABLE galaxia_roles;
CREATE TABLE galaxia_roles (
  roleId IDENTITY int(14) NOT NULL,
  pId numeric(14) DEFAULT '0' NOT NULL,
  lastModif numeric(14) DEFAULT NULL NULL,
  name varchar(80) DEFAULT NULL NULL,
  description varchar(255) NULL,
  CONSTRAINT pk_galaxia_roles PRIMARY KEY (roleId)
);

--
-- Table: galaxia_transitions
--

DROP TABLE galaxia_transitions;
CREATE TABLE galaxia_transitions (
  pId numeric(14) DEFAULT '0' NOT NULL,
  actFromId numeric(14) DEFAULT '0' NOT NULL,
  actToId numeric(14) DEFAULT '0' NOT NULL,
  CONSTRAINT pk_galaxia_transitions PRIMARY KEY (actFromId, actToId)
);

--
-- Table: galaxia_user_roles
--

DROP TABLE galaxia_user_roles;
CREATE TABLE galaxia_user_roles (
  pId numeric(14) DEFAULT '0' NOT NULL,
  roleId IDENTITY int(14) NOT NULL,
  user_ varchar(200) DEFAULT '' NOT NULL,
  CONSTRAINT pk_galaxia_user_roles PRIMARY KEY (roleId, user_)
);

--
-- Table: galaxia_workitems
--

DROP TABLE galaxia_workitems;
CREATE TABLE galaxia_workitems (
  itemId IDENTITY int(14) NOT NULL,
  instanceId numeric(14) DEFAULT '0' NOT NULL,
  orderId numeric(14) DEFAULT '0' NOT NULL,
  activityId numeric(14) DEFAULT '0' NOT NULL,
  properties longblob NULL,
  started numeric(14) DEFAULT NULL NULL,
  ended numeric(14) DEFAULT NULL NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  CONSTRAINT pk_galaxia_workitems PRIMARY KEY (itemId)
);

--
-- Table: messu_messages
--

DROP TABLE messu_messages;
CREATE TABLE messu_messages (
  msgId IDENTITY int(14) NOT NULL,
  user_ varchar(200) DEFAULT '' NOT NULL,
  user_from varchar(200) DEFAULT '' NOT NULL,
  user_to varchar(255) NULL,
  user_cc varchar(255) NULL,
  user_bcc varchar(255) NULL,
  subject varchar(255) DEFAULT NULL NULL,
  body varchar(255) NULL,
  hash varchar(32) DEFAULT NULL NULL,
  date numeric(14) DEFAULT NULL NULL,
  isRead char(1) DEFAULT NULL NULL,
  isReplied char(1) DEFAULT NULL NULL,
  isFlagged char(1) DEFAULT NULL NULL,
  priority numeric(2) DEFAULT NULL NULL,
  CONSTRAINT pk_messu_messages PRIMARY KEY (msgId)
);

--
-- Table: tiki_actionlog
--

DROP TABLE tiki_actionlog;
CREATE TABLE tiki_actionlog (
  action varchar(255) DEFAULT '' NOT NULL,
  lastModif numeric(14) DEFAULT NULL NULL,
  pageName varchar(200) DEFAULT NULL NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  ip varchar(15) DEFAULT NULL NULL,
  comment varchar(200) DEFAULT NULL NULL
);

--
-- Table: tiki_articles
--

DROP TABLE tiki_articles;
CREATE TABLE tiki_articles (
  articleId IDENTITY int(8) NOT NULL,
  title varchar(80) DEFAULT NULL NULL,
  authorName varchar(60) DEFAULT NULL NULL,
  topicId numeric(14) DEFAULT NULL NULL,
  topicName varchar(40) DEFAULT NULL NULL,
  size numeric(12) DEFAULT NULL NULL,
  useImage char(1) DEFAULT NULL NULL,
  image_name varchar(80) DEFAULT NULL NULL,
  image_type varchar(80) DEFAULT NULL NULL,
  image_size numeric(14) DEFAULT NULL NULL,
  image_x numeric(4) DEFAULT NULL NULL,
  image_y numeric(4) DEFAULT NULL NULL,
  image_data longblob NULL,
  publishDate numeric(14) DEFAULT NULL NULL,
  created numeric(14) DEFAULT NULL NULL,
  heading varchar(255) NULL,
  body varchar(255) NULL,
  hash varchar(32) DEFAULT NULL NULL,
  author varchar(200) DEFAULT NULL NULL,
  reads numeric(14) DEFAULT NULL NULL,
  votes numeric(8) DEFAULT NULL NULL,
  points numeric(14) DEFAULT NULL NULL,
  type varchar(50) DEFAULT NULL NULL,
  rating decimal(3,2) DEFAULT NULL NULL,
  isfloat char(1) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_articles PRIMARY KEY (articleId)
);

CREATE INDEX title ON tiki_articles (title);

CREATE INDEX heading ON tiki_articles (heading(255));

CREATE INDEX body ON tiki_articles (body(255));

CREATE INDEX reads ON tiki_articles (reads);

CREATE INDEX ft ON tiki_articles (title, heading, body);

--
-- Table: tiki_banners
--

DROP TABLE tiki_banners;
CREATE TABLE tiki_banners (
  bannerId IDENTITY int(12) NOT NULL,
  client varchar(200) DEFAULT '' NOT NULL,
  url varchar(255) DEFAULT NULL NULL,
  title varchar(255) DEFAULT NULL NULL,
  alt varchar(250) DEFAULT NULL NULL,
  which varchar(50) DEFAULT NULL NULL,
  imageData longblob NULL,
  imageType varchar(200) DEFAULT NULL NULL,
  imageName varchar(100) DEFAULT NULL NULL,
  HTMLData varchar(255) NULL,
  fixedURLData varchar(255) DEFAULT NULL NULL,
  textData varchar(255) NULL,
  fromDate numeric(14) DEFAULT NULL NULL,
  toDate numeric(14) DEFAULT NULL NULL,
  useDates char(1) DEFAULT NULL NULL,
  mon char(1) DEFAULT NULL NULL,
  tue char(1) DEFAULT NULL NULL,
  wed char(1) DEFAULT NULL NULL,
  thu char(1) DEFAULT NULL NULL,
  fri char(1) DEFAULT NULL NULL,
  sat char(1) DEFAULT NULL NULL,
  sun char(1) DEFAULT NULL NULL,
  hourFrom varchar(4) DEFAULT NULL NULL,
  hourTo varchar(4) DEFAULT NULL NULL,
  created numeric(14) DEFAULT NULL NULL,
  maxImpressions numeric(8) DEFAULT NULL NULL,
  impressions numeric(8) DEFAULT NULL NULL,
  clicks numeric(8) DEFAULT NULL NULL,
  zone varchar(40) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_banners PRIMARY KEY (bannerId)
);

--
-- Table: tiki_banning
--

DROP TABLE tiki_banning;
CREATE TABLE tiki_banning (
  banId IDENTITY int(12) NOT NULL,
  mode enumcharacter varying(4) DEFAULT NULL NULL,
  title varchar(200) DEFAULT NULL NULL,
  ip1 char(3) DEFAULT NULL NULL,
  ip2 char(3) DEFAULT NULL NULL,
  ip3 char(3) DEFAULT NULL NULL,
  ip4 char(3) DEFAULT NULL NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  date_from datetime(14) NOT NULL,
  date_to datetime(14) NOT NULL,
  use_dates char(1) DEFAULT NULL NULL,
  created numeric(14) DEFAULT NULL NULL,
  message varchar(255) NULL,
  CONSTRAINT chk_tiki_banning_mode CHECK (mode IN ('user','ip')),
  CONSTRAINT pk_tiki_banning PRIMARY KEY (banId)
);

--
-- Table: tiki_banning_sections
--

DROP TABLE tiki_banning_sections;
CREATE TABLE tiki_banning_sections (
  banId numeric(12) DEFAULT '0' NOT NULL,
  section varchar(100) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_banning_sections PRIMARY KEY (banId, section)
);

--
-- Table: tiki_blog_activity
--

DROP TABLE tiki_blog_activity;
CREATE TABLE tiki_blog_activity (
  blogId numeric(8) DEFAULT '0' NOT NULL,
  day numeric(14) DEFAULT '0' NOT NULL,
  posts numeric(8) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_blog_activity PRIMARY KEY (blogId, day)
);

--
-- Table: tiki_blog_posts
--

DROP TABLE tiki_blog_posts;
CREATE TABLE tiki_blog_posts (
  postId IDENTITY int(8) NOT NULL,
  blogId numeric(8) DEFAULT '0' NOT NULL,
  data varchar(255) NULL,
  created numeric(14) DEFAULT NULL NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  trackbacks_to varchar(255) NULL,
  trackbacks_from varchar(255) NULL,
  title varchar(80) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_blog_posts PRIMARY KEY (postId)
);

CREATE INDEX data ON tiki_blog_posts (data(255));

CREATE INDEX blogId ON tiki_blog_posts (blogId);

CREATE INDEX created ON tiki_blog_posts (created);

CREATE INDEX ft ON tiki_blog_posts (data);

--
-- Table: tiki_blog_posts_images
--

DROP TABLE tiki_blog_posts_images;
CREATE TABLE tiki_blog_posts_images (
  imgId IDENTITY int(14) NOT NULL,
  postId numeric(14) DEFAULT '0' NOT NULL,
  filename varchar(80) DEFAULT NULL NULL,
  filetype varchar(80) DEFAULT NULL NULL,
  filesize numeric(14) DEFAULT NULL NULL,
  data longblob NULL,
  CONSTRAINT pk_tiki_blog_posts_images PRIMARY KEY (imgId)
);

--
-- Table: tiki_blogs
--

DROP TABLE tiki_blogs;
CREATE TABLE tiki_blogs (
  blogId IDENTITY int(8) NOT NULL,
  created numeric(14) DEFAULT NULL NULL,
  lastModif numeric(14) DEFAULT NULL NULL,
  title varchar(200) DEFAULT NULL NULL,
  description varchar(255) NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  public_ char(1) DEFAULT NULL NULL,
  posts numeric(8) DEFAULT NULL NULL,
  maxPosts numeric(8) DEFAULT NULL NULL,
  hits numeric(8) DEFAULT NULL NULL,
  activity decimal(4,2) DEFAULT NULL NULL,
  heading varchar(255) NULL,
  use_find char(1) DEFAULT NULL NULL,
  use_title char(1) DEFAULT NULL NULL,
  add_date char(1) DEFAULT NULL NULL,
  add_poster char(1) DEFAULT NULL NULL,
  allow_comments char(1) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_blogs PRIMARY KEY (blogId)
);

CREATE INDEX title ON tiki_blogs (title);

CREATE INDEX description ON tiki_blogs (description(255));

CREATE INDEX hits ON tiki_blogs (hits);

CREATE INDEX ft ON tiki_blogs (title, description);

--
-- Table: tiki_calendar_categories
--

DROP TABLE tiki_calendar_categories;
CREATE TABLE tiki_calendar_categories (
  calcatId IDENTITY int(11) NOT NULL,
  calendarId numeric(14) DEFAULT '0' NOT NULL,
  name varchar(255) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_calendar_categories PRIMARY KEY (calcatId),
  CONSTRAINT catname UNIQUE (calendarId, name)
);

--
-- Table: tiki_calendar_items
--

DROP TABLE tiki_calendar_items;
CREATE TABLE tiki_calendar_items (
  calitemId IDENTITY int(14) NOT NULL,
  calendarId numeric(14) DEFAULT '0' NOT NULL,
  start numeric(14) DEFAULT '0' NOT NULL,
  end_ numeric(14) DEFAULT '0' NOT NULL,
  locationId numeric(14) DEFAULT NULL NULL,
  categoryId numeric(14) DEFAULT NULL NULL,
  priority enumcharacter varying(1) DEFAULT '1' NOT NULL,
  status enumcharacter varying(1) DEFAULT '0' NOT NULL,
  url varchar(255) DEFAULT NULL NULL,
  lang char(2) DEFAULT 'en' NOT NULL,
  name varchar(255) DEFAULT '' NOT NULL,
  description blob NULL,
  user_ varchar(40) DEFAULT NULL NULL,
  created numeric(14) DEFAULT '0' NOT NULL,
  lastmodif numeric(14) DEFAULT '0' NOT NULL,
  CONSTRAINT chk_tiki_calendar_items_priori CHECK (priority IN ('1','2','3','4','5','6','7','8','9')),
  CONSTRAINT chk_tiki_calendar_items_status CHECK (status IN ('0','1','2')),
  CONSTRAINT pk_tiki_calendar_items PRIMARY KEY (calitemId)
);

CREATE INDEX calendarId ON tiki_calendar_items (calendarId);

--
-- Table: tiki_calendar_locations
--

DROP TABLE tiki_calendar_locations;
CREATE TABLE tiki_calendar_locations (
  callocId IDENTITY int(14) NOT NULL,
  calendarId numeric(14) DEFAULT '0' NOT NULL,
  name varchar(255) DEFAULT '' NOT NULL,
  description blob NULL,
  CONSTRAINT pk_tiki_calendar_locations PRIMARY KEY (callocId),
  CONSTRAINT locname UNIQUE (calendarId, name)
);

--
-- Table: tiki_calendar_roles
--

DROP TABLE tiki_calendar_roles;
CREATE TABLE tiki_calendar_roles (
  calitemId numeric(14) DEFAULT '0' NOT NULL,
  username varchar(40) DEFAULT '' NOT NULL,
  role enumcharacter varying(1) DEFAULT '0' NOT NULL,
  CONSTRAINT chk_tiki_calendar_roles_role CHECK (role IN ('0','1','2','3','6')),
  CONSTRAINT pk_tiki_calendar_roles PRIMARY KEY (calitemId, username, role)
);

--
-- Table: tiki_calendars
--

DROP TABLE tiki_calendars;
CREATE TABLE tiki_calendars (
  calendarId IDENTITY int(14) NOT NULL,
  name varchar(80) DEFAULT '' NOT NULL,
  description varchar(255) DEFAULT NULL NULL,
  user_ varchar(40) DEFAULT '' NOT NULL,
  customlocations enumcharacter varying(1) DEFAULT 'n' NOT NULL,
  customcategories enumcharacter varying(1) DEFAULT 'n' NOT NULL,
  customlanguages enumcharacter varying(1) DEFAULT 'n' NOT NULL,
  custompriorities enumcharacter varying(1) DEFAULT 'n' NOT NULL,
  customparticipants enumcharacter varying(1) DEFAULT 'n' NOT NULL,
  created numeric(14) DEFAULT '0' NOT NULL,
  lastmodif numeric(14) DEFAULT '0' NOT NULL,
  CONSTRAINT chk_tiki_calendars_customlocat CHECK (customlocations IN ('n','y')),
  CONSTRAINT chk_tiki_calendars_customcateg CHECK (customcategories IN ('n','y')),
  CONSTRAINT chk_tiki_calendars_customlangu CHECK (customlanguages IN ('n','y')),
  CONSTRAINT chk_tiki_calendars_customprior CHECK (custompriorities IN ('n','y')),
  CONSTRAINT chk_tiki_calendars_customparti CHECK (customparticipants IN ('n','y')),
  CONSTRAINT pk_tiki_calendars PRIMARY KEY (calendarId)
);

--
-- Table: tiki_categories
--

DROP TABLE tiki_categories;
CREATE TABLE tiki_categories (
  categId IDENTITY int(12) NOT NULL,
  name varchar(100) DEFAULT NULL NULL,
  description varchar(250) DEFAULT NULL NULL,
  parentId numeric(12) DEFAULT NULL NULL,
  hits numeric(8) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_categories PRIMARY KEY (categId)
);

--
-- Table: tiki_categorized_objects
--

DROP TABLE tiki_categorized_objects;
CREATE TABLE tiki_categorized_objects (
  catObjectId IDENTITY int(12) NOT NULL,
  type varchar(50) DEFAULT NULL NULL,
  objId varchar(255) DEFAULT NULL NULL,
  description varchar(255) NULL,
  created numeric(14) DEFAULT NULL NULL,
  name varchar(200) DEFAULT NULL NULL,
  href varchar(200) DEFAULT NULL NULL,
  hits numeric(8) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_categorized_objects PRIMARY KEY (catObjectId)
);

--
-- Table: tiki_category_objects
--

DROP TABLE tiki_category_objects;
CREATE TABLE tiki_category_objects (
  catObjectId numeric(12) DEFAULT '0' NOT NULL,
  categId numeric(12) DEFAULT '0' NOT NULL,
  CONSTRAINT pk_tiki_category_objects PRIMARY KEY (catObjectId, categId)
);

--
-- Table: tiki_category_sites
--

DROP TABLE tiki_category_sites;
CREATE TABLE tiki_category_sites (
  categId numeric(10) DEFAULT '0' NOT NULL,
  siteId numeric(14) DEFAULT '0' NOT NULL,
  CONSTRAINT pk_tiki_category_sites PRIMARY KEY (categId, siteId)
);

--
-- Table: tiki_chart_items
--

DROP TABLE tiki_chart_items;
CREATE TABLE tiki_chart_items (
  itemId IDENTITY int(14) NOT NULL,
  title varchar(250) DEFAULT NULL NULL,
  description varchar(255) NULL,
  chartId numeric(14) DEFAULT '0' NOT NULL,
  created numeric(14) DEFAULT NULL NULL,
  URL varchar(250) DEFAULT NULL NULL,
  votes numeric(14) DEFAULT NULL NULL,
  points numeric(14) DEFAULT NULL NULL,
  average decimal(4,2) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_chart_items PRIMARY KEY (itemId)
);

--
-- Table: tiki_charts
--

DROP TABLE tiki_charts;
CREATE TABLE tiki_charts (
  chartId IDENTITY int(14) NOT NULL,
  title varchar(250) DEFAULT NULL NULL,
  description varchar(255) NULL,
  hits numeric(14) DEFAULT NULL NULL,
  singleItemVotes char(1) DEFAULT NULL NULL,
  singleChartVotes char(1) DEFAULT NULL NULL,
  suggestions char(1) DEFAULT NULL NULL,
  autoValidate char(1) DEFAULT NULL NULL,
  topN numeric(6) DEFAULT NULL NULL,
  maxVoteValue numeric(4) DEFAULT NULL NULL,
  frequency numeric(14) DEFAULT NULL NULL,
  showAverage char(1) DEFAULT NULL NULL,
  isActive char(1) DEFAULT NULL NULL,
  showVotes char(1) DEFAULT NULL NULL,
  useCookies char(1) DEFAULT NULL NULL,
  lastChart numeric(14) DEFAULT NULL NULL,
  voteAgainAfter numeric(14) DEFAULT NULL NULL,
  created numeric(14) DEFAULT NULL NULL,
  hist numeric(12) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_charts PRIMARY KEY (chartId)
);

--
-- Table: tiki_charts_rankings
--

DROP TABLE tiki_charts_rankings;
CREATE TABLE tiki_charts_rankings (
  chartId numeric(14) DEFAULT '0' NOT NULL,
  itemId numeric(14) DEFAULT '0' NOT NULL,
  position numeric(14) DEFAULT '0' NOT NULL,
  timestamp numeric(14) DEFAULT '0' NOT NULL,
  lastPosition numeric(14) DEFAULT '0' NOT NULL,
  period numeric(14) DEFAULT '0' NOT NULL,
  rvotes numeric(14) DEFAULT '0' NOT NULL,
  raverage decimal(4,2) DEFAULT '0.00' NOT NULL,
  CONSTRAINT pk_tiki_charts_rankings PRIMARY KEY (chartId, itemId, period)
);

--
-- Table: tiki_charts_votes
--

DROP TABLE tiki_charts_votes;
CREATE TABLE tiki_charts_votes (
  user_ varchar(200) DEFAULT '' NOT NULL,
  itemId numeric(14) DEFAULT '0' NOT NULL,
  timestamp numeric(14) DEFAULT NULL NULL,
  chartId numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_charts_votes PRIMARY KEY (user_, itemId)
);

--
-- Table: tiki_chat_channels
--

DROP TABLE tiki_chat_channels;
CREATE TABLE tiki_chat_channels (
  channelId IDENTITY int(8) NOT NULL,
  name varchar(30) DEFAULT NULL NULL,
  description varchar(250) DEFAULT NULL NULL,
  max_users numeric(8) DEFAULT NULL NULL,
  mode char(1) DEFAULT NULL NULL,
  moderator varchar(200) DEFAULT NULL NULL,
  active char(1) DEFAULT NULL NULL,
  refresh numeric(6) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_chat_channels PRIMARY KEY (channelId)
);

--
-- Table: tiki_chat_messages
--

DROP TABLE tiki_chat_messages;
CREATE TABLE tiki_chat_messages (
  messageId IDENTITY int(8) NOT NULL,
  channelId numeric(8) DEFAULT '0' NOT NULL,
  data varchar(255) DEFAULT NULL NULL,
  poster varchar(200) DEFAULT 'anonymous' NOT NULL,
  timestamp numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_chat_messages PRIMARY KEY (messageId)
);

--
-- Table: tiki_chat_users
--

DROP TABLE tiki_chat_users;
CREATE TABLE tiki_chat_users (
  nickname varchar(200) DEFAULT '' NOT NULL,
  channelId numeric(8) DEFAULT '0' NOT NULL,
  timestamp numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_chat_users PRIMARY KEY (nickname, channelId)
);

--
-- Table: tiki_comments
--

DROP TABLE tiki_comments;
CREATE TABLE tiki_comments (
  threadId IDENTITY int(14) NOT NULL,
  object varchar(32) DEFAULT '' NOT NULL,
  parentId numeric(14) DEFAULT NULL NULL,
  userName varchar(200) DEFAULT NULL NULL,
  commentDate numeric(14) DEFAULT NULL NULL,
  hits numeric(8) DEFAULT NULL NULL,
  type char(1) DEFAULT NULL NULL,
  points decimal(8,2) DEFAULT NULL NULL,
  votes numeric(8) DEFAULT NULL NULL,
  average decimal(8,4) DEFAULT NULL NULL,
  title varchar(100) DEFAULT NULL NULL,
  data varchar(255) NULL,
  hash varchar(32) DEFAULT NULL NULL,
  user_ip varchar(15) DEFAULT NULL NULL,
  summary varchar(240) DEFAULT NULL NULL,
  smiley varchar(80) DEFAULT NULL NULL,
  message_id varchar(250) default NULL,
  in_reply_to varchar(250) default NULL,
  CONSTRAINT pk_tiki_comments PRIMARY KEY (threadId)
);

CREATE INDEX title ON tiki_comments (title);

CREATE INDEX data ON tiki_comments (data(255));

CREATE INDEX object ON tiki_comments (object);

CREATE INDEX hits ON tiki_comments (hits);

CREATE INDEX tc_pi ON tiki_comments (parentId);

CREATE INDEX ft ON tiki_comments (title, data);

--
-- Table: tiki_content
--

DROP TABLE tiki_content;
CREATE TABLE tiki_content (
  contentId IDENTITY int(8) NOT NULL,
  description varchar(255) NULL,
  CONSTRAINT pk_tiki_content PRIMARY KEY (contentId)
);

--
-- Table: tiki_content_templates
--

DROP TABLE tiki_content_templates;
CREATE TABLE tiki_content_templates (
  templateId IDENTITY int(10) NOT NULL,
  content longblob NULL,
  name varchar(200) DEFAULT NULL NULL,
  created numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_content_templates PRIMARY KEY (templateId)
);

--
-- Table: tiki_content_templates_section
--

DROP TABLE tiki_content_templates_section;
CREATE TABLE tiki_content_templates_section (
  templateId numeric(10) DEFAULT '0' NOT NULL,
  section varchar(250) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_content_templates_sect PRIMARY KEY (templateId, section)
);

--
-- Table: tiki_cookies
--

DROP TABLE tiki_cookies;
CREATE TABLE tiki_cookies (
  cookieId IDENTITY int(10) NOT NULL,
  cookie varchar(255) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_cookies PRIMARY KEY (cookieId)
);

--
-- Table: tiki_copyrights
--

DROP TABLE tiki_copyrights;
CREATE TABLE tiki_copyrights (
  copyrightId IDENTITY int(12) NOT NULL,
  page varchar(200) DEFAULT NULL NULL,
  title varchar(200) DEFAULT NULL NULL,
  year numeric(11) DEFAULT NULL NULL,
  authors varchar(200) DEFAULT NULL NULL,
  copyright_order numeric(11) DEFAULT NULL NULL,
  userName varchar(200) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_copyrights PRIMARY KEY (copyrightId)
);

--
-- Table: tiki_directory_categories
--

DROP TABLE tiki_directory_categories;
CREATE TABLE tiki_directory_categories (
  categId IDENTITY int(10) NOT NULL,
  parent numeric(10) DEFAULT NULL NULL,
  name varchar(240) DEFAULT NULL NULL,
  description varchar(255) NULL,
  childrenType char(1) DEFAULT NULL NULL,
  sites numeric(10) DEFAULT NULL NULL,
  viewableChildren numeric(4) DEFAULT NULL NULL,
  allowSites char(1) DEFAULT NULL NULL,
  showCount char(1) DEFAULT NULL NULL,
  editorGroup varchar(200) DEFAULT NULL NULL,
  hits numeric(12) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_directory_categories PRIMARY KEY (categId)
);

--
-- Table: tiki_directory_search
--

DROP TABLE tiki_directory_search;
CREATE TABLE tiki_directory_search (
  term varchar(250) DEFAULT '' NOT NULL,
  hits numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_directory_search PRIMARY KEY (term)
);

--
-- Table: tiki_directory_sites
--

DROP TABLE tiki_directory_sites;
CREATE TABLE tiki_directory_sites (
  siteId IDENTITY int(14) NOT NULL,
  name varchar(240) DEFAULT NULL NULL,
  description varchar(255) NULL,
  url varchar(255) DEFAULT NULL NULL,
  country varchar(255) DEFAULT NULL NULL,
  hits numeric(12) DEFAULT NULL NULL,
  isValid char(1) DEFAULT NULL NULL,
  created numeric(14) DEFAULT NULL NULL,
  lastModif numeric(14) DEFAULT NULL NULL,
  cache longblob NULL,
  cache_timestamp numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_directory_sites PRIMARY KEY (siteId)
);

CREATE INDEX ft ON tiki_directory_sites (name, description);

--
-- Table: tiki_drawings
--

DROP TABLE tiki_drawings;
CREATE TABLE tiki_drawings (
  drawId IDENTITY int(12) NOT NULL,
  version numeric(8) DEFAULT NULL NULL,
  name varchar(250) DEFAULT NULL NULL,
  filename_draw varchar(250) DEFAULT NULL NULL,
  filename_pad varchar(250) DEFAULT NULL NULL,
  timestamp numeric(14) DEFAULT NULL NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_drawings PRIMARY KEY (drawId)
);

--
-- Table: tiki_dsn
--

DROP TABLE tiki_dsn;
CREATE TABLE tiki_dsn (
  dsnId IDENTITY int(12) NOT NULL,
  name varchar(200) DEFAULT '' NOT NULL,
  dsn varchar(255) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_dsn PRIMARY KEY (dsnId)
);

--
-- Table: tiki_eph
--

DROP TABLE tiki_eph;
CREATE TABLE tiki_eph (
  ephId IDENTITY int(12) NOT NULL,
  title varchar(250) DEFAULT NULL NULL,
  isFile char(1) DEFAULT NULL NULL,
  filename varchar(250) DEFAULT NULL NULL,
  filetype varchar(250) DEFAULT NULL NULL,
  filesize varchar(250) DEFAULT NULL NULL,
  data longblob NULL,
  textdata longblob NULL,
  publish numeric(14) DEFAULT NULL NULL,
  hits numeric(10) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_eph PRIMARY KEY (ephId)
);

--
-- Table: tiki_extwiki
--

DROP TABLE tiki_extwiki;
CREATE TABLE tiki_extwiki (
  extwikiId IDENTITY int(12) NOT NULL,
  name varchar(200) DEFAULT '' NOT NULL,
  extwiki varchar(255) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_extwiki PRIMARY KEY (extwikiId)
);

--
-- Table: tiki_faq_questions
--

DROP TABLE tiki_faq_questions;
CREATE TABLE tiki_faq_questions (
  questionId IDENTITY int(10) NOT NULL,
  faqId numeric(10) DEFAULT NULL NULL,
  position numeric(4) DEFAULT NULL NULL,
  question varchar(255) NULL,
  answer varchar(255) NULL,
  CONSTRAINT pk_tiki_faq_questions PRIMARY KEY (questionId)
);

CREATE INDEX faqId ON tiki_faq_questions (faqId);

CREATE INDEX question ON tiki_faq_questions (question(255));

CREATE INDEX answer ON tiki_faq_questions (answer(255));

CREATE INDEX ft ON tiki_faq_questions (question, answer);

--
-- Table: tiki_faqs
--

DROP TABLE tiki_faqs;
CREATE TABLE tiki_faqs (
  faqId IDENTITY int(10) NOT NULL,
  title varchar(200) DEFAULT NULL NULL,
  description varchar(255) NULL,
  created numeric(14) DEFAULT NULL NULL,
  questions numeric(5) DEFAULT NULL NULL,
  hits numeric(8) DEFAULT NULL NULL,
  canSuggest char(1) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_faqs PRIMARY KEY (faqId)
);

CREATE INDEX title ON tiki_faqs (title);

CREATE INDEX description ON tiki_faqs (description(255));

CREATE INDEX hits ON tiki_faqs (hits);

CREATE INDEX ft ON tiki_faqs (title, description);

--
-- Table: tiki_featured_links
--

DROP TABLE tiki_featured_links;
CREATE TABLE tiki_featured_links (
  url varchar(200) DEFAULT '' NOT NULL,
  title varchar(200) DEFAULT NULL NULL,
  description varchar(255) NULL,
  hits numeric(8) DEFAULT NULL NULL,
  position numeric(6) DEFAULT NULL NULL,
  type char(1) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_featured_links PRIMARY KEY (url)
);

--
-- Table: tiki_file_galleries
--

DROP TABLE tiki_file_galleries;
CREATE TABLE tiki_file_galleries (
  galleryId IDENTITY int(14) NOT NULL,
  name varchar(80) DEFAULT '' NOT NULL,
  description varchar(255) NULL,
  created numeric(14) DEFAULT NULL NULL,
  visible char(1) DEFAULT NULL NULL,
  lastModif numeric(14) DEFAULT NULL NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  hits numeric(14) DEFAULT NULL NULL,
  votes numeric(8) DEFAULT NULL NULL,
  points decimal(8,2) DEFAULT NULL NULL,
  maxRows numeric(10) DEFAULT NULL NULL,
  public_ char(1) DEFAULT NULL NULL,
  show_id char(1) DEFAULT NULL NULL,
  show_icon char(1) DEFAULT NULL NULL,
  show_name char(1) DEFAULT NULL NULL,
  show_size char(1) DEFAULT NULL NULL,
  show_description char(1) DEFAULT NULL NULL,
  max_desc numeric(8) DEFAULT NULL NULL,
  show_created char(1) DEFAULT NULL NULL,
  show_dl char(1) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_file_galleries PRIMARY KEY (galleryId)
);

--
-- Table: tiki_files
--

DROP TABLE tiki_files;
CREATE TABLE tiki_files (
  fileId IDENTITY int(14) NOT NULL,
  galleryId numeric(14) DEFAULT '0' NOT NULL,
  name varchar(200) DEFAULT '' NOT NULL,
  description varchar(255) NULL,
  created numeric(14) DEFAULT NULL NULL,
  filename varchar(80) DEFAULT NULL NULL,
  filesize numeric(14) DEFAULT NULL NULL,
  filetype varchar(250) DEFAULT NULL NULL,
  data longblob NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  downloads numeric(14) DEFAULT NULL NULL,
  votes numeric(8) DEFAULT NULL NULL,
  points decimal(8,2) DEFAULT NULL NULL,
  path varchar(255) DEFAULT NULL NULL,
  reference_url varchar(250) DEFAULT NULL NULL,
  is_reference char(1) DEFAULT NULL NULL,
  hash varchar(32) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_files PRIMARY KEY (fileId)
);

CREATE INDEX name ON tiki_files (name);

CREATE INDEX description ON tiki_files (description(255));

CREATE INDEX downloads ON tiki_files (downloads);

CREATE INDEX ft ON tiki_files (name, description);

--
-- Table: tiki_forum_attachments
--

DROP TABLE tiki_forum_attachments;
CREATE TABLE tiki_forum_attachments (
  attId IDENTITY int(14) NOT NULL,
  threadId numeric(14) DEFAULT '0' NOT NULL,
  qId numeric(14) DEFAULT '0' NOT NULL,
  forumId numeric(14) DEFAULT NULL NULL,
  filename varchar(250) DEFAULT NULL NULL,
  filetype varchar(250) DEFAULT NULL NULL,
  filesize numeric(12) DEFAULT NULL NULL,
  data longblob NULL,
  dir varchar(200) DEFAULT NULL NULL,
  created numeric(14) DEFAULT NULL NULL,
  path varchar(250) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_forum_attachments PRIMARY KEY (attId)
);

--
-- Table: tiki_forum_reads
--

DROP TABLE tiki_forum_reads;
CREATE TABLE tiki_forum_reads (
  user_ varchar(200) DEFAULT '' NOT NULL,
  threadId numeric(14) DEFAULT '0' NOT NULL,
  forumId numeric(14) DEFAULT NULL NULL,
  timestamp numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_forum_reads PRIMARY KEY (user_, threadId)
);

--
-- Table: tiki_forums
--

DROP TABLE tiki_forums;
CREATE TABLE tiki_forums (
  forumId IDENTITY int(8) NOT NULL,
  name varchar(200) DEFAULT NULL NULL,
  description varchar(255) NULL,
  created numeric(14) DEFAULT NULL NULL,
  lastPost numeric(14) DEFAULT NULL NULL,
  threads numeric(8) DEFAULT NULL NULL,
  comments numeric(8) DEFAULT NULL NULL,
  controlFlood char(1) DEFAULT NULL NULL,
  floodInterval numeric(8) DEFAULT NULL NULL,
  moderator varchar(200) DEFAULT NULL NULL,
  hits numeric(8) DEFAULT NULL NULL,
  mail varchar(200) DEFAULT NULL NULL,
  useMail char(1) DEFAULT NULL NULL,
  section varchar(200) DEFAULT NULL NULL,
  usePruneUnreplied char(1) DEFAULT NULL NULL,
  pruneUnrepliedAge numeric(8) DEFAULT NULL NULL,
  usePruneOld char(1) DEFAULT NULL NULL,
  pruneMaxAge numeric(8) DEFAULT NULL NULL,
  topicsPerPage numeric(6) DEFAULT NULL NULL,
  topicOrdering varchar(100) DEFAULT NULL NULL,
  threadOrdering varchar(100) DEFAULT NULL NULL,
  att varchar(80) DEFAULT NULL NULL,
  att_store varchar(4) DEFAULT NULL NULL,
  att_store_dir varchar(250) DEFAULT NULL NULL,
  att_max_size numeric(12) DEFAULT NULL NULL,
  ui_level char(1) DEFAULT NULL NULL,
  forum_password varchar(32) DEFAULT NULL NULL,
  forum_use_password char(1) DEFAULT NULL NULL,
  moderator_group varchar(200) DEFAULT NULL NULL,
  approval_type varchar(20) DEFAULT NULL NULL,
  outbound_address varchar(250) DEFAULT NULL NULL,
  outbound_mails_for_inbound_mails char(1) DEFAULT NULL NULL,
  outbound_mails_reply_link char(1) DEFAULT NULL NULL,
  outbound_from varchar(250) default NULL,
  inbound_pop_server varchar(250) DEFAULT NULL NULL,
  inbound_pop_port numeric(4) DEFAULT NULL NULL,
  inbound_pop_user varchar(200) DEFAULT NULL NULL,
  inbound_pop_password varchar(80) DEFAULT NULL NULL,
  topic_smileys char(1) DEFAULT NULL NULL,
  ui_avatar char(1) DEFAULT NULL NULL,
  ui_flag char(1) DEFAULT NULL NULL,
  ui_posts char(1) DEFAULT NULL NULL,
  ui_email char(1) DEFAULT NULL NULL,
  ui_online char(1) DEFAULT NULL NULL,
  topic_summary char(1) DEFAULT NULL NULL,
  show_description char(1) DEFAULT NULL NULL,
  topics_list_replies char(1) DEFAULT NULL NULL,
  topics_list_reads char(1) DEFAULT NULL NULL,
  topics_list_pts char(1) DEFAULT NULL NULL,
  topics_list_lastpost char(1) DEFAULT NULL NULL,
  topics_list_author char(1) DEFAULT NULL NULL,
  vote_threads char(1) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_forums PRIMARY KEY (forumId)
);

--
-- Table: tiki_forums_queue
--

DROP TABLE tiki_forums_queue;
CREATE TABLE tiki_forums_queue (
  qId IDENTITY int(14) NOT NULL,
  object varchar(32) DEFAULT NULL NULL,
  parentId numeric(14) DEFAULT NULL NULL,
  forumId numeric(14) DEFAULT NULL NULL,
  timestamp numeric(14) DEFAULT NULL NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  title varchar(240) DEFAULT NULL NULL,
  data varchar(255) NULL,
  type varchar(60) DEFAULT NULL NULL,
  hash varchar(32) DEFAULT NULL NULL,
  topic_smiley varchar(80) DEFAULT NULL NULL,
  topic_title varchar(240) DEFAULT NULL NULL,
  summary varchar(240) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_forums_queue PRIMARY KEY (qId)
);

--
-- Table: tiki_forums_reported
--

DROP TABLE tiki_forums_reported;
CREATE TABLE tiki_forums_reported (
  threadId numeric(12) DEFAULT '0' NOT NULL,
  forumId numeric(12) DEFAULT '0' NOT NULL,
  parentId numeric(12) DEFAULT '0' NOT NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  timestamp numeric(14) DEFAULT NULL NULL,
  reason varchar(250) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_forums_reported PRIMARY KEY (threadId)
);

--
-- Table: tiki_galleries
--

DROP TABLE tiki_galleries;
CREATE TABLE tiki_galleries (
  galleryId IDENTITY int(14) NOT NULL,
  name varchar(80) DEFAULT '' NOT NULL,
  description varchar(255) NULL,
  created numeric(14) DEFAULT NULL NULL,
  lastModif numeric(14) DEFAULT NULL NULL,
  visible char(1) DEFAULT NULL NULL,
  theme varchar(60) DEFAULT NULL NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  hits numeric(14) DEFAULT NULL NULL,
  maxRows numeric(10) DEFAULT NULL NULL,
  rowImages numeric(10) DEFAULT NULL NULL,
  thumbSizeX numeric(10) DEFAULT NULL NULL,
  thumbSizeY numeric(10) DEFAULT NULL NULL,
  public_ char(1) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_galleries PRIMARY KEY (galleryId)
);

CREATE INDEX name ON tiki_galleries (name);

CREATE INDEX description ON tiki_galleries (description(255));

CREATE INDEX hits ON tiki_galleries (hits);

CREATE INDEX ft ON tiki_galleries (name, description);

--
-- Table: tiki_galleries_scales
--

DROP TABLE tiki_galleries_scales;
CREATE TABLE tiki_galleries_scales (
  galleryId numeric(14) DEFAULT '0' NOT NULL,
  xsize numeric(11) DEFAULT '0' NOT NULL,
  ysize numeric(11) DEFAULT '0' NOT NULL,
  CONSTRAINT pk_tiki_galleries_scales PRIMARY KEY (galleryId, xsize, ysize)
);

--
-- Table: tiki_games
--

DROP TABLE tiki_games;
CREATE TABLE tiki_games (
  gameName varchar(200) DEFAULT '' NOT NULL,
  hits numeric(8) DEFAULT NULL NULL,
  votes numeric(8) DEFAULT NULL NULL,
  points numeric(8) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_games PRIMARY KEY (gameName)
);

--
-- Table: tiki_group_inclusion
--

DROP TABLE tiki_group_inclusion;
CREATE TABLE tiki_group_inclusion (
  groupName varchar(30) DEFAULT '' NOT NULL,
  includeGroup varchar(30) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_group_inclusion PRIMARY KEY (groupName, includeGroup)
);

--
-- Table: tiki_history
--

DROP TABLE tiki_history;
CREATE TABLE tiki_history (
  pageName varchar(160) DEFAULT '' NOT NULL,
  version numeric(8) DEFAULT '0' NOT NULL,
  lastModif numeric(14) DEFAULT NULL NULL,
  description varchar(200) DEFAULT NULL NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  ip varchar(15) DEFAULT NULL NULL,
  comment varchar(200) DEFAULT NULL NULL,
  data longblob NULL,
  CONSTRAINT pk_tiki_history PRIMARY KEY (pageName, version)
);

--
-- Table: tiki_hotwords
--

DROP TABLE tiki_hotwords;
CREATE TABLE tiki_hotwords (
  word varchar(40) DEFAULT '' NOT NULL,
  url varchar(255) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_hotwords PRIMARY KEY (word)
);

--
-- Table: tiki_html_pages
--

DROP TABLE tiki_html_pages;
CREATE TABLE tiki_html_pages (
  pageName varchar(200) DEFAULT '' NOT NULL,
  content longblob NULL,
  refresh numeric(10) DEFAULT NULL NULL,
  type char(1) DEFAULT NULL NULL,
  created numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_html_pages PRIMARY KEY (pageName)
);

--
-- Table: tiki_html_pages_dynamic_zones
--

DROP TABLE tiki_html_pages_dynamic_zones;
CREATE TABLE tiki_html_pages_dynamic_zones (
  pageName varchar(40) DEFAULT '' NOT NULL,
  zone varchar(80) DEFAULT '' NOT NULL,
  type char(2) DEFAULT NULL NULL,
  content varchar(255) NULL,
  CONSTRAINT pk_tiki_html_pages_dynamic_zon PRIMARY KEY (pageName, zone)
);

--
-- Table: tiki_images
--

DROP TABLE tiki_images;
CREATE TABLE tiki_images (
  imageId IDENTITY int(14) NOT NULL,
  galleryId numeric(14) DEFAULT '0' NOT NULL,
  name varchar(200) DEFAULT '' NOT NULL,
  description varchar(255) NULL,
  created numeric(14) DEFAULT NULL NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  hits numeric(14) DEFAULT NULL NULL,
  path varchar(255) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_images PRIMARY KEY (imageId)
);

CREATE INDEX name ON tiki_images (name);

CREATE INDEX description ON tiki_images (description(255));

CREATE INDEX hits ON tiki_images (hits);

CREATE INDEX ti_gId ON tiki_images (galleryId);

CREATE INDEX ti_cr ON tiki_images (created);

CREATE INDEX ti_us ON tiki_images (user);

CREATE INDEX ft ON tiki_images (name, description);

--
-- Table: tiki_images_data
--

DROP TABLE tiki_images_data;
CREATE TABLE tiki_images_data (
  imageId numeric(14) DEFAULT '0' NOT NULL,
  xsize numeric(8) DEFAULT '0' NOT NULL,
  ysize numeric(8) DEFAULT '0' NOT NULL,
  type char(1) DEFAULT '' NOT NULL,
  filesize numeric(14) DEFAULT NULL NULL,
  filetype varchar(80) DEFAULT NULL NULL,
  filename varchar(80) DEFAULT NULL NULL,
  data longblob NULL,
  CONSTRAINT pk_tiki_images_data PRIMARY KEY (imageId, xsize, ysize, type)
);

CREATE INDEX t_i_d_it ON tiki_images_data (imageId, type);

--
-- Table: tiki_language
--

DROP TABLE tiki_language;
CREATE TABLE tiki_language (
  source tinyblob NOT NULL,
  lang char(2) DEFAULT '' NOT NULL,
  tran tinyblob NULL,
  CONSTRAINT pk_tiki_language PRIMARY KEY (source, lang)
);

--
-- Table: tiki_languages
--

DROP TABLE tiki_languages;
CREATE TABLE tiki_languages (
  lang char(2) DEFAULT '' NOT NULL,
  language varchar(255) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_languages PRIMARY KEY (lang)
);

--
-- Table: tiki_link_cache
--

DROP TABLE tiki_link_cache;
CREATE TABLE tiki_link_cache (
  cacheId IDENTITY int(14) NOT NULL,
  url varchar(250) DEFAULT NULL NULL,
  data longblob NULL,
  refresh numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_link_cache PRIMARY KEY (cacheId)
);

--
-- Table: tiki_links
--

DROP TABLE tiki_links;
CREATE TABLE tiki_links (
  fromPage varchar(160) DEFAULT '' NOT NULL,
  toPage varchar(160) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_links PRIMARY KEY (fromPage, toPage)
);

--
-- Table: tiki_live_support_events
--

DROP TABLE tiki_live_support_events;
CREATE TABLE tiki_live_support_events (
  eventId IDENTITY int(14) NOT NULL,
  reqId varchar(32) DEFAULT '' NOT NULL,
  type varchar(40) DEFAULT NULL NULL,
  seqId numeric(14) DEFAULT NULL NULL,
  senderId varchar(32) DEFAULT NULL NULL,
  data varchar(255) NULL,
  timestamp numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_live_support_events PRIMARY KEY (eventId)
);

--
-- Table: tiki_live_support_message_comm
--

DROP TABLE tiki_live_support_message_comm;
CREATE TABLE tiki_live_support_message_comm (
  cId IDENTITY int(12) NOT NULL,
  msgId numeric(12) DEFAULT NULL NULL,
  data varchar(255) NULL,
  timestamp numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_live_support_message_c PRIMARY KEY (cId)
);

--
-- Table: tiki_live_support_messages
--

DROP TABLE tiki_live_support_messages;
CREATE TABLE tiki_live_support_messages (
  msgId IDENTITY int(12) NOT NULL,
  data varchar(255) NULL,
  timestamp numeric(14) DEFAULT NULL NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  username varchar(200) DEFAULT NULL NULL,
  priority numeric(2) DEFAULT NULL NULL,
  status char(1) DEFAULT NULL NULL,
  assigned_to varchar(200) DEFAULT NULL NULL,
  resolution varchar(100) DEFAULT NULL NULL,
  title varchar(200) DEFAULT NULL NULL,
  module numeric(4) DEFAULT NULL NULL,
  email varchar(250) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_live_support_messages PRIMARY KEY (msgId)
);

--
-- Table: tiki_live_support_modules
--

DROP TABLE tiki_live_support_modules;
CREATE TABLE tiki_live_support_modules (
  modId IDENTITY int(4) NOT NULL,
  name varchar(90) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_live_support_modules PRIMARY KEY (modId)
);

--
-- Table: tiki_live_support_operators
--

DROP TABLE tiki_live_support_operators;
CREATE TABLE tiki_live_support_operators (
  user_ varchar(200) DEFAULT '' NOT NULL,
  accepted_requests numeric(10) DEFAULT NULL NULL,
  status varchar(20) DEFAULT NULL NULL,
  longest_chat numeric(10) DEFAULT NULL NULL,
  shortest_chat numeric(10) DEFAULT NULL NULL,
  average_chat numeric(10) DEFAULT NULL NULL,
  last_chat numeric(14) DEFAULT NULL NULL,
  time_online numeric(10) DEFAULT NULL NULL,
  votes numeric(10) DEFAULT NULL NULL,
  points numeric(10) DEFAULT NULL NULL,
  status_since numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_live_support_operators PRIMARY KEY (user_)
);

--
-- Table: tiki_live_support_requests
--

DROP TABLE tiki_live_support_requests;
CREATE TABLE tiki_live_support_requests (
  reqId varchar(32) DEFAULT '' NOT NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  tiki_user varchar(200) DEFAULT NULL NULL,
  email varchar(200) DEFAULT NULL NULL,
  operator varchar(200) DEFAULT NULL NULL,
  operator_id varchar(32) DEFAULT NULL NULL,
  user_id varchar(32) DEFAULT NULL NULL,
  reason varchar(255) NULL,
  req_timestamp numeric(14) DEFAULT NULL NULL,
  timestamp numeric(14) DEFAULT NULL NULL,
  status varchar(40) DEFAULT NULL NULL,
  resolution varchar(40) DEFAULT NULL NULL,
  chat_started numeric(14) DEFAULT NULL NULL,
  chat_ended numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_live_support_requests PRIMARY KEY (reqId)
);

--
-- Table: tiki_mail_events
--

DROP TABLE tiki_mail_events;
CREATE TABLE tiki_mail_events (
  event varchar(200) DEFAULT NULL NULL,
  object varchar(200) DEFAULT NULL NULL,
  email varchar(200) DEFAULT NULL NULL
);

--
-- Table: tiki_mailin_accounts
--

DROP TABLE tiki_mailin_accounts;
CREATE TABLE tiki_mailin_accounts (
  accountId IDENTITY int(12) NOT NULL,
  user_ varchar(200) DEFAULT '' NOT NULL,
  account varchar(50) DEFAULT '' NOT NULL,
  pop varchar(255) DEFAULT NULL NULL,
  port numeric(4) DEFAULT NULL NULL,
  username varchar(100) DEFAULT NULL NULL,
  pass varchar(100) DEFAULT NULL NULL,
  active char(1) DEFAULT NULL NULL,
  type varchar(40) DEFAULT NULL NULL,
  smtp varchar(255) DEFAULT NULL NULL,
  useAuth char(1) DEFAULT NULL NULL,
  smtpPort numeric(4) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_mailin_accounts PRIMARY KEY (accountId)
);

--
-- Table: tiki_menu_languages
--

DROP TABLE tiki_menu_languages;
CREATE TABLE tiki_menu_languages (
  menuId IDENTITY int(8) NOT NULL,
  language char(2) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_menu_languages PRIMARY KEY (menuId, language)
);

--
-- Table: tiki_menu_options
--

DROP TABLE tiki_menu_options;
CREATE TABLE tiki_menu_options (
  optionId IDENTITY int(8) NOT NULL,
  menuId numeric(8) DEFAULT NULL NULL,
  type char(1) DEFAULT NULL NULL,
  name varchar(200) DEFAULT NULL NULL,
  url varchar(255) DEFAULT NULL NULL,
  position numeric(4) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_menu_options PRIMARY KEY (optionId)
);

--
-- Table: tiki_menus
--

DROP TABLE tiki_menus;
CREATE TABLE tiki_menus (
  menuId IDENTITY int(8) NOT NULL,
  name varchar(200) DEFAULT '' NOT NULL,
  description varchar(255) NULL,
  type char(1) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_menus PRIMARY KEY (menuId)
);

--
-- Table: tiki_minical_events
--

DROP TABLE tiki_minical_events;
CREATE TABLE tiki_minical_events (
  user_ varchar(200) DEFAULT NULL NULL,
  eventId IDENTITY int(12) NOT NULL,
  title varchar(250) DEFAULT NULL NULL,
  description varchar(255) NULL,
  start numeric(14) DEFAULT NULL NULL,
  end_ numeric(14) DEFAULT NULL NULL,
  security char(1) DEFAULT NULL NULL,
  duration numeric(3) DEFAULT NULL NULL,
  topicId numeric(12) DEFAULT NULL NULL,
  reminded char(1) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_minical_events PRIMARY KEY (eventId)
);

--
-- Table: tiki_minical_topics
--

DROP TABLE tiki_minical_topics;
CREATE TABLE tiki_minical_topics (
  user_ varchar(200) DEFAULT NULL NULL,
  topicId IDENTITY int(12) NOT NULL,
  name varchar(250) DEFAULT NULL NULL,
  filename varchar(200) DEFAULT NULL NULL,
  filetype varchar(200) DEFAULT NULL NULL,
  filesize varchar(200) DEFAULT NULL NULL,
  data longblob NULL,
  path varchar(250) DEFAULT NULL NULL,
  isIcon char(1) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_minical_topics PRIMARY KEY (topicId)
);

--
-- Table: tiki_modules
--

DROP TABLE tiki_modules;
CREATE TABLE tiki_modules (
  name varchar(200) DEFAULT '' NOT NULL,
  position char(1) DEFAULT NULL NULL,
  ord numeric(4) DEFAULT NULL NULL,
  type char(1) DEFAULT NULL NULL,
  title varchar(40) DEFAULT NULL NULL,
  cache_time numeric(14) DEFAULT NULL NULL,
  rows numeric(4) DEFAULT NULL NULL,
  params varchar(255) DEFAULT NULL NULL,
  groups varchar(255) NULL,
  CONSTRAINT pk_tiki_modules PRIMARY KEY (name)
);

--
-- Table: tiki_newsletter_subscriptions
--

DROP TABLE tiki_newsletter_subscriptions;
CREATE TABLE tiki_newsletter_subscriptions (
  nlId numeric(12) DEFAULT '0' NOT NULL,
  email varchar(255) DEFAULT '' NOT NULL,
  code varchar(32) DEFAULT NULL NULL,
  valid char(1) DEFAULT NULL NULL,
  subscribed numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_newsletter_subscriptio PRIMARY KEY (nlId, email)
);

--
-- Table: tiki_newsletters
--

DROP TABLE tiki_newsletters;
CREATE TABLE tiki_newsletters (
  nlId IDENTITY int(12) NOT NULL,
  name varchar(200) DEFAULT NULL NULL,
  description varchar(255) NULL,
  created numeric(14) DEFAULT NULL NULL,
  lastSent numeric(14) DEFAULT NULL NULL,
  editions numeric(10) DEFAULT NULL NULL,
  users numeric(10) DEFAULT NULL NULL,
  allowAnySub char(1) DEFAULT NULL NULL,
  frequency numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_newsletters PRIMARY KEY (nlId)
);

--
-- Table: tiki_newsreader_marks
--

DROP TABLE tiki_newsreader_marks;
CREATE TABLE tiki_newsreader_marks (
  user_ varchar(200) DEFAULT '' NOT NULL,
  serverId numeric(12) DEFAULT '0' NOT NULL,
  groupName varchar(255) DEFAULT '' NOT NULL,
  timestamp numeric(14) DEFAULT '0' NOT NULL,
  CONSTRAINT pk_tiki_newsreader_marks PRIMARY KEY (user_, serverId, groupName)
);

--
-- Table: tiki_newsreader_servers
--

DROP TABLE tiki_newsreader_servers;
CREATE TABLE tiki_newsreader_servers (
  user_ varchar(200) DEFAULT '' NOT NULL,
  serverId IDENTITY int(12) NOT NULL,
  server varchar(250) DEFAULT NULL NULL,
  port numeric(4) DEFAULT NULL NULL,
  username varchar(200) DEFAULT NULL NULL,
  password varchar(200) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_newsreader_servers PRIMARY KEY (serverId)
);

--
-- Table: tiki_page_footnotes
--

DROP TABLE tiki_page_footnotes;
CREATE TABLE tiki_page_footnotes (
  user_ varchar(200) DEFAULT '' NOT NULL,
  pageName varchar(250) DEFAULT '' NOT NULL,
  data varchar(255) NULL,
  CONSTRAINT pk_tiki_page_footnotes PRIMARY KEY (user_, pageName)
);

--
-- Table: tiki_pages
--

DROP TABLE tiki_pages;
CREATE TABLE tiki_pages (
  pageName varchar(160) DEFAULT '' NOT NULL,
  hits numeric(8) DEFAULT NULL NULL,
  data varchar(255) NULL,
  description varchar(200) DEFAULT NULL NULL,
  lastModif numeric(14) DEFAULT NULL NULL,
  comment varchar(200) DEFAULT NULL NULL,
  version numeric(8) DEFAULT '0' NOT NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  ip varchar(15) DEFAULT NULL NULL,
  flag char(1) DEFAULT NULL NULL,
  points numeric(8) DEFAULT NULL NULL,
  votes numeric(8) DEFAULT NULL NULL,
  cache varchar(255) NULL,
  cache_timestamp numeric(14) DEFAULT NULL NULL,
  pageRank decimal(4,3) DEFAULT NULL NULL,
  creator varchar(200) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_pages PRIMARY KEY (pageName)
);

CREATE INDEX data ON tiki_pages (data(255));

CREATE INDEX pageRank ON tiki_pages (pageRank);

CREATE INDEX ft ON tiki_pages (pageName, data);

--
-- Table: tiki_pageviews
--

DROP TABLE tiki_pageviews;
CREATE TABLE tiki_pageviews (
  day numeric(14) DEFAULT '0' NOT NULL,
  pageviews numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_pageviews PRIMARY KEY (day)
);

--
-- Table: tiki_poll_options
--

DROP TABLE tiki_poll_options;
CREATE TABLE tiki_poll_options (
  pollId numeric(8) DEFAULT '0' NOT NULL,
  optionId IDENTITY int(8) NOT NULL,
  title varchar(200) DEFAULT NULL NULL,
  votes numeric(8) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_poll_options PRIMARY KEY (optionId)
);

--
-- Table: tiki_polls
--

DROP TABLE tiki_polls;
CREATE TABLE tiki_polls (
  pollId IDENTITY int(8) NOT NULL,
  title varchar(200) DEFAULT NULL NULL,
  votes numeric(8) DEFAULT NULL NULL,
  active char(1) DEFAULT NULL NULL,
  publishDate numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_polls PRIMARY KEY (pollId)
);

--
-- Table: tiki_preferences
--

DROP TABLE tiki_preferences;
CREATE TABLE tiki_preferences (
  name varchar(40) DEFAULT '' NOT NULL,
  value varchar(250) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_preferences PRIMARY KEY (name)
);

--
-- Table: tiki_private_messages
--

DROP TABLE tiki_private_messages;
CREATE TABLE tiki_private_messages (
  messageId IDENTITY int(8) NOT NULL,
  toNickname varchar(200) DEFAULT '' NOT NULL,
  data varchar(255) DEFAULT NULL NULL,
  poster varchar(200) DEFAULT 'anonymous' NOT NULL,
  timestamp numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_private_messages PRIMARY KEY (messageId)
);

--
-- Table: tiki_programmed_content
--

DROP TABLE tiki_programmed_content;
CREATE TABLE tiki_programmed_content (
  pId IDENTITY int(8) NOT NULL,
  contentId numeric(8) DEFAULT '0' NOT NULL,
  publishDate numeric(14) DEFAULT '0' NOT NULL,
  data varchar(255) NULL,
  CONSTRAINT pk_tiki_programmed_content PRIMARY KEY (pId)
);

--
-- Table: tiki_quiz_question_options
--

DROP TABLE tiki_quiz_question_options;
CREATE TABLE tiki_quiz_question_options (
  optionId IDENTITY int(10) NOT NULL,
  questionId numeric(10) DEFAULT NULL NULL,
  optionText varchar(255) NULL,
  points numeric(4) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_quiz_question_options PRIMARY KEY (optionId)
);

--
-- Table: tiki_quiz_questions
--

DROP TABLE tiki_quiz_questions;
CREATE TABLE tiki_quiz_questions (
  questionId IDENTITY int(10) NOT NULL,
  quizId numeric(10) DEFAULT NULL NULL,
  question varchar(255) NULL,
  position numeric(4) DEFAULT NULL NULL,
  type char(1) DEFAULT NULL NULL,
  maxPoints numeric(4) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_quiz_questions PRIMARY KEY (questionId)
);

--
-- Table: tiki_quiz_results
--

DROP TABLE tiki_quiz_results;
CREATE TABLE tiki_quiz_results (
  resultId IDENTITY int(10) NOT NULL,
  quizId numeric(10) DEFAULT NULL NULL,
  fromPoints numeric(4) DEFAULT NULL NULL,
  toPoints numeric(4) DEFAULT NULL NULL,
  answer varchar(255) NULL,
  CONSTRAINT pk_tiki_quiz_results PRIMARY KEY (resultId)
);

--
-- Table: tiki_quiz_stats
--

DROP TABLE tiki_quiz_stats;
CREATE TABLE tiki_quiz_stats (
  quizId numeric(10) DEFAULT '0' NOT NULL,
  questionId numeric(10) DEFAULT '0' NOT NULL,
  optionId numeric(10) DEFAULT '0' NOT NULL,
  votes numeric(10) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_quiz_stats PRIMARY KEY (quizId, questionId, optionId)
);

--
-- Table: tiki_quiz_stats_sum
--

DROP TABLE tiki_quiz_stats_sum;
CREATE TABLE tiki_quiz_stats_sum (
  quizId numeric(10) DEFAULT '0' NOT NULL,
  quizName varchar(255) DEFAULT NULL NULL,
  timesTaken numeric(10) DEFAULT NULL NULL,
  avgpoints decimal(5,2) DEFAULT NULL NULL,
  avgavg decimal(5,2) DEFAULT NULL NULL,
  avgtime decimal(5,2) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_quiz_stats_sum PRIMARY KEY (quizId)
);

--
-- Table: tiki_quizzes
--

DROP TABLE tiki_quizzes;
CREATE TABLE tiki_quizzes (
  quizId IDENTITY int(10) NOT NULL,
  name varchar(255) DEFAULT NULL NULL,
  description varchar(255) NULL,
  canRepeat char(1) DEFAULT NULL NULL,
  storeResults char(1) DEFAULT NULL NULL,
  questionsPerPage numeric(4) DEFAULT NULL NULL,
  timeLimited char(1) DEFAULT NULL NULL,
  timeLimit numeric(14) DEFAULT NULL NULL,
  created numeric(14) DEFAULT NULL NULL,
  taken numeric(10) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_quizzes PRIMARY KEY (quizId)
);

--
-- Table: tiki_received_articles
--

DROP TABLE tiki_received_articles;
CREATE TABLE tiki_received_articles (
  receivedArticleId IDENTITY int(14) NOT NULL,
  receivedFromSite varchar(200) DEFAULT NULL NULL,
  receivedFromUser varchar(200) DEFAULT NULL NULL,
  receivedDate numeric(14) DEFAULT NULL NULL,
  title varchar(80) DEFAULT NULL NULL,
  authorName varchar(60) DEFAULT NULL NULL,
  size numeric(12) DEFAULT NULL NULL,
  useImage char(1) DEFAULT NULL NULL,
  image_name varchar(80) DEFAULT NULL NULL,
  image_type varchar(80) DEFAULT NULL NULL,
  image_size numeric(14) DEFAULT NULL NULL,
  image_x numeric(4) DEFAULT NULL NULL,
  image_y numeric(4) DEFAULT NULL NULL,
  image_data longblob NULL,
  publishDate numeric(14) DEFAULT NULL NULL,
  created numeric(14) DEFAULT NULL NULL,
  heading varchar(255) NULL,
  body longblob NULL,
  hash varchar(32) DEFAULT NULL NULL,
  author varchar(200) DEFAULT NULL NULL,
  type varchar(50) DEFAULT NULL NULL,
  rating decimal(3,2) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_received_articles PRIMARY KEY (receivedArticleId)
);

--
-- Table: tiki_received_pages
--

DROP TABLE tiki_received_pages;
CREATE TABLE tiki_received_pages (
  receivedPageId IDENTITY int(14) NOT NULL,
  pageName varchar(160) DEFAULT '' NOT NULL,
  data longblob NULL,
  description varchar(200) DEFAULT NULL NULL,
  comment varchar(200) DEFAULT NULL NULL,
  receivedFromSite varchar(200) DEFAULT NULL NULL,
  receivedFromUser varchar(200) DEFAULT NULL NULL,
  receivedDate numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_received_pages PRIMARY KEY (receivedPageId)
);

--
-- Table: tiki_referer_stats
--

DROP TABLE tiki_referer_stats;
CREATE TABLE tiki_referer_stats (
  referer varchar(50) DEFAULT '' NOT NULL,
  hits numeric(10) DEFAULT NULL NULL,
  last numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_referer_stats PRIMARY KEY (referer)
);

--
-- Table: tiki_related_categories
--

DROP TABLE tiki_related_categories;
CREATE TABLE tiki_related_categories (
  categId numeric(10) DEFAULT '0' NOT NULL,
  relatedTo numeric(10) DEFAULT '0' NOT NULL,
  CONSTRAINT pk_tiki_related_categories PRIMARY KEY (categId, relatedTo)
);

--
-- Table: tiki_rss_modules
--

DROP TABLE tiki_rss_modules;
CREATE TABLE tiki_rss_modules (
  rssId IDENTITY int(8) NOT NULL,
  name varchar(30) DEFAULT '' NOT NULL,
  description varchar(255) NULL,
  url varchar(255) DEFAULT '' NOT NULL,
  refresh numeric(8) DEFAULT NULL NULL,
  lastUpdated numeric(14) DEFAULT NULL NULL,
  content longblob NULL,
  CONSTRAINT pk_tiki_rss_modules PRIMARY KEY (rssId)
);

--
-- Table: tiki_search_stats
--

DROP TABLE tiki_search_stats;
CREATE TABLE tiki_search_stats (
  term varchar(50) DEFAULT '' NOT NULL,
  hits numeric(10) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_search_stats PRIMARY KEY (term)
);

--
-- Table: tiki_semaphores
--

DROP TABLE tiki_semaphores;
CREATE TABLE tiki_semaphores (
  semName varchar(250) DEFAULT '' NOT NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  timestamp numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_semaphores PRIMARY KEY (semName)
);

--
-- Table: tiki_sent_newsletters
--

DROP TABLE tiki_sent_newsletters;
CREATE TABLE tiki_sent_newsletters (
  editionId IDENTITY int(12) NOT NULL,
  nlId numeric(12) DEFAULT '0' NOT NULL,
  users numeric(10) DEFAULT NULL NULL,
  sent numeric(14) DEFAULT NULL NULL,
  subject varchar(200) DEFAULT NULL NULL,
  data longblob NULL,
  CONSTRAINT pk_tiki_sent_newsletters PRIMARY KEY (editionId)
);

--
-- Table: tiki_sessions
--

DROP TABLE tiki_sessions;
CREATE TABLE tiki_sessions (
  sessionId varchar(32) DEFAULT '' NOT NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  timestamp numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_sessions PRIMARY KEY (sessionId)
);

--
-- Table: tiki_shoutbox
--

DROP TABLE tiki_shoutbox;
CREATE TABLE tiki_shoutbox (
  msgId IDENTITY int(10) NOT NULL,
  message varchar(255) DEFAULT NULL NULL,
  timestamp numeric(14) DEFAULT NULL NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  hash varchar(32) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_shoutbox PRIMARY KEY (msgId)
);

--
-- Table: tiki_structures
--

DROP TABLE tiki_structures;
CREATE TABLE tiki_structures (
  page varchar(240) DEFAULT '' NOT NULL,
  parent varchar(240) DEFAULT '' NOT NULL,
  pos numeric(4) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_structures PRIMARY KEY (page, parent)
);

--
-- Table: tiki_submissions
--

DROP TABLE tiki_submissions;
CREATE TABLE tiki_submissions (
  subId IDENTITY int(8) NOT NULL,
  title varchar(80) DEFAULT NULL NULL,
  authorName varchar(60) DEFAULT NULL NULL,
  topicId numeric(14) DEFAULT NULL NULL,
  topicName varchar(40) DEFAULT NULL NULL,
  size numeric(12) DEFAULT NULL NULL,
  useImage char(1) DEFAULT NULL NULL,
  image_name varchar(80) DEFAULT NULL NULL,
  image_type varchar(80) DEFAULT NULL NULL,
  image_size numeric(14) DEFAULT NULL NULL,
  image_x numeric(4) DEFAULT NULL NULL,
  image_y numeric(4) DEFAULT NULL NULL,
  image_data longblob NULL,
  publishDate numeric(14) DEFAULT NULL NULL,
  created numeric(14) DEFAULT NULL NULL,
  heading varchar(255) NULL,
  body varchar(255) NULL,
  hash varchar(32) DEFAULT NULL NULL,
  author varchar(200) DEFAULT NULL NULL,
  reads numeric(14) DEFAULT NULL NULL,
  votes numeric(8) DEFAULT NULL NULL,
  points numeric(14) DEFAULT NULL NULL,
  type varchar(50) DEFAULT NULL NULL,
  rating decimal(3,2) DEFAULT NULL NULL,
  isfloat char(1) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_submissions PRIMARY KEY (subId)
);

--
-- Table: tiki_suggested_faq_questions
--

DROP TABLE tiki_suggested_faq_questions;
CREATE TABLE tiki_suggested_faq_questions (
  sfqId IDENTITY int(10) NOT NULL,
  faqId numeric(10) DEFAULT '0' NOT NULL,
  question varchar(255) NULL,
  answer varchar(255) NULL,
  created numeric(14) DEFAULT NULL NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_suggested_faq_question PRIMARY KEY (sfqId)
);

--
-- Table: tiki_survey_question_options
--

DROP TABLE tiki_survey_question_options;
CREATE TABLE tiki_survey_question_options (
  optionId IDENTITY int(12) NOT NULL,
  questionId numeric(12) DEFAULT '0' NOT NULL,
  qoption varchar(255) NULL,
  votes numeric(10) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_survey_question_option PRIMARY KEY (optionId)
);

--
-- Table: tiki_survey_questions
--

DROP TABLE tiki_survey_questions;
CREATE TABLE tiki_survey_questions (
  questionId IDENTITY int(12) NOT NULL,
  surveyId numeric(12) DEFAULT '0' NOT NULL,
  question varchar(255) NULL,
  options varchar(255) NULL,
  type char(1) DEFAULT NULL NULL,
  position numeric(5) DEFAULT NULL NULL,
  votes numeric(10) DEFAULT NULL NULL,
  value numeric(10) DEFAULT NULL NULL,
  average decimal(4,2) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_survey_questions PRIMARY KEY (questionId)
);

--
-- Table: tiki_surveys
--

DROP TABLE tiki_surveys;
CREATE TABLE tiki_surveys (
  surveyId IDENTITY int(12) NOT NULL,
  name varchar(200) DEFAULT NULL NULL,
  description varchar(255) NULL,
  taken numeric(10) DEFAULT NULL NULL,
  lastTaken numeric(14) DEFAULT NULL NULL,
  created numeric(14) DEFAULT NULL NULL,
  status char(1) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_surveys PRIMARY KEY (surveyId)
);

--
-- Table: tiki_tags
--

DROP TABLE tiki_tags;
CREATE TABLE tiki_tags (
  tagName varchar(80) DEFAULT '' NOT NULL,
  pageName varchar(160) DEFAULT '' NOT NULL,
  hits numeric(8) DEFAULT NULL NULL,
  description varchar(200) DEFAULT NULL NULL,
  data longblob NULL,
  lastModif numeric(14) DEFAULT NULL NULL,
  comment varchar(200) DEFAULT NULL NULL,
  version numeric(8) DEFAULT '0' NOT NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  ip varchar(15) DEFAULT NULL NULL,
  flag char(1) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_tags PRIMARY KEY (tagName, pageName)
);

--
-- Table: tiki_theme_control_categs
--

DROP TABLE tiki_theme_control_categs;
CREATE TABLE tiki_theme_control_categs (
  categId numeric(12) DEFAULT '0' NOT NULL,
  theme varchar(250) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_theme_control_categs PRIMARY KEY (categId)
);

--
-- Table: tiki_theme_control_objects
--

DROP TABLE tiki_theme_control_objects;
CREATE TABLE tiki_theme_control_objects (
  objId varchar(250) DEFAULT '' NOT NULL,
  type varchar(250) DEFAULT '' NOT NULL,
  name varchar(250) DEFAULT '' NOT NULL,
  theme varchar(250) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_theme_control_objects PRIMARY KEY (objId)
);

--
-- Table: tiki_theme_control_sections
--

DROP TABLE tiki_theme_control_sections;
CREATE TABLE tiki_theme_control_sections (
  section varchar(250) DEFAULT '' NOT NULL,
  theme varchar(250) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_theme_control_sections PRIMARY KEY (section)
);

--
-- Table: tiki_topics
--

DROP TABLE tiki_topics;
CREATE TABLE tiki_topics (
  topicId IDENTITY int(14) NOT NULL,
  name varchar(40) DEFAULT NULL NULL,
  image_name varchar(80) DEFAULT NULL NULL,
  image_type varchar(80) DEFAULT NULL NULL,
  image_size numeric(14) DEFAULT NULL NULL,
  image_data longblob NULL,
  active char(1) DEFAULT NULL NULL,
  created numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_topics PRIMARY KEY (topicId)
);

--
-- Table: tiki_tracker_fields
--

DROP TABLE tiki_tracker_fields;
CREATE TABLE tiki_tracker_fields (
  fieldId IDENTITY int(12) NOT NULL,
  trackerId numeric(12) DEFAULT '0' NOT NULL,
  name varchar(80) DEFAULT NULL NULL,
  options varchar(255) NULL,
  type char(1) DEFAULT NULL NULL,
  isMain char(1) DEFAULT NULL NULL,
  isTblVisible char(1) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_tracker_fields PRIMARY KEY (fieldId)
);

--
-- Table: tiki_tracker_item_attachments
--

DROP TABLE tiki_tracker_item_attachments;
CREATE TABLE tiki_tracker_item_attachments (
  attId IDENTITY int(12) NOT NULL,
  itemId varchar(40) DEFAULT '' NOT NULL,
  filename varchar(80) DEFAULT NULL NULL,
  filetype varchar(80) DEFAULT NULL NULL,
  filesize numeric(14) DEFAULT NULL NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  data longblob NULL,
  path varchar(255) DEFAULT NULL NULL,
  downloads numeric(10) DEFAULT NULL NULL,
  created numeric(14) DEFAULT NULL NULL,
  comment varchar(250) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_tracker_item_attachmen PRIMARY KEY (attId)
);

--
-- Table: tiki_tracker_item_comments
--

DROP TABLE tiki_tracker_item_comments;
CREATE TABLE tiki_tracker_item_comments (
  commentId IDENTITY int(12) NOT NULL,
  itemId numeric(12) DEFAULT '0' NOT NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  data varchar(255) NULL,
  title varchar(200) DEFAULT NULL NULL,
  posted numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_tracker_item_comments PRIMARY KEY (commentId)
);

--
-- Table: tiki_tracker_item_fields
--

DROP TABLE tiki_tracker_item_fields;
CREATE TABLE tiki_tracker_item_fields (
  itemId numeric(12) DEFAULT '0' NOT NULL,
  fieldId numeric(12) DEFAULT '0' NOT NULL,
  value varchar(255) NULL,
  CONSTRAINT pk_tiki_tracker_item_fields PRIMARY KEY (itemId, fieldId)
);

--
-- Table: tiki_tracker_items
--

DROP TABLE tiki_tracker_items;
CREATE TABLE tiki_tracker_items (
  itemId IDENTITY int(12) NOT NULL,
  trackerId numeric(12) DEFAULT '0' NOT NULL,
  created numeric(14) DEFAULT NULL NULL,
  status char(1) DEFAULT NULL NULL,
  lastModif numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_tracker_items PRIMARY KEY (itemId)
);

--
-- Table: tiki_trackers
--

DROP TABLE tiki_trackers;
CREATE TABLE tiki_trackers (
  trackerId IDENTITY int(12) NOT NULL,
  name varchar(80) DEFAULT NULL NULL,
  description varchar(255) NULL,
  created numeric(14) DEFAULT NULL NULL,
  lastModif numeric(14) DEFAULT NULL NULL,
  showCreated char(1) DEFAULT NULL NULL,
  showStatus char(1) DEFAULT NULL NULL,
  showLastModif char(1) DEFAULT NULL NULL,
  useComments char(1) DEFAULT NULL NULL,
  useAttachments char(1) DEFAULT NULL NULL,
  items numeric(10) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_trackers PRIMARY KEY (trackerId)
);

--
-- Table: tiki_untranslated
--

DROP TABLE tiki_untranslated;
CREATE TABLE tiki_untranslated (
  id IDENTITY int(14) NOT NULL,
  source tinyblob NOT NULL,
  lang char(2) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_untranslated PRIMARY KEY (source, lang),
  CONSTRAINT id UNIQUE (id)
);

CREATE INDEX id_2 ON tiki_untranslated (id);

--
-- Table: tiki_user_answers
--

DROP TABLE tiki_user_answers;
CREATE TABLE tiki_user_answers (
  userResultId numeric(10) DEFAULT '0' NOT NULL,
  quizId numeric(10) DEFAULT '0' NOT NULL,
  questionId numeric(10) DEFAULT '0' NOT NULL,
  optionId numeric(10) DEFAULT '0' NOT NULL,
  CONSTRAINT pk_tiki_user_answers PRIMARY KEY (userResultId, quizId, questionId, optionId)
);

--
-- Table: tiki_user_assigned_modules
--

DROP TABLE tiki_user_assigned_modules;
CREATE TABLE tiki_user_assigned_modules (
  name varchar(200) DEFAULT '' NOT NULL,
  position char(1) DEFAULT NULL NULL,
  ord numeric(4) DEFAULT NULL NULL,
  type char(1) DEFAULT NULL NULL,
  title varchar(40) DEFAULT NULL NULL,
  cache_time numeric(14) DEFAULT NULL NULL,
  rows numeric(4) DEFAULT NULL NULL,
  groups varchar(255) NULL,
  params varchar(250) DEFAULT NULL NULL,
  user_ varchar(200) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_user_assigned_modules PRIMARY KEY (name, user_)
);

--
-- Table: tiki_user_bookmarks_folders
--

DROP TABLE tiki_user_bookmarks_folders;
CREATE TABLE tiki_user_bookmarks_folders (
  folderId IDENTITY int(12) NOT NULL,
  parentId numeric(12) DEFAULT NULL NULL,
  user_ varchar(200) DEFAULT '' NOT NULL,
  name varchar(30) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_user_bookmarks_folders PRIMARY KEY (user_, folderId)
);

--
-- Table: tiki_user_bookmarks_urls
--

DROP TABLE tiki_user_bookmarks_urls;
CREATE TABLE tiki_user_bookmarks_urls (
  urlId IDENTITY int(12) NOT NULL,
  name varchar(30) DEFAULT NULL NULL,
  url varchar(250) DEFAULT NULL NULL,
  data longblob NULL,
  lastUpdated numeric(14) DEFAULT NULL NULL,
  folderId numeric(12) DEFAULT '0' NOT NULL,
  user_ varchar(200) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_user_bookmarks_urls PRIMARY KEY (urlId)
);

--
-- Table: tiki_user_mail_accounts
--

DROP TABLE tiki_user_mail_accounts;
CREATE TABLE tiki_user_mail_accounts (
  accountId IDENTITY int(12) NOT NULL,
  user_ varchar(200) DEFAULT '' NOT NULL,
  account varchar(50) DEFAULT '' NOT NULL,
  pop varchar(255) DEFAULT NULL NULL,
  current char(1) DEFAULT NULL NULL,
  port numeric(4) DEFAULT NULL NULL,
  username varchar(100) DEFAULT NULL NULL,
  pass varchar(100) DEFAULT NULL NULL,
  msgs numeric(4) DEFAULT NULL NULL,
  smtp varchar(255) DEFAULT NULL NULL,
  useAuth char(1) DEFAULT NULL NULL,
  smtpPort numeric(4) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_user_mail_accounts PRIMARY KEY (accountId)
);

--
-- Table: tiki_user_menus
--

DROP TABLE tiki_user_menus;
CREATE TABLE tiki_user_menus (
  user_ varchar(200) DEFAULT '' NOT NULL,
  menuId IDENTITY int(12) NOT NULL,
  url varchar(250) DEFAULT NULL NULL,
  name varchar(40) DEFAULT NULL NULL,
  position numeric(4) DEFAULT NULL NULL,
  mode char(1) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_user_menus PRIMARY KEY (menuId)
);

--
-- Table: tiki_user_modules
--

DROP TABLE tiki_user_modules;
CREATE TABLE tiki_user_modules (
  name varchar(200) DEFAULT '' NOT NULL,
  title varchar(40) DEFAULT NULL NULL,
  data longblob NULL,
  CONSTRAINT pk_tiki_user_modules PRIMARY KEY (name)
);

--
-- Table: tiki_user_notes
--

DROP TABLE tiki_user_notes;
CREATE TABLE tiki_user_notes (
  user_ varchar(200) DEFAULT '' NOT NULL,
  noteId IDENTITY int(12) NOT NULL,
  created numeric(14) DEFAULT NULL NULL,
  name varchar(255) DEFAULT NULL NULL,
  lastModif numeric(14) DEFAULT NULL NULL,
  data varchar(255) NULL,
  size numeric(14) DEFAULT NULL NULL,
  parse_mode varchar(20) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_user_notes PRIMARY KEY (noteId)
);

--
-- Table: tiki_user_postings
--

DROP TABLE tiki_user_postings;
CREATE TABLE tiki_user_postings (
  user_ varchar(200) DEFAULT '' NOT NULL,
  posts numeric(12) DEFAULT NULL NULL,
  last numeric(14) DEFAULT NULL NULL,
  first numeric(14) DEFAULT NULL NULL,
  level numeric(8) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_user_postings PRIMARY KEY (user_)
);

--
-- Table: tiki_user_preferences
--

DROP TABLE tiki_user_preferences;
CREATE TABLE tiki_user_preferences (
  user_ varchar(200) DEFAULT '' NOT NULL,
  prefName varchar(40) DEFAULT '' NOT NULL,
  value varchar(250) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_user_preferences PRIMARY KEY (user_, prefName)
);

--
-- Table: tiki_user_quizzes
--

DROP TABLE tiki_user_quizzes;
CREATE TABLE tiki_user_quizzes (
  user_ varchar(100) DEFAULT NULL NULL,
  quizId numeric(10) DEFAULT NULL NULL,
  timestamp numeric(14) DEFAULT NULL NULL,
  timeTaken numeric(14) DEFAULT NULL NULL,
  points numeric(12) DEFAULT NULL NULL,
  maxPoints numeric(12) DEFAULT NULL NULL,
  resultId numeric(10) DEFAULT NULL NULL,
  userResultId IDENTITY int(10) NOT NULL,
  CONSTRAINT pk_tiki_user_quizzes PRIMARY KEY (userResultId)
);

--
-- Table: tiki_user_taken_quizzes
--

DROP TABLE tiki_user_taken_quizzes;
CREATE TABLE tiki_user_taken_quizzes (
  user_ varchar(200) DEFAULT '' NOT NULL,
  quizId varchar(255) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_user_taken_quizzes PRIMARY KEY (user_, quizId)
);

--
-- Table: tiki_user_tasks
--

DROP TABLE tiki_user_tasks;
CREATE TABLE tiki_user_tasks (
  user_ varchar(200) DEFAULT NULL NULL,
  taskId IDENTITY int(14) NOT NULL,
  title varchar(250) DEFAULT NULL NULL,
  description varchar(255) NULL,
  date numeric(14) DEFAULT NULL NULL,
  status char(1) DEFAULT NULL NULL,
  priority numeric(2) DEFAULT NULL NULL,
  completed numeric(14) DEFAULT NULL NULL,
  percentage numeric(4) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_user_tasks PRIMARY KEY (taskId)
);

--
-- Table: tiki_user_votings
--

DROP TABLE tiki_user_votings;
CREATE TABLE tiki_user_votings (
  user_ varchar(200) DEFAULT '' NOT NULL,
  id varchar(255) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_user_votings PRIMARY KEY (user_, id)
);

--
-- Table: tiki_user_watches
--

DROP TABLE tiki_user_watches;
CREATE TABLE tiki_user_watches (
  user_ varchar(200) DEFAULT '' NOT NULL,
  event varchar(40) DEFAULT '' NOT NULL,
  object varchar(200) DEFAULT '' NOT NULL,
  hash varchar(32) DEFAULT NULL NULL,
  title varchar(250) DEFAULT NULL NULL,
  type varchar(200) DEFAULT NULL NULL,
  url varchar(250) DEFAULT NULL NULL,
  email varchar(200) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_user_watches PRIMARY KEY (user_, event, object)
);

--
-- Table: tiki_userfiles
--

DROP TABLE tiki_userfiles;
CREATE TABLE tiki_userfiles (
  user_ varchar(200) DEFAULT '' NOT NULL,
  fileId IDENTITY int(12) NOT NULL,
  name varchar(200) DEFAULT NULL NULL,
  filename varchar(200) DEFAULT NULL NULL,
  filetype varchar(200) DEFAULT NULL NULL,
  filesize varchar(200) DEFAULT NULL NULL,
  data longblob NULL,
  hits numeric(8) DEFAULT NULL NULL,
  isFile char(1) DEFAULT NULL NULL,
  path varchar(255) DEFAULT NULL NULL,
  created numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_userfiles PRIMARY KEY (fileId)
);

--
-- Table: tiki_userpoints
--

DROP TABLE tiki_userpoints;
CREATE TABLE tiki_userpoints (
  user_ varchar(200) DEFAULT NULL NULL,
  points decimal(8,2) DEFAULT NULL NULL,
  voted numeric(8) DEFAULT NULL NULL
);

--
-- Table: tiki_users
--

DROP TABLE tiki_users;
CREATE TABLE tiki_users (
  user_ varchar(200) DEFAULT '' NOT NULL,
  password varchar(40) DEFAULT NULL NULL,
  email varchar(200) DEFAULT NULL NULL,
  lastLogin numeric(14) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_users PRIMARY KEY (user_)
);

--
-- Table: tiki_webmail_contacts
--

DROP TABLE tiki_webmail_contacts;
CREATE TABLE tiki_webmail_contacts (
  contactId IDENTITY int(12) NOT NULL,
  firstName varchar(80) DEFAULT NULL NULL,
  lastName varchar(80) DEFAULT NULL NULL,
  email varchar(250) DEFAULT NULL NULL,
  nickname varchar(200) DEFAULT NULL NULL,
  user_ varchar(200) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_webmail_contacts PRIMARY KEY (contactId)
);

--
-- Table: tiki_webmail_messages
--

DROP TABLE tiki_webmail_messages;
CREATE TABLE tiki_webmail_messages (
  accountId numeric(12) DEFAULT '0' NOT NULL,
  mailId varchar(255) DEFAULT '' NOT NULL,
  user_ varchar(200) DEFAULT '' NOT NULL,
  isRead char(1) DEFAULT NULL NULL,
  isReplied char(1) DEFAULT NULL NULL,
  isFlagged char(1) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_webmail_messages PRIMARY KEY (accountId, mailId)
);

--
-- Table: tiki_wiki_attachments
--

DROP TABLE tiki_wiki_attachments;
CREATE TABLE tiki_wiki_attachments (
  attId IDENTITY int(12) NOT NULL,
  page varchar(200) DEFAULT '' NOT NULL,
  filename varchar(80) DEFAULT NULL NULL,
  filetype varchar(80) DEFAULT NULL NULL,
  filesize numeric(14) DEFAULT NULL NULL,
  user_ varchar(200) DEFAULT NULL NULL,
  data longblob NULL,
  path varchar(255) DEFAULT NULL NULL,
  downloads numeric(10) DEFAULT NULL NULL,
  created numeric(14) DEFAULT NULL NULL,
  comment varchar(250) DEFAULT NULL NULL,
  CONSTRAINT pk_tiki_wiki_attachments PRIMARY KEY (attId)
);

--
-- Table: tiki_zones
--

DROP TABLE tiki_zones;
CREATE TABLE tiki_zones (
  zone varchar(40) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_zones PRIMARY KEY (zone)
);

--
-- Table: users_grouppermissions
--

DROP TABLE users_grouppermissions;
CREATE TABLE users_grouppermissions (
  groupName varchar(30) DEFAULT '' NOT NULL,
  permName varchar(30) DEFAULT '' NOT NULL,
  value char(1) DEFAULT '' NOT NULL,
  CONSTRAINT pk_users_grouppermissions PRIMARY KEY (groupName, permName)
);

--
-- Table: users_groups
--

DROP TABLE users_groups;
CREATE TABLE users_groups (
  groupName varchar(30) DEFAULT '' NOT NULL,
  groupDesc varchar(255) DEFAULT NULL NULL,
  CONSTRAINT pk_users_groups PRIMARY KEY (groupName)
);

--
-- Table: users_objectpermissions
--

DROP TABLE users_objectpermissions;
CREATE TABLE users_objectpermissions (
  groupName varchar(30) DEFAULT '' NOT NULL,
  permName varchar(30) DEFAULT '' NOT NULL,
  objectType varchar(20) DEFAULT '' NOT NULL,
  objectId varchar(32) DEFAULT '' NOT NULL,
  CONSTRAINT pk_users_objectpermissions PRIMARY KEY (objectId, groupName, permName)
);

--
-- Table: users_permissions
--

DROP TABLE users_permissions;
CREATE TABLE users_permissions (
  permName varchar(30) DEFAULT '' NOT NULL,
  permDesc varchar(250) DEFAULT NULL NULL,
  level varchar(80) DEFAULT NULL NULL,
  type varchar(20) DEFAULT NULL NULL,
  CONSTRAINT pk_users_permissions PRIMARY KEY (permName)
);

--
-- Table: users_usergroups
--

DROP TABLE users_usergroups;
CREATE TABLE users_usergroups (
  userId numeric(8) DEFAULT '0' NOT NULL,
  groupName varchar(30) DEFAULT '' NOT NULL,
  CONSTRAINT pk_users_usergroups PRIMARY KEY (userId, groupName)
);

--
-- Table: users_users
--

DROP TABLE users_users;
CREATE TABLE users_users (
  userId IDENTITY int(8) NOT NULL,
  email varchar(200) DEFAULT NULL NULL,
  login varchar(40) DEFAULT '' NOT NULL,
  password varchar(30) DEFAULT '' NOT NULL,
  provpass varchar(30) DEFAULT NULL NULL,
  realname varchar(80) DEFAULT NULL NULL,
  homePage varchar(200) DEFAULT NULL NULL,
  lastLogin numeric(14) DEFAULT NULL NULL,
  currentLogin numeric(14) DEFAULT NULL NULL,
  registrationDate numeric(14) DEFAULT NULL NULL,
  challenge varchar(32) DEFAULT NULL NULL,
  pass_due numeric(14) DEFAULT NULL NULL,
  hash varchar(32) DEFAULT NULL NULL,
  created numeric(14) DEFAULT NULL NULL,
  country varchar(80) DEFAULT NULL NULL,
  avatarName varchar(80) DEFAULT NULL NULL,
  avatarSize numeric(14) DEFAULT NULL NULL,
  avatarFileType varchar(250) DEFAULT NULL NULL,
  avatarData longblob NULL,
  avatarLibName varchar(200) DEFAULT NULL NULL,
  avatarType char(1) DEFAULT NULL NULL,
  CONSTRAINT pk_users_users PRIMARY KEY (userId)
);

