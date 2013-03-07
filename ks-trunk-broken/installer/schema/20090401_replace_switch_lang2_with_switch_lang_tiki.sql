# jonnyb
# switch_lang (mode=words) replacing switch_lang2 module
UPDATE `tiki_modules` SET `name` = 'switch_lang', `params` = 'mode=words' WHERE `tiki_modules`.`name` = 'switch_lang2'
