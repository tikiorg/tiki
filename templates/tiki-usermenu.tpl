{*Smarty template*}
<h1><a class="pagetitle" href="tiki-usermenu.php">{tr}User Menu{/tr}</a></h1>
{include file=tiki-mytiki_bar.tpl}
<br />
{if $prefs.feature_user_bookmarks eq 'y' and $tiki_p_create_bookmarks eq 'y'}
<a title="({tr}May need to refresh twice to see changes{/tr})" class="link" href="tiki-usermenu.php?addbk=1">{tr}Add top level bookmarks to menu{/tr}</a> 
{/if}
<h2>{tr}Menus{/tr}</h2>

{include file='find.tpl' _sort_mode='y'}

<form action="tiki-usermenu.php" method="post">
<table class="normal">
<tr>
<td  class="heading"><input type="submit" name="delete" value="x " title="{tr}Delete Selected{/tr}" /></td>
<td class="heading" ><a class="tableheading" href="tiki-usermenu.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'position_desc'}position_asc{else}position_desc{/if}">{tr}Pos{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="tiki-usermenu.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="tiki-usermenu.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}URL{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="tiki-usermenu.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'mode_desc'}mode_asc{else}mode_desc{/if}">{tr}Mode{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td style="text-align:center;" class="{cycle advance=false}">
<input type="checkbox" name="menu[{$channels[user].menuId}]" />
</td>
<td class="{cycle advance=false}">{$channels[user].position}</td>
<td class="{cycle advance=false}"><a class="link" href="tiki-usermenu.php?menuId={$channels[user].menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}">{$channels[user].name}</a></td>
<td class="{cycle advance=false}">{$channels[user].url|truncate:40:"...":true}</td>
<td style="text-align:center;" class="{cycle advance=false}">{$channels[user].mode}</td>
</tr>
{/section}
</table>
</form>
<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-usermenu.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-usermenu.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-usermenu.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>


<h2>{tr}Add or edit an item{/tr}</h2>
<form action="tiki-usermenu.php" method="post">
<input type="hidden" name="menuId" value="{$menuId|escape}" />
<table class="normal">
  <tr><td class="formcolor">{tr}Name{/tr}</td>
      <td class="formcolor"><input type="text" name="name" value="{$info.name|escape}" /></td>
  </tr>
  <tr><td class="formcolor">{tr}URL{/tr}</td>
      <td class="formcolor"><input type="text" name="url" value="{$info.url|escape}" /></td>  </tr>
  <tr><td class="formcolor">{tr}Position{/tr}</td>
      <td class="formcolor"><input type="text" name="position" value="{$info.position|escape}" /></td>
  </tr>
  <tr><td class="formcolor">{tr}Mode{/tr}</td>
      <td class="formcolor">
        <select name="mode">
          <option value="n" {if $info.mode eq 'n'}selected="selected"{/if}>{tr}New Window{/tr}</option>
          <option value="w" {if $info.mode eq 'w'}selected="selected"{/if}>{tr}replace window{/tr}</option>
        </select>
      </td>
  </tr>
  <tr>
    <td class="formcolor">&nbsp;</td>
    <td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
  </tr>
</table>
</form>

  
