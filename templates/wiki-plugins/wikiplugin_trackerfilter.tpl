{*param :  $msgTrackerFilter, $line, $open, $iTrackerFilter, $trackerId, $filters(array(name, format, fieldId, selected, opts)), $showFieldId *}
{strip}
{if $msgTrackerFilter}
<div class="simplebox highlight">{$msgTrackerFilter|escape}</div>
{/if}
{if $line ne 'y' and $prefs.javascript_enabled eq 'y'}
{button _text="{tr}Filters{/tr}" _flip_id="trackerFilter$iTrackerFilter"}
{/if}
<div id="trackerFilter{$iTrackerFilter}" style="display:{if $open eq 'y' or $line eq 'y' or $prefs.javascript_enabled ne 'y'}block{else}none{/if}">
{if empty($inForm)}<form method="post">{/if}
<input type="hidden" name="trackerId" value="{$trackerId}" />
<table class="normal">
{if $line eq 'y'}<tr>{/if}
{cycle values="even,odd" print=false}
{section name=if loop=$filters}
	{if $line ne 'y'}<tr class="{cycle}">{/if}
		<td>
		<label for="f_{$filters[if].fieldId}">{$filters[if].name|tr_if}</label>
		{if $showFieldId eq 'y'} -- {$filters[if].fieldId}{/if}
		{if $line ne 'y'}</td><td>{else}:{/if}
{*------drop-down, multiple *}
		{if $filters[if].format eq 'd' or  $filters[if].format eq 'm'}
			<select id="f_{$filters[if].fieldId}" name="f_{$filters[if].fieldId}{if $filters[if].format eq "m"}[]{/if}" {if $filters[if].format eq "m"} size="5" multiple="multiple"{/if}> 
			<option value=""{if !$filters[if].selected} selected="selected"{/if}>{tr}Any{/tr}</option>
			{section name=io loop=$filters[if].opts}
				<option value="{$filters[if].opts[io].id|escape}"{if $filters[if].opts[io].selected eq "y"} selected="selected"{/if}>
					{$filters[if].opts[io].name|tr_if}
				</option>
			{/section}
			</select>
			{if $filters[if].format eq "m"} {tr}Tip: Hold down CTRL to select multiple{/tr}{/if}
{*------text *} 
		{elseif $filters[if].format eq 't' or $filters[if].format eq 'T' or $filters[if].format eq 'i'}
			{if $filters[if].format eq 'i'}
				{capture name=i_f}f_{$filters[if].fieldId}{/capture}
				{initials_filter_links _initial=$smarty.capture.i_f}
			{/if}
			<input id="f_{$filters[if].fieldId}" type="text" name="f_{$filters[if].fieldId}" value="{$filters[if].selected}"/>
{*------checkbox, radio *}
		{else}
			<input {if $filters[if].format eq "c"}type="checkbox"{else}type="radio"{/if} name="f_{$filters[if].fieldId}{if $filters[if].format eq "c"}[]{/if}" value=""{if !$filters[if].selected} checked="checked"{/if} /> {tr}Any{/tr}</input>{if $line ne 'y'}<br />{/if}
			{section name=io loop=$filters[if].opts}
				<input {if $filters[if].format eq "c"}type="checkbox"{else}type="radio"{/if} name="f_{$filters[if].fieldId}{if $filters[if].format eq "c"}[]{/if}" value="{$filters[if].opts[io].id|escape}"{if $filters[if].opts[io].selected eq "y"} checked="checked"{/if} /> {$filters[if].opts[io].name|tr_if}</input>{if $line ne 'y'}<br />{/if}
			{/section}
		{/if}
		</td>
		{if $line ne 'y'}</tr>{else} {/if}
{/section}
{if $line ne 'y' and $action}<tr>{/if}
{if $action}
<td>&nbsp;</td><td><input type="submit" name="filter" value="{if empty($action)}{tr}Filter{/tr}{else}{tr}{$action}{/tr}{/if}" /></td>
{/if}
{if $line ne 'y' and $action}</tr>{/if}
</table>
{if empty($inForm)}</form>{/if}
</div>
{/strip}
