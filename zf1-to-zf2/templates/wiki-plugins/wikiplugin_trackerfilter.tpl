{*param : $msgTrackerFilter, $line, $open, $iTrackerFilter, $trackerId, $filters(array(name, format, fieldId, selected, opts)), $showFieldId *}
{strip}
	{if isset($msgTrackerFilter) && $msgTrackerFilter}
		<div class="alert alert-danger">{$msgTrackerFilter|escape}</div>
	{/if}
	{if (!isset($line) || $line ne 'y') and $prefs.javascript_enabled eq 'y' and $noflipflop ne 'y'}
		{button _text="{tr}Filters{/tr}" _flip_id="trackerFilter$iTrackerFilter"}
	{/if}
	<div id="trackerFilter{$iTrackerFilter}" class="trackerfilter" style="display:{if isset($open) && $open eq 'y'}block{else}none{/if}">
		{if empty($inForm)}
			{if empty($export_action)}
				<form action="{$smarty.server.PHP_SELF}?{query}#trackerFilter{$iTrackerFilter}-result" method="post">
			{else}
				{jq notonready=true}
					function tf_export_submit(fm) {
						$("input[name=export_filter]").attr("disabled", "disabled").css("opacity", 0.5);
						return true;
					}
				{/jq}
				<form action="tiki-export_tracker.php" method="post" onsubmit="tf_export_submit(this);">
					{query _type='form_input' listfields=$export_fields showItemId=$export_itemid showStatus=$export_status showCreated=$export_created showLastModif=$export_modif encoding=$export_charset}
					{if not empty($export_itemId)}<input type="hidden" name="itemId" value="{$export_itemId}">{/if}
					{foreach from=$f_fields item=f_v key=f_k}
						<input type="hidden" name="{$f_k}" value="{$f_v}">
					{/foreach}
			{/if}
		{/if}
		{if isset($mapview) && $mapview}
			<input type="hidden" name="mapview" value="y">
		{else}
			<input type="hidden" name="mapview" value="n">
		{/if}
		<input type="hidden" name="trackerId" value="{$trackerId}">
		<input type="hidden" name="iTrackerFilter" value="{$iTrackerFilter}">
		{if !empty($count_item)}<input type="hidden" name="count_item" value="{$count_item}">{/if}
		<div class="table-responsive">
			<table class="table">
				{if isset($line) && $line eq 'y'}<tr>{/if}

				{foreach from=$filters item=filter}
					{if !isset($line) || $line ne 'y'}<tr>{/if}
					<td class="tracker_filter_label">
						{if $indrop ne 'y' or ($filter.format ne 'd' and $filter.format ne 'm')}<label for="f_{$filter.fieldId}">{$filter.name|tr_if}</label>{/if}
						{if $showFieldId eq 'y'} -- {$filter.fieldId}{/if}
						{if !isset($line) || $line ne 'y'}</td><td class="tracker_filter_input tracker_field{$filter.fieldId}">{elseif $indrop ne 'y' or ($filter.format ne 'd' and $filter.format ne 'm')}:{/if}
	{*------drop-down, multiple *}
						{if $filter.format eq 'd' or $filter.format eq 'm'}
							<select id="f_{$filter.fieldId}" name="f_{$filter.fieldId}{if $filter.format eq "m"}[]{/if}" {if $filter.format eq "m"} size="5" multiple="multiple"{/if}>
								{if $indrop eq 'y'}<option value="">--{$filter.name|tr_if}--</option>{/if}
								<option value="">{tr}Any{/tr}</option>
								{$last = ''}
								{section name=io loop=$filter.opts}
									{if $last neq $filter.opts[io].name or $filter.field.type neq 'd'}{* hide repeated entries, used for defaults in other cases *}
										<option value="{$filter.opts[io].id}"{if $filter.opts[io].selected eq "y"} selected="selected"{/if}>
											{$filter.opts[io].name|tr_if|escape}
										</option>
									{/if}
									{$last = $filter.opts[io].name}
								{/section}
							</select>
							{if $filter.format eq 'm' and $prefs.jquery_ui_chosen neq 'y'}{remarksbox type='tip' title="{tr}Tip{/tr}"}{tr}Use Ctrl+Click to select multiple options{/tr}{/remarksbox}{/if}
	{*------<,> operator *}
						{elseif $filter.format eq '<' or $filter.format eq '>' or $filter.format eq '<=' or $filter.format eq '>='or $filter.format eq 'f' or $filter.format eq 'j'}
							{if $filter.field.type eq 'f' or $filter.field.type eq 'j'}
								{if $filter.format eq '<' or $filter.format eq '<='}
									{tr}Before:{/tr}&nbsp;
								{elseif $filter.format eq '>' or $filter.format eq '>='}
									{tr}After:{/tr}&nbsp;
								{/if}
							{/if}
							{trackerinput field=$filter.field inForm="y"}
	{*------text *}
						{elseif $filter.format eq 't' or $filter.format eq 'T' or $filter.format eq 'i'}
							{if $filter.format eq 'i'}
								{capture name=i_f}f_{$filter.fieldId}{/capture}
								{initials_filter_links _initial=$smarty.capture.i_f}
							{/if}
							<input id="f_{$filter.fieldId}" type="text" name="f_{$filter.fieldId}" value="{$filter.selected}">
	{*------sqlsearch *}
						{elseif $filter.format eq 'sqlsearch'}
							<a href="{bootstrap_modal controller=tracker action=search_help}">{icon name='help'}</a>
	{*------rating *}
						{elseif $filter.format eq '*'}
							<select id="f_{$filter.fieldId}" name="f_{$filter.fieldId}">
								<option value="">{tr}Any{/tr}</option>
								{foreach from=$filter.opts item=option}
									<option value="{$option.id|escape}"{if $option.selected eq 'y'} selected="selected"{/if}>{$option.name|escape}</option>
								{/foreach}
							</select>
	{*------checkbox, radio *}
						{else}
							<label>
								<input {if $filter.format eq "c"}type="checkbox"{else}type="radio"{/if}
									name="f_{$filter.fieldId}{if $filter.format eq "c"}[]{/if}"
									value=""{if !$filter.selected} checked="checked"{/if}
								>
								{tr}Any{/tr}
							</label>
							{if !isset($line) || $line ne 'y'}<br>{/if}
							{section name=io loop=$filter.opts}
								<label>
									<input {if $filter.format eq "c"}type="checkbox"{else}type="radio"{/if}
										name="f_{$filter.fieldId}{if $filter.format eq "c"}[]{/if}"
										value="{$filter.opts[io].id|escape:url}"
										{if $filter.opts[io].selected eq "y"} checked="checked"{/if}
									>
								</label>
								{$filter.opts[io].name|tr_if}
								{if !isset($line) || $line ne 'y'}<br>{/if}
							{/section}
						{/if}
					</td>
					{if !isset($line) || $line ne 'y'}</tr>{else} {/if}
				{/foreach}
				{if (!isset($line) || $line ne 'y') and (!isset($action) || $action neq " ")}<tr>{/if}
				{if (!isset($action) || $action neq " ") or !empty($export_action)}
					<td>&nbsp;</td>
					<td>
						<div id="trackerFilter{$iTrackerFilter}-result"></div>
						{if !empty($export_action)}
							<input class="button submit btn btn-default" type="submit" name="export_filter" value="{tr}{$export_action}{/tr}">
						{elseif $action and $action neq " "}
							<input class="button submit btn btn-default" type="submit" name="filter" value="{if empty($action)}{tr}Filter{/tr}{else}{tr}{$action}{/tr}{/if}">
							<input class="button submit btn btn-default" type="submit" name="reset_filter" value="{tr}Reset{/tr}">
						{else}
							&nbsp;
						{/if}
						{if $mapButtons && $mapButtons eq 'y'}
							{if isset($mapview) && $mapview}
							<br><input class="button submit btn btn-default" type="submit" name="searchlist" value="{tr}List View{/tr}">
							{else}
							<br><input class="button submit btn btn-default" type="submit" name="searchmap" value="{tr}Map View{/tr}">
							{/if}
						{/if}
					</td>
				{/if}
				{if !empty($sortchoice)}
					{if $line ne 'y'}<tr>{/if}
					<td>{tr}Sort{/tr}</td>
					<td>{include file='tracker_sort_input.tpl' iTRACKERLIST=$iTrackerFilter}
					{if !isset($line) || $line ne 'y'}</tr>{/if}
				{/if}
				{if (!isset($line) || $line ne 'y' ) and $action}</tr>{/if}
			</table>
		</div>
		{if empty($inForm)}</form>{/if}
	</div>
	{if !empty($dataRes)}<div class="trackerfilter-result">{$dataRes}</div>{/if}
{/strip}
