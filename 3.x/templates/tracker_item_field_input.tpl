{strip}
{* param: field_value(id, ins_id, type, value,options_array, http_request,flags,defaultvalue, isMandatory, itemChoice, list, isHidden), tiki_p_.... item(creator, my_rate), input_err, ling, groups, item(creator,rating,trackerId)*}

{if $field_value.isMandatory eq 'y'}
	<span class="mandatory_field">
{/if}

{* ---- visible admin only ---- *}
{if $field_value.isHidden eq 'y' and $tiki_p_admin_trackers ne 'y'}

{* ---- visible by admin and creator --- *}
{elseif $field_value.isHidden eq 'c' and $tiki_p_admin_trackers ne 'y' and isset($item) and $user ne $item.creator}
	
{* ---- editable admin only ---- *}
{elseif $field_value.isHidden eq 'p' and $tiki_p_admin_trackers ne 'y'}
	{if $field_value.value}{$field_value.value|escape}{/if}

{* -- visible for some groups -- *}
{elseif !empty($field_value.visibleBy) and !in_array($default_group, $field_value.visibleBy) and $tiki_p_admin_trackers ne 'y'}

{* -- editable for some groups -- *}
{elseif !empty($field_value.editableBy) and !in_array($default_group, $field_value.editableBy) and $tiki_p_admin_trackers ne 'y'}
	{include file="tracker_item_field_value.tpl"}

{* -------------------- system -------------------- *}
{elseif $field_value.type eq 's' and ($field_value.name eq "Rating" or $field_value.name eq tra("Rating")) and $tiki_p_tracker_vote_ratings eq 'y'}
	{section name=i loop=$field_value.options_array}
		<input name="{$field_value.ins_id}"{if $field_value.options_array[i] eq $item.my_rate} checked="checked"{/if} type="radio" value="{$field_value.options_array[i]|escape}" id="{$field_value.ins_id}{$smarty.section.i.index}" /><label for="{$field_value.ins_id}{$smarty.section.i.index}">{$field_value.options_array[i]}</label>
	{/section}

{* -------------------- user selector -------------------- *}
{elseif $field_value.type eq 'u'}
	{if empty($field_value.options_array) or ($field_value.options_array[0] !=1 and $field_value.options_array[0] !=2) or $tiki_p_admin_trackers eq 'y'}
		<select name="{$field_value.ins_id}" {if $field_value.http_request}onchange="selectValues('trackerIdList={$field_value.http_request[0]}&amp;fieldlist={$field_value.http_request[3]}&amp;filterfield={$field_value.http_request[1]}&amp;status={$field_value.http_request[4]}&amp;mandatory={$field_value.http_request[6]}&amp;filtervalue='+escape(this.value),'{$listfields.$fid.http_request[5]}')"{/if}>
		<option value="">{tr}None{/tr}</option>
		{foreach key=id item=one from=$field_value.list}
			{if ( ! isset($field_value.itemChoices) || $field_value.itemChoices|@count eq 0 || in_array($one, $field_value.itemChoices) )}
				{if $field_value.options_array[0] ne '2'}
					<option value="{$one|escape}"{if $one eq $field_value.value} selected="selected"{/if}>{$one|username|escape}</option>
				{else}
					<option value="{$one|escape}"{if $one eq $user} selected="selected"{/if}>{$one|username|escape}</option>
				{/if}
			{/if}
		{/foreach}
		</select>
	{else}
		{$user|username|escape}
	{/if}

{* -------------------- IP selector -------------------- *}
{elseif $field_value.type eq 'I'}
	{if $field_value.options_array[0] eq 0 or $tiki_p_admin_trackers eq 'y'}
		<input type="text" name="{$field_value.ins_id}" value="{if $field_value.value}{$field_value.value|escape}{elseif $field_value.defaultvalue}{$field_value.defaultvalue|escape}{else}{$IP|escape}{/if}" />
	{else}
		{if $field_value.options_array[0] eq 1 && empty($field_value.value)}<input type="hidden" name="authoripid" value="{$field_value.fieldId}" />{/if}
		{$IP|escape}
	{/if}

