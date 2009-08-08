{* $Id$ 
 *
 * MOD-SINCE_LAST_VISIT_NEW
 * Template for the module mod-since_last_visit_new. 
 *}
{if $user}
	{capture name=module_title}{tr}{$slvn_info.label}{/tr}{/capture}
	{tikimodule error=$module_params.error title=$smarty.capture.module_title name="since_last_visit_new" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<div style="margin-bottom: 5px; text-align:center;">
		{if $prefs.feature_calendar eq 'y'}
			<a class="linkmodule" href="tiki-calendar.php?todate={$slvn_info.lastLogin}" title="{tr}click to edit{/tr}">
		{/if}
		<b>{$slvn_info.lastLogin|tiki_short_date}</b>
		{if $prefs.feature_calendar eq 'y'}
			</a>
		{/if}
	</div>
	{if $slvn_info.cant == 0}
		<div class="separator">{tr}Nothing has changed{/tr}</div>
	{else}
		{foreach key=pos item=slvn_item from=$slvn_info.items}
			{if $slvn_item.count > 0 }
				{assign var=cname value=$slvn_item.cname}
				<div class="separator"><a class="separator" href="javascript:flip('{$cname}');">{$slvn_item.count}&nbsp;{tr}{$slvn_item.label}{/tr}</a></div>
				{assign var=showcname value=show_$cname}

             	{if $pos eq 'trackers' or $pos eq 'utrackers'}
					<div id="{$cname}" style="display:{if !isset($cookie.$showcname) or $cookie.$showcname eq 'y'}block{else}none{/if};">

                {****** Parse out the trackers *****}
					{foreach key=tp item=tracker from=$slvn_item.tid}
						{assign var=tcname value=$tracker.cname}
						<div class="separator"  style="margin-left: 10px; display:{if !isset($cookie.$showcname) or $cookie.$showcname eq 'y'}block{else}none{/if};">
							{assign var=showtcname value=show_$tcname}
							<a class="separator" href="javascript:flip('{$tcname}');">{$tracker.count}&nbsp;{tr}{$tracker.label}{/tr}</a>
							<div id="{$tcname}" style="display:{if !isset($cookie.$showtcname) or $cookie.$showtcname eq 'y'}block{else}none{/if};"> 
								{if $nonums != 'y'}<ol>{else}<ul>{/if}
								{section name=xx loop=$tracker.list}
									<li><a  class="linkmodule"
												href="{$tracker.list[xx].href|escape}"
												title="{$tracker.list[xx].title|escape}">{if $tracker.list[xx].label == ''}-{else}{tr}{$tracker.list[xx].label|escape}{/tr}{/if}
											</a>
									</li>
								{/section}
								{if $nonums != 'y'}</ol>{else}</ul>{/if}
							</div>
						</div>
					{/foreach}
                {****** End tracker section *****}
					</div>

				{else}
					<div id="{$cname}" style="display:{if !isset($cookie.$showcname) or $cookie.$showcname eq 'y'}block{else}none{/if};">
						{if $nonums != 'y'}<ol>{else}<ul>{/if}
						{section name=ix loop=$slvn_item.list}
							<li>
									<a  class="linkmodule" 
										href="{$slvn_item.list[ix].href|escape}"
										title="{$slvn_item.list[ix].title|escape}">
										{if $slvn_item.list[ix].label == ''}-{else}{tr}{$slvn_item.list[ix].label|escape}{/tr}{/if}
									</a>
								</li>
						{/section}
						{if $nonums != 'y'}</ol>{else}</ul>{/if}
					</div>
				{/if}
			{/if}
		{/foreach}
	{/if}
{*	<div style="color:#aaaaaa; text-align:right; font-size:8px; margin-bottom: 0;">SLV{$slvn_info.version}</div> *}
	{/tikimodule}
{/if}
      
