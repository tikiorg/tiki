<a class="pagetitle" href="tiki-admin_cookies.php">{tr}Admin Cookies{/tr}</a>
<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=FortuneCookieDoc" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin FortuneCookie{/tr}"><img border="0" alt="{tr}Help{/tr}" src="img/icons/help.gif" /></a>
{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_cookies.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin FortuneCookie tpl{/tr}"><img border="0"  alt="{tr}Edit template{/tr}" src="img/icons/info.gif" /></a>
{/if}

<!-- begin -->

<br/>

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
<input type="hidden" name="MAX_FILE_SIZE" value="1000000000">
<input name="userfile1" type="file"></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="upload" value="{tr}Upload{/tr}" /></td></tr>
</table>
</form>
<br/>

<h2>{tr}Cookies{/tr}</h2>
<a href="tiki-admin_cookies.php?removeall=1" class="linkbut" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete all cookies?{/tr}')" >[{tr}Delete all cookies{/tr}]</a><br/><br/>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Search{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_cookies.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Go{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_cookies.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'cookieId_desc'}cookieId_asc{else}cookieId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_cookies.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'cookie_desc'}cookie_asc{else}cookie_desc{/if}">{tr}Cookie{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$channels[user].cookieId}</td>
<td class="odd">{$channels[user].cookie}</td>
<td class="odd">
   &nbsp;&nbsp;<a class="link" href="tiki-admin_cookies.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].cookieId}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this cookie?{/tr}')" 
title="{tr}Click here to delete this cookie{/tr}"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>&nbsp;&nbsp;
   <a class="link" href="tiki-admin_cookies.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;cookieId={$channels[user].cookieId}"><img border="0" alt="{tr}Edit{/tr}" src="img/icons/edit.gif" /></a>
</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].cookieId}</td>
<td class="even">{$channels[user].cookie}</td>
<td class="even">
   &nbsp;&nbsp;<a class="link" href="tiki-admin_cookies.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].cookieId}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this cookie?{/tr}')" 
title="{tr}Click here to delete this cookie{/tr}"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>&nbsp;&nbsp;
   <a class="link" href="tiki-admin_cookies.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;cookieId={$channels[user].cookieId}"><img border="0" alt="{tr}Edit{/tr}" src="img/icons/edit.gif" /></a>
</td>
</tr>
{/if}
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
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_cookies.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>