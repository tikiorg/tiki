{strip}
	{if $heading ne 'n'}
		{if $showItemId ne 'n'}
			{assign var='comma' value='y'}
			{$delimitorL}itemId{$delimitorR}
		{/if}
		{if $showStatus ne 'n'}
			{if $comma eq 'y'}{$separator}{else}{assign var='comma' value='y'}{/if}
			{$delimitorL}status{$delimitorR}
		{/if}
		{if $showCreated ne 'n'}
			{if $comma eq 'y'}{$separator}{else}{assign var='comma' value='y'}{/if}
			{$delimitorL}created{$delimitorR}
		{/if}
		{if $showLastModif ne 'n'}
			{if $comma eq 'y'}{$separator}{else}{assign var='comma' value='y'}{/if}
			{$delimitorL}lastModif{$delimitorR}
		{/if}
		{if !empty($listfields)}
			{if $comma eq 'y'}{$separator}{else}{assign var='comma' value='y'}{/if}
			{foreach item=field key=fieldId from=$listfields name=list}
				{$delimitorL}{$field.name} -- {$fieldId}{$delimitorR}
				{if !$smarty.foreach.list.last}{$separator}{/if}
			{/foreach}
		{/if}
		{assign var='comma' value='n'}
	{/if}
{/strip}{* this empty line below is necessary to make data starts on the line after the headers *}

{foreach from=$items item=item}
{strip}{* DO NOT ident this strip - the line must not begin with space *}
		{assign var='comma' value='n'}
		{if $showItemId ne 'n'}
			{assign var='comma' value='y'}
			{$delimitorL}{$item.itemId}{$delimitorR}
		{/if}
		{if $showStatus eq 'y'}
			{if $comma eq 'y'}{$separator}{else}{assign var='comma' value='y'}{/if}
			{$delimitorL}{$item.status}{$delimitorR}
		{/if}
		{if $showCreated ne 'n'}
			{if $comma eq 'y'}{$separator}{else}{assign var='comma' value='y'}{/if}
			{$delimitorL}{$item.created|tiki_short_datetime}{$delimitorR}
		{/if}
		{if $showLastModif ne 'n'}
			{if $comma eq 'y'}{$separator}{else}{assign var='comma' value='y'}{/if}
			{$delimitorL}{$item.lastModif|tiki_short_datetime}{$delimitorR}
		{/if}
		{if !empty($listfields)}
			{if $comma eq 'y'}{$separator}{else}{assign var='comma' value='y'}{/if}
			{foreach item=field_value from=$item.field_values name=list}
				{if $field_value.isHidden ne 'c' or ($field_value.isHidden eq 'c' and ($item.itemUser eq $user or $tiki_p_admin_trackers eq 'y'))}
					{capture name="line"}
						{trackeroutput field=$field_value item=$item list_mode='csv' showlinks='n'}
					{/capture}
					{$delimitorL}{$smarty.capture.line|replace:"\r\n":"$CR"|replace:"\n":"$CR"|replace:"<br>":"$CR"|replace:"$delimitorL":"$delimitorL$delimitorL"|replace:"$delimitorR":"$delimitorR$delimitorR"}{$delimitorR}
				{else}
					{$delimitorL}{$delimitorR}
				{/if}
				{if !$smarty.foreach.list.last}{$separator}{/if}
			{/foreach}
		{/if}
	{/strip}{* this empty line below is necessary to make separate rows - but make sure there are no empty lines after the /foreach end tag to avoid empty rows being generated after every 100th row *}

{/foreach}