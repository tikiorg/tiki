UPDATE `tiki_modules`
SET `params` = CONCAT(`params`,'&compact=y')
WHERE `name` = 'search' AND `position` LIKE 'o' AND `params` NOT LIKE '%compact=%';
