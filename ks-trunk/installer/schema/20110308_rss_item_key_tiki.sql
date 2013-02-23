ALTER TABLE `tiki_rss_items` DROP KEY `tiki_rss_items_item`;
ALTER TABLE `tiki_rss_items` ADD KEY `tiki_rss_items_item` (`rssId`, `guid`(200));