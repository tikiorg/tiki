{assign var=opensec value='n'}
{if $menu_info.type eq 'e' or $menu_info.type eq 'd'}
{section name=ix loop=$channels}
{if $channels[ix].type eq 's'}
  {if $opensec eq 'y'}</div>{/if}
  <div class="separator"><a class='separator' href="javascript:hide('userm{$channels[ix].name}');">[-]</a> 
  <a class='separator' href="{$channels[ix].url}">{tr}{$channels[ix].name}{/tr}</a>
  <a class='separator' href="javascript:show('userm{$channels[ix].name}');">[+]</a>
  </div>
  {assign var=opensec value='y'}
  <div {if $menu_info.type eq 'd'}style="display:none;"{/if} id='userm{$channels[ix].name}'>
{else}
  <div class="button">&nbsp;<a href="{$channels[ix].url}" class="linkmenu">{tr}{$channels[ix].name}{/tr}</a></div>
{/if}
{/section}
{if $opensec eq 'y'}</div>{/if}
{else}
{section name=ix loop=$channels}
{if $channels[ix].type eq 's'}
  <div class="separator"><a class='separator' href="{$channels[ix].url}">{tr}{$channels[ix].name}{/tr}</a></div>
{else}
  <div class="button">&nbsp;<a href="{$channels[ix].url}" class="linkmenu">{tr}{$channels[ix].name}{/tr}</a></div>
{/if}
{/section}
{/if}
