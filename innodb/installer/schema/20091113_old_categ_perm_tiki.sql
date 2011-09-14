update `users_permissions` set `permDesc`='Obsolete tw>=4 (Can edit items in categories)' where `permName`='tiki_p_edit_categorized';
update `users_permissions` set `permDesc`='Obsolete tw>=4 (Can view categorized items)' where `permName`='tiki_p_view_categorized';
update `users_permissions` set `permDesc`='Obsolete tw>=4 (Can view categories)' where `permName`='tiki_p_view_categories';