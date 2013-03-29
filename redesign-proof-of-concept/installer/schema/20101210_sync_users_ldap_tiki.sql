REPLACE INTO `tiki_preferences` (`name`, `value`)
SELECT 'syncUsersWithDirectory', p.`value`
FROM `tiki_preferences` p
WHERE p.`name` = 'cas_create_user_tiki_ldap';