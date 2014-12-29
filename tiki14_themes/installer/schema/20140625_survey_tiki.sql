#2014-06-25 survey
ALTER TABLE `tiki_survey_questions` CHANGE `votes` `votes` INT(10) NULL DEFAULT '0';
ALTER TABLE `tiki_survey_questions` CHANGE `value` `value` INT(10) NULL DEFAULT '0';