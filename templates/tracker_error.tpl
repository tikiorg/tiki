{* $Id: tiki-view_tracker_item.tpl 23988 2009-12-22 13:55:35Z sylvieg $ *}
{****  Display warnings about incorrect values and missing mandatory fields ***}
{if count($err_mandatory) > 0}
{remarksbox type='errors' title='{tr}Errors{/tr}'}
	<em class='mandatory_note'>{tr}Following mandatory fields are missing{/tr}</em>&nbsp;:<br/>
	{section name=ix loop=$err_mandatory}
		{$err_mandatory[ix].name|escape}{if !$smarty.section.ix.last},&nbsp;{/if}
	{/section}
{/remarksbox}
<br />
{/if}

{if count($err_value) > 0}
{remarksbox type='errors' title='{tr}Errors{/tr}'}
	<em class='mandatory_note'>{tr}Following fields are incorrect{/tr}</em>&nbsp;:<br/>
	{section name=ix loop=$err_value}
		{$err_value[ix].name|escape}{if !$smarty.section.ix.last},&nbsp;{/if}
	{/section}
{/remarksbox}
<br />
{/if}
