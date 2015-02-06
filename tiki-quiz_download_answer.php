<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');

$access->check_feature('feature_quizzes');

$quizlib = TikiLib::lib('quiz');
if (isset($_REQUEST['answerUploadId'])) {
	$quizlib->download_answer($_REQUEST['answerUploadId']);
}
