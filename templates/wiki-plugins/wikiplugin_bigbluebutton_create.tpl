{tr}Meeting ID:{/tr} {$bbb_meeting|escape} 
<p>{tr}Last time we checked, the room you requested did not exist.{/tr}</p>
{permission name=bigbluebutton_create type=bigbluebutton object=$bbb_meeting}
	<form target="_blank" method="post" action="">
		<input type="hidden" name="bbb" value="{$bbb_meeting|escape}"/>
		<input type="submit" class="button" value="{tr}Create{/tr}"/>
	</form>
	{if $bbb_recordings}
		<p>{tr}Current recordings:{/tr}</p>
		<ol>
			{foreach from=$bbb_recordings item=recording}
				{if $recording.published}
				<li>
					{tr _0=$recording.startTime|tiki_long_date _1=$recording.startTime|tiki_short_time _2=$recording.endTime|tiki_short_time}On %0 from %1 to %2{/tr}
					<ul>
						{foreach from=$recording.playback key=type item=url}
							<a href="{$url|escape}">{$type|escape}</a>
						{/foreach}
					</ul>
				</li>
				{/if}
			{/foreach}
		</ol>
	{/if}
{/permission}
