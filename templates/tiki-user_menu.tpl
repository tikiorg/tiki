{* $Id$ *}
{assign var=opensec value='0'}
{assign var=sep value=''}

{foreach key=pos item=chdata from=$menu_channels}
{assign var=cname value=$menu_info.menuId|cat:'__'|cat:$chdata.position}
{* ----------------------------- section *}
{if $chdata.type ne 'o' and  $chdata.type ne '-'}

{if $opensec > 0}
{assign var=sectionType value=$chdata.type}
{php}
global $smarty;
$opensec = $smarty->get_template_vars('opensec');
$sectionType= $smarty->get_template_vars('sectionType');
if ($sectionType == 's' or $sectionType == 'r') {
	$sectionType = 0;
}
while ($opensec > $sectionType) {
	--$opensec;
	echo '</div>';
}
$smarty->assign('opensec', $opensec);
{/php}
{/if}

<div class="separator{$sep}">
{if $sep eq 'line'}{assign var=sep value=''}{/if}
{if $menu_info.type eq 'e' or $menu_info.type eq 'd'}
	{if $prefs.feature_menusfolderstyle eq 'y'}
	{assign var="name" value=icnmenu$cname}
	<a class='separator' href="javascript:icntoggle('menu{$cname}');" title="{tr}Toggle options{/tr}">{if $menu_info.type ne 'd'}AAAA{icon _id="folder_open" alt='Toggle' name="$name"}{else}{icon _id="folder" alt='Toggle' name="$name"}{/if}</a>
	{else}
	<a class='separator' href="javascript:toggle('menu{$cname}');">[-]</a>
	{/if}
{/if} 
{if $chdata.url and $link_on_section eq 'y'}
<a href="{if $prefs.feature_sefurl eq 'y' and $chdata.sefurl}{$chdata.sefurl}{else}{$chdata.url}{/if}" class="separator">
{else}
<a href="javascript:icntoggle('menu{$cname}');" class="separator">
{/if}
{tr}{$chdata.name}{/tr}
</a>
{if ($menu_info.type eq 'e' or $menu_info.type eq 'd') and $prefs.feature_menusfolderstyle ne 'y'}<a class='separator' href="javascript:toggle('menu{$cname}');">[+]</a>{/if} 
</div> {* separator *}

{assign var=opensec value=$opensec+1}
{if $menu_info.type eq 'e' or $menu_info.type eq 'd'}
<div class="menuSection" {if $menu_info.type eq 'd' and $smarty.cookies.menu ne ''}style="display:none;"{else}style="display:block;"{/if} id='menu{$cname}'>
{else}
<div class="menuSection">
{/if}

{* ----------------------------- option *}
{elseif $chdata.type eq 'o'}
<div class="option{$sep}"><a href="{if $prefs.feature_sefurl eq 'y' and $chdata.sefurl}{$chdata.sefurl}{else}{$chdata.url}{/if}" class="linkmenu">{tr}{$chdata.name}{/tr}</a></div>
{if $sep eq 'line'}{assign var=sep value=''}{/if}

{* ----------------------------- separator *}
{elseif $chdata.type eq '-'}
{if $opensec > 0}</div>{assign var=opensec value=$opensec-1}{/if}
{assign var=sep value="line"}
{/if}
{/foreach}

{if $opensec > 0}
{php}
global $smarty;
$opensec = $smarty->get_template_vars('opensec');
while ($opensec) {
	--$opensec;
	echo '</div>';
}
{/php}
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

