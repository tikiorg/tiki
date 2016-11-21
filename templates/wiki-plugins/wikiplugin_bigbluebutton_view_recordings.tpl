{if $bbb_recordings}
	<p>{tr}Current recordings:{/tr}</p>
	<ol class="bbb-recordings">
		{foreach from=$bbb_recordings item=recording}
			{if $recording.published}
			<li>
				{tr _0=$recording.startTime|tiki_long_date _1=$recording.startTime|tiki_short_time _2=$recording.endTime|tiki_short_time}On %0 from %1 to %2{/tr}
				{permission name=admin}
					<a data-confirm="{tr}This will permanently remove the recording{/tr}" class="bbb-remove-link" href="{service controller=bigbluebutton action=delete_recording recording_id=$recording.recordID}">{icon name='delete'}</a>
				{/permission}
				<ul>
					{foreach from=$recording.playback key=type item=url}
						<a href="{$url|escape}">{$type|escape}</a> {tr}(including audio recording & chat log){/tr}
					{/foreach}
				</ul>
			</li>
			{/if}
		{/foreach}
	</ol>
{/if}

{jq}
$('.bbb-remove-link').requireConfirm({
	success: function () {
		$(this).closest('li').remove();
	}
});
{/jq}
