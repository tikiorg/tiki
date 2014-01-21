CREATE TABLE `tiki_file_backlinks` (
	   `fileId` int(14) NOT NULL,
	   `objectId` int(12) NOT NULL,
	   KEY `objectId` (`objectId`),
	   KEY `fileId` (`fileId`)
);