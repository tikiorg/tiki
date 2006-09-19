<h1><a class="pagetitle" href="tiki-received_pages.php">{tr}Received pages{/tr}</a>
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/CommunicationsCenterDoc" target="tikihelp" class="tikihelp" title="{tr}Help on Communication Center{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>{/if}

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-received_pages.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}received pages tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}edit tpl{/tr}' /></a>{/if}</h1>

{if $receivedPageId > 0 or $view eq 'y'}
<h2>{tr}Preview{/tr}</h2>
<div class="wikitext">{$parsed}</div>
{/if}
{if $receivedPageId > 0}
<h2>{tr}Edit received page{/tr}</h2>
<form action="tiki-received_pages.php" method="post">
<input type="hidden" name="receivedPageId" value="{$receivedPageId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="pageName" value="{$pageName|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Data{/tr}:</td><td class="formcolor"><textarea name="data" rows="10" cols="60">{$data|escape}</textarea></td></tr>
<tr><td class="formcolor">{tr}Comment{/tr}:</td><td class="formcolor">
<input type="text" name="comment" value="{$comment|escape}" />
</td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="preview" value="{tr}Preview{/tr}" />&nbsp;<input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
{/if}

<h2>{tr}Received pages{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-received_pages.php">
     <input type="text" name="find" />
     <input type="submit" name="search" value="{tr}find{/tr}" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedPageId_desc'}receivedPageId_asc{else}receivedPageId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'pageName_desc'}pageName_asc{else}pageName_desc{/if}">{tr}name{/tr}</a></td>
<!--<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'comment_desc'}comment_asc{else}comment_desc{/if}">{tr}comment{/tr}</a></td>-->
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedDate_desc'}receivedDate_asc{else}receivedDate_desc{/if}">{tr}Date{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedFromSite_desc'}receivedFromSite_asc{else}receivedFromSite_desc{/if}">{tr}Site{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedFromUser_desc'}receivedFromUser_asc{else}receivedFromUser_desc{/if}">{tr}User{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="even,odd" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].receivedPageId}</td>
{if $channels[user].exists eq 'y'}
<td class="{cycle advance=false}"><span class="warn">{$channels[user].pageName}</span></td>
{else}
<td class="{cycle advance=false}">{$channels[user].pageName}</td>
{/if}
<!--<td class="{cycle advance=false}">{$channels[user].comment}</td>-->
<td class="{cycle advance=false}">{$channels[user].receivedDate|tiki_short_date}</td>
<td class="{cycle advance=false}">{$channels[user].receivedFromSite}</td>
<td class="{cycle advance=false}">{$channels[user].receivedFromUser}</td>
<td class="{cycle advance=false}">
   <a class="link" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;receivedPageId={$channels[user].receivedPageId}"><img src='img/icons/edit.gif' alt='{tr}edit{/tr}' border='0' title='{tr}edit{/tr}' /></a>
   <a class="link" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;view={$channels[user].receivedPageId}"><img src='img/icons2/icn_view.gif' border='0' alt='{tr}view{/tr}' title='{tr}view{/tr}' /></a>
   <a class="link" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;accept={$channels[user].receivedPageId}"><img src='img/icons2/post.gif' border='0' alt='{tr}accept{/tr}' title='{tr}accept{/tr}' /></a>
   <a class="link" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].receivedPageId}"><img src='img/icons2/delete.gif' alt='{tr}remove{/tr}' border='0' title='{tr}remove{/tr}' /></a>
</td>
</tr>
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-received_pages.php?offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-received_pages.php?offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-received_pages.php?offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
