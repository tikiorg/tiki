{assign var=opensec value='n'}
<div id="treemenu"><ul>
 {if $menu_info.type eq 'e' or $menu_info.type eq 'd'}
  {foreach key=pos item=chdata from=$channels}
  {assign var=cname value=$menu_info.menuId|cat:'__'|cat:$chdata.position}
  {if $chdata.type eq 's' or $chdata.type eq 'r'}
   {if $opensec eq 'y'}</ul></li>{/if}
   {if $chdata.name or $chdata.url}<li>
    {if $prefs.feature_menusfolderstyle eq 'y'}
     <a href="javascript:icntoggle('menu{$cname}');"><img src="img/icons/{if $menu_info.type ne 'd'}o{/if}fo.gif" border="0" name="menu{$cname}icn" alt=''/></a>
     {else}<a href="javascript:toggle('menu{$cname}');">[-]</a>
    {/if} 
    <a href="{$chdata.url|escape}" >{tr}{$chdata.name}{/tr}</a>
    {if $prefs.feature_menusfolderstyle ne 'y'}<a href="javascript:toggle('menu{$cname}');">[+]</a>{/if} 
   {else}
    {if $prefs.feature_menusfolderstyle eq 'y'}
     <a href="javascript:icntoggle('menu{$cname}');"><img src="img/icons/{if $menu_info.type ne 'd'}o{/if}fo.gif" border="0" name="menu{$cname}icn" alt=''/></li>
     {else}<a href="javascript:toggle('menu{$cname}');">[-]{/if}{tr}{$chdata.name}{/tr}{if $prefs.feature_menusfolderstyle ne 'y'}[+]{/if}</a></li> 
    {/if}
    {assign var=opensec value='y'}
    <ul {if $menu_info.type eq 'd'}style="display:none;"{else}style="display:block;"{/if} id='menu{$cname}'>
   {elseif $chdata.type eq 'o'}
    <li><a href="{$chdata.url|escape}">{tr}{$chdata.name}{/tr}</a></li>
   {else}
    {if $chdata.type eq '-'}{if $opensec eq 'y'}</ul>{/if}{assign var=opensec value='n'}{/if}
  {/if}
{/foreach}
{if $opensec eq 'y'}</ul>{/if}

{else}

{foreach key=pos item=chdata from=$channels}
{if $chdata.type eq 's' or $chdata.type eq 'r'}
{if $opensec eq 'y'}</ul></li>{assign var=opensec value='n'}{/if}
<li><a href="{$chdata.url|escape}">{tr}{$chdata.name}{/tr}</a><ul>
{assign var=opensec value='y'}
{elseif $chdata.type eq 'o'}
<li><a href="{$chdata.url|escape}">{tr}{$chdata.name}{/tr}</a></li>
{else}
{assign var=opensec value='n'}</ul>
{/if}
{/foreach}
{if $opensec eq 'y'}</ul>{assign var=opensec value='n'}{/if}
{/if}

{if $menu_info.type eq 'e' or $menu_info.type eq 'd'}
<script type='text/javascript'>
{foreach key=pos item=chdata from=$channels}
{if $chdata.type eq 's' or $chdata.type eq 'r'}
  {if $prefs.feature_menusfolderstyle eq 'y'}
    setfolderstate('menu{$menu_info.menuId|cat:'__'|cat:$chdata.position}', '{$menu_info.type}');
  {else}
    setsectionstate('menu{$menu_info.menuId|cat:'__'|cat:$chdata.position}');
  {/if}
{/if}
{/foreach}
</script>
{/if}

</li></ul>

</div>
