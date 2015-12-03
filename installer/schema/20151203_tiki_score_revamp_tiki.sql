#Add needed columns
alter table tiki_score change event event varchar(255); #increase varchar length
alter table tiki_score add column data TEXT;
alter table tiki_score add column reversalEvent varchar(255);
# Insert old values from tiki_score into new format
# Insert user stuff. Login, message, friend pts.
INSERT INTO tiki_score (event, data)
SELECT "tiki.user.login" as event, concat('[{"ruleId":"User logs in","recipientType":"user","recipient":"user","score":"',score,'","validObjectIds":[""],"expiration":""}]') as data from tiki_score where event='login';
INSERT INTO tiki_score (event, data)
SELECT "tiki.user.view" as event, concat('[{"ruleId":"See other user''s profile","recipientType":"user","recipient":"user","score":"',max(case when event = 'profile_see' then score else 0 end),'","validObjectIds":[""],"expiration":""},{"ruleId":"Have your profile seen","recipientType":"user","recipient":"object","score":"',max(case when event = 'profile_is_seen' then score else 0 end),'","validObjectIds":[""],"expiration":""}]') as data from tiki_score limit 1;
INSERT INTO tiki_score (event, data)
SELECT "tiki.user.friend" as event, concat('[{"ruleId":"Make friends","recipientType":"user","recipient":"user","score":"',score,'","validObjectIds":[""],"expiration":""}]') as data from tiki_score where event='friend_new';
INSERT INTO tiki_score (event, data)
SELECT "tiki.user.message" as event, concat('[{"ruleId":"Send message","recipientType":"user","recipient":"user","score":"',max(case when event = 'message_send' then score else 0 end),'","validObjectIds":[""],"expiration":""},{"ruleId":"Receive message","recipientType":"user","recipient":"object","score":"',max(case when event = 'message_receive' then score else 0 end),'","validObjectIds":[""],"expiration":""}]') as data from tiki_score limit 1;
# Articles
INSERT INTO tiki_score (event, data)
SELECT "tiki.article.create" as event, concat('[{"ruleId":"Publish new article","recipientType":"user","recipient":"user","score":"',score,'","validObjectIds":[""],"expiration":""}]') as data from tiki_score where event='article_new';
INSERT INTO tiki_score (event, data)
SELECT "tiki.article.view" as event, concat('[{"ruleId":"Read an article","recipientType":"user","recipient":"user","score":"',max(case when event = 'article_read' then score else 0 end),'","validObjectIds":[""],"expiration":""},{"ruleId":"Have your article read","recipientType":"user","recipient":"author","score":"',max(case when event = 'article_is_read' then score else 0 end),'","validObjectIds":[""],"expiration":""}]') as data from tiki_score limit 1;
# File Gallery
INSERT INTO tiki_score (event, data)
SELECT "tiki.filegallery.create" as event, concat('[{"ruleId":"Create new file gallery","recipientType":"user","recipient":"user","score":"',score,'","validObjectIds":[""],"expiration":""}]') as data from tiki_score where event='fgallery_new';
INSERT INTO tiki_score (event, data)
SELECT "tiki.file.create" as event, concat('[{"ruleId":"Upload new file to gallery","recipientType":"user","recipient":"user","score":"',score,'","validObjectIds":[""],"expiration":""}]') as data from tiki_score where event='fgallery_new_file';
INSERT INTO tiki_score (event, data)
SELECT "tiki.file.download" as event, concat('[{"ruleId":"Download other user''s file","recipientType":"user","recipient":"user","score":"',max(case when event = 'fgallery_download' then score else 0 end),'","validObjectIds":[""],"expiration":""},{"ruleId":"Have your file downloaded","recipientType":"user","recipient":"owner","score":"',max(case when event = 'fgallery_is_downloaded' then score else 0 end),'","validObjectIds":[""],"expiration":""}]') as data from tiki_score limit 1;
# Image Gallery
INSERT INTO tiki_score (event, data)
SELECT "tiki.imagegallery.create" as event, concat('[{"ruleId":"Create new image gallery","recipientType":"user","recipient":"user","score":"',score,'","validObjectIds":[""],"expiration":""}]') as data from tiki_score where event='igallery_new';
INSERT INTO tiki_score (event, data)
SELECT "tiki.image.create" as event, concat('[{"ruleId":"Upload new image to gallery","recipientType":"user","recipient":"user","score":"',score,'","validObjectIds":[""],"expiration":""}]') as data from tiki_score where event='igallery_new_img';
INSERT INTO tiki_score (event, data)
SELECT "tiki.image.view" as event, concat('[{"ruleId":"See other user''s image","recipientType":"user","recipient":"user","score":"',max(case when event = 'igallery_see_img' then score else 0 end),'","validObjectIds":[""],"expiration":""},{"ruleId":"Have your image seen","recipientType":"user","recipient":"owner","score":"',max(case when event = 'igallery_img_seen' then score else 0 end),'","validObjectIds":[""],"expiration":""}]') as data from tiki_score limit 1;
#Blogs
INSERT INTO tiki_score (event, data)
SELECT "tiki.blog.create" as event, concat('[{"ruleId":"Create new blog","recipientType":"user","recipient":"user","score":"',score,'","validObjectIds":[""],"expiration":""}]') as data from tiki_score where event='blog_new';
INSERT INTO tiki_score (event, data)
SELECT "tiki.blogpost.create" as event, concat('[{"ruleId":"Post in a blog","recipientType":"user","recipient":"user","score":"',score,'","validObjectIds":[""],"expiration":""}]') as data from tiki_score where event='blog_post';
INSERT INTO tiki_score (event, data)
SELECT "tiki.blog.view" as event, concat('[{"ruleId":"Read other user''s blog","recipientType":"user","recipient":"user","score":"',max(case when event = 'blog_read' then score else 0 end),'","validObjectIds":[""],"expiration":""},{"ruleId":"Have your blog read","recipientType":"user","recipient":"author","score":"',max(case when event = 'blog_is_read' then score else 0 end),'","validObjectIds":[""],"expiration":""}]') as data from tiki_score limit 1;
# Wikis
INSERT INTO tiki_score (event, data)
SELECT "tiki.wiki.create" as event, concat('[{"ruleId":"Create a wiki page","recipientType":"user","recipient":"user","score":"',score,'","validObjectIds":[""],"expiration":""}]') as data from tiki_score where event='wiki_new';
INSERT INTO tiki_score (event, data)
SELECT "tiki.wiki.update" as event, concat('[{"ruleId":"Edit an existing wiki page","recipientType":"user","recipient":"user","score":"',score,'","validObjectIds":[""],"expiration":""}]') as data from tiki_score where event='wiki_edit';
INSERT INTO tiki_score (event, data)
SELECT "tiki.wiki.attachfile" as event, concat('[{"ruleId":"Attach file to wiki page","recipientType":"user","recipient":"user","score":"',score,'","validObjectIds":[""],"expiration":""}]') as data from tiki_score where event='wiki_attach_file';
# Remove unnecessary fields
alter table tiki_score drop column score;
alter table tiki_score drop column expiration;
alter table tiki_score drop column validObjectIds;
#remove old scores from the tiki_score table
delete from tiki_score where data is null;

create table tiki_object_scores (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
triggerObjectType VARCHAR(255) NOT NULL,
triggerObjectId VARCHAR(255) NOT NULL,
triggerUser VARCHAR(255) NOT NULL,
triggerEvent VARCHAR(255) NOT NULL,
ruleId VARCHAR(255) NOT NULL,
recipientObjectType VARCHAR(255) NOT NULL,
recipientObjectId VARCHAR(255) NOT NULL,
pointsAssigned INT NOT NULL,
pointsBalance INT NOT NULL,
date INT NOT NULL,
reversalOf INT UNSIGNED
);
# Import legacy score from users table
insert into tiki_object_scores (triggerObjectType, triggerObjectId, triggerUser, triggerEvent, ruleId, recipientObjectType, recipientObjectId, pointsAssigned, pointsBalance, date)
select 'legacy_score', '0', login, 'tiki.legacy.score', 'Legacy Score', 'user', login, score, score, UNIX_TIMESTAMP() from users_users;
# Drop users score column, we now use pointsBalance from tiki_object_scores table
alter table users_users drop column score;
