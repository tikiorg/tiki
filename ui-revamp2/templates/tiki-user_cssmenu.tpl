{* $Id$ *}
{if count($menu_channels) > 0}
{assign var=opensec value='0'}
{assign var=sep value=''}

<ul id="cssmenu{$idCssmenu}" class="cssmenu{if $menu_type}_{$menu_type}{/if} menu{$menu_info.menuId}">

{foreach key=pos item=chdata from=$menu_channels}

{* ----------------------------- section *}
{if $chdata.type ne 'o' and  $chdata.type ne '-'}

{if $opensec > 0}
	{if $chdata.type eq 's' or $chdata.type eq 'r'}
		{assign var=sectionType value=0}
	{else}
		{assign var=sectionType value=$chdata.type}
	{/if}
	{if $opensec > $sectionType}
		{assign var=nb_opensec value=$opensec-$sectionType}
		{repeat count=$nb_opensec}</ul></li>{/repeat}
		{assign var=opensec value=$sectionType}
	{/if}
{/if}

<li class="option{$chdata.optionId} menuSection menuSection{$opensec} menuLevel{$opensec}{if $chdata.selected} selected{/if}{if $chdata.selectedAscendant} selectedAscendant{/if}">
{if $icon}{icon _id='folder' align="left"}{/if}
{if $link_on_section ne 'n'}<a href="{if $prefs.feature_sefurl eq 'y' and $chdata.sefurl}{$chdata.sefurl}{else}{$chdata.url}{/if}">{/if}
{if $translate eq 'n'}{$chdata.name|escape}{else}{tr}{$chdata.name}{/tr}{/if}
{if $link_on_section ne 'n'}</a>{/if}

{assign var=opensec value=$opensec+1}
<ul>

{* ----------------------------- option *}
{elseif $chdata.type eq 'o'}
<li class="option{$chdata.optionId} menuOption menuLevel{$opensec}{if $chdata.selected} selected{/if}{if $chdata.selectedAscendant} selectedAscendant{/if}"><a href="{if $prefs.feature_sefurl eq 'y' and $chdata.sefurl}{$chdata.sefurl}{else}{$chdata.url}{/if}">{if $translate eq 'n'}{$chdata.name|escape}{else}{tr}{$chdata.name}{/tr}{/if}</a></li>
{if $sep eq 'line'}{assign var=sep value=''}{/if}

{* ----------------------------- separator *}
{elseif $chdata.type eq '-'}
{if $opensec > 0}</ul></li>{assign var=opensec value=$opensec-1}{/if}
{assign var=sep value="line"}
{/if}

{/foreach}

{if $opensec > 0}
	{repeat count=$opensec}</ul></li>{/repeat}
	{assign var=opensec value=0}
{/if}

</ul>
{/if}
