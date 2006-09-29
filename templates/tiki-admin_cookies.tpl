<h1><a class="pagetitle" href="tiki-admin_cookies.php">{tr}Admin cookies{/tr}</a>
  
{if $feature_help eq 'y'}
<a href="{$helpurl}FortuneCookieDoc" target="tikihelp" class="tikihelp" title="{tr}admin FortuneCookie{/tr}"><img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>
{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_cookies.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}admin FortuneCookie tpl{/tr}"><img src="pics/icons/shape_square_edit.png" border="0" width="16" height="16" alt='{tr}Edit template{/tr}' /></a>
{/if}</h1>

<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}To use cookie in a text area (Wiki page, etc), a <a class="rbox-link" href="tiki-admin_modules.php">module</a> or a template, use {literal}{cookie}{/literal}.{/tr}</div>
</div>
<br />

<h2>{tr}Create/edit cookies{/tr}</h2>
<form action="tiki-admin_cookies.php" method="post">
<input type="hidden" name="cookieId" value="{$cookieId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Cookie{/tr}:</td><td class="formcolor"><input type="text" maxlength="255" size="40" name="cookie" value="{$cookie|escape}" /></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>

<h2>{tr}Upload Cookies from textfile{/tr}</h2>
<form enctype="multipart/form-data" action="tiki-admin_cookies.php" method="post">
<table class="normal">
<tr><td class="formcolor">{tr}Upload from disk:{/tr}</td><td class="formcolor">
<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
<input name="userfile1" type="file" /></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="upload" value="{tr}upload{/tr}" /></td></tr>
</table>
</form>
<br />

<h2>{tr}Cookies{/tr}</h2>
<a href="tiki-admin_cookies.php?removeall=1" class="linkbut">{tr}Remove all cookies{/tr}</a><br /><br />
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_cookies.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_cookies.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'cookieId_desc'}cookieId_asc{else}cookieId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_cookies.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'cookie_desc'}cookie_asc{else}cookie_desc{/if}">{tr}cookie{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print=false advance=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].cookieId}</td>
<td class="{cycle advance=false}">{$channels[user].cookie}</td>
<td class="{cycle advance=true}">
   &nbsp;&nbsp;
   <a title="{tr}edit{/tr}" class="link" href="tiki-admin_cookies.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;cookieId={$channels[user].cookieId}">
   <img src="pics/icons/page_edit.png" border="0" width="16" height="16"  alt='{tr}edit{/tr}' /></a> &nbsp;
   <a title="{tr}delete{/tr}" class="link" href="tiki-admin_cookies.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].cookieId}" >
   <img src="pics/icons/cross.png" border="0" height="16" width="16" alt='{tr}delete{/tr}' /></a>
</td>
</tr>
{sectionelse}
<tr><td colspan="3" class="odd">{tr}No records found{/tr}</td></tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_cookies.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_cookies.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_cookies.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
