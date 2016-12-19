
DELETE FROM tiki_preferences WHERE name='feature_crypt_passwords';

ALTER TABLE `users_users` CHANGE `hash` `hash` VARCHAR(255);
