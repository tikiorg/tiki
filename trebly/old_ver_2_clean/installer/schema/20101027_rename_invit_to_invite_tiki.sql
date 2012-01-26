ALTER TABLE `tiki_invit` RENAME TO `tiki_invite`;
ALTER TABLE `tiki_invited` DROP KEY `id_invit`;
ALTER TABLE `tiki_invited` CHANGE `id_invit` `id_invite` int(11) NOT NULL;
ALTER TABLE `tiki_invited` ADD KEY `id_invite` (`id_invite`);
UPDATE users_permissions SET feature_check="feature_invite", permName='tiki_p_invite_to_my_groups' where permName="tiki_p_invit";
