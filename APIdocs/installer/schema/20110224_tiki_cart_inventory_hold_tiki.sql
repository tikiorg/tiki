CREATE TABLE IF NOT EXISTS `tiki_cart_inventory_hold` (
    `productId` INT( 14 ) NOT NULL,
    `quantity` INT( 14 ) NOT NULL,
    `timeHeld` INT( 14 ) NOT NULL,
    `hash` CHAR( 32 ) NOT NULL 
);

