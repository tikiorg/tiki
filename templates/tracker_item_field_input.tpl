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

{* -------------------- user selector -------------------- *}
{elseif $field_value.type eq 'u'}
	{if empty($field_value.options_array) or ($field_value.options_array[0] !=1 and $field_value.options_array[0] !=2) or $tiki_p_admin_trackers eq 'y'}
		{if $prefs.javascript_enabled eq 'y' and $prefs.feature_jquery_autocomplete eq 'y' and $field_value.list|@count > $prefs.user_selector_threshold and $field_value.isMandatory ne 'y'}
			{* since autocomplete allows blank entry it can't be used for mandatory selection. *}
			<input id="user_selector_{$field_value.fieldId}" type="text" size="20" name="{$field_value.ins_id}" value="{if $field_value.options_array[0] eq '2'}{$user}{else}{$field_value.value}{/if}" />
			{if $prefs.user_selector_realnames_tracker == 'y'}
			{jq}
				$("#user_selector_{{$field_value.fieldId}}").tiki("autocomplete", "userrealname", {mustMatch: true});
			{/jq}
			{else}
			{jq}
				$("#user_selector_{{$field_value.fieldId}}").tiki("autocomplete", "username", {mustMatch: true});
			{/jq}
			{/if}
		{else}
		<select name="{$field_value.ins_id}" {if $field_value.http_request}onchange="selectValues('trackerIdList={$field_value.http_request[0]}&amp;fieldlist={$field_value.http_request[3]}&amp;filterfield={$field_value.http_request[1]}&amp;status={$field_value.http_request[4]}&amp;mandatory={$field_value.http_request[6]}&amp;filtervalue='+escape(this.value),'{$listfields.$fid.http_request[5]}')"{/if}>
		{if $field_value.isMandatory ne 'y'}
			<option value=""{if empty($field_value.value) && !empty($item.itemId)} selected="selected"{/if}>{tr}None{/tr}</option>
		{/if}
		{foreach key=id item=one from=$field_value.list}
			{if ( ! isset($field_value.itemChoices) || $field_value.itemChoices|@count eq 0 || in_array($one, $field_value.itemChoices) )}
				<option value="{$one|escape}"
				{if empty($item.itemId) and $one eq $user}
					selected="selected"
				{elseif $field_value.options_array[0] eq 2 and $one eq $user}
					selected="selected"
				{elseif $one eq $field_value.value}
					selected="selected"
				{/if}>
					{$one|username}
				</option>
			{/if}
		{/foreach}
		</select>
		{/if}
	{else}
		{$user|username}
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
				{foreach from=$field_value.list item=group}
				{if ( ! isset($field_value.itemChoices) || $field_value.itemChoices|@count eq 0 || in_array($group, $field_value.itemChoices) )}
					<option value="{$group|escape}" {if $input_err and $field_value.value eq $group} selected="selected"{/if}>{$group|escape}</option>
				{/if}
			{/foreach}
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
		{if $field_value.options_array[1] eq 'm'}<small>{tr}Hold "Ctrl" in order to select multiple values{/tr}</small><br />{/if}
		<select name="{$field_value.ins_id}[]"{if $field_value.options_array[1] eq 'm'} multiple="multiple"{/if}>
		{if $field_value.options_array[1] eq 'd' and (empty($field_value.value[0]) or $field_value.isMandatory ne 'y')}
	   		<option value=""></option>
		{/if}
		{foreach key=ku item=cat from=$field_value.list}
			{assign var=fcat value=$cat.categId}
			<option value="{$cat.categId}"{if $field_value.cat.$fcat eq 'y'} selected="selected"{/if}>{$cat.categpath|escape}</option>
		{/foreach}
		</select>
	{else}
	<table width="100%">
		<tr>
		{foreach key=ku item=iu from=$field_value.list name=eforeach}
		{assign var=fcat value=$iu.categId}
		<td width="50%"  class="trackerCategoryName">
			<input type={if $field_value.options_array[1] eq "radio"}"radio"{else}"checkbox"{/if} name="{$field_value.ins_id}[]" value="{$iu.categId}" id="cat{$iu.categId}" {if $field_value.cat.$fcat eq 'y'} checked="checked"{/if}/>
			{if $field_value.options_array[4] eq 1 && !empty($iu.description)}<a href="{$iu.description|escape}" target="tikihelp" class="tikihelp" title="{$iu.name|escape}:{$iu.description|escape}">{icon _id=help alt=''}</a>{/if}
			<label for="cat{$iu.categId}">{$iu.name|escape}</label>
		</td>{if !$smarty.foreach.eforeach.last and $smarty.foreach.eforeach.index % 2}</tr><tr>{elseif $smarty.foreach.eforeach.last and !($smarty.foreach.eforeach.index % 2)}<td width="50%"  class="trackerCategoryName">&nbsp;</td>{/if}
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
		{if $field_value.isMandatory ne 'y'}
			<a href="{$smarty.server.PHP_SELF}?{query removeImage='y' fieldId=`$field_value.fieldId` itemId=`$item.itemId` trackerId=`$item.trackerId` fieldName=`$field_value.name`}">{icon _id='cross' alt="{tr}Remove Image{/tr}"}</a>
		{/if}
   {/if}


{* -------------------- file -------------------- *}
{elseif $field_value.type eq 'A'}
	{if $tiki_p_attach_trackers ne 'y'}
		{tr}Permission denied{/tr}
	{else}
		<input type="file" name="{$field_value.ins_id}"{if isset($input_err)} value="{$field_value.value}"{/if} />
	{/if}
	{if $field_value.value ne ''}
		<br />
		{$field_value.info.filename}&nbsp;
		<a href="tiki-download_item_attachment.php?attId={$field_value.value}" title="{tr}Download{/tr}">{icon _id='disk' alt="{tr}Download{/tr}"}</a>
		{if ($tiki_p_admin_trackers eq 'y' or $field_value.info.user eq $user) and $field_value.isMandatory ne 'y'}
			<a href="{$smarty.server.PHP_SELF}?{query removeattach=$field_value.value}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
		{/if}
	{/if}

{* -------------------- user groups -------------------- *}
{elseif $field_value.type eq 'usergroups'}
	
{* -------------------- preference --------------------- *}
{elseif $field_value.type eq 'p'}
	{if $field_value.options_array[0] eq 'password'}
		{if ($prefs.auth_method neq 'cas' || ($prefs.cas_skip_admin eq 'y' && $user eq 'admin')) and $prefs.change_password neq 'n'}
			<input type="password" name="{$field_value.ins_id}" />
		{/if}
	{elseif $field_value.options_array[0] eq 'language'}
		<select name="{$field_value.ins_id}">
			{section name=ix loop=$languages}
				{if count($prefs.available_languages) == 0 || in_array($languages[ix].value, $prefs.available_languages)}
					<option value="{$languages[ix].value|escape}"
					{if $user_prefs.language eq $languages[ix].value}selected="selected"{/if}>
					{$languages[ix].name}
					</option>
				{/if}
			{/section}
			<option value='' {if !$user_prefs.language}selected="selected"{/if}>{tr}Site default{/tr}</option>
		</select>
	{else}
			<input type="text" name="{$field_value.ins_id}" value="{$field_value.value}" />
	{/if}

{* -------------------- text field  -------------------- *}
{elseif $field_value.type eq 't'}
	{if $field_value.isMultilingual ne 'y'}
		{*prepend*}{if $field_value.options_array[2]}<span class="formunit">{$field_value.options_array[2]}&nbsp;</span>{/if}
		<input type="text" id="{$field_value.ins_id|replace:'[':'_'|replace:']':''}" name="{$field_value.ins_id}" {if $field_value.options_array[1]}size="{$field_value.options_array[1]}"{/if} {if $field_value.options_array[4]}maxlength="{$field_value.options_array[4]}"{/if} value="{if $field_value.value}{$field_value.value|escape}{else}{$field_value.defaultvalue|escape}{/if}" />
		{*append*}{if $field_value.options_array[3]}<span class="formunit">&nbsp;{$field_value.options_array[3]}</span>{/if}
		{if $field_value.options_array[5] eq 'y' && $prefs.javascript_enabled eq 'y' and $prefs.feature_jquery_autocomplete eq 'y'}
			{if !empty($item)}
				{autocomplete element="#"|cat:$field_value.ins_id|replace:"[":"_"|replace:"]":"" type="trackervalue"
						options="trackerId:"|cat:$item.trackerId|cat:",fieldId:"|cat:$field_value.fieldId}
			{else}
				{autocomplete element="#"|cat:$field_value.ins_id|replace:"[":"_"|replace:"]":"" type="trackervalue"
						options="trackerId:"|cat:$trackerId|cat:",fieldId:"|cat:$field_value.fieldId}
			{/if}
		{/if}
	{else}
    	{foreach from=$field_value.lingualvalue item=ling name=multi}
    		<label for="{$field_value.ins_id|replace:'[':'_'|replace:']':''}_{$ling.lang}">{$ling.lang|langname}</label>
			<br />
            {*prepend*}{if $field_value.options_array[2]}<span class="formunit">{$field_value.options_array[2]}&nbsp;</span>{/if}
        	<input type="text" id="{$field_value.ins_id|replace:'[':'_'|replace:']':''}_{$ling.lang}" name="{$field_value.ins_id}[{$ling.lang}]" value="{$ling.value|escape}" {if $field_value.options_array[1]}size="{$field_value.options_array[1]}"{/if} {if $field_value.options_array[4]}maxlength="{$field_value.options_array[4]}"{/if} /> {*@@ missing value*}
        	{*append*}{if $field_value.options_array[3]}<span class="formunit">&nbsp;{$field_value.options_array[3]}</span>{/if}
			{if $field_value.options_array[5] eq 'y' && $prefs.javascript_enabled eq 'y' and $prefs.feature_jquery_autocomplete eq 'y'}
				{if !empty($item)}
					{autocomplete element="#"|cat:$field_value.ins_id|replace:"[":"_"|replace:"]":""|cat:"_"|cat:$ling.lang type="trackervalue"
							options="trackerId:"|cat:$item.trackerId|cat:",fieldId:"|cat:$field_value.fieldId}
				{else}
					{autocomplete element="#"|cat:$field_value.ins_id|replace:"[":"_"|replace:"]":""|cat:"_"|cat:$ling.lang type="trackervalue"
							options="trackerId:"|cat:$trackerId|cat:",fieldId:"|cat:$field_value.fieldId}
				{/if}
			{/if}
			{if !$smarty.foreach.multi.last}<br />{/if}
		{/foreach}
	{/if}

{* -------------------- page selector  -------------------- *}
{elseif $field_value.type eq 'k'}
	{if $field_value.options[0] != 1 || $tiki_p_admin_trackers == 'y'}
		<input type="text" id="page_selector_{$field_value.fieldId}" name="{$field_value.ins_id}" {if $field_value.options_array[1] gt 0}size="{$field_value.options_array[1]}"{/if} value="{if $field_value.value}{$field_value.value|escape}{else}{$field_value.defaultvalue|escape}{/if}" />
		{if $field_value.isMandatory ne 'y'} {* since autocomplete allows blank entry it can't be used for mandatory selection. *}     
			{autocomplete element="#page_selector_`$field_value.fieldId`" type='pagename'}
		{/if}
	{else}
		{$field_value.value|escape}
	{/if}

{* -------------------- email  -------------------- *}
{elseif $field_value.type eq 'm'}
	<input type="text" name="{$field_value.ins_id}" id="{$field_value.ins_id}" value="{$field_value.value|escape}" />

{* -------------------- url  -------------------- *}
{elseif $field_value.type eq 'L'}
	<input type="text" name="{$field_value.ins_id}" id="{$field_value.ins_id}" value="{$field_value.value|escape}" />

{* -------------------- numeric and currency -------------------- *}
{elseif $field_value.type eq 'n' or $field_value.type eq 'b'}
	{*prepend*}
	{if $field_value.options_array[2]}
		<span class="formunit">{$field_value.options_array[2]}&nbsp;</span>
	{/if}
	<input type="text" class="numeric" name="{$field_value.ins_id}" {if $field_value.options_array[1]}size="{$field_value.options_array[1]}" 
		 maxlength="{$field_value.options_array[1]}"{/if} value="{$field_value.value|escape}" id="{$field_value.ins_id}" />
	{*append*}
	{if $field_value.options_array[3]}
		<span class="formunit">&nbsp;{$field_value.options_array[3]}</span>
	{/if}

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
	{if $field_value.isMultilingual ne 'y'}
		{if $field_value.options_array[2] == 1}
			{if $field_value.options_array[0] eq 1}
    			{toolbars qtnum=$field_value.fieldId area_id="area_"|cat:$field_value.fieldId section="trackers"}
			{/if}
			<input type="text" id="area_{$field_value.fieldId}" name="{$field_value.ins_id}"{if $field_value.options_array[1] > 0} size="{$field_value.options_array[1]}"{/if}{if $field_value.options_array[3]>0} maxlength="{$field_value.options_array[3]}"{/if} value="{$field_value.value|escape}"{if $field_value.options_array[5]} onkeyup="wordCount({$field_value.options_array[5]}, this, 'cpt_{$field_value.fieldId}', "{tr}Word Limit Exceeded{/tr}")"{/if} {if $field_value.options_array[3]} onkeyup="charCount({$field_value.options_array[3]}, this, 'cpt_{$field_value.fieldId}', "{tr}Character Limit Exceeded{/tr}")"{/if} />
		{else}
			{capture name=textarea_id}area_{$field_value.fieldId}{/capture}
			{capture name=textarea_toolbars}{if $field_value.options_array[0] eq 1}y{else}n{/if}{/capture}
			{capture name=textarea_simple}n{/capture}
			{capture name=textarea_cols}{if $field_value.options_array[1] >= 1}{$field_value.options_array[1]}{else}50{/if}{/capture}
			{capture name=textarea_rows}{if $field_value.options_array[2] >= 1}{$field_value.options_array[2]}{else}4{/if}{/capture}
			{capture name=textarea_onkeyup}{if $field_value.options_array[5]}wordCount({$field_value.options_array[5]}, this, 'cpt_{$field_value.fieldId}', '{tr}Word Limit Exceeded{/tr}'){elseif $field_value.options_array[3]}charCount({$field_value.options_array[3]}, this, 'cpt_{$field_value.fieldId}', '{tr}Character Limit Exceeded{/tr}'){/if}{/capture}
			{textarea id=$smarty.capture.textarea_id name=$field_value.ins_id _toolbars=$smarty.capture.textarea_toolbars _simple=$smarty.capture.textarea_simple cols=$smarty.capture.textarea_cols rows=$smarty.capture.textarea_rows onkeyup=$smarty.capture.textarea_onkeyup _wysiwyg='n' section="trackers"}
				{$field_value.value}
			{/textarea}
		{/if}
		{if $field_value.options_array[3]}
			<div class="charCount">
				{if $prefs.javascript_enabled eq 'y'}
				 	 {tr}Character Count:{/tr} <input type="text" id="cpt_{$field_value.fieldId}" size="4" readOnly="true"{if !empty($field_value.value)} value="{$field_value.value|count_characters}"{/if} />
				{/if}
				{if $field_value.options_array[3] > 0} {tr}Max:{/tr} {$field_value.options_array[3]}{/if}
			</div>
		{/if}
		{if $field_value.options_array[5]}
			<div class="wordCount">
				{if $prefs.javascript_enabled eq 'y'}
					 {tr}Word Count:{/tr} <input type="text" id="cpt_{$field_value.fieldId}" size="4" readOnly="true"{if !empty($field_value.value)} value="{$field_value.value|count_words}"{/if} />
				{/if}
				{if $field_value.options_array[5] > 0} {tr}Max:{/tr} {$field_value.options_array[5]}{/if}
			</div>
		{/if}
	{else}
		{foreach name=lg from=$field_value.lingualvalue item=ling}
			<label for="area_{$field_value.fieldId}_{$ling.lang}">{$ling.lang|langname}</label><br />
				{if $field_value.options_array[0] eq 1}
        			{toolbars qtnum=$field_value.id area_id=area_`$field_value.id`_`$ling.lang`}
        		{/if}
				<textarea id="area_{$field_value.fieldId}_{$ling.lang}" name="{$field_value.ins_id}[{$ling.lang}]" cols="{if $field_value.options_array[1] gt 1}{$field_value.options_array[1]}{else}50{/if}" rows="{if $field_value.options_array[2] gt 1}{$field_value.options_array[2]}{else}4{/if}"{if $field_value.options_array[5] > 0} onkeyup="wordCount({$field_value.options_array[5]}, this, 'cpt_{$field_value.fieldId}_{$ling.lang}', '{tr}Word Limit Exceeded{/tr}')"{/if}>
					{$ling.value|escape}
				</textarea>
				{if $field_value.options_array[5]}<div class="wordCount">{tr}Word Count:{/tr} <input type="text" id="cpt_{$field_value.fieldId}_{$ling.lang}" size="4" readOnly="true"{if !empty($ling.value)} value="{$ling.value|count_words}"{/if} />{if $field_value.options_array[5] > 0}{tr}Max:{/tr} {$field_value.options_array[5]}{/if}</div>i{elseif !$smarty.foreach.lg.last}<br />{/if}
		{/foreach}
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
	{if $field_value.value eq '' or ($field_value.options_array[3] eq 'blank' and empty($item.itemId))}
		{assign var=time value="--"}
	{else}
		{assign var=time value=$field_value.value}
	{/if}
	{if $field_value.options_array[0] ne 't'}
		{if ($field_value.isMandatory ne 'y' and (isset($field_value.options_array[3]) and ($field_value.options_array[3] eq 'blank' or $field_value.options_array[3] eq 'empty'))) or (isset($inExportForm) and $inExportForm eq 'y')}
			{html_select_date prefix=$field_value.ins_id time=$time start_year=$start end_year=$end field_order=$prefs.display_field_order all_empty=" "}
		{else}
			{html_select_date prefix=$field_value.ins_id time=$time start_year=$start end_year=$end field_order=$prefs.display_field_order}
		{/if}
	{/if}
	{if $field_value.options_array[0] eq 'dt'}
		{tr}at{/tr} 
	{/if}
	{if $field_value.options_array[0] ne 'd'}
		{if $field_value.isMandatory ne 'y' and (isset($field_value.options_array[3]) and ($field_value.options_array[3] eq 'blank' or $field_value.options_array[3] eq 'empty'))or (isset($inExportForm) and $inExportForm eq 'y')}
			{html_select_time prefix=$field_value.ins_id time=$time display_seconds=false all_empty=" " use_24_hours=$use_24hr_clock}
		{else}
			{html_select_time prefix=$field_value.ins_id time=$time display_seconds=false use_24_hours=$use_24hr_clock}
		{/if}
	{/if}

{* -------------------- drop down -------------------- *}
{elseif $field_value.type eq 'd' or $field_value.type eq 'D'}
	<select name="{$field_value.ins_id}" {if $field_value.http_request}onchange="selectValues('trackerIdList={$field_value.http_request[0]}&amp;fieldlist={$field_value.http_request[3]}&amp;filterfield={$field_value.http_request[1]}&amp;status={$field_value.http_request[4]}&amp;mandatory={$field_value.http_request[6]}&amp;filtervalue='+escape(this.value),'{$field_value.http_request[5]}')"{/if}>
	{assign var=otherValue value=$field_value.value}
		{if $field_value.isMandatory ne 'y' || empty($field_value.value)}
			<option value="">&nbsp;</option>
		{/if}
		{section name=jx loop=$field_value.options_array}
			<option value="{$field_value.options_array[jx]|escape}" {if !empty($item.itemId) && ($field_value.value eq $field_value.options_array[jx] or (isset($field_value.isset) && $field_value.isset == 'n' && $field_value.defaultvalue eq $field_value.options_array[jx]))}{assign var=otherValue value=''}selected="selected"{elseif (empty($item.itemId) || !isset($field_value.value)) && $field_value.defaultvalue eq $field_value.options_array[jx]}selected="selected"{/if}>
				{$field_value.options_array[jx]|tr_if}
			</option>
		{/section}
	</select>
	{if $field_value.type eq 'D'}
	<br /><label for="other_{$field_value.ins_id}">{tr}Other:{/tr}</label> <input type="text" name="other_{$field_value.ins_id}" value="{$otherValue|escape}" id="other_{$field_value.ins_id}" />
	{/if}

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

{* -------------------- checkbox -------------------- *}
{elseif $field_value.type eq 'c'}
	<input type="checkbox" name="{$field_value.ins_id}"{if $field_value.value eq 'y' or $field_value.value eq 'on' or strtolower($field_value.value) eq 'yes' or $field_value.defaultvalue eq 'y'} checked="checked"{/if}/>

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

{* -------------------- item link -------------------- *}
{elseif $field_value.type eq 'r'}
	<select name="{$field_value.ins_id}" {if $field_value.http_request}onchange="selectValues('trackerIdList={$field_value.http_request[0]}&amp;fieldlist={$field_value.http_request[3]}&amp;filterfield={$field_value.http_request[1]}&amp;status={$field_value.http_request[4]}&amp;mandatory={$field_value.http_request[6]}&amp;filtervalue='+escape(this.value),'{$field_value.http_request[5]}')"{/if}>
		{if $field_value.isMandatory ne 'y' || empty($field_value.value)}
			<option value=""></option>
		{/if}
		{foreach key=id item=label from=$field_value.list}
			<option value="{$label|escape}" {if $field_value.value eq $label or $defaultvalues.$fid eq $label or $field_value.defaultvalue eq $label}selected="selected"{/if}>
				{if $field_value.listdisplay[$id] eq ""}{$label}{else}{$field_value.listdisplay[$id]}{/if}
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
<select name="{$field_value.ins_id}" {if $field_value.http_request}onchange="selectValues('trackerIdList={$field_value.http_request[0]}&amp;fieldlist={$field_value.http_request[3]}&amp;filterfield={$field_value.http_request[1]}&amp;status={$field_value.http_request[4]}&amp;mandatory={$field_value.http_request[6]}&amp;filtervalue='+escape(this.value),'{$field_value.http_request[5]}')"{/if}>
	</select>


{* -------------------- User subscription -------------------- *}
{elseif $field_value.type eq 'U'}
	<input type="text" name="{$field_value.ins_id}" value="{$field_value.value}" />


{* -------------------- Google Map -------------------- *}
{elseif $field_value.type eq 'G'}
	{$headerlib->add_map()}
	<div class="map-container" data-target-field="{$field_value.ins_id}" style="height: 250px; width: 250px;"></div>
	<input type="text" name="{$field_value.ins_id}" id="{$field_value.ins_id}" value="{$field_value.value}" size="60" />
	<br />{tr}Format: x,y,zoom where x is the longitude, and y is the latitude. Zoom is between 0(view Earth) and 19.{/tr}

{* -------------------- country selector -------------------- *}
{elseif $field_value.type eq 'y'}
	<select name="{$field_value.ins_id}" {if $field_value.http_request}onchange="selectValues('trackerIdList={$field_value.http_request[0]}&amp;fieldlist={$field_value.http_request[3]}&amp;filterfield={$field_value.http_request[1]}&amp;status={$field_value.http_request[4]}&amp;mandatory={$field_value.http_request[6]}&amp;filtervalue='+escape(this.value),'{$field_value.http_request[5]}')"{/if}>
		{if $field_value.isMandatory ne 'y' || empty($field_value.value)}
			<option value=""{if $field_value.value eq '' or $field_value.value eq 'None'} selected="selected"{/if}>&nbsp;</option>
		{/if}
		{if empty($field_value.itemChoices)}
			<option value="Other"{if $field_value.value eq 'None'} selected="selected"{/if}{if $field_value.options_array[0] ne '1'} style="background-image:url('img/flags/Other.gif');background-repeat:no-repeat;padding-left:25px;padding-bottom:3px;"{/if}>{tr}Other{/tr}</option>
		{/if}

		{foreach key=flagicon item=flag from=$field_value.flags}
			{if $flagicon ne 'None' and $flagicon ne 'Other' and ( ! isset($field_value.itemChoices) || $field_value.itemChoices|@count eq 0 || in_array($flagicon, $field_value.itemChoices) )}
				 <option value="{$flagicon|escape}" {if $field_value.value eq $flagicon}selected="selected"{elseif $flagicon eq $field_value.defaultvalue}selected="selected"{/if}{if $field_value.options_array[0] ne '1'} style="background-image:url('img/flags/{$flagicon}.gif');background-repeat:no-repeat;padding-left:25px;padding-bottom:3px;"{/if}>{$flag|escape}</option>
			 {/if}
		{/foreach} 
	</select>

{* -------------------- auto increment -------------------- *}
{elseif $field_value.type eq 'q'}
	<input type="hidden" name="track[{$field_value.fieldId}]" />
	<input type="hidden" name="{$field_value.ins_id}" value="{$field_field.value|escape}" />
	{include file='tracker_item_field_value.tpl'}

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

{* -------------------- LDAP -------------------- *}
{elseif $field_value.type eq 'P'}
	{if $field_value.value ne ''}
		{$field_value.value}
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
