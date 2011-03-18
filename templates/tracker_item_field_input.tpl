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

{* -------------------- dynamic list -------------------- *}
{elseif $field_value.type eq 'w'}
	<script type="text/javascript" src="lib/trackers/dynamic_list.js"></script>
<select name="{$field_value.ins_id}" {if $field_value.http_request}onchange="selectValues('trackerIdList={$field_value.http_request[0]}&amp;fieldlist={$field_value.http_request[3]}&amp;filterfield={$field_value.http_request[1]}&amp;status={$field_value.http_request[4]}&amp;mandatory={$field_value.http_request[6]}&amp;filtervalue='+escape(this.value),'{$field_value.http_request[5]}')"{/if}>
	</select>


{* -------------------- User subscription -------------------- *}
{elseif $field_value.type eq 'U'}
	<input type="text" name="{$field_value.ins_id}" value="{$field_value.value}" />
{* -------------------- Webservice -------------------- *}
{elseif $field_value.type eq 'W'}
	{if $field_value.value ne ''}
                {$field_value.value}
        {/if}

{* -------------------- header ------------------------- *}
{elseif $field_value.type eq 'h'}
	{include file='tracker_item_field_value.tpl'}

{/if}

{if $field_value.isMandatory eq 'y' and $showmandatory eq 'y'}
	<strong class='mandatory_star'>*</strong></div>
{/if}

{/strip}