{* -------------------- group selector -------------------- *}
{elseif $field_value.type eq 'g'}
	{if $field_value.options_array[0] eq 0 or $tiki_p_admin_trackers eq 'y'}
		<select name="{$field_value.ins_id}" {if $field_value.http_request}onchange="selectValues('trackerIdList={$field_value.http_request[0]}&amp;fieldlist={$field_value.http_request[3]}&amp;filterfield={$field_value.http_request[1]}&amp;status={$field_value.http_request[4]}&amp;mandatory={$field_value.http_request[6]}&amp;filtervalue='+escape(this.value),'{$listfields.$fid.http_request[5]}')"{/if}>
			<option value="">{tr}None{/tr}</option>
				{section name=ux loop=$field_value.list}
				{if ( ! isset($field_value.itemChoices) || $field_value.itemChoices|@count eq 0 || in_array($groups[ux], $field_value.itemChoices) )}
					<option value="{$groups[ux]|escape}" {if $input_err and $field_value.value eq $groups[ux]} selected="selected"{/if}>{$groups[ux]}</option>
				{/if}
			{/section}
		</select>
	{elseif $field_value.options_array[0] eq 1}
		{if empty($field_value.value)}
			{$group|escape}
			<input type="hidden" name="authorgroupfieldid" value="{$field_value.fieldId}" />
		{else}
			{$field_value.value|escape}
		{/if}
	{else}
		{$group|escape}
	{/if}

{* -------------------- category -------------------- *}
{elseif $field_value.type eq 'e'}
	{if !empty($field_value.options_array[2]) && ($field_value.options_array[2] eq '1' or $field_value.options_array[2] eq 'y')}
		{select_all checkbox_names=`$field_value.ins_id`[] label="{tr}Select All{/tr}"}
	{/if}
	{if $field_value.options_array[1] eq 'd' || $field_value.options_array[1] eq 'm'}
		<select name="{$field_value.ins_id}[]"{if $field_value.options_array[1] eq 'm'} multiple="multiple"{/if}>
		{if $field_value.options_array[1] eq 'd' and $field_value.isMandatory ne 'y'}
	   		<option value=""></option>
		{/if}
		{foreach key=ku item=cat from=$field_value.list}
			<option value="{$cat.categId}"{if (!is_array($field_value.value) and $field_value.value eq $cat.categId) or (is_array($field_value.value) and in_array($cat.categId, $field_value.value))} selected="selected"{/if}>{$cat.name|escape}</option>
		{/foreach}
	{else}
	{assign var=fca value=$field_value.options}
	<table width="100%">
		<tr>{cycle name=2_$fca values=",</tr><tr>" advance=false print=false}
		{foreach key=ku item=iu from=$field_value.list name=eforeach}
		{assign var=fcat value=$iu.categId }
		<td width="50%" nowrap="nowrap"><input type={if $field_value.options_array[1] eq "radio"}"radio"{else}"checkbox"{/if} name="{$field_value.ins_id}[]" value="{$iu.categId}" id="cat{$iu.categId}" {if (!is_array($field_value.value) and $field_value.value eq $fcat) or (is_array($field_value.value) and in_array($fcat, $field_value.value))} checked="checked"{/if}/><label for="cat{$iu.categId}">{$iu.name|escape}</label></td>{if !$smarty.foreach.eforeach.last}{cycle name=2_$fca}{else}{if $field_value.list|@count%2}<td></td>{/if}{/if}
		{/foreach}
		</tr>
	</table>
	{/if}

{* -------------------- image -------------------- *}
{elseif $field_value.type eq 'i'}
	<input type="file" name="{$field_value.ins_id}"{if isset($input_err)} value="{$field_value.value}"{/if} />
	{if $field_value.value ne ''}
		<br />
		<img src="{$field_value.value}" alt="" width="{$field_value.options_array[2]}" height="{$field_value.options_array[3]}" />
		<a href="{$smarty.server.PHP_SELF}?{query removeImage='y' fieldId=`$field_value.fieldId` itemId=`$item.itemId` trackerId=`$item.trackerId` fieldName=`$field_value.name`}">{icon _id='cross' alt='{tr}Remove Image{/tr}'}</a>
   {/if}

{* -------------------- multimedia -------------------- *}
{elseif $field_value.type eq 'M'}
	{if ($field_value.options_array[0] > '2')}
		<input type="file" name="{$field_value.ins_id}"  value="{$field_value.value} />
	{else}
		<input type="text" name="{$field_value.ins_id}" value="{$field_value.value}" />
	{/if}
	{assign var='Height' value=$prefs.MultimediaDefaultHeight}
	{assign var='Length' value=$prefs.MultimediaDefaultLength}

	{if $field_value.value ne ''}	
		{if isset($cur_field.options_array[1]) and $field_value.options_array[1] ne '' } {assign var=$Length value=$field_value.options_array[1] }{/if}
		{if isset($cur_field.options_array[2]) and $field_value.options_array[2] ne '' } {assign var=$Height value=$field_value.options_array[2] }{/if}
		{if $ModeVideo eq 'y' } { assign var="Height" value=$Height+$prefs.VideoHeight}{/if}
		{include file=multiplayer.tpl url=$field_value.value w=$Length h=$Height video=$ModeVideo}
	{/if}

