ALTER TABLE `tiki_survey_questions` ADD COLUMN `mandatory` char(1) NOT NULL default 'n';
ALTER TABLE `tiki_survey_questions` ADD COLUMN `max_answers` int(5) NOT NULL default 0;
ALTER TABLE `tiki_survey_questions` ADD COLUMN `min_answers` int(5) NOT NULL default 0;
