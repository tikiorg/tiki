<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
if ($prefs['feature_quizzes'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled") . ": feature_quizzes");
	$smarty->display("error.tpl");
	die;
}
require_once ('lib/quizzes/quizlib.php');
if (isset($_REQUEST['answerUploadId'])) {
	$quizlib->download_answer($_REQUEST['answerUploadId']);
}
