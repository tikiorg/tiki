{strip}
{* $Id$ *}
	{**** Display warnings about incorrect values and missing mandatory fields ***}
	{if isset($err_mandatory) && count($err_mandatory) > 0}
		{remarksbox type='errors' title="{tr}Errors{/tr}"}
			<em class='mandatory_note'>{tr}The following mandatory fields are missing{/tr}</em> :<br/>
			{section name=ix loop=$err_mandatory}
				{$err_mandatory[ix].name|escape}
				{if !$smarty.section.ix.last}, {/if}
			{/section}
		{/remarksbox}
	{/if}

	{if isset($err_value) && count($err_value) > 0}
		{remarksbox type='errors' title="{tr}Errors{/tr}"}
			<em class='mandatory_note'>{tr}Following fields are incorrect{/tr}</em> :<br/>
			{section name=ix loop=$err_value}
				{$err_value[ix].name|escape}
				{if !empty($err_value[ix].errorMsg)} (<em>{$err_value[ix].errorMsg|escape}</em>){/if}
				{if !$smarty.section.ix.last}, {/if}
			{/section}
		{/remarksbox}
	{/if}
{/strip}
