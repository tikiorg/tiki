<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// All Rights Reserved.
// See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE.
// See license.txt for details.
// $Id$

$bypass_siteclose_check = 'y';
require_once ('tiki-setup.php');

$userlib->user_logout($user);
