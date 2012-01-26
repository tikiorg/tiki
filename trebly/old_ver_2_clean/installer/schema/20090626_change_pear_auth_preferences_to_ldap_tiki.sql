update tiki_preferences set name='ldap_create_user_tiki' where name='auth_create_user_tiki';
update tiki_preferences set name='ldap_create_ldap_tiki' where name='auth_create_auth_tiki';
update tiki_preferences set name='ldap_skip_admin' where name='auth_skip_admin';
update tiki_preferences set name='auth_ldap_host' where name='auth_pear_host';
update tiki_preferences set name='auth_ldap_port' where name='auth_pear_port';
update tiki_preferences set value='ldap' where name='auth_method' and value='auth';
alter table users_groups add column(`isExternal` char default 'n');
