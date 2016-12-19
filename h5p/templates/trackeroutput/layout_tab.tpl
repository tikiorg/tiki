{tabset name="tracker_section_select"}
	{foreach $sections as $pos => $sect}
		{tab name=$sect.heading}
			<dl class="dl-horizontal">
				{if ! $pos}
					{if $tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $tiki_p_admin_trackers eq 'y')}
						{assign var=ustatus value=$info.status|default:"p"}
						<dt>{tr}Status{/tr}</dt>
						<dd>
							{icon name=$status_types.$ustatus.iconname}
							{$status_types.$ustatus.label}
						</dd>
					{/if}
				{/if}
				{foreach from=$sect.fields item=field}
					<dt>{$field.name|tra|escape}</dt>
					<dd>{trackeroutput field=$field item=$item_info showlinks=n list_mode=n}</dd>
				{/foreach}
			</dl>
		{/tab}
	{/foreach}
{/tabset}
