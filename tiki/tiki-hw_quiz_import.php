<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-hw_quiz_import.php,v 1.4 2004-04-30 06:05:30 ggeller Exp $

// Copyright (c) 2004 George G. Geller
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Put up a text box where the user can enter a question for importing.
// The format is:
// A question on the first line.
// *the correct answer
// an incorrect answer
// another incorrect answer

// Correct answers start with an "*"
// White space before and after text is ignored.

// In this version the output isn't stored.
// The output will go to the tiki database, maybe the hw_quiz table.

// Todo:
//  test with very long question and answers.  Does tiki/mozilla insert \n when the text is autowrapped?

// Notes:
//  All the questions are multiple choice.
//  There are a bunch of tables used for quizes:
//   tiki_quiz_question_options stores the possible answers for question, like 'Huck\'s dad.', etc. 
//   tiki_quiz_questions store the actual question, like 'Who took care of Tom?'
//     has a field for type, so maybe we can make other kinds of questions.  At present this field is always set
//       to 'o' on tiki-edit_quiz_questions.php, line 97.

error_reporting (E_ALL);

// Initialization
require_once('tiki-setup.php');
require_once('lib/homework/homeworklib.php');

$homeworklib = new HomeworkLib($dbTiki);

if (1) {
  $smarty->assign('msg', tra("This function is deprecated in favor of the improved quizzes outside of the Homework feature."));
  $smarty->display("error.tpl");
  die;
}

require_once('doc/devtools/ggg-trace.php');
$ggg_tracer->outln(__FILE__." line: ".__LINE__);

// data
$preview = false; // Show the preview or not

if (isset($_REQUEST["preview"])) {
  $preview = true;
}

if (isset($_REQUEST["import"])) {
  $input = trim($_REQUEST["input_data"]);
  // Split into an array of strings on the line boundarys, also cutting out
  //   leading and trailing white space
  $input_array = preg_split("/\s*[\r\n]+\s*/", $input);

  $question = new HW_QuizQuestionMultipleChoice($input_array);
  $lines = $question->to_text(True);
  $ggg_tracer->out(__FILE__." line ".__LINE__.': $lines = ');
  $ggg_tracer->outvar($lines);

  $OKOK = "";
  foreach ($lines as $line)
    $OKOK .= $line."\n";
  $smarty->assign("OKOK",$OKOK);
  $smarty->assign('mid', 'tiki-hw_quiz_import_done.tpl');
  $smarty->assign('show_page_bar', 'n'); // Do not show the wiki-specific tiki-page_bar.tpl
  $smarty->assign('page', "Quiz Questions");    // Display the assignment title in the browser titlebar
  $smarty->display("tiki.tpl");
  die;
}

$smarty->assign('preview', $preview);
$smarty->assign('mid', 'tiki-hw_quiz_import.tpl');
$smarty->assign('show_page_bar', 'n'); // Do not show the wiki-specific tiki-page_bar.tpl
$smarty->assign('page', "Import quiz question");    // Display the assignment title in the browser titlebar
$smarty->display("tiki.tpl");
?>
