<form action="{$smarty.server.PHP_SELF}" method="get">
	<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
	{if $status}<input type="hidden" name="status" value="{$status}" />{/if}
	{if $sort_mode}<input type="hidden" name="sort_mode" value="{$sort_mode}" />{/if}
	<table class="formcolor">
		<tr>
			{if ($tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $tiki_p_admin_trackers eq 'y')) and $showstatus ne 'n'}
				{foreach key=st item=stdata from=$status_types}
					<td>
						<div class="{$stdata.class}">
							<a href="tiki-view_tracker.php?trackerId={$trackerId}{if $filtervalue}&amp;filtervalue={$filtervalue|escape:"url"}{/if}{if $filterfield}&amp;filterfield={$filterfield|escape:"url"}{/if}{if $sort_mode}&amp;sort_mode={$sort_mode}{/if}&amp;status={$stdata.statuslink}" class="statusimg">
								<img src="{$stdata.image}" title="{$stdata.label}" alt="{$stdata.label}" align="top" width="12" height="12" />
							</a>
						</div>
					</td>
				{/foreach}
			{/if}

			<td style="width:100%;text-align:right;">
				{if $show_filters eq 'y'}
					{jq}
						fields = new Array({{$cnt}});
						{{assign var=c value=0}}
						{{foreach key=fid item=field from=$listfields}
							{if $field.isSearchable eq 'y' and $field.type ne 'f' and $field.type ne 'j' and $field.type ne 'i'}
								fields[{$c}] = '{$fid}';
								{assign var=c value=$c+1}
							{/if}
						{/foreach}}
					{/jq}
					{*FIX flip from tikijs.js this only a paleative<select name="filterfield" onchange="multitoggle(fields,this.options[selectedIndex].value);flip('filterbutton');">*}
					<select name="filterfield" onchange="multitoggle(fields,this.options[selectedIndex].value); {literal}showit = 'show_filterbutton'; if(this.selectedIndex == 0){document.getElementById('filterbutton').style.display = 'none';setSessionVar(showit,'n');}else{ document.getElementById('filterbutton').style.display = 'block'; setSessionVar(showit,'y');}{/literal}">
						{*FIX flip from tikijs.js this only a paleative<select name="filterfield" onchange="multitoggle(fields,this.options[selectedIndex].value);flip('filterbutton');">*}
						<option value="">{tr}Choose a filter{/tr}</option>
						{foreach key=fid item=field from=$listfields}
							{if $field.isSearchable eq 'y' and $field.type ne 'f' and $field.type ne 'j' and $field.type ne 'i' and ($field.isHidden ne 'y' or $tiki_p_admin_trackers eq 'y')}
								<option value="{$fid}"{if $fid eq $filterfield} selected="selected"{/if}>{$field.name|truncate:65|escape}</option>
								{assign var=filter_button value='y'}
							{/if}
						{/foreach}
					</select>
				{/if}
			</td>
			<td>
				{assign var=cnt value=0}
				{foreach key=fid item=field from=$listfields}
					{if $field.isSearchable eq 'y' and $field.type ne 'f' and $field.type ne 'j' and $field.type ne 'i'}
						{if $field.type eq 'c'}
							<div style="display:{if $filterfield eq $fid}block{else}none{/if};" id="fid{$fid}">
								<select name="filtervalue[{$fid}]">
									<option value="y"{if $filtervalue eq 'y'} selected="selected"{/if}>{tr}Yes{/tr}</option>
									<option value="n"{if $filtervalue eq 'n'} selected="selected"{/if}>{tr}No{/tr}</option>
								</select>
							</div>
						{elseif $field.type eq 'd' or $field.type eq 'D'}
							<div style="display:{if $filterfield eq $fid}block{else}none{/if};" id="fid{$fid}">
								<select name="filtervalue[{$fid}]">
									{if $field.type eq 'D'}<option value="" />{/if}
									{section name=jx loop=$field.options_array}
										<option value="{$field.options_array[jx]|escape}" {if $fid == $filterfield}{if $filtervalue eq $field.options_array[jx]}{assign var=gotit value=y}selected="selected"{/if}{/if}>{$field.options_array[jx]|tr_if}</option>
									{/section}
								</select>
								{if $field.type eq 'D'}
									<input type="text" name="filtervalue_other"{if $gotit ne 'y'} value="{if $fid == $filterfield}{$filtervalue}{/if}"{/if} />
								{/if}
							</div>

						{elseif $field.type eq 'R'}
							<div style="display:{if $filterfield eq $fid}block{else}none{/if};" id="fid{$fid}">
								{section name=jx loop=$field.options_array}
									<input type="radio" name="filtervalue[{$fid}]" value="{$field.options_array[jx]|escape}" {if $fid == $filterfield}{if $filtervalue eq $field.options_array[jx]}checked="checked"{/if}{/if} />{$field.options_array[jx]|escape}
								{/section}
							</div>

						{elseif $field.type eq 'e'}{* category *}
							<div style="display:{if $filterfield eq $fid}block{else}none{/if};" id="fid{$fid}">
								<table>
									<tr>
										{cycle name=rows values=",</tr><tr>" advance=false print=false}
										{foreach key=ku item=iu from=$field.categories name=eforeach}
											<td width="50%" nowrap="nowrap">
												<input type="checkbox" name="filtervalue[{$fid}][]" value="{$iu.categId}" id="cat{$iu.categId}" {if $fid == $filterfield && is_array($filtervalue) && in_array($iu.categId,$filtervalue)} checked="checked"{/if} />
												<label for="cat{$i.categId}">{$iu.name|escape}</label>
											</td>
											{if !$smarty.foreach.eforeach.last}
												{cycle name=rows}
											{else}
												{if $fields[ix].categories|@count%2}
													<td>
													</td>
												{/if}
											{/if}
										{/foreach}
									</tr>
								</table>
							</div>
						{elseif $field.type eq 'u'}{* user with autocomplete *}
							<div style="display:{if $filterfield eq $fid}block{else}none{/if};" id="fid{$fid}">
								<input type="text" name="filtervalue[{$fid}]" value="{if $fid == $filterfield}{$filtervalue}{/if}" id="filter-username" />
							</div>
							{autocomplete element='#filter-username' type='username'}
						{else}
							<div style="display:{if $filterfield eq $fid}block{else}none{/if};" id="fid{$fid}">
								<input type="text" name="filtervalue[{$fid}]" value="{if $fid == $filterfield}{$filtervalue}{/if}" />
							</div>
						{/if}
						{assign var=cnt value=$cnt+1}
					{/if}
				{/foreach}
			</td>
			{if isset($filter_button) && $filter_button eq 'y'}
				<td>
					<input id="filterbutton" type="submit" name="filter" value="{tr}Filter{/tr}" style="display:{if $filterfield}inline{else}none{/if}" />
				</td>
			{/if}
		</tr>
	</table>
</form>
