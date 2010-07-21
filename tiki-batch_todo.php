<?php
  /*
if (empty($argv)) { // can only be used in a cron or line command
	return;
}
  */
include('tiki-setup.php');
include_once('lib/todolib.php');

$todos = $todolib->listTodoObject();
foreach ($todos as $todo) {
	// echo '<pre>';print_r($todo); echo '</pre>';
	$todolib->applyTodo($todo);
}

