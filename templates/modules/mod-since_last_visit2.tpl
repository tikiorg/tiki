{* 
 * MOD-SINCE_LAST_VISIT2
 * Template for the module based off of since_last_visit_new. 
 * More or less the same as SLVN, except separates new entries from updates
 * in some system areas, and breaks trackers into individual trackers.  Use 
 * whichever meets your needs.
 *}
{if $user}
	{assign var=module_title value=$slv_info.label}
	{tikimodule title="$module_title" name="since_last_visit2" flip=$module_params.flip decorations=$module_params.decorations}
	<div style="margin-bottom: 5px; text-align:center;">
		{if $prefs.feature_calendar eq 'y'}
			<a class="linkmodule" href="tiki-calendar.php?todate={$slv_info.lastLogin}" title="{tr}click to edit{/tr}">
		{/if}
		<b>{$slv_info.lastLogin|tiki_short_date}</b>
		{if $prefs.feature_calendar eq 'y'}
			</a>
		{/if}
	</div>
	{if $slv_info.cant == 0}
		<div class="separator">{tr}Nothing has changed{/tr}</div>
	{else}
		{foreach key=pos item=slv_item from=$slv_info.items}
			{if $slv_item.count > 0 }
				{assign var=cname value=$slv_item.cname}
				<div class="separator"><a class="separator" href="javascript:flip('{$cname}');">{$slv_item.count}&nbsp;{$slv_item.label}</a></div>
				{assign var=showcname value=show_$cname}

             	{if $pos eq 'trackers' or $pos eq 'utrackers'}
					<div id="{$cname}" style="display:{if !isset($cookie.$showcname) or $cookie.$showcname eq 'y'}block{else}none{/if};">

                {****** Parse out the trackers *****}
					{foreach key=tp item=tracker from=$slv_item.tid}
						{assign var=tcname value=$tracker.cname}
						<div class="separator"  style="margin-left: 10px; display:{if !isset($cookie.$showcname) or $cookie.$showcname eq 'y'}block{else}none{/if};">
							{assign var=showtcname value=show_$tcname}
							<a class="separator" href="javascript:flip('{$tcname}');">{$tracker.count}&nbsp;{$tracker.label}</a>
							<div id="{$tcname}" style="display:{if !isset($cookie.$showtcname) or $cookie.$showtcname eq 'y'}block{else}none{/if};"> 
								<table cellpadding="0" cellspacing="0">
								{section name=xx loop=$tracker.list}
									<tr class="module">
										<td width="10" />
										<td width="20" align="right" class="module">&nbsp;{$smarty.section.xx.index_next})</td> 
										<td>
											<a  class="linkmodule"
												href="{$tracker.list[xx].href|escape}"
												title="{$tracker.list[xx].title|escape}">{if $tracker.list[xx].label == ''}-{else}{$tracker.list[xx].label|escape}{/if}
											</a>
										</td>
									</tr>
								{/section}
								</table>
							</div>
						</div>
					{/foreach}
                {****** End tracker section *****}
					</div>

				{else}
					<div id="{$cname}" style="display:{if !isset($cookie.$showcname) or $cookie.$showcname eq 'y'}block{else}none{/if};">
						<table cellpadding="0" cellspacing="0">
						{section name=ix loop=$slv_item.list}
							<tr class="module">
								<td width="10" />
								<td width="20" align="right" class="module">{$smarty.section.ix.index_next})</td>
								<td>
									<a  class="linkmodule" 
										href="{$slv_item.list[ix].href|escape}"
										title="{$slv_item.list[ix].title|escape}">
										{if $slv_item.list[ix].label == ''}-{else}{$slv_item.list[ix].label|escape}{/if}
									</a>
								</td>
							</tr>
						{/section}
						</table>
					</div>
				{/if}
			{/if}
		{/foreach}
	{/if}
	{* <div style="color:#aaaaaa; text-align:right; font-size:8px; margin-bottom: 0;">SLV{$slv_info.version}</div> *}
	{/tikimodule}
{/if}
      
