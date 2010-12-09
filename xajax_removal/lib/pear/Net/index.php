<?php

// $Id: /cvsroot/tikiwiki/tiki/lib/pear/Net/index.php,v 1.6 2007-03-06 19:30:19 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// This redirects to the sites root to prevent directory browsing

header ("location: ../index.php");
die;
