# jonnyb
# rename labels to they list more sensibly
UPDATE  `tiki_quicktags` SET  `taglabel` =  '  text, bold' WHERE  `tiki_quicktags`.`taglabel` = 'text, bold';
UPDATE  `tiki_quicktags` SET  `taglabel` =  '  text, bold' WHERE  `tiki_quicktags`.`taglabel` = 'bold';
UPDATE  `tiki_quicktags` SET  `taglabel` =  '  text, italic' WHERE  `tiki_quicktags`.`taglabel` = 'text, italic';
UPDATE  `tiki_quicktags` SET  `taglabel` =  '  text, italic' WHERE  `tiki_quicktags`.`taglabel` = 'italic';
UPDATE  `tiki_quicktags` SET  `taglabel` =  '  text, underline' WHERE  `tiki_quicktags`.`taglabel` = 'text, underline';
UPDATE  `tiki_quicktags` SET  `taglabel` =  '  text, underline' WHERE  `tiki_quicktags`.`taglabel` = 'underline';
UPDATE  `tiki_quicktags` SET  `taglabel` =  ' heading1' WHERE  `tiki_quicktags`.`taglabel` =  'heading1';
UPDATE  `tiki_quicktags` SET  `taglabel` =  ' heading2' WHERE  `tiki_quicktags`.`taglabel` =  'heading2';
UPDATE  `tiki_quicktags` SET  `taglabel` =  ' heading3' WHERE  `tiki_quicktags`.`taglabel` =  'heading3';
