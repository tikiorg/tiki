{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}&quot;Modules&quot; are the items of content at the top &amp; bottom and in the right &amp; left columns of the site.{/tr} {tr}Select{/tr}
	<a class="rbox-link" href="tiki-admin_modules.php">{tr}Admin &gt; Modules{/tr}</a> {tr}from the menu to create and edit modules{/tr}.
{/remarksbox}

<form action="tiki-admin.php?page=module" method="post">
	<input type="hidden" name="modulesetup" />
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
	<fieldset>
		<legend>{tr}{$crumbs[$crumb]->description}{/tr}{help crumb=$crumbs[$crumb]}</legend>

		{preference name=feature_modulecontrols}
		{preference name=user_assigned_modules}
		{preference name=user_flip_modules}
		{preference name=modallgroups}
		{remarksbox type="tip" title="{tr}Hint{/tr}"}
			{tr}If you lose your login module, use tiki-login_scr.php to be able to login!{/tr}
		{/remarksbox}
		{preference name=modseparateanon}
		{preference name=modhideanonadmin}
		{preference name=feature_left_column}
		{preference name=feature_right_column}

		</fieldset>
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
</form>
