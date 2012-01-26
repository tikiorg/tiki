<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-quiz_download_answer.php 33195 2011-03-02 17:43:40Z changi67 $

require_once ('tiki-setup.php');

$access->check_feature('feature_quizzes');

require_once ('lib/quizzes/quizlib.php');
if (isset($_REQUEST['answerUploadId'])) {
	$quizlib->download_answer($_REQUEST['answerUploadId']);
}
