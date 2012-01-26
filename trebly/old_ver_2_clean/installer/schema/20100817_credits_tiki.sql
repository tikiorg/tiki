CREATE TABLE IF NOT EXISTS `tiki_credits` (
    `creditId` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
    `userId` INT( 8 ) NOT NULL ,
    `credit_type` VARCHAR( 25 ) NOT NULL ,
    `creation_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    `expiration_date` TIMESTAMP NULL ,
    `total_amount` FLOAT NOT NULL DEFAULT 0,
    `used_amount` FLOAT NOT NULL DEFAULT 0,
    `product_id` INT( 8 ) NULL ,
    PRIMARY KEY ( `creditId` ) ,
    INDEX ( `userId` , `credit_type` )
);

CREATE TABLE IF NOT EXISTS `tiki_credits_usage` (
    `usageId` INT NOT NULL AUTO_INCREMENT,
    `userId` INT NOT NULL,
    `usage_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `credit_type` VARCHAR( 25 ) NOT NULL,
    `used_amount` FLOAT NOT NULL DEFAULT 0,
    `product_id` INT( 8 ) NULL ,
    PRIMARY KEY ( `usageId` )
);

CREATE TABLE IF NOT EXISTS `tiki_credits_types` (
    `credit_type` VARCHAR( 25 ) NOT NULL,
    `display_text` VARCHAR( 50 ) DEFAULT NULL,
    `unit_text` VARCHAR( 25 ) DEFAULT NULL,
    `is_static_level` CHAR( 1 ) DEFAULT 'n',
    `scaling_divisor` FLOAT NOT NULL DEFAULT 1,
    PRIMARY KEY ( `credit_type` ) 
);
