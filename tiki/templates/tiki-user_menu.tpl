{assign var=opensec value='n'}
{if $menu_info.type eq 'e' or $menu_info.type eq 'd'}
{section name=ix loop=$channels}
{if $channels[ix].type eq 's'}
  {if $opensec eq 'y'}</div>{/if}
  <div class="separator">
  {if $feature_menusfolderstyle eq 'y'}
  <a class='separator' href="javascript:icntoggle('userm{$menu_info.menuId}{$channels[ix].name|regex_replace:"/[^a-zA-Z0-9]/":""}');"><img 
	src="img/icons/fo.gif" border="0" name="userm{$menu_info.menuId}{$channels[ix].name|regex_replace:"/[^a-zA-Z0-9]/":""}icn" alt=''/></a>&nbsp;
  {else}<a class='separator' href="javascript:toggle('userm{$menu_info.menuId}{$channels[ix].name|regex_replace:"/[^a-zA-Z0-9]/":""}');">[-]</a>{/if} 
  <a href="{$channels[ix].url}" class="separator">{tr}{$channels[ix].name}{/tr}</a>
  {if $feature_menusfolderstyle ne 'y'}<a class='separator' href="javascript:toggle('userm{$menu_info.menuId}{$channels[ix].name|regex_replace:"/[^a-zA-Z0-9]/":""}');">[+]</a>{/if} 
  </div>
  {assign var=opensec value='y'}
  <div {if $menu_info.type eq 'd'}style="display:none;"{/if} id='userm{$menu_info.menuId}{$channels[ix].name|regex_replace:"/[^a-zA-Z0-9]/":""}'>
{else}
  <div>&nbsp;<a href="{$channels[ix].url}" class="linkmenu">{tr}{$channels[ix].name}{/tr}</a></div>
{/if}
{/section}
{if $opensec eq 'y'}</div>{/if}
{else}
{section name=ix loop=$channels}
{if $channels[ix].type eq 's'}
  <div class="separator"><a class='separator' href="{$channels[ix].url}">{tr}{$channels[ix].name}{/tr}</a></div>
{else}
  <div>&nbsp;<a href="{$channels[ix].url}" class="linkmenu">{tr}{$channels[ix].name}{/tr}</a></div>
{/if}
{/section}
{/if}

{if $menu_info.type eq 'e' or $menu_info.type eq 'd'}
<script language='Javascript' type='text/javascript'>
{section name=ix loop=$channels}
{if $channels[ix].type eq 's'}
  {if $feature_menusfolderstyle eq 'y'}
	setfolderstate('userm{$channels[ix].name|regex_replace:"/[^a-zA-Z0-9]/":""}');
  {/if}
{/if}
{/section}
</script>
{/if}
