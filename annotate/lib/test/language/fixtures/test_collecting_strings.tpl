{* $Id$ *}

		{tab name="{tr}Bytecode Cache{/tr}"}
			{if $opcode_cache}
				<p>{tr 0=$opcode_cache}Using <strong>%0</strong>.These stats affect all PHP applications running on the server.{/tr}</p>

				{if $opcode_stats.warning_xcache_blocked}
					<p>{tr}Configuration setting <em>xcache.admin.enable_auth</em> prevents from accessing statistics. This will also prevent the cache from being cleared when clearing template cache.{/tr}</p>
				{/if}

				<p>
					<img src="http://chart.apis.google.com/chart?cht=p3&amp;chs=250x100&amp;chd=t:{$opcode_stats.memory_used},{$opcode_stats.memory_avail}&amp;chl={tr}Used{/tr}|{tr}Available{/tr}&amp;chtt={tr}Memory{/tr}" width="250" height="100"/>
					<img src="http://chart.apis.google.com/chart?cht=p3&amp;chs=250x100&amp;chd=t:{$opcode_stats.hit_hit},{$opcode_stats.hit_miss}&amp;chl={tr}Hit{/tr}|{tr}Miss{/tr}&amp;chtt={tr}Cache Hits{/tr}" width="250" height="100"/>
				</p>

				{if $opcode_stats.warning_fresh}
					<p>{tr}Few hits recorded. Statistics may not be representative.{/tr}</p>
				{/if}

				{if $opcode_stats.warning_ratio}
					<p>{tr 0=$opcode_cache}Low hit ratio. %0 may be misconfigured and not used.{/tr}</p>
				{/if}

			{else}
				{tr}Bytecode cache is not used. Using a bytecode cache (APC, XCache) is highly recommended for production environments.{/tr}
			{/if}
		{/tab}

		{foreach from=$ins_fields key=ix item=cur_field}
			{if ($cur_field.isHidden ne 'y' or $tiki_p_admin_trackers eq 'y') and !($tracker_info.doNotShowEmptyField eq 'y' and empty($cur_field.value) and empty($cur_field.cat) and empty($cur_field.links) and $cur_field.type ne 's' and $cur_field.type ne 'h') and ($cur_field.options_array[0] ne 'password') and (empty($cur_field.visibleBy) or in_array($default_group, $cur_field.visibleBy) or $tiki_p_admin_trackers eq 'y')}
				<tr class="field{$cur_field.fieldId}">
					<td class="formlabel" >
						{$cur_field.name|escape}
					</td>
					<td class="formcontent">
						{trackeroutput field=$cur_field item=$item_info showlinks=n list_mode=n inTable=y}
					</td>
				</tr>
			{/if}
		{/foreach}
		{if $tracker_info.showCreatedView eq 'y'}
			<tr>
				<td class="formlabel">{tr}Created{/tr}</td>
				<td colspan="3" class="formcontent">{$info.created|tiki_long_datetime}{if $tracker_info.showCreatedBy eq 'y'}<br />by {if $prefs.user_show_realnames eq 'y'}{if empty($info.createdBy)}Unknown{else}{$info.createdBy|username}{/if}{else}{if empty($info.createdBy)}Unknown{else}{$info.createdBy}{/if}{/if}{/if}</td>
			</tr>
		{/if}