update tiki_modules set name='who_is_there', params='content=count' where name='logged_users';
update tiki_modules set name='who_is_there', params='content=list' where name='online_users';
