
CREATE TABLE `tiki_payment_requests` (
	`paymentRequestId` INT NOT NULL AUTO_INCREMENT,
	`amount` DECIMAL(7,2) NOT NULL,
	`amount_paid` DECIMAL(7,2) NOT NULL DEFAULT 0.0,
	`currency` CHAR(3) NOT NULL,
	`request_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`due_date` TIMESTAMP NULL,
	`cancel_date` TIMESTAMP NULL,
	`description` VARCHAR(100) NOT NULL,
	`actions` TEXT,
	PRIMARY KEY( `paymentRequestId` )
);

CREATE TABLE `tiki_payment_received` (
	`paymentReceivedId` INT NOT NULL AUTO_INCREMENT,
	`paymentRequestId` INT NOT NULL,
	`payment_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`amount` DECIMAL(7,2),
	`type` VARCHAR(15),
	`details` TEXT,
	PRIMARY KEY(`paymentReceivedId`),
	KEY `payment_request_ix` (`paymentRequestId`)
);

INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_payment_admin', 'Can administer payments', 'admin', 'payment', 'y', 'payment_feature');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_payment_view', 'Can view payment requests and details', 'admin', 'payment', NULL, 'payment_feature');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_payment_manual', 'Can enter manual payments', 'admin', 'payment', NULL, 'payment_feature');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_payment_request', 'Can request a payment', 'admin', 'payment', NULL, 'payment_feature');