{* -------------------- file -------------------- *}
{elseif $field_value.type eq 'A'}
	<input type="file" name="{$field_value.ins_id}"{if isset($input_err)} value="{$field_value.value}"{/if} />
	{if !isset($input_err) and $field_value.value ne ''}
		<br />
		{$field_value.info.filename}&nbsp;
		<a href="tiki-download_item_attachment.php?attId={$field_value.value}" title="{tr}Download{/tr}">{icon _id='disk' alt="{tr}Download{/tr}"}</a>
		{if $tiki_p_admin_trackers eq 'y' or $field_value.info.user eq $user}
			<a href="{$smarty.server.PHP_SELF}?{query removeattach=$field_value.value}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
		{/if}
	{/if}

{* -------------------- preference --------------------- *}
{elseif $field_value.type eq 'p'}
	{if $field_value.options_array[0] eq 'password'}
		{if ($prefs.auth_method neq 'cas' || ($prefs.cas_skip_admin eq 'y' && $user eq 'admin')) and $prefs.change_password neq 'n'}
			<input type="password" name="{$field_value.ins_id}" />
		{/if}
	{else}
			<input type="text" name="{$field_value.ins_id}" value="{$field_value.value}" />
	{/if}

{* -------------------- text field  -------------------- *}
{elseif $field_value.type eq 't'}
	{if $field_value.isMultilingual ne 'y'}
		{*prepend*}{if $field_value.options_array[2]}<span class="formunit">{$field_value.options_array[2]}&nbsp;</span>{/if}
		<input type="text" name="{$field_value.ins_id}" {if $field_value.options_array[1]}size="{$field_value.options_array[1]}" maxlength="{$field_value.options_array[1]}"{/if} value="{if $field_value.value}{$field_value.value|escape}{else}{$field_value.defaultvalue|escape}{/if}" />
		{*append*}{if $field_value.options_array[3]}<span class="formunit">&nbsp;{$field_value.options_array[3]}</span>{/if}
	{else}
		<table>
    	{foreach from=$field_value.lingualvalue item=ling}
    		<tr><td>{$ling.lang}</td><td>
            {*prepend*}{if $field_value.options_array[2]}<span class="formunit">{$field_value.options_array[2]}&nbsp;</span>{/if}
        	<input type="text" name="{$field_value.ins_id}[{$ling.lang}]" value="{$ling.value|escape}" {if $field_value.options_array[1]}size="{$field_value.options_array[1]}" maxlength="{$field_value.options_array[1]}"{/if} /> {*@@ missing value*}
        	{*append*}{if $field_value.options_array[3]}<span class="formunit">&nbsp;{$field_value.options_array[3]}</span>{/if}
    		</td></tr>
		{/foreach}
		</table>
	{/if}

{* -------------------- page selector  -------------------- *}
{elseif $field_value.type eq 'k'}
	<input type="text" name="{$field_value.ins_id}" {if $field_value.options_array[1] gt 0}size="{$field_value.options_array[1]}"{/if} value="{if $field_value.value}{$field_value.value|escape}{else}{$field_value.defaultvalue|escape}{/if}" />
	

{* -------------------- email  -------------------- *}
{elseif $field_value.type eq 'm'}
	<input type="text" name="{$field_value.ins_id}" value="{$field_value.value|escape}" />

{* -------------------- numeric field -------------------- *}
{elseif $field_value.type eq 'n'}
	{*prepend*}{if $field_value.options_array[2]}<span class="formunit">{$field_value.options_array[2]}&nbsp;</span>{/if}
	<input type="text" name="{$field_value.ins_id}" {if $field_value.options_array[1]}size="{$field_value.options_array[1]}" maxlength="{$field_value.options_array[1]}"{/if} value="{$field_value.value|escape}" />
	{*append*}{if $field_value.options_array[3]}<span class="formunit">&nbsp;{$field_value.options_array[3]}</span>{/if}

