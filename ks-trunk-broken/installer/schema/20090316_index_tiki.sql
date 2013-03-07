#sylvieg
alter table `tiki_actionlog` add key `lastModif`(`lastModif`);
alter table `tiki_actionlog` add key `object`(`object`(100), `objectType`, `action`(100));
alter table `tiki_wiki_attachments` add key  `page` (`page`);