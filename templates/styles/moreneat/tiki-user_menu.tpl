{assign var=opensec value='n'}
{assign var=sep value=''}

{if $menu_info.type eq 'e' or $menu_info.type eq 'd'}

{foreach key=pos item=chdata from=$channels}
{assign var=cname value=$menu_info.menuId|cat:'__'|cat:$chdata.position}
{if $chdata.type eq 's'}
{if $opensec eq 'y'}</div>{/if}
<div class="separator{$sep}">
{if $sep eq 'line'}{assign var=sep value=''}{/if}

{if $chdata.url}
{if $prefs.feature_menusfolderstyle eq 'y'}
<a class='separator' href="javascript:icntoggle('menu{$cname}');"><img src="img/icons/fo.gif" border="0" name="menu{$cname}icn" alt=''/></a>&nbsp;
{else}<a class='separator' href="javascript:toggle('menu{$cname}');" title="{tr}Click{/tr}">::&nbsp;</a>{/if} 
<a href="{$chdata.url|escape}" class="separator">{tr}{$chdata.name}{/tr}</a>
{else}
{if $prefs.feature_menusfolderstyle eq 'y'}
<a class='separator' href="javascript:icntoggle('menu{$cname}');"><img src="img/icons/fo.gif" border="0" name="menu{$cname}icn" alt=''/>&nbsp;
{else}<a class='separator' href="javascript:toggle('menu{$cname}');">:: {/if}{tr}{$chdata.name}{/tr}</a> 
{/if}
</div>
{assign var=opensec value='y'}
<div {if $menu_info.type eq 'd' and $smarty.cookies.$cname ne '' and $smarty.cookies.$cname ne 'o'}style="display:none;"{else}style="display:block;"{/if} id='menu{$cname}'>
{elseif $chdata.type eq 'o'}
<div class="option{$sep}">&nbsp;<a href="{$chdata.url|escape}" class="linkmenu">{tr}{$chdata.name}{/tr}</a></div>
{if $sep eq 'line'}{assign var=sep value=''}{/if}
{else}
{if $chdata.type eq '-'}{if $opensec eq 'y'}</div>{/if}{assign var=opensec value='n'}{/if}
{assign var=sep value="line"}
{/if}
{/foreach}
{if $opensec eq 'y'}</div>{/if}

{else}
{foreach key=pos item=chdata from=$channels}
{if $chdata.type eq 's'}
<div class="separator{$sep}"><a class='separator' href="{$chdata.url|escape}">{tr}{$chdata.name}{/tr}</a></div>
{if $sep eq 'line'}{assign var=sep value=''}{/if}
{elseif $chdata.type eq 'o'}
<div class="option{$sep}">&nbsp;<a href="{$chdata.url|escape}" class="linkmenu">{tr}{$chdata.name}{/tr}</a></div>
{if $sep eq 'line'}{assign var=sep value=''}{/if}
{else}
{assign var=sep value='line'}
{/if}
{/foreach}
{/if}

{if $sep eq 'line'}
<div class="separator{$sep}">&nbsp;</div>
{/if}

{if $menu_info.type eq 'e' or $menu_info.type eq 'd'}
<script type='text/javascript'>
{foreach key=pos item=chdata from=$channels}
{if $chdata.type eq 's'}
  {if $prefs.feature_menusfolderstyle eq 'y'}
    setfolderstate('menu{$menu_info.menuId|cat:'__'|cat:$chdata.position}', '{$menu_info.type}');
  {else}
    setsectionstate('menu{$menu_info.menuId|cat:'__'|cat:$chdata.position}');
  {/if}
{/if}
{/foreach}
</script>
{/if}

