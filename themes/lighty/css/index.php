<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: index.php 57959 2016-03-17 20:00:24Z jonnybradley $

// This redirects to the sites root to prevent directory browsing
header("location: ../../../tiki-index.php");
die;