{* -------------------- static text -------------------- *}
{elseif $field_value.type eq 'S'}
	{if $field_value.description}
		{if $field_value.options_array[0] eq 1}
			{wiki}{$field_value.description}{/wiki}
		{else}
			{$field_value.description|escape|nl2br}
		{/if}
	{/if}

{* -------------------- textarea -------------------- *}
{elseif $field_value.type eq 'a'}
	{if $field_value.description}
		<em>{$field_value.description|escape|nl2br}</em><br />
	{/if}
	{if $field_value.isMultilingual ne 'y'}
		{if $prefs.quicktags_over_textarea eq 'y' and $field_value.options_array[0] eq 1}
    		{include file=tiki-edit_help_tool.tpl qtnum=$field_value.fieldId area_name="area_"|cat:$field_value.fieldId}
		{/if}
		{if $field_value.options_array[2] == 1}
				<input type="text" name="{$field_value.ins_id}"{if $field_value.options_array[1] > 0} size="{$field_value.options_array[1]}"{/if}{if $field_value.options_array[3]>0} maxlength="{$field_value.options_array[3]}"{/if} value="{$field_value.value|escape}"{if $field_value.options_array[5]} onKeyUp="wordCount({$field_value.options_array[5]}, this, 'cpt_{$field_value.fieldId}', '{tr}Word Limit Exceeded{/tr}')"{/if} />
		{else}
			<textarea id="area_{$field_value.fieldId}" name="{$field_value.ins_id}" cols="{if $field_value.options_array[1] >= 1}{$field_value.options_array[1]}{else}50{/if}" rows="{if $field_value.options_array[2] >= 1}{$field_value.options_array[2]}{else}4{/if}"{if $field_value.options_array[5]} onKeyUp="wordCount({$field_value.options_array[5]}, this, 'cpt_{$field_value.fieldId}', '{tr}Word Limit Exceeded{/tr}')"{/if}>
				{$field_value.value}
			</textarea>
		{/if}
		{if $field_value.options_array[5]}
			<div class="wordCount">{tr}Word Count:{/tr} <input type="text" id="cpt_{$field_value.fieldId}" size="4" readOnly=true{if !empty($field_value.value)} value="{$field_value.value|count_words}"{/if} />{if $field_value.options_array[5] > 0} {tr}Max:{/tr} {$field_value.options_array[5]}{/if}</div>
		{/if}
	{else}
		<table>
		{foreach from=$field_value.lingualvalue item=ling}
    	<tr>
			<td>{$ling.lang}</td>
      		<td>
				{if $prefs.quicktags_over_textarea eq 'y' and $field_value.options_array[0] eq 1}
        			{include file=tiki-edit_help_tool.tpl qtnum=$field_value.id area_name=area_`$field_value.id`_`$ling.lang`}
        		{/if}
				<textarea id="area_{$field_value.fieldId}_{$ling.lang}" name="{$field_value.ins_id}[{$ling.lang}]" cols="{if $field_value.options_array[1] gt 1}{$field_value.options_array[1]}{else}50{/if}" rows="{if $field_value.options_array[2] gt 1}{$field_value.options_array[2]}{else}4{/if}"{if $field_value.options_array[5] > 0} onKeyUp="wordCount({$field_value.options_array[5]}, this, 'cpt_{$field_value.fieldId}_{$ling.lang}', '{tr}Word Limit Exceeded{/tr}')"{/if}>
					{$ling.value|escape}
				</textarea>
				{if $field_value.options_array[5]}<div class="wordCount">{tr}Word Count:{/tr} <input type="text" id="cpt_{$field_value.fieldId}_{$ling.lang}" size="4" readOnly=true{if !empty($ling.value)} value="{$ling.value|count_words}"{/if} />{if $field_value.options_array[5] > 0}{tr}Max:{/tr} {$field_value.options_array[5]}{/if}</div>{/if}
      		</td>
    	</tr>
		{/foreach}
		</table>
{/if}

