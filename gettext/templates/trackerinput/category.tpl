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
