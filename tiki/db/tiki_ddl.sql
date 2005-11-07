

DROP TABLE IF EXISTS galaxia_activities;
CREATE TABLE galaxia_activities (
  activityId int(14) NOT NULL auto_increment,
  name varchar(80) default NULL,
  normalized_name varchar(80) default NULL,
  pId int(14) NOT NULL default '0',
  type enum('start','end','split','switch','join','activity','standalone') default NULL,
  isAutoRouted char(1) default NULL,
  flowNum int(10) default NULL,
  isInteractive char(1) default NULL,
  lastModif int(14) default NULL,
  description text,
  expirationTime int(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (activityId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS galaxia_activity_roles;
CREATE TABLE galaxia_activity_roles (
  activityId int(14) NOT NULL default '0',
  roleId int(14) NOT NULL default '0',
  PRIMARY KEY  (activityId,roleId)
) TYPE=MyISAM;


DROP TABLE IF EXISTS galaxia_instance_activities;
CREATE TABLE galaxia_instance_activities (
  instanceId int(14) NOT NULL default '0',
  activityId int(14) NOT NULL default '0',
  started int(14) NOT NULL default '0',
  ended int(14) NOT NULL default '0',
  user varchar(200) default NULL,
  status enum('running','completed') default NULL,
  PRIMARY KEY  (instanceId,activityId)
) TYPE=MyISAM;


DROP TABLE IF EXISTS galaxia_instance_comments;
CREATE TABLE galaxia_instance_comments (
  cId int(14) NOT NULL auto_increment,
  instanceId int(14) NOT NULL default '0',
  user varchar(200) default NULL,
  activityId int(14) default NULL,
  hash varchar(32) default NULL,
  title varchar(250) default NULL,
  comment text,
  activity varchar(80) default NULL,
  timestamp int(14) default NULL,
  PRIMARY KEY  (cId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS galaxia_instances;
CREATE TABLE galaxia_instances (
  instanceId int(14) NOT NULL auto_increment,
  pId int(14) NOT NULL default '0',
  started int(14) default NULL,
  name varchar(200) default 'No Name',
  owner varchar(200) default NULL,
  nextActivity int(14) default NULL,
  nextUser varchar(200) default NULL,
  ended int(14) default NULL,
  status enum('active','exception','aborted','completed') default NULL,
  properties longblob,
  PRIMARY KEY  (instanceId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS galaxia_processes;
CREATE TABLE galaxia_processes (
  pId int(14) NOT NULL auto_increment,
  name varchar(80) default NULL,
  isValid char(1) default NULL,
  isActive char(1) default NULL,
  version varchar(12) default NULL,
  description text,
  lastModif int(14) default NULL,
  normalized_name varchar(80) default NULL,
  PRIMARY KEY  (pId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS galaxia_roles;
CREATE TABLE galaxia_roles (
  roleId int(14) NOT NULL auto_increment,
  pId int(14) NOT NULL default '0',
  lastModif int(14) default NULL,
  name varchar(80) default NULL,
  description text,
  PRIMARY KEY  (roleId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS galaxia_transitions;
CREATE TABLE galaxia_transitions (
  pId int(14) NOT NULL default '0',
  actFromId int(14) NOT NULL default '0',
  actToId int(14) NOT NULL default '0',
  PRIMARY KEY  (actFromId,actToId)
) TYPE=MyISAM;


DROP TABLE IF EXISTS galaxia_user_roles;
CREATE TABLE galaxia_user_roles (
  pId int(14) NOT NULL default '0',
  roleId int(14) NOT NULL auto_increment,
  user varchar(200) NOT NULL default '',
  PRIMARY KEY  (roleId,user)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS galaxia_workitems;
CREATE TABLE galaxia_workitems (
  itemId int(14) NOT NULL auto_increment,
  instanceId int(14) NOT NULL default '0',
  orderId int(14) NOT NULL default '0',
  activityId int(14) NOT NULL default '0',
  properties longblob,
  started int(14) default NULL,
  ended int(14) default NULL,
  user varchar(200) default NULL,
  PRIMARY KEY  (itemId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS messu_messages;
CREATE TABLE messu_messages (
  msgId int(14) NOT NULL auto_increment,
  user varchar(200) NOT NULL default '',
  user_from varchar(200) NOT NULL default '',
  user_to text,
  user_cc text,
  user_bcc text,
  subject varchar(255) default NULL,
  body text,
  hash varchar(32) default NULL,
  replyto_hash varchar(32) default NULL,
  date int(14) default NULL,
  isRead char(1) default NULL,
  isReplied char(1) default NULL,
  isFlagged char(1) default NULL,
  priority int(2) default NULL,
  PRIMARY KEY  (msgId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS messu_archive;
CREATE TABLE messu_archive (
  msgId int(14) NOT NULL auto_increment,
  user varchar(200) NOT NULL default '',
  user_from varchar(200) NOT NULL default '',
  user_to text,
  user_cc text,
  user_bcc text,
  subject varchar(255) default NULL,
  body text,
  hash varchar(32) default NULL,
  replyto_hash varchar(32) default NULL,
  date int(14) default NULL,
  isRead char(1) default NULL,
  isReplied char(1) default NULL,
  isFlagged char(1) default NULL,
  priority int(2) default NULL,
  PRIMARY KEY  (msgId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS messu_sent;
CREATE TABLE messu_sent (
  msgId int(14) NOT NULL auto_increment,
  user varchar(200) NOT NULL default '',
  user_from varchar(200) NOT NULL default '',
  user_to text,
  user_cc text,
  user_bcc text,
  subject varchar(255) default NULL,
  body text,
  hash varchar(32) default NULL,
  replyto_hash varchar(32) default NULL,
  date int(14) default NULL,
  isRead char(1) default NULL,
  isReplied char(1) default NULL,
  isFlagged char(1) default NULL,
  priority int(2) default NULL,
  PRIMARY KEY  (msgId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS sessions;
CREATE TABLE sessions(
  sesskey char(32) NOT NULL,
  expiry int(11) unsigned NOT NULL,
  expireref varchar(64),
  data text NOT NULL,
  PRIMARY KEY  (sesskey),
  KEY expiry (expiry)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_actionlog;
CREATE TABLE tiki_actionlog (
  action varchar(255) NOT NULL default '',
  lastModif int(14) default NULL,
  pageName varchar(200) default NULL,
  user varchar(200) default NULL,
  ip varchar(15) default NULL,
  comment varchar(200) default NULL
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_articles;
CREATE TABLE tiki_articles (
  articleId int(8) NOT NULL auto_increment,
  topline varchar(255) default NULL,
  title varchar(80) default NULL,
  subtitle varchar(255) default NULL,
  linkto varchar(255) default NULL,
  lang varchar(16) default NULL,
  state char(1) default 's',
  authorName varchar(60) default NULL,
  topicId int(14) default NULL,
  topicName varchar(40) default NULL,
  size int(12) default NULL,
  useImage char(1) default NULL,
  image_name varchar(80) default NULL,
  image_caption text default NULL,
  image_type varchar(80) default NULL,
  image_size int(14) default NULL,
  image_x int(4) default NULL,
  image_y int(4) default NULL,
  image_data longblob,
  publishDate int(14) default NULL,
  expireDate int(14) default NULL,
  created int(14) default NULL,
  bibliographical_references text default NULL,
  resume text default NULL,
  heading text,
  body text,
  hash varchar(32) default NULL,
  author varchar(200) default NULL,
  reads int(14) default NULL,
  votes int(8) default NULL,
  points int(14) default NULL,
  type varchar(50) default NULL,
  rating decimal(3,2) default NULL,
  isfloat char(1) default NULL,
  PRIMARY KEY  (articleId),
  KEY title (title),
  KEY heading (heading(255)),
  KEY body (body(255)),
  KEY reads (reads),
  FULLTEXT KEY ft (title,heading,body)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS tiki_article_types;
CREATE TABLE tiki_article_types (
  type varchar(50) NOT NULL,
  use_ratings varchar(1) default NULL,
  show_pre_publ varchar(1) default NULL,
  show_post_expire varchar(1) default 'y',
  heading_only varchar(1) default NULL,
  allow_comments varchar(1) default 'y',
  show_image varchar(1) default 'y',
  show_avatar varchar(1) default NULL,
  show_author varchar(1) default 'y',
  show_pubdate varchar(1) default 'y',
  show_expdate varchar(1) default NULL,
  show_reads varchar(1) default 'y',
  show_size varchar(1) default 'y',
  show_topline varchar(1) default 'n',
  show_subtitle varchar(1) default 'n',
  show_linkto varchar(1) default 'n',
  show_image_caption varchar(1) default 'n',
  show_lang varchar(1) default 'n',
  creator_edit varchar(1) default NULL,
  comment_can_rate_article char(1) default NULL,
  PRIMARY KEY  (type)
) TYPE=MyISAM ;



DROP TABLE IF EXISTS tiki_banners;
CREATE TABLE tiki_banners (
  bannerId int(12) NOT NULL auto_increment,
  client varchar(200) NOT NULL default '',
  url varchar(255) default NULL,
  title varchar(255) default NULL,
  alt varchar(250) default NULL,
  which varchar(50) default NULL,
  imageData longblob,
  imageType varchar(200) default NULL,
  imageName varchar(100) default NULL,
  HTMLData text,
  fixedURLData varchar(255) default NULL,
  textData text,
  fromDate int(14) default NULL,
  toDate int(14) default NULL,
  useDates char(1) default NULL,
  mon char(1) default NULL,
  tue char(1) default NULL,
  wed char(1) default NULL,
  thu char(1) default NULL,
  fri char(1) default NULL,
  sat char(1) default NULL,
  sun char(1) default NULL,
  hourFrom varchar(4) default NULL,
  hourTo varchar(4) default NULL,
  created int(14) default NULL,
  maxImpressions int(8) default NULL,
  impressions int(8) default NULL,
  clicks int(8) default NULL,
  zone varchar(40) default NULL,
  PRIMARY KEY  (bannerId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_banning;
CREATE TABLE tiki_banning (
  banId int(12) NOT NULL auto_increment,
  mode enum('user','ip') default NULL,
  title varchar(200) default NULL,
  ip1 char(3) default NULL,
  ip2 char(3) default NULL,
  ip3 char(3) default NULL,
  ip4 char(3) default NULL,
  user varchar(200) default NULL,
  date_from timestamp(14) NOT NULL,
  date_to timestamp(14) NOT NULL,
  use_dates char(1) default NULL,
  created int(14) default NULL,
  message text,
  PRIMARY KEY  (banId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_banning_sections;
CREATE TABLE tiki_banning_sections (
  banId int(12) NOT NULL default '0',
  section varchar(100) NOT NULL default '',
  PRIMARY KEY  (banId,section)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_blog_activity;
CREATE TABLE tiki_blog_activity (
  blogId int(8) NOT NULL default '0',
  day int(14) NOT NULL default '0',
  posts int(8) default NULL,
  PRIMARY KEY  (blogId,day)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_blog_posts;
CREATE TABLE tiki_blog_posts (
  postId int(8) NOT NULL auto_increment,
  blogId int(8) NOT NULL default '0',
  data text,
  data_size int(11) unsigned NOT NULL default '0',
  created int(14) default NULL,
  user varchar(200) default NULL,
  priv varchar(1) default NULL,
  trackbacks_to text,
  trackbacks_from text,
  title varchar(80) default NULL,
  PRIMARY KEY  (postId),
  KEY data (data(255)),
  KEY blogId (blogId),
  KEY created (created),
  FULLTEXT KEY ft (data,title)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_blog_posts_images;
CREATE TABLE tiki_blog_posts_images (
  imgId int(14) NOT NULL auto_increment,
  postId int(14) NOT NULL default '0',
  filename varchar(80) default NULL,
  filetype varchar(80) default NULL,
  filesize int(14) default NULL,
  data longblob,
  PRIMARY KEY  (imgId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_blogs;
CREATE TABLE tiki_blogs (
  blogId int(8) NOT NULL auto_increment,
  created int(14) default NULL,
  lastModif int(14) default NULL,
  title varchar(200) default NULL,
  description text,
  user varchar(200) default NULL,
  public char(1) default NULL,
  posts int(8) default NULL,
  maxPosts int(8) default NULL,
  hits int(8) default NULL,
  activity decimal(4,2) default NULL,
  heading text,
  use_find char(1) default NULL,
  use_title char(1) default NULL,
  add_date char(1) default NULL,
  add_poster char(1) default NULL,
  allow_comments char(1) default NULL,
  show_avatar char(1) default NULL,
  PRIMARY KEY  (blogId),
  KEY title (title),
  KEY description (description(255)),
  KEY hits (hits),
  FULLTEXT KEY ft (title,description)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_calendar_categories;
CREATE TABLE tiki_calendar_categories (
  calcatId int(11) NOT NULL auto_increment,
  calendarId int(14) NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  PRIMARY KEY  (calcatId),
  UNIQUE KEY catname (calendarId,name(16))
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_calendar_items;
CREATE TABLE tiki_calendar_items (
  calitemId int(14) NOT NULL auto_increment,
  calendarId int(14) NOT NULL default '0',
  start int(14) NOT NULL default '0',
  end int(14) NOT NULL default '0',
  locationId int(14) default NULL,
  categoryId int(14) default NULL,
  priority enum('1','2','3','4','5','6','7','8','9') NOT NULL default '1',
  status enum('0','1','2') NOT NULL default '0',
  url varchar(255) default NULL,
  lang char(16) NOT NULL default 'en',
  name varchar(255) NOT NULL default '',
  description blob,
  user varchar(40) default NULL,
  nlId int(12) default NULL,
  created int(14) NOT NULL default '0',
  lastmodif int(14) NOT NULL default '0',
  PRIMARY KEY  (calitemId),
  KEY calendarId (calendarId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_calendar_locations;
CREATE TABLE tiki_calendar_locations (
  callocId int(14) NOT NULL auto_increment,
  calendarId int(14) NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  description blob,
  PRIMARY KEY  (callocId),
  UNIQUE KEY locname (calendarId,name(16))
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_calendar_roles;
CREATE TABLE tiki_calendar_roles (
  calitemId int(14) NOT NULL default '0',
  username varchar(40) NOT NULL default '',
  role enum('0','1','2','3','6') NOT NULL default '0',
  PRIMARY KEY  (calitemId,username(16),role)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_calendars;
CREATE TABLE tiki_calendars (
  calendarId int(14) NOT NULL auto_increment,
  name varchar(80) NOT NULL default '',
  description varchar(255) default NULL,
  user varchar(40) NOT NULL default '',
  customlocations enum('n','y') NOT NULL default 'n',
  customcategories enum('n','y') NOT NULL default 'n',
  customlanguages enum('n','y') NOT NULL default 'n',
  custompriorities enum('n','y') NOT NULL default 'n',
  customparticipants enum('n','y') NOT NULL default 'n',
  customsubscription enum('n','y') NOT NULL default 'n',
  created int(14) NOT NULL default '0',
  lastmodif int(14) NOT NULL default '0',
  personal enum ('n', 'y') NOT NULL default 'n',
  PRIMARY KEY  (calendarId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_categories;
CREATE TABLE tiki_categories (
  categId int(12) NOT NULL auto_increment,
  name varchar(100) default NULL,
  description varchar(250) default NULL,
  parentId int(12) default NULL,
  hits int(8) default NULL,
  PRIMARY KEY  (categId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_categorized_objects;
CREATE TABLE tiki_categorized_objects (
  catObjectId int(12) NOT NULL auto_increment,
  type varchar(50) default NULL,
  objId varchar(255) default NULL,
  description text,
  created int(14) default NULL,
  name varchar(200) default NULL,
  href varchar(200) default NULL,
  hits int(8) default NULL,
  PRIMARY KEY  (catObjectId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_category_objects;
CREATE TABLE tiki_category_objects (
  catObjectId int(12) NOT NULL default '0',
  categId int(12) NOT NULL default '0',
  PRIMARY KEY  (catObjectId,categId)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_object_ratings;
CREATE TABLE tiki_object_ratings (
  catObjectId int(12) NOT NULL default '0',
  pollId int(12) NOT NULL default '0',
  PRIMARY KEY  (catObjectId,pollId)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_category_sites;
CREATE TABLE tiki_category_sites (
  categId int(10) NOT NULL default '0',
  siteId int(14) NOT NULL default '0',
  PRIMARY KEY  (categId,siteId)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_chart_items;
CREATE TABLE tiki_chart_items (
  itemId int(14) NOT NULL auto_increment,
  title varchar(250) default NULL,
  description text,
  chartId int(14) NOT NULL default '0',
  created int(14) default NULL,
  URL varchar(250) default NULL,
  votes int(14) default NULL,
  points int(14) default NULL,
  average decimal(4,2) default NULL,
  PRIMARY KEY  (itemId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_charts;
CREATE TABLE tiki_charts (
  chartId int(14) NOT NULL auto_increment,
  title varchar(250) default NULL,
  description text,
  hits int(14) default NULL,
  singleItemVotes char(1) default NULL,
  singleChartVotes char(1) default NULL,
  suggestions char(1) default NULL,
  autoValidate char(1) default NULL,
  topN int(6) default NULL,
  maxVoteValue int(4) default NULL,
  frequency int(14) default NULL,
  showAverage char(1) default NULL,
  isActive char(1) default NULL,
  showVotes char(1) default NULL,
  useCookies char(1) default NULL,
  lastChart int(14) default NULL,
  voteAgainAfter int(14) default NULL,
  created int(14) default NULL,
  PRIMARY KEY  (chartId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_charts_rankings;
CREATE TABLE tiki_charts_rankings (
  chartId int(14) NOT NULL default '0',
  itemId int(14) NOT NULL default '0',
  position int(14) NOT NULL default '0',
  timestamp int(14) NOT NULL default '0',
  lastPosition int(14) NOT NULL default '0',
  period int(14) NOT NULL default '0',
  rvotes int(14) NOT NULL default '0',
  raverage decimal(4,2) NOT NULL default '0.00',
  PRIMARY KEY  (chartId,itemId,period)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_charts_votes;
CREATE TABLE tiki_charts_votes (
  user varchar(200) NOT NULL default '',
  itemId int(14) NOT NULL default '0',
  timestamp int(14) default NULL,
  chartId int(14) default NULL,
  PRIMARY KEY  (user,itemId)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_chat_channels;
CREATE TABLE tiki_chat_channels (
  channelId int(8) NOT NULL auto_increment,
  name varchar(30) default NULL,
  description varchar(250) default NULL,
  max_users int(8) default NULL,
  mode char(1) default NULL,
  moderator varchar(200) default NULL,
  active char(1) default NULL,
  refresh int(6) default NULL,
  PRIMARY KEY  (channelId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_chat_messages;
CREATE TABLE tiki_chat_messages (
  messageId int(8) NOT NULL auto_increment,
  channelId int(8) NOT NULL default '0',
  data varchar(255) default NULL,
  poster varchar(200) NOT NULL default 'anonymous',
  timestamp int(14) default NULL,
  PRIMARY KEY  (messageId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_chat_users;
CREATE TABLE tiki_chat_users (
  nickname varchar(200) NOT NULL default '',
  channelId int(8) NOT NULL default '0',
  timestamp int(14) default NULL,
  PRIMARY KEY  (nickname,channelId)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_comments;
CREATE TABLE tiki_comments (
  threadId int(14) NOT NULL auto_increment,
  object varchar(255) NOT NULL default '',
  objectType varchar(32) NOT NULL default '',
  parentId int(14) default NULL,
  userName varchar(200) default NULL,
  commentDate int(14) default NULL,
  hits int(8) default NULL,
  type char(1) default NULL,
  points decimal(8,2) default NULL,
  votes int(8) default NULL,
  average decimal(8,4) default NULL,
  title varchar(100) default NULL,
  data text,
  hash varchar(32) default NULL,
  user_ip varchar(15) default NULL,
  summary varchar(240) default NULL,
  smiley varchar(80) default NULL,
  message_id varchar(250) default NULL,
  in_reply_to varchar(250) default NULL,
  comment_rating tinyint(2) default NULL,  
  PRIMARY KEY  (threadId),
  KEY title (title),
  KEY data (data(255)),
  KEY object (object),
  KEY hits (hits),
  KEY tc_pi (parentId),
  FULLTEXT KEY ft (title,data)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_content;
CREATE TABLE tiki_content (
  contentId int(8) NOT NULL auto_increment,
  description text,
  PRIMARY KEY  (contentId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_content_templates;
CREATE TABLE tiki_content_templates (
  templateId int(10) NOT NULL auto_increment,
  content longblob,
  name varchar(200) default NULL,
  created int(14) default NULL,
  PRIMARY KEY  (templateId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_content_templates_sections;
CREATE TABLE tiki_content_templates_sections (
  templateId int(10) NOT NULL default '0',
  section varchar(250) NOT NULL default '',
  PRIMARY KEY  (templateId,section)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_cookies;
CREATE TABLE tiki_cookies (
  cookieId int(10) NOT NULL auto_increment,
  cookie varchar(255) default NULL,
  PRIMARY KEY  (cookieId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_copyrights;
CREATE TABLE tiki_copyrights (
  copyrightId int(12) NOT NULL auto_increment,
  page varchar(200) default NULL,
  title varchar(200) default NULL,
  year int(11) default NULL,
  authors varchar(200) default NULL,
  copyright_order int(11) default NULL,
  userName varchar(200) default NULL,
  PRIMARY KEY  (copyrightId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_directory_categories;
CREATE TABLE tiki_directory_categories (
  categId int(10) NOT NULL auto_increment,
  parent int(10) default NULL,
  name varchar(240) default NULL,
  description text,
  childrenType char(1) default NULL,
  sites int(10) default NULL,
  viewableChildren int(4) default NULL,
  allowSites char(1) default NULL,
  showCount char(1) default NULL,
  editorGroup varchar(200) default NULL,
  hits int(12) default NULL,
  PRIMARY KEY  (categId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_directory_search;
CREATE TABLE tiki_directory_search (
  term varchar(250) NOT NULL default '',
  hits int(14) default NULL,
  PRIMARY KEY  (term)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_directory_sites;
CREATE TABLE tiki_directory_sites (
  siteId int(14) NOT NULL auto_increment,
  name varchar(240) default NULL,
  description text,
  url varchar(255) default NULL,
  country varchar(255) default NULL,
  hits int(12) default NULL,
  isValid char(1) default NULL,
  created int(14) default NULL,
  lastModif int(14) default NULL,
  cache longblob,
  cache_timestamp int(14) default NULL,
  PRIMARY KEY  (siteId),
  FULLTEXT KEY ft (name,description)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_drawings;
CREATE TABLE tiki_drawings (
  drawId int(12) NOT NULL auto_increment,
  version int(8) default NULL,
  name varchar(250) default NULL,
  filename_draw varchar(250) default NULL,
  filename_pad varchar(250) default NULL,
  timestamp int(14) default NULL,
  user varchar(200) default NULL,
  PRIMARY KEY  (drawId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_dsn;
CREATE TABLE tiki_dsn (
  dsnId int(12) NOT NULL auto_increment,
  name varchar(200) NOT NULL default '',
  dsn varchar(255) default NULL,
  PRIMARY KEY  (dsnId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_dynamic_variables;
CREATE TABLE tiki_dynamic_variables (
  name varchar(40) NOT NULL,
  data text,
  PRIMARY KEY  (name)
);


DROP TABLE IF EXISTS tiki_eph;
CREATE TABLE tiki_eph (
  ephId int(12) NOT NULL auto_increment,
  title varchar(250) default NULL,
  isFile char(1) default NULL,
  filename varchar(250) default NULL,
  filetype varchar(250) default NULL,
  filesize varchar(250) default NULL,
  data longblob,
  textdata longblob,
  publish int(14) default NULL,
  hits int(10) default NULL,
  PRIMARY KEY  (ephId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_extwiki;
CREATE TABLE tiki_extwiki (
  extwikiId int(12) NOT NULL auto_increment,
  name varchar(200) NOT NULL default '',
  extwiki varchar(255) default NULL,
  PRIMARY KEY  (extwikiId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_faq_questions;
CREATE TABLE tiki_faq_questions (
  questionId int(10) NOT NULL auto_increment,
  faqId int(10) default NULL,
  position int(4) default NULL,
  question text,
  answer text,
  PRIMARY KEY  (questionId),
  KEY faqId (faqId),
  KEY question (question(255)),
  KEY answer (answer(255)),
  FULLTEXT KEY ft (question,answer)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_faqs;
CREATE TABLE tiki_faqs (
  faqId int(10) NOT NULL auto_increment,
  title varchar(200) default NULL,
  description text,
  created int(14) default NULL,
  questions int(5) default NULL,
  hits int(8) default NULL,
  canSuggest char(1) default NULL,
  PRIMARY KEY  (faqId),
  KEY title (title),
  KEY description (description(255)),
  KEY hits (hits),
  FULLTEXT KEY ft (title,description)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_featured_links;
CREATE TABLE tiki_featured_links (
  url varchar(200) NOT NULL default '',
  title varchar(200) default NULL,
  description text,
  hits int(8) default NULL,
  position int(6) default NULL,
  type char(1) default NULL,
  PRIMARY KEY  (url)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_file_galleries;
CREATE TABLE tiki_file_galleries (
  galleryId int(14) NOT NULL auto_increment,
  name varchar(80) NOT NULL default '',
  description text,
  created int(14) default NULL,
  visible char(1) default NULL,
  lastModif int(14) default NULL,
  user varchar(200) default NULL,
  hits int(14) default NULL,
  votes int(8) default NULL,
  points decimal(8,2) default NULL,
  maxRows int(10) default NULL,
  public char(1) default NULL,
  show_id char(1) default NULL,
  show_icon char(1) default NULL,
  show_name char(1) default NULL,
  show_size char(1) default NULL,
  show_description char(1) default NULL,
  max_desc int(8) default NULL,
  show_created char(1) default NULL,
  show_dl char(1) default NULL,
  PRIMARY KEY  (galleryId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_files;
CREATE TABLE tiki_files (
  fileId int(14) NOT NULL auto_increment,
  galleryId int(14) NOT NULL default '0',
  name varchar(200) NOT NULL default '',
  description text,
  created int(14) default NULL,
  filename varchar(80) default NULL,
  filesize int(14) default NULL,
  filetype varchar(250) default NULL,
  data longblob,
  user varchar(200) default NULL,
  downloads int(14) default NULL,
  votes int(8) default NULL,
  points decimal(8,2) default NULL,
  path varchar(255) default NULL,
  reference_url varchar(250) default NULL,
  is_reference char(1) default NULL,
  hash varchar(32) default NULL,
  search_data longtext,
  lastModif integer(14) DEFAULT NULL,
  lastModifUser varchar(200) DEFAULT NULL,
  PRIMARY KEY  (fileId),
  KEY name (name),
  KEY description (description(255)),
  KEY downloads (downloads),
  FULLTEXT KEY ft (name,description,search_data)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_forum_attachments;
CREATE TABLE tiki_forum_attachments (
  attId int(14) NOT NULL auto_increment,
  threadId int(14) NOT NULL default '0',
  qId int(14) NOT NULL default '0',
  forumId int(14) default NULL,
  filename varchar(250) default NULL,
  filetype varchar(250) default NULL,
  filesize int(12) default NULL,
  data longblob,
  dir varchar(200) default NULL,
  created int(14) default NULL,
  path varchar(250) default NULL,
  PRIMARY KEY  (attId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_forum_reads;
CREATE TABLE tiki_forum_reads (
  user varchar(200) NOT NULL default '',
  threadId int(14) NOT NULL default '0',
  forumId int(14) default NULL,
  timestamp int(14) default NULL,
  PRIMARY KEY  (user,threadId)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_forums;
CREATE TABLE tiki_forums (
  forumId int(8) NOT NULL auto_increment,
  name varchar(200) default NULL,
  description text,
  created int(14) default NULL,
  lastPost int(14) default NULL,
  threads int(8) default NULL,
  comments int(8) default NULL,
  controlFlood char(1) default NULL,
  floodInterval int(8) default NULL,
  moderator varchar(200) default NULL,
  hits int(8) default NULL,
  mail varchar(200) default NULL,
  useMail char(1) default NULL,
  section varchar(200) default NULL,
  usePruneUnreplied char(1) default NULL,
  pruneUnrepliedAge int(8) default NULL,
  usePruneOld char(1) default NULL,
  pruneMaxAge int(8) default NULL,
  topicsPerPage int(6) default NULL,
  topicOrdering varchar(100) default NULL,
  threadOrdering varchar(100) default NULL,
  att varchar(80) default NULL,
  att_store varchar(4) default NULL,
  att_store_dir varchar(250) default NULL,
  att_max_size int(12) default NULL,
  ui_level char(1) default NULL,
  forum_password varchar(32) default NULL,
  forum_use_password char(1) default NULL,
  moderator_group varchar(200) default NULL,
  approval_type varchar(20) default NULL,
  outbound_address varchar(250) default NULL,
  outbound_mails_for_inbound_mails char(1) default NULL,
  outbound_mails_reply_link char(1) default NULL,
  outbound_from varchar(250) default NULL,
  inbound_pop_server varchar(250) default NULL,
  inbound_pop_port int(4) default NULL,
  inbound_pop_user varchar(200) default NULL,
  inbound_pop_password varchar(80) default NULL,
  topic_smileys char(1) default NULL,
  ui_avatar char(1) default NULL,
  ui_flag char(1) default NULL,
  ui_posts char(1) default NULL,
  ui_email char(1) default NULL,
  ui_online char(1) default NULL,
  topic_summary char(1) default NULL,
  show_description char(1) default NULL,
  topics_list_replies char(1) default NULL,
  topics_list_reads char(1) default NULL,
  topics_list_pts char(1) default NULL,
  topics_list_lastpost char(1) default NULL,
  topics_list_author char(1) default NULL,
  vote_threads char(1) default NULL,
  forum_last_n int(2) default 0,
  PRIMARY KEY  (forumId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_forums_queue;
CREATE TABLE tiki_forums_queue (
  qId int(14) NOT NULL auto_increment,
  object varchar(32) default NULL,
  parentId int(14) default NULL,
  forumId int(14) default NULL,
  timestamp int(14) default NULL,
  user varchar(200) default NULL,
  title varchar(240) default NULL,
  data text,
  type varchar(60) default NULL,
  hash varchar(32) default NULL,
  topic_smiley varchar(80) default NULL,
  topic_title varchar(240) default NULL,
  summary varchar(240) default NULL,
  PRIMARY KEY  (qId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_forums_reported;
CREATE TABLE tiki_forums_reported (
  threadId int(12) NOT NULL default '0',
  forumId int(12) NOT NULL default '0',
  parentId int(12) NOT NULL default '0',
  user varchar(200) default NULL,
  timestamp int(14) default NULL,
  reason varchar(250) default NULL,
  PRIMARY KEY  (threadId)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_galleries;
CREATE TABLE tiki_galleries (
  galleryId int(14) NOT NULL auto_increment,
  name varchar(80) NOT NULL default '',
  description text,
  created int(14) default NULL,
  lastModif int(14) default NULL,
  visible char(1) default NULL,
  geographic char(1) default NULL,
  theme varchar(60) default NULL,
  user varchar(200) default NULL,
  hits int(14) default NULL,
  maxRows int(10) default NULL,
  rowImages int(10) default NULL,
  thumbSizeX int(10) default NULL,
  thumbSizeY int(10) default NULL,
  public char(1) default NULL,
  sortorder varchar(20) NOT NULL default 'created',
  sortdirection varchar(4) NOT NULL default 'desc',
  galleryimage varchar(20) NOT NULL default 'first',
  parentgallery int(14) NOT NULL default -1,
  showname char(1) NOT NULL default 'y',
  showimageid char(1) NOT NULL default 'n',
  showdescription char(1) NOT NULL default 'n',
  showcreated char(1) NOT NULL default 'n',
  showuser char(1) NOT NULL default 'n',
  showhits char(1) NOT NULL default 'y',
  showxysize char(1) NOT NULL default 'y',
  showfilesize char(1) NOT NULL default 'n',
  showfilename char(1) NOT NULL default 'n',
  defaultscale varchar(10) NOT NULL DEFAULT 'o',
  PRIMARY KEY  (galleryId),
  KEY name (name),
  KEY description (description(255)),
  KEY hits (hits),
  FULLTEXT KEY ft (name,description)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_galleries_scales;
CREATE TABLE tiki_galleries_scales (
  galleryId int(14) NOT NULL default '0',
  scale int(11) NOT NULL default '0',
  PRIMARY KEY  (galleryId,scale)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_games;
CREATE TABLE tiki_games (
  gameName varchar(200) NOT NULL default '',
  hits int(8) default NULL,
  votes int(8) default NULL,
  points int(8) default NULL,
  PRIMARY KEY  (gameName)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_group_inclusion;
CREATE TABLE tiki_group_inclusion (
  groupName varchar(255) NOT NULL default '',
  includeGroup varchar(255) NOT NULL default '',
  PRIMARY KEY  (groupName(30),includeGroup(30))
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_history;
CREATE TABLE tiki_history (
  pageName varchar(160) NOT NULL default '',
  version int(8) NOT NULL default '0',
  version_minor int(8) NOT NULL default '0',
  lastModif int(14) default NULL,
  description varchar(200) default NULL,
  user varchar(200) default NULL,
  ip varchar(15) default NULL,
  comment varchar(200) default NULL,
  data longblob,
  PRIMARY KEY  (pageName,version)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_hotwords;
CREATE TABLE tiki_hotwords (
  word varchar(40) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  PRIMARY KEY  (word)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_html_pages;
CREATE TABLE tiki_html_pages (
  pageName varchar(200) NOT NULL default '',
  content longblob,
  refresh int(10) default NULL,
  type char(1) default NULL,
  created int(14) default NULL,
  PRIMARY KEY  (pageName)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_html_pages_dynamic_zones;
CREATE TABLE tiki_html_pages_dynamic_zones (
  pageName varchar(40) NOT NULL default '',
  zone varchar(80) NOT NULL default '',
  type char(2) default NULL,
  content text,
  PRIMARY KEY  (pageName,zone)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_images;
CREATE TABLE tiki_images (
  imageId int(14) NOT NULL auto_increment,
  galleryId int(14) NOT NULL default '0',
  name varchar(200) NOT NULL default '',
  description text,
  lat float default NULL,
  lon float default NULL,
  created int(14) default NULL,
  user varchar(200) default NULL,
  hits int(14) default NULL,
  path varchar(255) default NULL,
  PRIMARY KEY  (imageId),
  KEY name (name),
  KEY description (description(255)),
  KEY hits (hits),
  KEY ti_gId (galleryId),
  KEY ti_cr (created),
  KEY ti_us (user),
  FULLTEXT KEY ft (name,description)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_images_data;
CREATE TABLE tiki_images_data (
  imageId int(14) NOT NULL default '0',
  xsize int(8) NOT NULL default '0',
  ysize int(8) NOT NULL default '0',
  type char(1) NOT NULL default '',
  filesize int(14) default NULL,
  filetype varchar(80) default NULL,
  filename varchar(80) default NULL,
  etag varchar(32) default NULL,
  data longblob,
  PRIMARY KEY  (imageId,xsize,ysize,type),
  KEY t_i_d_it (imageId,type)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_language;
CREATE TABLE tiki_language (
  source tinyblob NOT NULL,
  lang char(16) NOT NULL default '',
  tran tinyblob,
  PRIMARY KEY  (source(255),lang)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_languages;
CREATE TABLE tiki_languages (
  lang char(16) NOT NULL default '',
  language varchar(255) default NULL,
  PRIMARY KEY  (lang)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_link_cache;
CREATE TABLE tiki_link_cache (
  cacheId int(14) NOT NULL auto_increment,
  url varchar(250) default NULL,
  data longblob,
  refresh int(14) default NULL,
  PRIMARY KEY  (cacheId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;
CREATE INDEX urlindex ON tiki_link_cache (url(250));


DROP TABLE IF EXISTS tiki_links;
CREATE TABLE tiki_links (
  fromPage varchar(160) NOT NULL default '',
  toPage varchar(160) NOT NULL default '',
  PRIMARY KEY  (fromPage,toPage)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_live_support_events;
CREATE TABLE tiki_live_support_events (
  eventId int(14) NOT NULL auto_increment,
  reqId varchar(32) NOT NULL default '',
  type varchar(40) default NULL,
  seqId int(14) default NULL,
  senderId varchar(32) default NULL,
  data text,
  timestamp int(14) default NULL,
  PRIMARY KEY  (eventId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_live_support_message_comments;
CREATE TABLE tiki_live_support_message_comments (
  cId int(12) NOT NULL auto_increment,
  msgId int(12) default NULL,
  data text,
  timestamp int(14) default NULL,
  PRIMARY KEY  (cId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_live_support_messages;
CREATE TABLE tiki_live_support_messages (
  msgId int(12) NOT NULL auto_increment,
  data text,
  timestamp int(14) default NULL,
  user varchar(200) default NULL,
  username varchar(200) default NULL,
  priority int(2) default NULL,
  status char(1) default NULL,
  assigned_to varchar(200) default NULL,
  resolution varchar(100) default NULL,
  title varchar(200) default NULL,
  module int(4) default NULL,
  email varchar(250) default NULL,
  PRIMARY KEY  (msgId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_live_support_modules;
CREATE TABLE tiki_live_support_modules (
  modId int(4) NOT NULL auto_increment,
  name varchar(90) default NULL,
  PRIMARY KEY  (modId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_live_support_operators;
CREATE TABLE tiki_live_support_operators (
  user varchar(200) NOT NULL default '',
  accepted_requests int(10) default NULL,
  status varchar(20) default NULL,
  longest_chat int(10) default NULL,
  shortest_chat int(10) default NULL,
  average_chat int(10) default NULL,
  last_chat int(14) default NULL,
  time_online int(10) default NULL,
  votes int(10) default NULL,
  points int(10) default NULL,
  status_since int(14) default NULL,
  PRIMARY KEY  (user)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_live_support_requests;
CREATE TABLE tiki_live_support_requests (
  reqId varchar(32) NOT NULL default '',
  user varchar(200) default NULL,
  tiki_user varchar(200) default NULL,
  email varchar(200) default NULL,
  operator varchar(200) default NULL,
  operator_id varchar(32) default NULL,
  user_id varchar(32) default NULL,
  reason text,
  req_timestamp int(14) default NULL,
  timestamp int(14) default NULL,
  status varchar(40) default NULL,
  resolution varchar(40) default NULL,
  chat_started int(14) default NULL,
  chat_ended int(14) default NULL,
  PRIMARY KEY  (reqId)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_logs;
CREATE TABLE tiki_logs (
  logId int(8) NOT NULL auto_increment,
  logtype varchar(20) NOT NULL,
  logmessage text NOT NULL,
  loguser varchar(200) NOT NULL,
  logip varchar(200) NOT NULL,
  logclient text NOT NULL,
  logtime int(14) NOT NULL,
  PRIMARY KEY  (logId),
  KEY logtype (logtype)
) TYPE=MyISAM;



DROP TABLE IF EXISTS tiki_mail_events;
CREATE TABLE tiki_mail_events (
  event varchar(200) default NULL,
  object varchar(200) default NULL,
  email varchar(200) default NULL
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_mailin_accounts;
CREATE TABLE tiki_mailin_accounts (
  accountId int(12) NOT NULL auto_increment,
  user varchar(200) NOT NULL default '',
  account varchar(50) NOT NULL default '',
  pop varchar(255) default NULL,
  port int(4) default NULL,
  username varchar(100) default NULL,
  pass varchar(100) default NULL,
  active char(1) default NULL,
  type varchar(40) default NULL,
  smtp varchar(255) default NULL,
  useAuth char(1) default NULL,
  smtpPort int(4) default NULL,
  anonymous char(1) NOT NULL default 'y',
  attachments char(1) NOT NULL default 'n',
  article_topicId int(4) default NULL,
  article_type varchar(50) default NULL,
  discard_after varchar(255) default NULL,
  PRIMARY KEY  (accountId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_menu_languages;
CREATE TABLE tiki_menu_languages (
  menuId int(8) NOT NULL auto_increment,
  language char(16) NOT NULL default '',
  PRIMARY KEY  (menuId,language)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_menu_options;
CREATE TABLE tiki_menu_options (
  optionId int(8) NOT NULL auto_increment,
  menuId int(8) default NULL,
  type char(1) default NULL,
  name varchar(200) default NULL,
  url varchar(255) default NULL,
  position int(4) default NULL,
  section varchar(255) default NULL,
  perm varchar(255) default NULL,
  groupname varchar(255) default NULL,
  PRIMARY KEY  (optionId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;
























DROP TABLE IF EXISTS tiki_menus;
CREATE TABLE tiki_menus (
  menuId int(8) NOT NULL auto_increment,
  name varchar(200) NOT NULL default '',
  description text,
  type char(1) default NULL,
  PRIMARY KEY  (menuId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_minical_events;
CREATE TABLE tiki_minical_events (
  user varchar(200) default NULL,
  eventId int(12) NOT NULL auto_increment,
  title varchar(250) default NULL,
  description text,
  start int(14) default NULL,
  end int(14) default NULL,
  security char(1) default NULL,
  duration int(3) default NULL,
  topicId int(12) default NULL,
  reminded char(1) default NULL,
  PRIMARY KEY  (eventId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_minical_topics;
CREATE TABLE tiki_minical_topics (
  user varchar(200) default NULL,
  topicId int(12) NOT NULL auto_increment,
  name varchar(250) default NULL,
  filename varchar(200) default NULL,
  filetype varchar(200) default NULL,
  filesize varchar(200) default NULL,
  data longblob,
  path varchar(250) default NULL,
  isIcon char(1) default NULL,
  PRIMARY KEY  (topicId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_modules;
CREATE TABLE tiki_modules (
  name varchar(200) NOT NULL default '',
  position char(1) default NULL,
  ord int(4) default NULL,
  type char(1) default NULL,
  title varchar(255) default NULL,
  cache_time int(14) default NULL,
  rows int(4) default NULL,
  params varchar(255) default NULL,
  groups text,
  PRIMARY KEY  (name)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_newsletter_subscriptions;
CREATE TABLE tiki_newsletter_subscriptions (
  nlId int(12) NOT NULL default '0',
  email varchar(255) NOT NULL default '',
  code varchar(32) default NULL,
  valid char(1) default NULL,
  subscribed int(14) default NULL,
  isUser char(1) NOT NULL default 'n',
  PRIMARY KEY  (nlId,email,isUser)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_newsletter_groups;
CREATE TABLE tiki_newsletter_groups (
  nlId int(12) NOT NULL default '0',
  groupName varchar(255) NOT NULL default '',
  code varchar(32) default NULL,
  PRIMARY KEY  (nlId,groupName)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_newsletters;
CREATE TABLE tiki_newsletters (
  nlId int(12) NOT NULL auto_increment,
  name varchar(200) default NULL,
  description text,
  created int(14) default NULL,
  lastSent int(14) default NULL,
  editions int(10) default NULL,
  users int(10) default NULL,
  allowUserSub char(1) default 'y',
  allowAnySub char(1) default NULL,
  unsubMsg char(1) default 'y',
  validateAddr char(1) default 'y',
  frequency int(14) default NULL,
  PRIMARY KEY  (nlId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_newsreader_marks;
CREATE TABLE tiki_newsreader_marks (
  user varchar(200) NOT NULL default '',
  serverId int(12) NOT NULL default '0',
  groupName varchar(255) NOT NULL default '',
  timestamp int(14) NOT NULL default '0',
  PRIMARY KEY  (user,serverId,groupName)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_newsreader_servers;
CREATE TABLE tiki_newsreader_servers (
  user varchar(200) NOT NULL default '',
  serverId int(12) NOT NULL auto_increment,
  server varchar(250) default NULL,
  port int(4) default NULL,
  username varchar(200) default NULL,
  password varchar(200) default NULL,
  PRIMARY KEY  (serverId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_page_footnotes;
CREATE TABLE tiki_page_footnotes (
  user varchar(200) NOT NULL default '',
  pageName varchar(250) NOT NULL default '',
  data text,
  PRIMARY KEY  (user,pageName)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_pages;
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
  wiki_cache int(10) default NULL,
  cache_timestamp int(14) default NULL,
  pageRank decimal(4,3) default NULL,
  creator varchar(200) default NULL,
  page_size int(10) unsigned default '0',
  lang varchar(16) default NULL,
  lockedby varchar(200) default NULL,
  created int(14),
  PRIMARY KEY  (page_id),
  UNIQUE KEY pageName (pageName),
  KEY data (data(255)),
  KEY pageRank (pageRank),
  FULLTEXT KEY ft (pageName,description,data)
) TYPE=MyISAM AUTO_INCREMENT=1;


DROP TABLE IF EXISTS tiki_pageviews;
CREATE TABLE tiki_pageviews (
  day int(14) NOT NULL default '0',
  pageviews int(14) default NULL,
  PRIMARY KEY  (day)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_poll_objects;
CREATE TABLE `tiki_poll_objects` (
  `catObjectId` int(11) NOT NULL default '0',
  `pollId` int(11) NOT NULL default '0',
  `title` varchar(255) default NULL,
  PRIMARY KEY  (`catObjectId`,`pollId`)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_poll_options;
CREATE TABLE tiki_poll_options (
  pollId int(8) NOT NULL default '0',
  optionId int(8) NOT NULL auto_increment,
  title varchar(200) default NULL,
  position int(4) NOT NULL default '0',
  votes int(8) default NULL,
  PRIMARY KEY  (optionId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_polls;
CREATE TABLE tiki_polls (
  pollId int(8) NOT NULL auto_increment,
  title varchar(200) default NULL,
  votes int(8) default NULL,
  active char(1) default NULL,
  publishDate int(14) default NULL,
  PRIMARY KEY  (pollId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_preferences;
CREATE TABLE tiki_preferences (
  name varchar(40) NOT NULL default '',
  value varchar(250) default NULL,
  PRIMARY KEY  (name)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_private_messages;
CREATE TABLE tiki_private_messages (
  messageId int(8) NOT NULL auto_increment,
  toNickname varchar(200) NOT NULL default '',
  data varchar(255) default NULL,
  poster varchar(200) NOT NULL default 'anonymous',
  timestamp int(14) default NULL,
  PRIMARY KEY  (messageId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_programmed_content;
CREATE TABLE tiki_programmed_content (
  pId int(8) NOT NULL auto_increment,
  contentId int(8) NOT NULL default '0',
  publishDate int(14) NOT NULL default '0',
  data text,
  PRIMARY KEY  (pId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS tiki_projects;
CREATE TABLE tiki_projects (
  projectId int(10) unsigned NOT NULL auto_increment,
  active char(1) NOT NULL default 'n',
  projectName varchar(200) NOT NULL default '',
  projectFriendlyName varchar(200) NOT NULL default '',
  projectDescription text NOT NULL,
  Created int(14) default NULL,
  lastModif int(14) default NULL,
  CreatedBy varchar(100) default NULL,
  PRIMARY KEY  (projectId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS tiki_projects_objects;
CREATE TABLE tiki_projects_objects (
  prjobjId int(10) unsigned NOT NULL auto_increment,
  projectId int(10) NOT NULL,
  objectType varchar(20) NOT NULL,
  objectId int(11) NOT NULL,
  url varchar(250) NULL,
  PRIMARY KEY (prjobjId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS tiki_projects_preferences;
CREATE TABLE tiki_projects_preferences (
  preferenceId int(10) unsigned NOT NULL auto_increment,
  projectId int(10) NOT NULL default '0',
  name varchar(40) NOT NULL,
  value varchar(250) default NULL,
  PRIMARY KEY (preferenceId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;
  


DROP TABLE IF EXISTS tiki_quiz_question_options;
CREATE TABLE tiki_quiz_question_options (
  optionId int(10) NOT NULL auto_increment,
  questionId int(10) default NULL,
  optionText text,
  points int(4) default NULL,
  PRIMARY KEY  (optionId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_quiz_questions;
CREATE TABLE tiki_quiz_questions (
  questionId int(10) NOT NULL auto_increment,
  quizId int(10) default NULL,
  question text,
  position int(4) default NULL,
  type char(1) default NULL,
  maxPoints int(4) default NULL,
  PRIMARY KEY  (questionId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_quiz_results;
CREATE TABLE tiki_quiz_results (
  resultId int(10) NOT NULL auto_increment,
  quizId int(10) default NULL,
  fromPoints int(4) default NULL,
  toPoints int(4) default NULL,
  answer text,
  PRIMARY KEY  (resultId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_quiz_stats;
CREATE TABLE tiki_quiz_stats (
  quizId int(10) NOT NULL default '0',
  questionId int(10) NOT NULL default '0',
  optionId int(10) NOT NULL default '0',
  votes int(10) default NULL,
  PRIMARY KEY  (quizId,questionId,optionId)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_quiz_stats_sum;
CREATE TABLE tiki_quiz_stats_sum (
  quizId int(10) NOT NULL default '0',
  quizName varchar(255) default NULL,
  timesTaken int(10) default NULL,
  avgpoints decimal(5,2) default NULL,
  avgavg decimal(5,2) default NULL,
  avgtime decimal(5,2) default NULL,
  PRIMARY KEY  (quizId)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_quizzes;
CREATE TABLE tiki_quizzes (
  quizId int(10) NOT NULL auto_increment,
  name varchar(255) default NULL,
  description text,
  canRepeat char(1) default NULL,
  storeResults char(1) default NULL,
  questionsPerPage int(4) default NULL,
  timeLimited char(1) default NULL,
  timeLimit int(14) default NULL,
  created int(14) default NULL,
  taken int(10) default NULL,
  immediateFeedback char(1) default NULL,
  showAnswers char(1) default NULL,
  shuffleQuestions char(1) default NULL,
  shuffleAnswers char(1) default NULL,
  publishDate int(14) default NULL,
  expireDate int(14) default NULL,
  bDeleted char(1) default NULL,
  nVersion int(4) NOT NULL,
  nAuthor int(4) default NULL,
  bOnline char(1) default NULL,
  bRandomQuestions char(1) default NULL,
  nRandomQuestions tinyint(4) default NULL,
  bLimitQuestionsPerPage char(1) default NULL,
  nLimitQuestionsPerPage tinyint(4) default NULL,
  bMultiSession char(1) default NULL,
  nCanRepeat tinyint(4) default NULL,
  sGradingMethod varchar(80) default NULL,
  sShowScore varchar(80) default NULL,
  sShowCorrectAnswers varchar(80) default NULL,
  sPublishStats varchar(80) default NULL,
  bAdditionalQuestions char(1) default NULL,
  bForum char(1) default NULL,
  sForum varchar(80) default NULL,
  sPrologue text,
  sData text,
  sEpilogue text,
  passingperct int(4) default 0,
  PRIMARY KEY  (quizId, nVersion)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_received_articles;
CREATE TABLE tiki_received_articles (
  receivedArticleId int(14) NOT NULL auto_increment,
  receivedFromSite varchar(200) default NULL,
  receivedFromUser varchar(200) default NULL,
  receivedDate int(14) default NULL,
  title varchar(80) default NULL,
  authorName varchar(60) default NULL,
  size int(12) default NULL,
  useImage char(1) default NULL,
  image_name varchar(80) default NULL,
  image_type varchar(80) default NULL,
  image_size int(14) default NULL,
  image_x int(4) default NULL,
  image_y int(4) default NULL,
  image_data longblob,
  publishDate int(14) default NULL,
  expireDate int(14) default NULL,
  created int(14) default NULL,
  heading text,
  body longblob,
  hash varchar(32) default NULL,
  author varchar(200) default NULL,
  type varchar(50) default NULL,
  rating decimal(3,2) default NULL,
  PRIMARY KEY  (receivedArticleId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_received_pages;
CREATE TABLE tiki_received_pages (
  receivedPageId int(14) NOT NULL auto_increment,
  pageName varchar(160) NOT NULL default '',
  data longblob,
  description varchar(200) default NULL,
  comment varchar(200) default NULL,
  receivedFromSite varchar(200) default NULL,
  receivedFromUser varchar(200) default NULL,
  receivedDate int(14) default NULL,
  PRIMARY KEY  (receivedPageId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_referer_stats;
CREATE TABLE tiki_referer_stats (
  referer varchar(50) NOT NULL default '',
  hits int(10) default NULL,
  last int(14) default NULL,
  PRIMARY KEY  (referer)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_related_categories;
CREATE TABLE tiki_related_categories (
  categId int(10) NOT NULL default '0',
  relatedTo int(10) NOT NULL default '0',
  PRIMARY KEY  (categId,relatedTo)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_rss_modules;
CREATE TABLE tiki_rss_modules (
  rssId int(8) NOT NULL auto_increment,
  name varchar(30) NOT NULL default '',
  description text,
  url varchar(255) NOT NULL default '',
  refresh int(8) default NULL,
  lastUpdated int(14) default NULL,
  showTitle char(1) default 'n',
  showPubDate char(1) default 'n',
  content longblob,
  PRIMARY KEY  (rssId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_rss_feeds;
CREATE TABLE tiki_rss_feeds (
  name varchar(30) NOT NULL default '',
  rssVer char(1) NOT NULL default '1',
  refresh int(8) default '300',
  lastUpdated int(14) default NULL,
  cache longblob,
  PRIMARY KEY  (name,rssVer)
) TYPE=MyISAM;

DROP TABLE IF EXISTS tiki_searchindex;
CREATE TABLE tiki_searchindex(
  searchword varchar(80) NOT NULL default '',
  location varchar(80) NOT NULL default '',
  page varchar(255) NOT NULL default '',
  count int(11) NOT NULL default '1',
  last_update int(11) NOT NULL default '0',
  PRIMARY KEY  (searchword,location,page),
  KEY last_update (last_update)
) TYPE=MyISAM;

DROP TABLE IF EXISTS tiki_searchsyllable;
CREATE TABLE tiki_searchsyllable(
  syllable varchar(80) NOT NULL default '',
  lastUsed int(11) NOT NULL default '0',
  lastUpdated int(11) NOT NULL default '0',
  PRIMARY KEY  (syllable),
  KEY lastUsed (lastUsed)
) TYPE=MyISAM;

DROP TABLE IF EXISTS tiki_searchwords;
CREATE TABLE tiki_searchwords(
  syllable varchar(80) NOT NULL default '',
  searchword varchar(80) NOT NULL default '',
  PRIMARY KEY  (syllable,searchword)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_search_stats;
CREATE TABLE tiki_search_stats (
  term varchar(50) NOT NULL default '',
  hits int(10) default NULL,
  PRIMARY KEY  (term)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_semaphores;
CREATE TABLE tiki_semaphores (
  semName varchar(250) NOT NULL default '',
  user varchar(200) default NULL,
  timestamp int(14) default NULL,
  PRIMARY KEY  (semName)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_sent_newsletters;
CREATE TABLE tiki_sent_newsletters (
  editionId int(12) NOT NULL auto_increment,
  nlId int(12) NOT NULL default '0',
  users int(10) default NULL,
  sent int(14) default NULL,
  subject varchar(200) default NULL,
  data longblob,
  PRIMARY KEY  (editionId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_sessions;
CREATE TABLE tiki_sessions (
  sessionId varchar(32) NOT NULL default '',
  user varchar(200) default NULL,
  timestamp int(14) default NULL,
  PRIMARY KEY  (sessionId)
) TYPE=MyISAM;

DROP TABLE IF EXISTS tiki_sheet_layout;
CREATE TABLE tiki_sheet_layout (
  sheetId int(8) NOT NULL default '0',
  begin int(10) NOT NULL default '0',
  end int(10) default NULL,
  headerRow int(4) NOT NULL default '0',
  footerRow int(4) NOT NULL default '0',
  className varchar(64) default NULL,
  UNIQUE KEY sheetId (sheetId,begin)
) TYPE=MyISAM;

DROP TABLE IF EXISTS tiki_sheet_values;
CREATE TABLE tiki_sheet_values (
  sheetId int(8) NOT NULL default '0',
  begin int(10) NOT NULL default '0',
  end int(10) default NULL,
  rowIndex int(4) NOT NULL default '0',
  columnIndex int(4) NOT NULL default '0',
  value varchar(255) default NULL,
  calculation varchar(255) default NULL,
  width int(4) NOT NULL default '1',
  height int(4) NOT NULL default '1',
  format varchar(255) default NULL,
  UNIQUE KEY sheetId (sheetId,begin,rowIndex,columnIndex),
  KEY sheetId_2 (sheetId,rowIndex,columnIndex)
) TYPE=MyISAM;

DROP TABLE IF EXISTS tiki_sheets;
CREATE TABLE tiki_sheets (
  sheetId int(8) NOT NULL auto_increment,
  title varchar(200) NOT NULL default '',
  description text,
  author varchar(200) NOT NULL default '',
  PRIMARY KEY  (sheetId)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_shoutbox;
CREATE TABLE tiki_shoutbox (
  msgId int(10) NOT NULL auto_increment,
  message varchar(255) default NULL,
  timestamp int(14) default NULL,
  user varchar(200) default NULL,
  hash varchar(32) default NULL,
  PRIMARY KEY  (msgId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_shoutbox_words;
CREATE TABLE tiki_shoutbox_words (
  word VARCHAR( 40 ) NOT NULL ,
  qty INT DEFAULT '0' NOT NULL ,
  PRIMARY KEY  (word)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_structure_versions;
CREATE TABLE tiki_structure_versions (
  structure_id int(14) NOT NULL auto_increment,
  version int(14) default NULL,
  PRIMARY KEY  (structure_id)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_structures;
CREATE TABLE tiki_structures (
  page_ref_id int(14) NOT NULL auto_increment,
  structure_id int(14) NOT NULL,
  parent_id int(14) default NULL,
  page_id int(14) NOT NULL,
  page_version int(8) default NULL,
  page_alias varchar(240) NOT NULL default '',
  pos int(4) default NULL,
  PRIMARY KEY  (page_ref_id),
  KEY pidpaid (page_id,parent_id)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_submissions;
CREATE TABLE tiki_submissions (
  subId int(8) NOT NULL auto_increment,
  topline varchar(255) default NULL,
  title varchar(80) default NULL,
  subtitle varchar(255) default NULL,
  linkto varchar(255) default NULL,
  lang varchar(16) default NULL,
  authorName varchar(60) default NULL,
  topicId int(14) default NULL,
  topicName varchar(40) default NULL,
  size int(12) default NULL,
  useImage char(1) default NULL,
  image_name varchar(80) default NULL,
  image_caption text default NULL,
  image_type varchar(80) default NULL,
  image_size int(14) default NULL,
  image_x int(4) default NULL,
  image_y int(4) default NULL,
  image_data longblob,
  publishDate int(14) default NULL,
  expireDate int(14) default NULL,
  created int(14) default NULL,
  bibliographical_references text default NULL,
  resume text default NULL,
  heading text,
  body text,
  hash varchar(32) default NULL,
  author varchar(200) default NULL,
  reads int(14) default NULL,
  votes int(8) default NULL,
  points int(14) default NULL,
  type varchar(50) default NULL,
  rating decimal(3,2) default NULL,
  isfloat char(1) default NULL,
  PRIMARY KEY  (subId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_suggested_faq_questions;
CREATE TABLE tiki_suggested_faq_questions (
  sfqId int(10) NOT NULL auto_increment,
  faqId int(10) NOT NULL default '0',
  question text,
  answer text,
  created int(14) default NULL,
  user varchar(200) default NULL,
  PRIMARY KEY  (sfqId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_survey_question_options;
CREATE TABLE tiki_survey_question_options (
  optionId int(12) NOT NULL auto_increment,
  questionId int(12) NOT NULL default '0',
  qoption text,
  votes int(10) default NULL,
  PRIMARY KEY  (optionId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_survey_questions;
CREATE TABLE tiki_survey_questions (
  questionId int(12) NOT NULL auto_increment,
  surveyId int(12) NOT NULL default '0',
  question text,
  options text,
  type char(1) default NULL,
  position int(5) default NULL,
  votes int(10) default NULL,
  value int(10) default NULL,
  average decimal(4,2) default NULL,
  PRIMARY KEY  (questionId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_surveys;
CREATE TABLE tiki_surveys (
  surveyId int(12) NOT NULL auto_increment,
  name varchar(200) default NULL,
  description text,
  taken int(10) default NULL,
  lastTaken int(14) default NULL,
  created int(14) default NULL,
  status char(1) default NULL,
  PRIMARY KEY  (surveyId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_tags;
CREATE TABLE tiki_tags (
  tagName varchar(80) NOT NULL default '',
  pageName varchar(160) NOT NULL default '',
  hits int(8) default NULL,
  description varchar(200) default NULL,
  data longblob,
  lastModif int(14) default NULL,
  comment varchar(200) default NULL,
  version int(8) NOT NULL default '0',
  user varchar(200) default NULL,
  ip varchar(15) default NULL,
  flag char(1) default NULL,
  PRIMARY KEY  (tagName,pageName)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_theme_control_categs;
CREATE TABLE tiki_theme_control_categs (
  categId int(12) NOT NULL default '0',
  theme varchar(250) NOT NULL default '',
  PRIMARY KEY  (categId)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_theme_control_objects;
CREATE TABLE tiki_theme_control_objects (
  objId varchar(250) NOT NULL default '',
  type varchar(250) NOT NULL default '',
  name varchar(250) NOT NULL default '',
  theme varchar(250) NOT NULL default '',
  PRIMARY KEY  (objId)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_theme_control_sections;
CREATE TABLE tiki_theme_control_sections (
  section varchar(250) NOT NULL default '',
  theme varchar(250) NOT NULL default '',
  PRIMARY KEY  (section)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_topics;
CREATE TABLE tiki_topics (
  topicId int(14) NOT NULL auto_increment,
  name varchar(40) default NULL,
  image_name varchar(80) default NULL,
  image_type varchar(80) default NULL,
  image_size int(14) default NULL,
  image_data longblob,
  active char(1) default NULL,
  created int(14) default NULL,
  PRIMARY KEY  (topicId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_tracker_fields;
CREATE TABLE tiki_tracker_fields (
  fieldId int(12) NOT NULL auto_increment,
  trackerId int(12) NOT NULL default '0',
  name varchar(255) default NULL,
  options text,
  position int(4) default NULL,
  type char(1) default NULL,
  isMain char(1) default NULL,
  isTblVisible char(1) default NULL,
  isSearchable char(1) default NULL,
  isPublic char(1) NOT NULL default 'n',
  isHidden char(1) NOT NULL default 'n',
  isMandatory char(1) NOT NULL default 'n',
  PRIMARY KEY  (fieldId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_tracker_item_attachments;
CREATE TABLE tiki_tracker_item_attachments (
  attId int(12) NOT NULL auto_increment,
  itemId int(12) NOT NULL default 0,
  filename varchar(80) default NULL,
  filetype varchar(80) default NULL,
  filesize int(14) default NULL,
  user varchar(200) default NULL,
  data longblob,
  longdesc blob,
  path varchar(255) default NULL,
  downloads int(10) default NULL,
  version varchar(40) default NULL,
  created int(14) default NULL,
  comment varchar(250) default NULL,
  PRIMARY KEY  (attId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_tracker_item_comments;
CREATE TABLE tiki_tracker_item_comments (
  commentId int(12) NOT NULL auto_increment,
  itemId int(12) NOT NULL default '0',
  user varchar(200) default NULL,
  data text,
  title varchar(200) default NULL,
  posted int(14) default NULL,
  PRIMARY KEY  (commentId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_tracker_item_fields;
CREATE TABLE tiki_tracker_item_fields (
  itemId int(12) NOT NULL default '0',
  fieldId int(12) NOT NULL default '0',
  value text,
  PRIMARY KEY  (itemId,fieldId)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_tracker_items;
CREATE TABLE tiki_tracker_items (
  itemId int(12) NOT NULL auto_increment,
  trackerId int(12) NOT NULL default '0',
  created int(14) default NULL,
  status char(1) default NULL,
  lastModif int(14) default NULL,
  PRIMARY KEY  (itemId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_tracker_options;
CREATE TABLE tiki_tracker_options (
  trackerId int(12) NOT NULL default '0',
  name varchar(80) NOT NULL default '',
  value text default NULL,
  PRIMARY KEY  (trackerId,name(30))
) TYPE=MyISAM ;



DROP TABLE IF EXISTS tiki_trackers;
CREATE TABLE tiki_trackers (
  trackerId int(12) NOT NULL auto_increment,
  name varchar(255) default NULL,
  description text,
  created int(14) default NULL,
  lastModif int(14) default NULL,
  showCreated char(1) default NULL,
  showStatus char(1) default NULL,
  showLastModif char(1) default NULL,
  useComments char(1) default NULL,
  showComments char(1) default NULL,
  useAttachments char(1) default NULL,
  showAttachments char(1) default NULL,
  orderAttachments varchar(255) NOT NULL default 'filename,created,filesize,downloads,desc',
  items int(10) default NULL,
  PRIMARY KEY  (trackerId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_untranslated;
CREATE TABLE tiki_untranslated (
  id int(14) NOT NULL auto_increment,
  source tinyblob NOT NULL,
  lang char(16) NOT NULL default '',
  PRIMARY KEY  (source(255),lang),
  UNIQUE KEY id (id),
  KEY id_2 (id)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_user_answers;
CREATE TABLE tiki_user_answers (
  userResultId int(10) NOT NULL default '0',
  quizId int(10) NOT NULL default '0',
  questionId int(10) NOT NULL default '0',
  optionId int(10) NOT NULL default '0',
  PRIMARY KEY  (userResultId,quizId,questionId,optionId)
) TYPE=MyISAM;





CREATE TABLE `tiki_user_answers_uploads` (
  `answerUploadId` int(4) NOT NULL auto_increment,
  `userResultId` int(11) NOT NULL default '0',
  `questionId` int(11) NOT NULL default '0',
  `filename` varchar(255) NOT NULL default '',
  `filetype` varchar(64) NOT NULL default '',
  `filesize` varchar(255) NOT NULL default '',
  `filecontent` longblob NOT NULL,
  PRIMARY KEY  (`answerUploadId`)
) TYPE=MyISAM;



DROP TABLE IF EXISTS tiki_user_assigned_modules;
CREATE TABLE tiki_user_assigned_modules (
  name varchar(200) NOT NULL default '',
  position char(1) default NULL,
  ord int(4) default NULL,
  type char(1) default NULL,
  user varchar(200) NOT NULL default '',
  PRIMARY KEY  (name,user)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_user_bookmarks_folders;
CREATE TABLE tiki_user_bookmarks_folders (
  folderId int(12) NOT NULL auto_increment,
  parentId int(12) default NULL,
  user varchar(200) NOT NULL default '',
  name varchar(30) default NULL,
  PRIMARY KEY  (user,folderId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_user_bookmarks_urls;
CREATE TABLE tiki_user_bookmarks_urls (
  urlId int(12) NOT NULL auto_increment,
  name varchar(30) default NULL,
  url varchar(250) default NULL,
  data longblob,
  lastUpdated int(14) default NULL,
  folderId int(12) NOT NULL default '0',
  user varchar(200) NOT NULL default '',
  PRIMARY KEY  (urlId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_user_mail_accounts;
CREATE TABLE tiki_user_mail_accounts (
  accountId int(12) NOT NULL auto_increment,
  user varchar(200) NOT NULL default '',
  account varchar(50) NOT NULL default '',
  pop varchar(255) default NULL,
  current char(1) default NULL,
  port int(4) default NULL,
  username varchar(100) default NULL,
  pass varchar(100) default NULL,
  msgs int(4) default NULL,
  smtp varchar(255) default NULL,
  useAuth char(1) default NULL,
  smtpPort int(4) default NULL,
  PRIMARY KEY  (accountId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_user_menus;
CREATE TABLE tiki_user_menus (
  user varchar(200) NOT NULL default '',
  menuId int(12) NOT NULL auto_increment,
  url varchar(250) default NULL,
  name varchar(40) default NULL,
  position int(4) default NULL,
  mode char(1) default NULL,
  PRIMARY KEY  (menuId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_user_modules;
CREATE TABLE tiki_user_modules (
  name varchar(200) NOT NULL default '',
  title varchar(40) default NULL,
  data longblob,
  parse char(1) default NULL,
  PRIMARY KEY  (name)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_user_notes;
CREATE TABLE tiki_user_notes (
  user varchar(200) NOT NULL default '',
  noteId int(12) NOT NULL auto_increment,
  created int(14) default NULL,
  name varchar(255) default NULL,
  lastModif int(14) default NULL,
  data text,
  size int(14) default NULL,
  parse_mode varchar(20) default NULL,
  PRIMARY KEY  (noteId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_user_postings;
CREATE TABLE tiki_user_postings (
  user varchar(200) NOT NULL default '',
  posts int(12) default NULL,
  last int(14) default NULL,
  first int(14) default NULL,
  level int(8) default NULL,
  PRIMARY KEY  (user)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_user_preferences;
CREATE TABLE tiki_user_preferences (
  user varchar(200) NOT NULL default '',
  prefName varchar(40) NOT NULL default '',
  value varchar(250) default NULL,
  PRIMARY KEY  (user,prefName)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_user_quizzes;
CREATE TABLE tiki_user_quizzes (
  user varchar(100) default NULL,
  quizId int(10) default NULL,
  timestamp int(14) default NULL,
  timeTaken int(14) default NULL,
  points int(12) default NULL,
  maxPoints int(12) default NULL,
  resultId int(10) default NULL,
  userResultId int(10) NOT NULL auto_increment,
  PRIMARY KEY  (userResultId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_user_taken_quizzes;
CREATE TABLE tiki_user_taken_quizzes (
  user varchar(200) NOT NULL default '',
  quizId varchar(255) NOT NULL default '',
  PRIMARY KEY  (user,quizId)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_user_tasks_history;
CREATE TABLE tiki_user_tasks_history (
  belongs_to integer(14) NOT NULL,                   -- the fist task in a history it has the same id as the task id
  task_version integer(4) NOT NULL DEFAULT 0,        -- version number for the history it starts with 0
  title varchar(250) NOT NULL,                       -- title
  description text DEFAULT NULL,                     -- description
  start integer(14) DEFAULT NULL,                    -- date of the starting, if it is not set than there is not starting date
  end integer(14) DEFAULT NULL,                      -- date of the end, if it is not set than there is not dealine
  lasteditor varchar(200) NOT NULL,                  -- lasteditor: username of last editior
  lastchanges integer(14) NOT NULL,                  -- date of last changes
  priority integer(2) NOT NULL DEFAULT 3,                     -- priority
  completed integer(14) DEFAULT NULL,                -- date of the completation if it is null it is not yet completed
  deleted integer(14) DEFAULT NULL,                  -- date of the deleteation it it is null it is not deleted
  status char(1) DEFAULT NULL,                       -- null := waiting, 
                                                     -- o := open / in progress, 
                                                     -- c := completed -> (percentage = 100) 
  percentage int(4) DEFAULT NULL,
  accepted_creator char(1) DEFAULT NULL,             -- y - yes, n - no, null - waiting
  accepted_user char(1) DEFAULT NULL,                -- y - yes, n - no, null - waiting
  PRIMARY KEY (belongs_to, task_version)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_user_tasks;
CREATE TABLE tiki_user_tasks (
  taskId integer(14) NOT NULL auto_increment,        -- task id
  last_version integer(4) NOT NULL DEFAULT 0,        -- last version of the task starting with 0
  user varchar(200) NOT NULL,                        -- task user
  creator varchar(200) NOT NULL,                     -- username of creator
  public_for_group varchar(30) DEFAULT NULL,         -- this group can also view the task, if it is null it is not public
  rights_by_creator char(1) DEFAULT NULL,            -- null the user can delete the task, 
  created integer(14) NOT NULL,                      -- date of the creation
  PRIMARY KEY (taskId),
  UNIQUE(creator, created)
) TYPE=MyISAM AUTO_INCREMENT=1;



DROP TABLE IF EXISTS tiki_user_votings;
CREATE TABLE tiki_user_votings (
  user varchar(200) NOT NULL default '',
  id varchar(255) NOT NULL default '',
  optionId int(10) NOT NULL default 0,
  PRIMARY KEY  (user,id)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_user_watches;
CREATE TABLE tiki_user_watches (
  user varchar(200) NOT NULL default '',
  event varchar(40) NOT NULL default '',
  object varchar(200) NOT NULL default '',
  hash varchar(32) default NULL,
  title varchar(250) default NULL,
  type varchar(200) default NULL,
  url varchar(250) default NULL,
  email varchar(200) default NULL,
  PRIMARY KEY  (user,event,object)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_userfiles;
CREATE TABLE tiki_userfiles (
  user varchar(200) NOT NULL default '',
  fileId int(12) NOT NULL auto_increment,
  name varchar(200) default NULL,
  filename varchar(200) default NULL,
  filetype varchar(200) default NULL,
  filesize varchar(200) default NULL,
  data longblob,
  hits int(8) default NULL,
  isFile char(1) default NULL,
  path varchar(255) default NULL,
  created int(14) default NULL,
  PRIMARY KEY  (fileId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_userpoints;
CREATE TABLE tiki_userpoints (
  user varchar(200) default NULL,
  points decimal(8,2) default NULL,
  voted int(8) default NULL
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_users;
CREATE TABLE tiki_users (
  user varchar(200) NOT NULL default '',
  password varchar(40) default NULL,
  email varchar(200) default NULL,
  lastLogin int(14) default NULL,
  PRIMARY KEY  (user)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_webmail_contacts;
CREATE TABLE tiki_webmail_contacts (
  contactId int(12) NOT NULL auto_increment,
  firstName varchar(80) default NULL,
  lastName varchar(80) default NULL,
  email varchar(250) default NULL,
  nickname varchar(200) default NULL,
  user varchar(200) NOT NULL default '',
  PRIMARY KEY  (contactId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_webmail_messages;
CREATE TABLE tiki_webmail_messages (
  accountId int(12) NOT NULL default '0',
  mailId varchar(255) NOT NULL default '',
  user varchar(200) NOT NULL default '',
  isRead char(1) default NULL,
  isReplied char(1) default NULL,
  isFlagged char(1) default NULL,
  PRIMARY KEY  (accountId,mailId)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_wiki_attachments;
CREATE TABLE tiki_wiki_attachments (
  attId int(12) NOT NULL auto_increment,
  page varchar(200) NOT NULL default '',
  filename varchar(80) default NULL,
  filetype varchar(80) default NULL,
  filesize int(14) default NULL,
  user varchar(200) default NULL,
  data longblob,
  path varchar(255) default NULL,
  downloads int(10) default NULL,
  created int(14) default NULL,
  comment varchar(250) default NULL,
  PRIMARY KEY  (attId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS tiki_zones;
CREATE TABLE tiki_zones (
  zone varchar(40) NOT NULL default '',
  PRIMARY KEY  (zone)
) TYPE=MyISAM;

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
) TYPE=MyISAM;


DROP TABLE IF EXISTS users_grouppermissions;
CREATE TABLE users_grouppermissions (
  groupName varchar(255) NOT NULL default '',
  permName varchar(30) NOT NULL default '',
  value char(1) default '',
  PRIMARY KEY  (groupName(30),permName)
) TYPE=MyISAM;



DROP TABLE IF EXISTS users_groups;
CREATE TABLE users_groups (
  groupName varchar(255) NOT NULL default '',
  groupDesc varchar(255) default NULL,
  groupHome varchar(255),
  groupHomeLocalized char(1) default 'n',
  usersTrackerId int(11),
  groupTrackerId int(11),
  usersFieldId int(11),
  groupFieldId int(11),
  PRIMARY KEY  (groupName(30))
) TYPE=MyISAM;


DROP TABLE IF EXISTS users_objectpermissions;
CREATE TABLE users_objectpermissions (
  groupName varchar(255) NOT NULL default '',
  permName varchar(30) NOT NULL default '',
  objectType varchar(20) NOT NULL default '',
  objectId varchar(32) NOT NULL default '',
  PRIMARY KEY  (objectId, objectType, groupName(30),permName)
) TYPE=MyISAM;


DROP TABLE IF EXISTS users_permissions;
CREATE TABLE users_permissions (
  permName varchar(30) NOT NULL default '',
  permDesc varchar(250) default NULL,
  level varchar(80) default NULL,
  type varchar(20) default NULL,
  PRIMARY KEY  (permName)
) TYPE=MyISAM;




DROP TABLE IF EXISTS users_usergroups;
CREATE TABLE users_usergroups (
  userId int(8) NOT NULL default '0',
  groupName varchar(255) NOT NULL default '',
  PRIMARY KEY  (userId,groupName(30))
) TYPE=MyISAM;


DROP TABLE IF EXISTS users_users;
CREATE TABLE users_users (
  userId int(8) NOT NULL auto_increment,
  email varchar(200) default NULL,
  login varchar(40) NOT NULL default '',
  password varchar(30) default '',
  provpass varchar(30) default NULL,
  default_group varchar(255),
  lastLogin int(14) default NULL,
  currentLogin int(14) default NULL,
  registrationDate int(14) default NULL,
  challenge varchar(32) default NULL,
  pass_due int(14) default NULL,
  hash varchar(32) default NULL,
  created int(14) default NULL,
  avatarName varchar(80) default NULL,
  avatarSize int(14) default NULL,
  avatarFileType varchar(250) default NULL,
  avatarData longblob,
  avatarLibName varchar(200) default NULL,
  avatarType char(1) default NULL,
  score int(4) NOT NULL default 0,
  PRIMARY KEY  (userId),
  KEY score (score)
) TYPE=MyISAM AUTO_INCREMENT=1 ;




DROP TABLE IF EXISTS tiki_integrator_reps;
CREATE TABLE tiki_integrator_reps (
  repID int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  path varchar(255) NOT NULL default '',
  start_page varchar(255) NOT NULL default '',
  css_file varchar(255) NOT NULL default '',
  visibility char(1) NOT NULL default 'y',
  cacheable char(1) NOT NULL default 'y',
  expiration int(11) NOT NULL default '0',
  description text NOT NULL,
  PRIMARY KEY  (repID)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_integrator_rules;
CREATE TABLE tiki_integrator_rules (
  ruleID int(11) NOT NULL auto_increment,
  repID int(11) NOT NULL default '0',
  ord int(2) unsigned NOT NULL default '0',
  srch blob NOT NULL,
  repl blob NOT NULL,
  type char(1) NOT NULL default 'n',
  casesense char(1) NOT NULL default 'y',
  rxmod varchar(20) NOT NULL default '',
  enabled char(1) NOT NULL default 'n',
  description text NOT NULL,
  PRIMARY KEY  (ruleID),
  KEY repID (repID)
) TYPE=MyISAM;



DROP TABLE IF EXISTS tiki_quicktags;
CREATE TABLE tiki_quicktags (
  tagId int(4) unsigned NOT NULL auto_increment,
  taglabel varchar(255) default NULL,
  taginsert text,
  tagicon varchar(255) default NULL,
  tagcategory varchar(255) default NULL,
  PRIMARY KEY  (tagId),
  KEY taglabel (taglabel),
  KEY tagcategory (tagcategory)
) TYPE=MyISAM AUTO_INCREMENT=1 ;



DROP TABLE IF EXISTS hw_actionlog;
DROP TABLE IF EXISTS tiki_hw_actionlog;
CREATE TABLE tiki_hw_actionlog (
  action varchar(255) NOT NULL default '',
  lastModif int(14) NOT NULL default '0',
  pageId int(14) default NULL,
  user varchar(200) default NULL,
  ip varchar(15) default NULL,
  comment varchar(200) default NULL,
  PRIMARY KEY  (lastModif)
) TYPE=MyISAM;

DROP TABLE IF EXISTS hw_assignments;
DROP TABLE IF EXISTS tiki_hw_assignments;
CREATE TABLE tiki_hw_assignments (
  assignmentId int(8) NOT NULL auto_increment,
  title varchar(80) default NULL,
  teacherName varchar(40) NOT NULL default '',
  created int(14) NOT NULL default '0',
  dueDate int(14) default NULL,
  modified int(14) NOT NULL default '0',
  heading text,
  body text,
  deleted tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (assignmentId),
  KEY dueDate (dueDate)
) TYPE=MyISAM;

DROP TABLE IF EXISTS hw_grading_queue;
DROP TABLE IF EXISTS tiki_hw_grading_queue;
CREATE TABLE tiki_hw_grading_queue (
  id int(14) NOT NULL auto_increment,
  status int(4) default NULL,
  submissionDate int(14) default NULL,
  userLogin varchar(40) NOT NULL default '',
  userIp varchar(15) default NULL,
  pageId int(14) default NULL,
  pageDate int(14) default NULL,
  pageVersion int(14) default NULL,
  assignmentId int(14) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

DROP TABLE IF EXISTS hw_history;
DROP TABLE IF EXISTS tiki_hw_history;
CREATE TABLE tiki_hw_history (
  id int(14) NOT NULL default '0',
  version int(8) NOT NULL default '0',
  lastModif int(14) NOT NULL default '0',
  user varchar(200) NOT NULL default '',
  ip varchar(15) NOT NULL default '',
  comment varchar(200) default NULL,
  data text,
  PRIMARY KEY  (id,version)
) TYPE=MyISAM;

DROP TABLE IF EXISTS hw_pages;
DROP TABLE IF EXISTS tiki_hw_pages;
CREATE TABLE tiki_hw_pages (
  id int(14) NOT NULL auto_increment,
  assignmentId int(14) NOT NULL default '0',
  studentName varchar(200) NOT NULL default '',
  data text,
  description varchar(200) default NULL,
  lastModif int(14) default NULL,
  user varchar(200) default NULL,
  comment varchar(200) default NULL,
  version int(8) NOT NULL default '0',
  ip varchar(15) default NULL,
  flag char(1) default NULL,
  points int(8) default NULL,
  votes int(8) default NULL,
  cache text,
  wiki_cache int(10) default '0',
  cache_timestamp int(14) default NULL,
  page_size int(10) unsigned default '0',
  lockUser varchar(200) default NULL,
  lockExpires int(14) default '0',
  PRIMARY KEY  (studentName,assignmentId),
  KEY id (id),
  KEY assignmentId (assignmentId),
  KEY studentName (studentName)
) TYPE=MyISAM;


DROP TABLE IF EXISTS tiki_translated_objects;
CREATE TABLE tiki_translated_objects (
  traId int(14) NOT NULL auto_increment,
  type varchar(50) NOT NULL,
  objId varchar(255) NOT NULL,
  lang varchar(16) default NULL,
  PRIMARY KEY (type, objId),
  KEY tradid (traId)
) TYPE=MyISAM AUTO_INCREMENT=1;



DROP TABLE IF EXISTS tiki_friends;
CREATE TABLE tiki_friends (
  user char(40) NOT NULL default '',
  friend char(40) NOT NULL default '',
  PRIMARY KEY  (user,friend)
) TYPE=MyISAM;

DROP TABLE IF EXISTS tiki_friendship_requests;
CREATE TABLE tiki_friendship_requests (
  userFrom char(40) NOT NULL default '',
  userTo char(40) NOT NULL default '',
  tstamp timestamp(14) NOT NULL,
  PRIMARY KEY  (userFrom,userTo)
) TYPE=MyISAM;

DROP TABLE IF EXISTS tiki_score;
CREATE TABLE tiki_score (
  event varchar(40) NOT NULL default '',
  score int(11) NOT NULL default '0',
  expiration int(11) NOT NULL default '0',
  PRIMARY KEY  (event)
) TYPE=MyISAM;

DROP TABLE IF EXISTS tiki_users_score;
CREATE TABLE tiki_users_score (
  user char(40) NOT NULL default '',
  event_id char(40) NOT NULL default '',
  expire int(14) NOT NULL default '0',
  tstamp timestamp(14) NOT NULL,
  PRIMARY KEY  (user,event_id),
  KEY user (user,event_id,expire)
) TYPE=MyISAM;




DROP TABLE IF EXISTS tiki_file_handlers;
CREATE TABLE tiki_file_handlers (
	mime_type varchar(64) default NULL,
	cmd varchar(238) default NULL
) TYPE=MyISAM;


DROP TABLE IF EXISTS `tiki_stats`;
CREATE TABLE `tiki_stats` (
  `object` varchar(255) NOT NULL default '',
  `type` varchar(20) NOT NULL default '',
  `day` int(14) NOT NULL default '0',
  `hits` int(14) NOT NULL default '0',
  PRIMARY KEY  (`object`,`type`,`day`)
) TYPE=MyISAM;