{* -------------------- date and time -------------------- *}
{elseif $field_value.type eq 'f'}
	{* ----- Start year --- *}
	{if isset($field_value.options_array[1]) and $field_value.options_array[1] ne ''}
		{assign var=start value=$field_value.options_array[1]}
	{elseif isset($prefs.calendar_start_year)}
		{assign var=start value=$prefs.calendar_start_year}
	{else}
		{assign var=start value=-4}
	{/if}	
	{if $field_value.year > 0 and $field_value.year < $start}
			{assign var=start value=$field_value.year}
	{/if}

	{* ----- End year --- *}
	{if isset($field_value.options_array[2]) and $field_value.options_array[2] ne ''}
		{assign var=end value=$field_value.options_array[2]}
	{elseif isset($prefs.calendar_end_year)}
		{assign var=end value=$prefs.calendar_end_year}
	{else}
		{assign var=end value=+4}
	{/if}
	{if $field_value.year > $end}
		{assign var=end value=$field_value.year}
	{/if}

	{if $field_value.value eq ''}
		{assign var=time value="--"}
	{else}
		{assign var=time value=$field_value.value}
	{/if}
	{if $field_value.options_array[0] ne 't'}
		{if $field_value.isMandatory ne 'y' and (isset($field_value.options_array[3]) and $field_value.options_array[3] eq 'blank')}
			{html_select_date prefix=$field_value.ins_id time=$time start_year=$start end_year=$end field_order=$prefs.display_field_order all_empty=" "}
		{else}
			{html_select_date prefix=$field_value.ins_id time=$time start_year=$start end_year=$end field_order=$prefs.display_field_order}
		{/if}
	{/if}
	{if $field_value.options_array[0] ne 'd'}
		{tr}at{/tr} {html_select_time prefix=$field_value.ins_id time=$time display_seconds=false}
	{/if}

{* -------------------- drop down -------------------- *}
{elseif $field_value.type eq 'd' or $field_value.type eq 'D'}
	<select name="{$field_value.ins_id}" {if $field_value.http_request}onchange="selectValues('trackerIdList={$field_value.http_request[0]}&amp;fieldlist={$field_value.http_request[3]}&amp;filterfield={$field_value.http_request[1]}&amp;status={$field_value.http_request[4]}&amp;mandatory={$field_value.http_request[6]}&amp;filtervalue={$field_value.value}{$field_value.http_request[5]}')"{/if}>
	{assign var=otherValue value=$field_value.value}
		{if $field_value.isMandatory ne 'y' || empty($field_value.value)}
			<option value="">&nbsp;</option>
		{/if}
		{section name=jx loop=$field_value.options_array}
			<option value="{$field_value.options_array[jx]|escape}" {if !empty($item.itemId) && $field_value.value eq $field_value.options_array[jx]}{assign var=otherValue value=''}selected="selected"{elseif empty($item.itemId) && $field_value.defaultvalue eq $field_value.options_array[jx]}selected="selected"{/if}>{$field_value.options_array[jx]|tr_if}</option>
		{/section}
	</select>
	{if $field_value.type eq 'D'}
	<br /><label for="other_{$field_value.ins_id}">{tr}Other:{/tr}</label> <input type="text" name="other_{$field_value.ins_id}" value="{$otherValue|escape}" id="other_{$field_value.ins_id}" />
	{/if}

{* -------------------- radio buttons -------------------- *}
{elseif $field_value.type eq 'R'}
	{section name=jx loop=$field_value.options_array}
		{if $smarty.section.jx.first}
			{if $field_value.options_array[jx] eq '<br>' or $field_value.options_array[jx] eq '<br />'}
				{assign var=sepR value='<br />'}
			{else}
				{assign var=sepR value=' '}
			{/if}
		{/if}
		{if !$smarty.section.jx.first or $sepR ne '<br />'}
			<input type="radio" name="{$field_value.ins_id}" value="{$field_value.options_array[jx]|escape}" {if $field_value.value eq $field_value.options_array[jx] or $field_value.defaultvalue eq $field_value.options_array[jx]}checked="checked"{/if} id="{$field_value.ins_id[jx]}" />
			<label for="{$field_value.ins_id[jx]}">{$field_value.options_array[jx]|escape}</label>
			{if !$smarty.section.jx.last}{$sepR}{/if}
		{/if}
	{/section}

{* -------------------- checkbox -------------------- *}
{elseif $field_value.type eq 'c'}
	<input type="checkbox" name="{$field_value.ins_id}"{if $field_value.value eq 'y' or $field_value.value eq 'on' or strtolower($field_value.value) eq 'yes' or $field_value.defaultvalue eq 'y'} checked="checked"{/if}/>

{* -------------------- jscalendar ------------------- *}
{elseif $field_value.type eq 'j'}
	{if $field_value.options_array[0] eq 'd'}
		{if empty($field_value.value)}
			{jscalendar id=$field_value.ins_id fieldname=$field_value.ins_id showtime="n"}
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

