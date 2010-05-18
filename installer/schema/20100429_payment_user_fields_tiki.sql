ALTER TABLE `tiki_payment_requests` ADD `user` VARCHAR( 200 ) NOT NULL DEFAULT '',
ADD INDEX ( `user` );
ALTER TABLE `tiki_payment_received` ADD `user` VARCHAR( 200 ) NOT NULL DEFAULT '',
ADD INDEX ( `user` ) ;
