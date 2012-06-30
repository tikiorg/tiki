#petjal 20120630: to allow hash field to handle CRYPT_BLOWFISH hashes
! ALTER TABLE users_users MODIFY COLUMN `hash` varchar(60) default NULL;