{* -------------------- item link -------------------- *}
{elseif $field_value.type eq 'r'}
	<select name="{$field_value.ins_id}" {if $field_value.http_request}onchange="selectValues('trackerIdList={$field_value.http_request[0]}&amp;fieldlist={$field_value.http_request[3]}&amp;filterfield={$field_value.http_request[1]}&amp;status={$field_value.http_request[4]}&amp;mandatory={$field_value.http_request[6]}&amp;filtervalue='+escape(this.value),'{$field_value.http_request[5]}')"{/if}>
		{if $field_value.isMandatory ne 'y' || empty($field_value.value)}
			<option value=""></option>
		{/if}
		{foreach key=id item=label from=$field_value.list}
			<option value="{$label|escape}" {if $field_value.value eq $label or $defaultvalues.$fid eq $label or $field_value.defaultvalue eq $label}selected="selected"{/if}>
				{if $field_value.displayedList.$id eq ''}{$label}{else}{$field_value.displayedList.$id}{/if}
			</option>
		{/foreach}
	</select>

{* -------------------- item list -------------------- *}
{elseif  $field_value.type eq 'l'}
	{foreach key=id item=label from=$field_value.value}
		{$label|escape}
	{/foreach}

{* -------------------- dynamic list -------------------- *}
{elseif $field_value.type eq 'w'}
	<script type="text/javascript" src="lib/trackers/dynamic_list.js"></script>
<select name="{$field_value.ins_id}" {if $field_value.http_request}onchange="selectValues('trackerIdList={$field_value.http_request[0]}&amp;fieldlist={$field_value.http_request[3]}&amp;filterfield={$field_value.http_request[1]}&amp;status={$field_value.http_request[4]}&amp;mandatory={$field_value.http_request[6]}&amp;filtervalue={$field_value.value}{$field_value.http_request[5]}')"{/if}>
	</select>


{* -------------------- User subscription -------------------- *}
{elseif $field_value.type eq 'U'}
	<input type="text" name="{$field_value.ins_id}" value="{$field_value.value}" />


{* -------------------- Google Map -------------------- *}
{elseif $field_value.type eq 'G'}
	<input type="text" name="{$field_value.ins_id}" value="{$field_value.value}" />
	<br />{tr}Format: x,y,zoom where x is the longitude, and y is the latitude. Zoom is between 0(view Earth) and 19. You can use <a href="tiki-gmap_locator.php?for=item&amp;itemId={$item.itemId}&amp;trackerId={$item.trackerId}&amp;fieldId={$field_value.fieldId}">{tr}Google Map Locator{/tr}</a>.{/tr}

{* -------------------- country selector -------------------- *}
{elseif $field_value.type eq 'y'}
	<select name="{$field_value.ins_id}" {if $field_value.http_request}onchange="selectValues('trackerIdList={$field_value.http_request[0]}&amp;fieldlist={$field_value.http_request[3]}&amp;filterfield={$field_value.http_request[1]}&amp;status={$field_value.http_request[4]}&amp;mandatory={$field_value.http_request[6]}&amp;filtervalue='+escape(this.value),'{$field_value.http_request[5]}')"{/if}>
		{if $field_value.isMandatory ne 'y' || empty($field.value)}
			<option value=""{if $field_value.value eq '' or $field_value.value eq 'None'} selected="selected"{/if}>&nbsp;</option>
		{/if}
		{sortlinks}
			{foreach item=flag from=$field_value.flags}
				 {if $flag ne 'None' and ( ! isset($field_value.itemChoices) || $field_value.itemChoices|@count eq 0 || in_array($flag, $field_value.itemChoices) )}
				 {capture name=flag}{tr}{$flag}{/tr}{/capture}
				 <option value="{$flag|escape}" {if $field_value.value eq $flag}selected="selected"{elseif $flag eq $field_value.defaultvalue}selected="selected"{/if}{if $field_value.options_array[0] ne '1'} style="background-image:url('img/flags/{$flag}.gif');background-repeat:no-repeat;padding-left:25px;padding-bottom:3px;"{/if}>{$smarty.capture.flag|replace:'_':' '}</option>
				 {/if}
		{/foreach}
		{/sortlinks}
	</select>

{* -------------------- auto increment -------------------- *}
{elseif $field_value.type eq 'q'}
	<input type="hidden" name="track[{$field_value.fieldId}]" />
	<input type="hidden" name="{$field_value.ins_id}" value="{$field_field.value|escape}" />
	{$field_value.value|escape}

{/if}

{if $field_value.isMandatory eq 'y'}
	</span>
{/if}

{/strip}
