{tabset name="tracker_section_select"}
	{foreach $sections as $sect}
		{tab name=$sect.heading}
			<dl class="dl-horizontal">
				{foreach from=$sect.fields item=field}
					<dt>{$field.name|escape}</dt>
					<dd>{trackeroutput field=$field item=$item_info showlinks=n list_mode=n}</dd>
				{/foreach}
			</dl>
		{/tab}
	{/foreach}
{/tabset}
