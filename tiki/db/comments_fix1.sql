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
