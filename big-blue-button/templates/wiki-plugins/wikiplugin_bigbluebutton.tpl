<form method="post" action="">
	<div style="overflow: hidden; width: 90px; float: left;">
		<input type="hidden" name="bbb" value="{$bbb_name|escape}"/>
		<input type="image" name="enter" src="{$bbb_image|escape}"/>
	</div>

	{if ! $user }
		<div>
			{tr}Name{/tr}: 
			<input type="text" name="bbb_name" value="{$bbb_username|escape}"/>
			<input type="submit" value="{tr}Join{/tr}"/>
		</div>
	{/if}
	

	{permission key=bigbluebutton object=$bbb_name name=bigbluebutton_moderate}
		{button href="tiki-objectpermissions.php?objectId=$bbb_name&amp;objectName=$bbb_name&amp;objectType=bigbluebutton&amp;permType=bigbluebutton"	_text="{tr}Permissions{/tr}"}
	{/permission}

	<div class="clear"></div>
</form>
