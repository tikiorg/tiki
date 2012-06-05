	{if $bbb_recordings}
		<p>{tr}Current recordings:{/tr}</p>
		<ol>
			{foreach from=$bbb_recordings item=recording}
				<li>
					{tr _0=$recording.startTime|tiki_long_date _1=$recording.startTime|tiki_short_time _2=$recording.endTime|tiki_short_time}On %0 from %1 to %2{/tr}
					<ul>
						{foreach from=$recording.playback key=type item=url}
							<a href="{$url|escape}">{$type|escape}</a>
						{/foreach}
					</ul>
				</li>
			{/foreach}
		</ol>
	{/if}