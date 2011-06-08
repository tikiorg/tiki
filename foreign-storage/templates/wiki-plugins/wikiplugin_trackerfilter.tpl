{*param :  $msgTrackerFilter, $line, $open, $iTrackerFilter, $trackerId, $filters(array(name, format, fieldId, selected, opts)), $showFieldId *}
{strip}
{if $msgTrackerFilter}
<div class="simplebox highlight">{$msgTrackerFilter|escape}</div>
{/if}
{if $line ne 'y' and $prefs.javascript_enabled eq 'y' and $noflipflop ne 'y'}
{button _text="{tr}Filters{/tr}" _flip_id="trackerFilter$iTrackerFilter"}
{/if}
<div id="trackerFilter{$iTrackerFilter}" class="trackerfilter" style="display:{if $open eq 'y'}block{else}none{/if}">
{if empty($inForm)}
	{if empty($export_action)}
		<form action="{$smarty.server.PHP_SELF}?{query}" method="post">
	{else}
		{jq notonready=true}
function tf_export_submit(fm) {
	$("input[name=export_filter]").attr("disabled", "disabled").css("opacity", 0.5);
	return true;
}
		{/jq}
		<form action="tiki-export_tracker.php" method="post" onsubmit="tf_export_submit(this);">
			{query _type='form_input' listfields=$export_fields showItemId=$export_itemid showStatus=$export_status showCreated=$export_created showLastModif=$export_modif encoding=$export_charset}
			{foreach from=$f_fields item=f_v key=f_k}
				<input type="hidden" name="{$f_k}" value="{$f_v}" />
			{/foreach}
	{/if}
{/if}
{if $mapview}
<input type="hidden" name="mapview" value="y" />
{else}
<input type="hidden" name="mapview" value="n" />
{/if}
<input type="hidden" name="trackerId" value="{$trackerId}" />
<input type="hidden" name="iTrackerFilter" value="{$iTrackerFilter}" />
{if !empty($count_item)}<input type="hidden" name="count_item" value="{$count_item}" />{/if}
<table class="normal">
{if $line eq 'y'}<tr>{/if}
{cycle values="even,odd" print=false}
{foreach from=$filters item=filter}
	{if $line ne 'y'}<tr class="{cycle}">{/if}
		<td>
		<label for="f_{$filter.fieldId}">{$filter.name|tr_if}</label>
		{if $showFieldId eq 'y'} -- {$filter.fieldId}{/if}
		{if $line ne 'y'}</td><td>{else}:{/if}
{*------drop-down, multiple *}
		{if $filter.format eq 'd' or  $filter.format eq 'm'}
			<select id="f_{$filter.fieldId}" name="f_{$filter.fieldId}{if $filter.format eq "m"}[]{/if}" {if $filter.format eq "m"} size="5" multiple="multiple"{/if}> 
			<option value=""{if !$filter.selected} selected="selected"{/if}>{tr}Any{/tr}</option>
			{section name=io loop=$filter.opts}
				<option value="{$filter.opts[io].id}"{if $filter.opts[io].selected eq "y"} selected="selected"{/if}>
					{$filter.opts[io].name|tr_if|escape}
				</option>
			{/section}
			</select>
			{if $filter.format eq "m"}{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use Ctrl+Click to select multiple options{/tr}{/remarksbox}{/if}
{*------<,> operator *}
		{elseif $filter.format eq '<' or $filter.format eq '>' or $filter.format eq '<=' or $filter.format eq '>='or $filter.format eq 'f' or $filter.format eq 'j'}
			{if $filter.field.type eq 'f' or $filter.field.type eq 'j'}
				{if $filter.format eq '<' or $filter.format eq '<='}
					{tr}Before:{/tr}&nbsp;
				{elseif $filter.format eq '>' or $filter.format eq '>='}
					{tr}After:{/tr}&nbsp;
				{/if}
			{/if}
			{trackerinput field=$filter.field}
{*------text *} 
		{elseif $filter.format eq 't' or $filter.format eq 'T' or $filter.format eq 'i'}
			{if $filter.format eq 'i'}
				{capture name=i_f}f_{$filter.fieldId}{/capture}
				{initials_filter_links _initial=$smarty.capture.i_f}
			{/if}
			<input id="f_{$filter.fieldId}" type="text" name="f_{$filter.fieldId}" value="{$filter.selected}"/>
{*------sqlsearch *}
		{elseif $filter.format eq 'sqlsearch'}
			{capture name=tpl_advanced_search_help}
				{include file='advanced_search_help.tpl'}
			{/capture}
			<input id="f_{$filter.fieldId}" type="text" name="f_{$filter.fieldId}" value="{$filter.selected}"/>
			{add_help show='y' title="{tr}Help{/tr}" id="advanced_search_help_filter"}
				{$smarty.capture.tpl_advanced_search_help}
			{/add_help}
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
			<input {if $filter.format eq "c"}type="checkbox"{else}type="radio"{/if}
					name="f_{$filter.fieldId}{if $filter.format eq "c"}[]{/if}"
					value=""{if !$filter.selected} checked="checked"{/if} />
			{tr}Any{/tr}{if $line ne 'y'}<br />{/if}
			{section name=io loop=$filter.opts}
				<input {if $filter.format eq "c"}type="checkbox"{else}type="radio"{/if}
						name="f_{$filter.fieldId}{if $filter.format eq "c"}[]{/if}"
						value="{$filter.opts[io].id|escape:url}"
						{if $filter.opts[io].selected eq "y"} checked="checked"{/if} />
				{$filter.opts[io].name|tr_if}
				{if $line ne 'y'}<br />{/if}
			{/section}
		{/if}
		</td>
		{if $line ne 'y'}</tr>{else} {/if}
{/foreach}
{if $line ne 'y' and $action and $action neq " "}<tr>{/if}
{if ($action and $action neq " ") or !empty($export_action)}
<td>&nbsp;</td>
<td>
	{if !empty($export_action)}
		<input class="button submit" type="submit" name="export_filter" value="{tr}{$export_action}{/tr}" />
	{elseif $action and $action neq " "}
		<input class="button submit" type="submit" name="filter" value="{if empty($action)}{tr}Filter{/tr}{else}{tr}{$action}{/tr}{/if}" />
		<input class="button submit" type="submit" name="reset_filter" value="{tr}Reset{/tr}" />
	{else}
		&nbsp;
	{/if}
	{if $googlemapButtons && $googlemapButtons eq 'y'}
        {if $mapview}
        <br /><input class="button submit" type="submit" name="searchlist" value="{tr}List View{/tr}" />
        {else}
        <br /><input class="button submit" type="submit" name="searchmap" value="{tr}Map View{/tr}" />
        {/if}
	{/if}
</td>
{/if}
{if !empty($sortchoice)}
	{if $line ne 'y'}<tr>{/if}
	<td>{tr}Sort{/tr}</td>
	<td>{include file='tracker_sort_input.tpl' iTRACKERLIST=$iTrackerFilter}
	{if $line ne 'y'}</tr>{/if}
{/if}
{if $line ne 'y' and $action}</tr>{/if}
</table>
{if empty($inForm)}</form>{/if}
</div>
{if !empty($dataRes)}<div class="trackerfilter-result">{$dataRes}</div>{/if}
{/strip}
