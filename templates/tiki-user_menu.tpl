{* $Id$ *}
{assign var=opensec value='0'}
{assign var=sep value=''}

{foreach key=pos item=chdata from=$menu_channels}
{assign var=cname value=$menu_info.menuId|cat:'__'|cat:$chdata.position}
{* ----------------------------- section *}
{if $chdata.type ne 'o' and  $chdata.type ne '-'}

{if $opensec > 0}
{assign var=sectionType value=$chdata.type}
{if $sectionType eq 's' or $sectionType eq 'r'}{assign var=sectionType value=0}{/if}
{if $opensec > $sectionType}
{assign var=m value=$opensec-$sectionType}
{section loop=$menu_channels name=close max=$m}
	   </div>
{/section}
{assign var=opensec value=$sectionType}
{/if}
{/if}

<div class="separator{$sep}{if isset($chdata.selected) and $chdata.selected} selected{/if}{if isset($chdata.selectedAscendant) and $chdata.selectedAscendant} selectedAscendant{/if}">
{if $sep eq 'line'}{assign var=sep value=''}{/if}
{if $menu_info.type eq 'e' or $menu_info.type eq 'd'}
	{if $prefs.feature_menusfolderstyle eq 'y'}
	{assign var="icon_name" value=icnmenu$cname}
	<a class='separator' href="javascript:icntoggle('menu{$cname}');" title="{tr}Toggle options{/tr}">
		{if $menu_info.type ne 'd'}
			{if empty($menu_info.icon)}
				{icon _id="ofolder" alt='Toggle' name="$icon_name"}
			{else}
				<img src="{$menu_info.oicon}" alt='{tr}Toggle{/tr}' name="{$icon_name}" />
			{/if}
		{else}
			{if empty($menu_info.icon)}
				{icon _id="folder" alt='Toggle' name="$icon_name"}
			{else}
				<img src="{$menu_info.icon}" alt='{tr}Toggle{/tr}' name="{$icon_name}" />
			{/if}
		{/if}
	</a>
	{else}
	<a class='separator' href="javascript:toggle('menu{$cname}');">[-]</a>
	{/if}
{/if} 
{if $chdata.url and $link_on_section eq 'y'}
<a href="{if $prefs.feature_sefurl eq 'y' and $chdata.sefurl}{$chdata.sefurl}{else}{$chdata.url}{/if}" class="separator">
{else}
<a href="javascript:icntoggle('menu{$cname}');" class="separator">
{/if}
{if $translate eq 'n'}{$chdata.name|escape}{else}{tr}{$chdata.name}{/tr}{/if}
</a>
{if ($menu_info.type eq 'e' or $menu_info.type eq 'd') and $prefs.feature_menusfolderstyle ne 'y'}<a class='separator' href="javascript:toggle('menu{$cname}');">[+]</a>{/if} 
</div> {* separator *}

{assign var=opensec value=$opensec+1}
{if $menu_info.type eq 'e' or $menu_info.type eq 'd'}
<div class="menuSection" {if $menu_info.type eq 'd' and $smarty.cookies.menu ne ''  and $prefs.javascript_enabled ne 'n'}style="display:none;"{else}style="display:block;"{/if} id='menu{$cname}'>
{else}
<div class="menuSection">
{/if}

{* ----------------------------- option *}
{elseif $chdata.type eq 'o'}
<div class="option{$sep}{if isset($chdata.selected) and $chdata.selected} selected{/if}"><a href="{if $prefs.feature_sefurl eq 'y' and $chdata.sefurl}{$chdata.sefurl}{else}{$chdata.url}{/if}" class="linkmenu">{if $translate eq 'n'}{$chdata.name|escape}{else}{tr}{$chdata.name}{/tr}{/if}</a></div>
{if $sep eq 'line'}{assign var=sep value=''}{/if}

{* ----------------------------- separator *}
{elseif $chdata.type eq '-'}
{if $opensec > 0}</div>{assign var=opensec value=$opensec-1}{/if}
{assign var=sep value="line"}
{/if}
{/foreach}

{if $opensec > 0}
{section loop=$menu_channels name=close max=$opensec}
	</div>
{/section}
{assign var=opensec value=0}
{/if}

{* --------------------Dynamic menus *}
{if $menu_info.type eq 'e' or $menu_info.type eq 'd'}
<script type='text/javascript'>
{foreach key=pos item=chdata from=$menu_channels}
{if $chdata.type ne 'o' and $chdata.type ne '-'}
  {if $prefs.feature_menusfolderstyle eq 'y'}
    setfolderstate('menu{$menu_info.menuId|cat:'__'|cat:$chdata.position}', '{$menu_info.type}');
  {else}
    setsectionstate('menu{$menu_info.menuId|cat:'__'|cat:$chdata.position}', '{$menu_info.type}');
  {/if}
{/if}
{/foreach}
</script>
{/if}

