<?php


require_once('tiki-setup.php');
require_once('lib/quizzes/quizlib.php');

if (isset($_REQUEST['answerUploadId'])) {
	$quizlib->download_answer($_REQUEST['answerUploadId']);	
}
?>
