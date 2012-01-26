alter table `tiki_faq_questions` add column `created` int(14) default NULL;
alter table `tiki_faq_questions` add key `created` (`created`);