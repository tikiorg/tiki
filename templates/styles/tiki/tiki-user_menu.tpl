{assign var=opensec value='n'}
{if $menu_info.type eq 'e' or $menu_info.type eq 'd'}
{section name=ix loop=$channels}
{if $channels[ix].type eq 's'}
{if $opensec eq 'y'}</div>{/if}
<div class="separator">
<a class='separator' href="#" onclick="javascript:toggle('userm{$channels[ix].name|regex_replace:"/[^a-zA-Z0-9]/":""}');">.:</a>
<a href="{$channels[ix].url}" class="separator">{tr}{$channels[ix].name}{/tr}</a>
</div>
{assign var=opensec value='y'}
<div {if $menu_info.type eq 'd'}style="display:none;"{/if} id='userm{$channels[ix].name|regex_replace:"/[^a-zA-Z0-9]/":""}'>
{else}
<div class="separated"><a href="{$channels[ix].url}" class="linkmenu">{tr}{$channels[ix].name}{/tr}</a></div>
{/if}
{/section}
{if $opensec eq 'y'}</div>{/if}
{else}
{section name=ix loop=$channels}
{if $channels[ix].type eq 's'}
<div class="separator"><a class='separator' href="{$channels[ix].url}">{tr}{$channels[ix].name}{/tr}</a></div>
{else}
<div class="separated"><a href="{$channels[ix].url}" class="linkmenu">{tr}{$channels[ix].name}{/tr}</a></div>
{/if}
{/section}
{/if}
