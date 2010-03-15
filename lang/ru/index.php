<?php
// Copyright (c) 2002-2010, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This redirects to the sites root to prevent directory browsing

header ('location: ../../tiki-index.php');
die;
