{assign var=opensec value='n'}

{if $menu_info.type eq 'e' or $menu_info.type eq 'd'}

{foreach key=pos item=chdata from=$channels}
{assign var=cname value=$menu_info.menuId|cat:'__'|cat:$chdata.position}
{if $chdata.groupname eq 'Adhérents'}
{assign var=sty value="adh"}
{elseif $chdata.groupname eq 'Admins'}
{assign var=sty value="adm"}
{else}
{assign var=sty value=""}
{/if}
{if $chdata.type eq 's'}
{if $opensec eq 'y'}</div>{/if}
<div class="separator">
<a class='separator' href="#" onclick="javascript:icntoggle('{$cname}');"><img src="styles/gmsih/fleche.gif" width="10" height="9" border="0" hspace="2" name="{$cname}icn" alt=''/></a>
<a class='separator{$sty}' href="{$chdata.url}">{tr}{$chdata.name}{/tr}</a>
</div>
{assign var=opensec value='y'}
<div {if $menu_info.type eq 'd' and $smarty.cookies.$cname ne 'o'}style="display:none;"{else}style="display:block;"{/if} id='{$cname}'>
{else}
<div>&nbsp;<a href="{$chdata.url}" class="linkmenu{$sty}">{tr}{$chdata.name}{/tr}</a></div>
{/if}
{/foreach}
{if $opensec eq 'y'}</div>{/if}

{else}
{foreach key=pos item=chdata from=$channels}
{if $chdata.groupname eq 'Adhérents'}
{assign var=sty value="adh"}
{elseif $chdata.groupname eq 'Admins'}
{assign var=sty value="adm"}
{else}
{assign var=sty value=""}
{/if}
{if $chdata.type eq 's'}
<div class="separator"><a class='separator{$sty}' href="{$chdata.url}">{tr}{$chdata.name}{/tr}</a></div>
{else}
<div>&nbsp;<a href="{$chdata.url}" class="linkmenu{$sty}">{tr}{$chdata.name}{/tr}</a></div>
{/if}
{/foreach}
{/if}

{if $menu_info.type eq 'e' or $menu_info.type eq 'd'}
<script language='Javascript' type='text/javascript'>
{foreach key=pos item=chdata from=$channels}
{if $chdata.type eq 's'}{if $feature_menusfolderstyle eq 'y'}
setfolderstate('{$menu_info.menuId|cat:'__'|cat:$chdata.position}');
{/if}{/if}
{/foreach}
</script>
{/if}
