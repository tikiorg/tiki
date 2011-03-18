{strip}
{* param: field_value(id, ins_id, type, value,options_array, http_request,flags,defaultvalue, isMandatory, itemChoice, list, isHidden), tiki_p_.... item(creator, my_rate), input_err, ling, groups, item(creator,rating,trackerId)*}

{if $field_value.isMandatory eq 'y' and $showmandatory eq 'y'}
	<div class="mandatory_field">
{/if}

{* ---- visible admin only ---- *}
{if $field_value.isHidden eq 'y' and $tiki_p_admin_trackers ne 'y'}

{* ---- visible by admin and creator --- *}
{elseif $field_value.isHidden eq 'c' and $tiki_p_admin_trackers ne 'y' and isset($item) and $user ne $item.creator}
	
{* ---- editable admin only ---- *}
{elseif $field_value.isHidden eq 'p' and $tiki_p_admin_trackers ne 'y'}
	{if $field_value.value}{$field_value.value|escape}{/if}

{* -- visible for some groups -- *}
{elseif !empty($field_value.visibleBy) and !in_array($default_group, $field_value.visibleBy) and !($user eq '' and in_array('Anonymous', $field_value.visibleBy)) and $tiki_p_admin_trackers ne 'y'}

{* -- editable for some groups -- *}
{elseif !empty($field_value.editableBy) and !in_array($default_group, $field_value.editableBy) and !($user eq '' and in_array('Anonymous', $field_value.editableBy)) and $tiki_p_admin_trackers ne 'y'}
	{include file='tracker_item_field_value.tpl'}

{* -------------------- system -------------------- *}
{elseif $field_value.type eq 's' and ($field_value.name eq "Rating" or $field_value.name eq tra("Rating")) and $tiki_p_tracker_vote_ratings eq 'y'}
	{section name=i loop=$field_value.options_array}
		<input name="{$field_value.ins_id}"{if $field_value.options_array[i] eq $item.my_rate} checked="checked"{/if} type="radio" value="{$field_value.options_array[i]|escape}" id="{$field_value.ins_id}{$smarty.section.i.index}" /><label for="{$field_value.ins_id}{$smarty.section.i.index}">{$field_value.options_array[i]}</label>
	{/section}

{* -------------------- group selector -------------------- *}
{elseif $field_value.type eq 'g'}
	{if $field_value.options_array[0] eq 0 or $tiki_p_admin_trackers eq 'y'}
		<select name="{$field_value.ins_id}" {if $field_value.http_request}onchange="selectValues('trackerIdList={$field_value.http_request[0]}&amp;fieldlist={$field_value.http_request[3]}&amp;filterfield={$field_value.http_request[1]}&amp;status={$field_value.http_request[4]}&amp;mandatory={$field_value.http_request[6]}&amp;filtervalue='+escape(this.value),'{$listfields.$fid.http_request[5]}')"{/if}>
			<option value="">{tr}None{/tr}</option>
				{foreach from=$field_value.list item=group}
				{if ( ! isset($field_value.itemChoices) || $field_value.itemChoices|@count eq 0 || in_array($group, $field_value.itemChoices) )}
					<option value="{$group|escape}" {if $input_err and $field_value.value eq $group} selected="selected"{/if}>{$group|escape}</option>
				{/if}
			{/foreach}
		</select>
	{elseif $field_value.options_array[0] eq 1}
		{if empty($field_value.value)}
			{$group|escape}
		{else}
			{$field_value.value|escape}
		{/if}
	{else}
		{$group|escape}
	{/if}

{* -------------------- user groups -------------------- *}
{elseif $field_value.type eq 'usergroups'}

{* -------------------- radio buttons -------------------- *}
{elseif $field_value.type eq 'R'}
	{section name=jx loop=$field_value.options_array}
		{if $smarty.section.jx.first}
			{if $field_value.options_array[jx] eq '<br />' or $field_value.options_array[jx] eq '<br />'}
				{assign var=sepR value='<br />'}
			{else}
				{assign var=sepR value=' '}
			{/if}
		{/if}
		{if !$smarty.section.jx.first or $sepR ne '<br />'}
			<input type="radio" name="{$field_value.ins_id}" value="{$field_value.options_array[jx]|escape}" {if $field_value.value eq $field_value.options_array[jx] or $field_value.defaultvalue eq $field_value.options_array[jx]}checked="checked"{/if} id="{$field_value.ins_id[jx]}" />
			<label {*for="{$field_value.ins_id[jx]}"*}>{$field_value.options_array[jx]|tr_if}</label>
			{if !$smarty.section.jx.last}{$sepR}{/if}
		{/if}
	{/section}

