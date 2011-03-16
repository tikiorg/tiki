{if !empty($field.options_array[2]) && ($field.options_array[2] eq '1' or $field.options_array[2] eq 'y')}
	{select_all checkbox_names=`$field.ins_id`[] label="{tr}Select All{/tr}"}
{/if}
{if $field.options_array[1] eq 'd' || $field.options_array[1] eq 'm'}
	{if $field.options_array[1] eq 'm'}<small>{tr}Hold "Ctrl" in order to select multiple values{/tr}</small><br />{/if}
	<select name="{$field.ins_id}[]"{if $field.options_array[1] eq 'm'} multiple="multiple"{/if}>
	{if $field.options_array[1] eq 'd' and (empty($field.value[0]) or $field.isMandatory ne 'y')}
		<option value=""></option>
	{/if}
	{foreach key=ku item=cat from=$field.list}
		{assign var=fcat value=$cat.categId}
		<option value="{$cat.categId}"{if $field.cat.$fcat eq 'y'} selected="selected"{/if}>{$cat.categpath|escape}</option>
	{/foreach}
	</select>
{else}
<table width="100%">
	<tr>
	{foreach key=ku item=iu from=$field.list name=eforeach}
	{assign var=fcat value=$iu.categId}
	<td width="50%"  class="trackerCategoryName">
		<input type={if $field.options_array[1] eq "radio"}"radio"{else}"checkbox"{/if} name="{$field.ins_id}[]" value="{$iu.categId}" id="cat{$iu.categId}" {if $field.cat.$fcat eq 'y'} checked="checked"{/if}/>
		{if $field.options_array[4] eq 1 && !empty($iu.description)}<a href="{$iu.description|escape}" target="tikihelp" class="tikihelp" title="{$iu.name|escape}:{$iu.description|escape}">{icon _id=help alt=''}</a>{/if}
		<label for="cat{$iu.categId}">{$iu.name|escape}</label>
	</td>{if !$smarty.foreach.eforeach.last and $smarty.foreach.eforeach.index % 2}</tr><tr>{elseif $smarty.foreach.eforeach.last and !($smarty.foreach.eforeach.index % 2)}<td width="50%"  class="trackerCategoryName">&nbsp;</td>{/if}
	{/foreach}
	</tr>
</table>
{/if}
