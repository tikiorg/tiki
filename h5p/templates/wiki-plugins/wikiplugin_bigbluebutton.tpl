<form target="_blank" method="post" action="{service controller=bigbluebutton action=join}">
	<input type="hidden" name="params" value="{$bbb_params|escape}">

	{tr}Meeting ID:{/tr} {$bbb_meeting|escape}
	{permission type=bigbluebutton object=$bbb_meeting name=tiki_p_assign_perm_bigbluebutton}
		{permission_link mode=button type=bigbluebutton id=$bbb_meeting title=$bbb_meeting}
	{/permission}
	<div class="clearfix">
		{if ! $user}
			{tr}Name:{/tr}
			<input type="text" name="bbb_name">
			<input type="submit" class="btn btn-default btn-sm" value="{tr}Join{/tr}">
		{else}
			<input type="submit" class="button btn btn-default" value="{tr}Join{/tr}">
		{/if}
	</div>
	{if $bbb_show_attendees}
		{if $bbb_attendees}
			<div>
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
	{/if}
	{include file="wiki-plugins/wikiplugin_bigbluebutton_view_recordings.tpl"}
</form>
