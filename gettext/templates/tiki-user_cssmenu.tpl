{if count($menu_channels) > 0}
	{assign var=opensec value='0'}
	{assign var=sep value=''}
	{strip}

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
			
			<li class="option{$chdata.optionId} menuSection menuSection{$opensec} menuLevel{$opensec}{if isset($chdata.selected) and $chdata.selected} selected{/if}{if isset($chdata.selectedAscendant) and $chdata.selectedAscendant} selectedAscendant{/if}">
			<a href="{if $prefs.feature_sefurl eq 'y' and $chdata.sefurl}{$chdata.sefurl|escape}{else}{if $prefs.menus_item_names_raw eq 'n'}{$chdata.url|escape}{else}{$chdata.url}{/if}{/if}">
				{if $menu_type eq 'vert' and $prefs.menus_items_icons eq 'y' and $menu_info.use_items_icons eq 'y' and $opensec eq 0}
					{icon _id=$chdata.icon alt='' _defaultdir=$prefs.menus_items_icons_path}
				{elseif isset($icon) and $icon}
					{icon _id='folder' align="left"}
				{/if}
				{capture name=chdataName}
					{if $translate eq 'n'}
						{if $prefs.menus_item_names_raw eq 'n'}{$chdata.name|escape}{else}{$chdata.name}{/if}
					{else}
						{tr}{if $prefs.menus_item_names_raw eq 'n'}{$chdata.name|escape}{else}{$chdata.name}{/if}{/tr}
					{/if}
				{/capture}
				<span class="menuText">{$smarty.capture.chdataName}</span>
			{if $link_on_section ne 'n'}</a>{/if}
			{assign var=opensec value=$opensec+1}
			<ul>
			
		{* ----------------------------- option *}
		{elseif $chdata.type eq 'o'}
			<li class="option{$chdata.optionId} menuOption menuLevel{$opensec}{if isset($chdata.selected) and $chdata.selected} selected{/if}{if isset($chdata.selectedAscendant) and $chdata.selectedAscendant} selectedAscendant{/if}">
				<a href="{if $prefs.feature_sefurl eq 'y' and $chdata.sefurl}{$chdata.sefurl|escape}{else}{if $prefs.menus_item_names_raw eq 'n'}{$chdata.url|escape}{else}{$chdata.url}{/if}{/if}">
					{if $menu_type eq 'vert' and $prefs.menus_items_icons eq 'y' and $menu_info.use_items_icons eq 'y' and $opensec eq 0}
						{icon _id=$chdata.icon alt='' _defaultdir=$prefs.menus_items_icons_path}
					{/if}
					{capture name=chdataName}
						{if $translate eq 'n'}
							{if $prefs.menus_item_names_raw eq 'n'}{$chdata.name|escape}{else}{$chdata.name}{/if}
						{else}
							{tr}{if $prefs.menus_item_names_raw eq 'n'}{$chdata.name|escape}{else}{$chdata.name}{/if}{/tr}
						{/if}
					{/capture}
					<span class="menuText">{$smarty.capture.chdataName}</span>
				</a>
			</li>
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
	{/strip}
{/if}
