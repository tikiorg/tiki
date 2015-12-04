ALTER TABLE tiki_auth_tokens ADD createUser char(1) default 'n';
ALTER TABLE tiki_auth_tokens ADD userPrefix varchar(200) default '_token';
