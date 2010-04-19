<form method="post" action="">
	<div style="overflow: hidden; width: 90px; float: left;">
		<input type="hidden" name="bbb" value="{$bbb_name|escape}"/>
		<input type="image" name="join" src="{$bbb_image|escape}" title="{tr}Join{/tr}"/>
	</div>

	<h2>{$bbb_name|escape}</h2>
	
	{permission key=bigbluebutton object=$bbb_name name=bigbluebutton_moderate}
		{button href="tiki-objectpermissions.php?objectId=$bbb_name&amp;objectName=$bbb_name&amp;objectType=bigbluebutton&amp;permType=bigbluebutton"	_text="{tr}Permissions{/tr}"}
	{/permission}

	{if $bbb_attendees}
		<div style="padding-left: 100px;">
			<p>{tr}Current attendees:{/tr}</p>
			<ol>
				{foreach from=$bbb_attendees item=att}
					<li>{$att.fullName|escape} ({$att.role|escape})</li>
				{/foreach}
			</ol>
		</div>
	{else}
		<p>{tr}No attendees at this time.{/tr}</p>
	{/if}

	{if ! $user }
		<div>
			{tr}Name{/tr}: 
			<input type="text" name="bbb_name"/>
			<input type="submit" value="{tr}Join{/tr}"/>
		</div>
	{/if}

	<div class="clear"></div>
</form>
