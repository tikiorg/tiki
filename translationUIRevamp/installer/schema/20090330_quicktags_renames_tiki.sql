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
UPDATE  `tiki_quicktags` SET  `taglabel` =  ' deleted' WHERE  `tiki_quicktags`.`taglabel` =  'Deleted';
UPDATE  `tiki_quicktags` SET  `taglabel` =  ' colored text' WHERE  `tiki_quicktags`.`taglabel` =  'colored text';
UPDATE  `tiki_quicktags` SET  `taglabel` =  ' horizontal rule' WHERE  `tiki_quicktags`.`taglabel` =  'hr';

UPDATE  `tiki_quicktags` SET  `tagicon` =  'pics/icons/database_gear.png' WHERE  `tiki_quicktags`.`taglabel` =  'dynamic variable';
UPDATE  `tiki_quicktags` SET  `taginsert` =  '||r1c1|r1c2|r1c3\nr2c1|r2c2|r2c3\nr3c1|r3c2|r3c3||' WHERE  `tiki_quicktags`.`taglabel` =  'table new';

INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('quote','{QUOTE(replyto= )}\ntext\n{QUOTE}\n','pics/icons/quotes.png','wiki');
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('code','{CODE( caption= wrap= colors= ln= wiki= rtl= ishtml=)}\ntext\n{CODE}\n','pics/icons/page_white_code.png','wiki');
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('flash','{flash movie= width= height= quality= }\n','pics/icons/page_white_actionscript.png','wiki');

INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('quote','{QUOTE(replyto= )}\ntext\n{QUOTE}\n','pics/icons/quotes.png','blogs');
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('code','{CODE( caption= wrap= colors= ln= wiki= rtl= ishtml=)}\ntext\n{CODE}\n','pics/icons/page_white_code.png','blogs');
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('flash','{flash movie= width= height= quality= }\n','pics/icons/page_white_actionscript.png','blogs');

INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('quote','{QUOTE(replyto= )}\ntext\n{QUOTE}\n','pics/icons/quotes.png','articles');
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('code','{CODE( caption= wrap= colors= ln= wiki= rtl= ishtml=)}\ntext\n{CODE}\n','pics/icons/page_white_code.png','articles');
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('flash','{flash movie= width= height= quality= }\n','pics/icons/page_white_actionscript.png','articles');

INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('quote','{QUOTE(replyto= )}\ntext\n{QUOTE}\n','pics/icons/quotes.png','forums');
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('code','{CODE( caption= wrap= colors= ln= wiki= rtl= ishtml=)}\ntext\n{CODE}\n','pics/icons/page_white_code.png','forums');
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('flash','{flash movie= width= height= quality= }\n','pics/icons/page_white_actionscript.png','forums');

INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('quote','{QUOTE(replyto= )}\ntext\n{QUOTE}\n','pics/icons/quotes.png','trackers');
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('code','{CODE( caption= wrap= colors= ln= wiki= rtl= ishtml=)}\ntext\n{CODE}\n','pics/icons/page_white_code.png','trackers');
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('flash','{flash movie= width= height= quality= }\n','pics/icons/page_white_actionscript.png','trackers');

