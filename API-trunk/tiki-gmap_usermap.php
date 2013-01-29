<?php
/**
 *
 *
 * @package   Tiki
 * @copyright (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project. All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * @license   LGPL. See license.txt for more details
 */
// $Id$

include "tiki-setup.php";
$access->check_feature('feature_gmap');
$smarty->assign('mid', 'tiki-gmap_usermap.tpl');
$smarty->display('tiki.tpl');
