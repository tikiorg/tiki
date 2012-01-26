alter table `tiki_object_attributes` modify `itemId` varchar(160) NOT NULL;
alter table `tiki_object_attributes` modify `attribute` varchar(70) NOT NULL;
alter table `tiki_object_attributes` modify `value` varchar(255);
alter table `tiki_object_relations` modify `relation` varchar(70) NOT NULL;
alter table `tiki_object_relations` modify `target_itemId` varchar(160) NOT NULL;
alter table `tiki_object_relations` modify `source_itemId` varchar(160) NOT NULL;