{* -------------------- jscalendar ------------------- *}
{elseif $field_value.type eq 'j'}
	{if $field_value.options_array[0] eq 'd'}
		{if empty($field_value.value) and empty($inForm)}
			{jscalendar id=$field_value.ins_id fieldname=$field_value.ins_id showtime="n"}
		{elseif !empty($inForm)}
			{* inside form set by tiki-export_tracker.tpl - so use a clear date so we can export all by default *}
			{jscalendar date="" id=$field_value.ins_id fieldname=$field_value.ins_id showtime="n"}
		{else}
			{jscalendar date=$field_value.value id=$field_value.ins_id fieldname=$field_value.ins_id showtime="n"}
		{/if}
	{else}
		{if empty($field_value.value)}
			{jscalendar id=$field_value.ins_id fieldname=$field_value.ins_id showtime="y"}
		{else}
			{jscalendar date=$field_value.value id=$field_value.ins_id fieldname=$field_value.ins_id showtime="y"}
		{/if}
	{/if}

{* -------------------- dynamic list -------------------- *}
{elseif $field_value.type eq 'w'}
	<script type="text/javascript" src="lib/trackers/dynamic_list.js"></script>
<select name="{$field_value.ins_id}" {if $field_value.http_request}onchange="selectValues('trackerIdList={$field_value.http_request[0]}&amp;fieldlist={$field_value.http_request[3]}&amp;filterfield={$field_value.http_request[1]}&amp;status={$field_value.http_request[4]}&amp;mandatory={$field_value.http_request[6]}&amp;filtervalue='+escape(this.value),'{$field_value.http_request[5]}')"{/if}>
	</select>


{* -------------------- User subscription -------------------- *}
{elseif $field_value.type eq 'U'}
	<input type="text" name="{$field_value.ins_id}" value="{$field_value.value}" />


{* -------------------- freetags -------------------- *}

{elseif $field_value.type eq 'F'}
	{if $field_value.options_array[1] neq 'y'}{tr}Put tags separated by spaces. For tags with more than one word, use no spaces and put words together or enclose them with double quotes.{/tr}{/if}
	<br />
	<input type="text" id="{$field_value.ins_id|replace:'[':'_'|replace:']':''}" name="{$field_value.ins_id}" {if $field_value.options_array[0]}size="{$field_value.options_array[0]}"{/if} value="{$field_value.value|escape}" />
	{if $field_value.options_array[2] neq 'y'}
	<br />
	{foreach from=$field_value.tag_suggestion item=t}
		{jq notonready=true}
		function addTag{{$field_value.ins_id|replace:"[":"_"|replace:"]":""}}(tag) {
			document.getElementById('{{$field_value.ins_id|replace:"[":"_"|replace:"]":""}').value = document.getElementById('{$field_value.ins_id|replace:"[":"_"|replace:"]":""}}').value + ' ' + tag;
		}
		{/jq}
		{capture name=tagurl}{if (strstr($t, ' '))}"{$t}"{else}{$t}{/if}{/capture}
		<a href="javascript:addTag{$field_value.ins_id|replace:"[":"_"|replace:"]":""}('{$smarty.capture.tagurl|escape:'javascript'|escape}');" onclick="javascript:needToConfirm=false">{$t|escape}</a>&nbsp; &nbsp; 
	{/foreach}
	{/if}


{* -------------------- Webservice -------------------- *}
{elseif $field_value.type eq 'W'}
	{if $field_value.value ne ''}
                {$field_value.value}
        {/if}

{* -------------------- in group -------------------- *}
{elseif $field_value.type eq 'N'}
	{include file='tracker_item_field_value.tpl'}

{* -------------------- header ------------------------- *}
{elseif $field_value.type eq 'h'}
	{include file='tracker_item_field_value.tpl'}

{/if}

{if $field_value.isMandatory eq 'y' and $showmandatory eq 'y'}
	<strong class='mandatory_star'>*</strong></div>
{/if}

{/strip}
