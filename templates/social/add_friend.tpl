<p>{tr}Know the username?{/tr}</p>
<form method="post" action="{service controller=social action=add_friend}">
	<p>
		<input type="text" name="username" value="{$username|escape}"/>
		<input type="submit" value="{tr}Add{/tr}"/>
	</p>
</form>
