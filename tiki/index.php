<?php

// $Header: /cvsroot/tikiwiki/tiki/index.php,v 1.5 2004-09-08 19:51:49 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');
if(!(headers_sent())){
header ("location: $tikiIndex");
}
die("header already sent");

?>
