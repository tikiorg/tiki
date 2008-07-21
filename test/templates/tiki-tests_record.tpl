<h1 class='pagetitle'><a href='#' class='pagetitle'>TikiTests Record Configuration</a></h1>

{include file='tiki-tests_menubar.tpl'}

<form action="tiki_tests/tiki-tests_record.php" method="post">
	{tr}File Name:{/tr}<input type="text" name="filename" /><br/>
	{tr}Use Current Session/Logout{/tr}<input type="checkbox" name="current_session" value="y" /><br/>
	<input type="submit" name="action" value="{tr}Start Recording Test{/tr}" />
</form>
