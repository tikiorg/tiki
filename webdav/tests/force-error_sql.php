<?php
// $Id: $

/* This file just causes an SQL error by trying to access a table that doesn't exist */


require_once('../tiki-setup.php');


echo $tikilib->getOne("select count(*) from `tiki_table_does_not_exist`");
