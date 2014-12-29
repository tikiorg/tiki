{title help="tests"}{tr}TikiTests Record Configuration{/tr}{/title}

{include file='tiki-tests_menubar.tpl'}

<form action="tiki_tests/tiki-tests_record.php" method="post">
	{tr}File Name:{/tr}<input type="text" name="filename"><br>
	{tr}Use Current Session/Log out{/tr}<input type="checkbox" name="current_session" value="y"><br>
	<input type="submit" class="btn btn-default btn-sm" name="action" value="{tr}Start Recording Test{/tr}">
</form>
