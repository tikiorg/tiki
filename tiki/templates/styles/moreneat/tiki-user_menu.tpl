{assign var=opensec value='n'}
{if $menu_info.type eq 'e' or $menu_info.type eq 'd'}
{section name=ix loop=$channels}
{assign var=cname value=$channels[ix].menulabel}
{if $channels[ix].type eq 's'}
{if $opensec eq 'y'}</div>{/if}
<div class="separator">
<a class='separator' href="javascript:toggle('{$channels[ix].menulabel}');">::</a>
<a href="{$channels[ix].url|escape:"html"}" class="separator">{tr}{$channels[ix].name}{/tr}</a>
</div>
{assign var=opensec value='y'}
<div {if $menu_info.type eq 'd' and $smarty.cookies.$cname ne 'o'}style="display:none;"{else}style="display:block;"{/if} id='{$channels[ix].menulabel}'>
{else}
<div>&nbsp;<a href="{$channels[ix].url|escape:"html"}" class="linkmenu">{tr}{$channels[ix].name}{/tr}</a></div>
{/if}
{/section}
{if $opensec eq 'y'}</div>{/if}
{else}
{section name=ix loop=$channels}
{if $channels[ix].type eq 's'}
<div class="separator"><a class='separator' href="{$channels[ix].url|escape:"html"}">{tr}{$channels[ix].name}{/tr}</a></div>
{else}
<div>&nbsp;<a href="{$channels[ix].url|escape:"html"}" class="linkmenu">{tr}{$channels[ix].name}{/tr}</a></div>
{/if}
{/section}
{/if}
