<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-hw_quiz_import.php,v 1.1 2004-04-28 00:47:10 ggeller Exp $

// Copyright (c) 2004 George G. Geller
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Put put a text box where the user can enter a question for importing.
// A question ending with a qestion mark?
// *the correct answer
// an incorrect answer
// another insorrect answer

// Correct answers must start with an "*"
// White space before and after text is ignored.

// In this version the output isn't stored.
// The output will go to the tiki database, maybe the hw_quiz table.

error_reporting (E_ALL);

// Initialization
require_once('tiki-setup.php');
require_once('lib/homework/homeworklib.php');

require_once('doc/devtools/ggg-trace.php');
$ggg_tracer->outln(__FILE__." line: ".__LINE__);

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
  $smarty->assign('page', "Sort Troop Roster");    // Display the assignment title in the browser titlebar
  $smarty->display("tiki.tpl");
  die;
}

$smarty->assign('mid', 'tiki-hw_quiz_import.tpl');
$smarty->assign('show_page_bar', 'n'); // Do not show the wiki-specific tiki-page_bar.tpl
$smarty->assign('page', "Import quiz question");    // Display the assignment title in the browser titlebar
$smarty->display("tiki.tpl");
?>
