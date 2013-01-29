<?php
/**
 *
 *
 * @package   Tiki
 * @copyright (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project. All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * @license   LGPL. See license.txt for more details
 */
// $Id$

include_once ('tiki-setup.php');

$access->check_feature('wiki_feature_3d');

$smarty->assign('page', $_REQUEST['page']);
$smarty->display('tiki-wiki3d.tpl');
