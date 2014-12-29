# xavidp. Option ' Control Panels' is set with a space in purpose, in order to have it shown in the first position under "Settings"
update tiki_menu_options set name=' Control Panels' where name=' Panels';
update tiki_menu_options set name='Settings' where name='Configuration';