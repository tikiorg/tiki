ALTER TABLE `tiki_language` MODIFY `source` text NOT NULL;
ALTER TABLE `tiki_language` MODIFY `changed` tinyint(1) DEFAULT NULL;
ALTER TABLE `tiki_user_votings` MODIFY `user` varchar(200) NOT NULL DEFAULT '';
ALTER TABLE `tiki_pages_translation_bits` MODIFY `flags` set('critical') DEFAULT '';
ALTER TABLE `tiki_user_mail_accounts` MODIFY `imap` varchar(255) DEFAULT NULL;
ALTER TABLE `tiki_user_mail_accounts` MODIFY `mbox` varchar(255) DEFAULT NULL;
ALTER TABLE `tiki_user_mail_accounts` MODIFY `maildir` varchar(255) DEFAULT NULL;
