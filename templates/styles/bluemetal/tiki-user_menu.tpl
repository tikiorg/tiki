{assign var=opensec value='n'}

{if $menu_info.type eq 'e' or $menu_info.type eq 'd'}

{foreach key=pos item=chdata from=$channels}
{assign var=cname value=$menu_info.menuId|cat:'__'|cat:$chdata.position}
{if $chdata.type eq 's'}
{if $opensec eq 'y'}</div>{/if}
<div class="separator">

{if $feature_menusfolderstyle eq 'y'}
<a class='separator' href="javascript:icntoggle('{$cname}');"><img src="img/icons/fo.gif" border="0" name="{$cname}icn" alt=''/></a>&nbsp;
{else}<a class='separator' href="javascript:toggle('{$cname}');" title="{tr}Click{/tr}">::&nbsp;</a>{/if} 
<a href="{$chdata.url}" class="separator">{tr}{$chdata.name}{/tr}</a>
</div>
{assign var=opensec value='y'}
<div {if $menu_info.type eq 'd' and $smarty.cookies.$cname ne 'o'}style="display:none;"{else}style="display:block;"{/if} id='{$cname}'>
{else}
<div>&nbsp;<a href="{$chdata.url}" class="linkmenu">{tr}{$chdata.name}{/tr}</a></div>
{/if}
{/foreach}
{if $opensec eq 'y'}</div>{/if}

{else}
{foreach key=pos item=chdata from=$channels}
{if $chdata.type eq 's'}
<div class="separator"><a class='separator' href="{$chdata.url}">{tr}{$chdata.name}{/tr}</a></div>
{else}
<div>&nbsp;<a href="{$chdata.url}" class="linkmenu">{tr}{$chdata.name}{/tr}</a></div>
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
