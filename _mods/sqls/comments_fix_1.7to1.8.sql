#########
# The stuff here is to remove the md5 hashes. -rlpowell
#########

# To execute this file do the following:
#
# $ mysql -f dbname <comments_fix1.sql
#
# where dbname is the name of your tiki database.
#
# For example, if your tiki database is named tiki (not a bad
# choice), type:
#
# $ mysql -f tiki <comments_fix1.sql


#############
# You may *****NOT**** execute this command as often as you like!
#
# Every run of this command after the first will *DESTROY* any
# comments posted since the run before!
#############

# Save the old comments!
rename table tiki_comments to old_tiki_comments;

# New comments table; really just a change to object.
CREATE TABLE tiki_comments (
  threadId integer(14) NOT NULL auto_increment,
  object varchar(255) NOT NULL DEFAULT '',
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

# Move over all the md5 stuff to the new format.
insert into tiki_comments select 
tc.threadId,
concat('wiki page', tp.pagename) 'object',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old_tiki_comments tc, tiki_pages tp
where tc.object = md5(concat('wiki page', tp.pageName));

insert into tiki_comments select 
tc.threadId,
concat('forum', tf.forumid) 'object',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old_tiki_comments tc, tiki_forums tf
where tc.object = md5(concat('forum', tf.forumId));

insert into tiki_comments select 
tc.threadId,
concat('postId', tb.postId) 'object',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old_tiki_comments tc, tiki_blog_posts tb
where tc.object = md5(concat('postId', tb.postId));

insert into tiki_comments select 
tc.threadId,
concat('post', tb.postId) 'object',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old_tiki_comments tc, tiki_blog_posts tb
where tc.object = md5(concat('post', tb.postId));

insert into tiki_comments select 
tc.threadId,
concat('blog', tb.postId) 'object',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old_tiki_comments tc, tiki_blog_posts tb
where tc.object = md5(concat('blog', tb.postId));

insert into tiki_comments select 
tc.threadId,
concat('image_gallery', tg.galleryId) 'object',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old_tiki_comments tc, tiki_galleries tg
where tc.object = md5(concat('image_gallery', tg.galleryId));

insert into tiki_comments select 
tc.threadId,
concat('image gallery', tg.galleryId) 'object',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old_tiki_comments tc, tiki_galleries tg
where tc.object = md5(concat('image gallery', tg.galleryId));

insert into tiki_comments select 
tc.threadId,
concat('file gallery', tg.galleryId) 'object',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old_tiki_comments tc, tiki_galleries tg
where tc.object = md5(concat('file gallery', tg.galleryId));

insert into tiki_comments select 
tc.threadId,
concat('faq', tf.faqId) 'object',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old_tiki_comments tc, tiki_faqs tf
where tc.object = md5(concat('faq', tf.faqId));

insert into tiki_comments select 
tc.threadId,
concat('article', ta.articleId) 'object',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old_tiki_comments tc, tiki_articles ta
where tc.object = md5(concat('article', ta.articleId));

insert into tiki_comments select 
tc.threadId,
concat('poll', tp.pollId) 'object',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old_tiki_comments tc, tiki_polls tp
where tc.object = md5(concat('poll', tp.pollId));

drop table old_tiki_comments;

#########
# The stuff here is to take the comments table with md5 hashes
# removed, and break the object names out into the object field and
# a type field encoding what type of object it is.  -rlpowell
#########

# To execute this file do the following:
#
# $ mysql -f dbname <comments_fix2.sql
#
# where dbname is the name of your tiki database.
#
# For example, if your tiki database is named tiki (not a bad
# choice), type:
#
# $ mysql -f tiki <comments_fix2.sql


#############
# You may *****NOT**** execute this command as often as you like!
#
# Every run of this command after the first will *DESTROY* any
# comments posted since the run before!
#############

# Save the old comments!
rename table tiki_comments to old2_tiki_comments;

# New comments table; really just a change to object.
CREATE TABLE tiki_comments (
  threadId integer(14) NOT NULL auto_increment,
  object varchar(255) NOT NULL DEFAULT '',
  objectType varchar(32) NOT NULL DEFAULT '',
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

# Move over all the md5 stuff to the new format.
insert into tiki_comments select 
tc.threadId,
tp.pagename 'object',
'wiki page',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old2_tiki_comments tc, tiki_pages tp
where tc.object = concat('wiki page', tp.pageName);

insert into tiki_comments select 
tc.threadId,
tf.forumid 'object',
'forum',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old2_tiki_comments tc, tiki_forums tf
where tc.object = concat('forum', tf.forumId);

insert into tiki_comments select 
tc.threadId,
tb.postId 'object',
'postId',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old2_tiki_comments tc, tiki_blog_posts tb
where tc.object = concat('postId', tb.postId);

insert into tiki_comments select 
tc.threadId,
tb.postId 'object',
'post',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old2_tiki_comments tc, tiki_blog_posts tb
where tc.object = concat('post', tb.postId);

insert into tiki_comments select 
tc.threadId,
tb.postId 'object',
'blog',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old2_tiki_comments tc, tiki_blog_posts tb
where tc.object = concat('blog', tb.postId);

insert into tiki_comments select 
tc.threadId,
tg.galleryId 'object',
'image gallery',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old2_tiki_comments tc, tiki_galleries tg
where tc.object = concat('image_gallery', tg.galleryId);

insert into tiki_comments select 
tc.threadId,
tg.galleryId 'object',
'image gallery',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old2_tiki_comments tc, tiki_galleries tg
where tc.object = concat('image gallery', tg.galleryId);

insert into tiki_comments select 
tc.threadId,
tg.galleryId 'object',
'file gallery',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old2_tiki_comments tc, tiki_galleries tg
where tc.object = concat('file gallery', tg.galleryId);

insert into tiki_comments select 
tc.threadId,
tf.faqId 'object',
'faq',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old2_tiki_comments tc, tiki_faqs tf
where tc.object = concat('faq', tf.faqId);

insert into tiki_comments select 
tc.threadId,
ta.articleId 'object',
'article',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old2_tiki_comments tc, tiki_articles ta
where tc.object = concat('article', ta.articleId);

insert into tiki_comments select 
tc.threadId,
tp.pollId 'object',
'poll',
tc.parentId,
tc.userName,
tc.commentDate,
tc.hits,
tc.type,
tc.points,
tc.votes,
tc.average,
tc.title,
tc.data,
tc.hash,
tc.user_ip,
tc.summary,
tc.smiley,
tc.message_id,
tc.in_reply_to
from old2_tiki_comments tc, tiki_polls tp
where tc.object = concat('poll', tp.pollId);

drop table old2_tiki_comments;

ALTER TABLE `tiki_comments` ADD `comment_rating` TINYINT( 2 ) ;

