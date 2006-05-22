<h1><a class="pagetitle" href="tiki-admin_html_pages.php">{tr}Admin HTML pages{/tr}</a>
  
      {if $feature_help eq 'y'}
<a href="{$helpurl}Html+Pages" target="tikihelp" class="tikihelp" title="{tr}admin Html Pages{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>{/if}

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_html_pages.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}admin Html Pages template{/tr}">
<img src="img/icons/info.gif" border="0" width="16" height="16" alt='{tr}edit{/tr}' /></a>{/if}
</h1>

{if $pageName ne ''}
<div class="navbar"><a href="tiki-admin_html_pages.php" class="linkbut">{tr}Create new HTML page{/tr}</a></div>
{/if}
{if $preview eq 'y'}
<h2>{tr}Preview{/tr}</h2>
<div class="wikitext">{$parsed}</div>
{/if}
{if $pageName eq ''}
<h2>{tr}Create new HTML page{/tr}</h2>
{else}
<h2>{tr}Edit this HTML page:{/tr} {$pageName}</h2>
{/if}
<form action="tiki-admin_html_pages.php" method="post" id='editpageform'>
<input type="hidden" name="pageName" value="{$pageName|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Page name{/tr}:</td><td class="formcolor"><input type="text" maxlength="255" size="40" name="pageName" value="{$info.pageName|escape}" /></td></tr>
{if $tiki_p_use_content_templates eq 'y'}
<tr><td class="formcolor">{tr}Apply template{/tr}</td><td class="formcolor">
<select name="templateId" onchange="javascript:document.getElementById('editpageform').submit();">
<option value="0">{tr}none{/tr}</option>
{section name=ix loop=$templates}
<option value="{$templates[ix].templateId|escape}">{tr}{$templates[ix].name}{/tr}</option>
{/section}
</select>
</td></tr>
{/if}
<tr class="formcolor"><td>{tr}Type{/tr}:</td><td>
<select name="type">
<option value='d' {if $info.type eq 'd'}selected="selected"{/if}>{tr}Dynamic{/tr}</option>
<option value='s' {if $info.type eq 's'}selected="selected"{/if}>{tr}Static{/tr}</option>
</select>
</td></tr>
<tr class="formcolor"><td>{tr}Refresh rate (if dynamic) [secs]{/tr}:</td><td><input type="text" size="40" name="refresh" value="{$info.refresh|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Content{/tr}: ({tr}Use {literal}{/literal}ed id=name} or {literal}{/literal}ted id=name} to insert dynamic zones{/tr})</td><td><textarea name="content" rows="25" cols="60">{$info.content|escape}</textarea></td></tr>
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="preview" value="{tr}Preview{/tr}" /></td></tr>
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>


<h2>{tr}HTML pages{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="tiki-admin_html_pages.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_html_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_html_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}type{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_html_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}last modif{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$channels[user].pageName}</td>
<td class="odd">{$channels[user].type} {if $channels[user].type eq 'd'}({$channels[user].refresh} secs){/if}</td>
<td class="odd">{$channels[user].created|tiki_short_datetime}</td>
<td class="odd">
   &nbsp;&nbsp;<a class="link" href="tiki-admin_html_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].pageName|escape:"url"}" 
title="{tr}delete{/tr}"><img src="img/icons2/delete.gif" border="0" height="16" width="16" alt='{tr}delete{/tr}' /></a>&nbsp;&nbsp;
   <a class="link" href="tiki-admin_html_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;pageName={$channels[user].pageName|escape:"url"}" title="{tr}edit{/tr}"><img src="img/icons/edit.gif" border="0" width="20" height="16"  alt='{tr}edit{/tr}' /></a>
   <a class="link" href="tiki-page.php?pageName={$channels[user].pageName|escape:"url"}">{tr}view{/tr}</a>
   <a class="link" href="tiki-admin_html_page_content.php?pageName={$channels[user].pageName|escape:"url"}">{tr}content{/tr}</a>
</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].pageName}</td>
<td class="even">{$channels[user].type} {if $channels[user].type eq 'd'}({$channels[user].refresh} secs){/if}</td>
<td class="even">{$channels[user].created|tiki_short_datetime}</td>
<td class="even">
   &nbsp;&nbsp;<a class="link" href="tiki-admin_html_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].pageName|escape:"url"}" 
title="{tr}delete{/tr}"><img src="img/icons2/delete.gif" border="0" height="16" width="16" alt='{tr}delete{/tr}' /></a>&nbsp;&nbsp;
   <a class="link" href="tiki-admin_html_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;pageName={$channels[user].pageName|escape:"url"}" title="{tr}edit{/tr}"><img src="img/icons/edit.gif" border="0" width="20" height="16"  alt='{tr}edit{/tr}' /></a>
   <a class="link" href="tiki-page.php?pageName={$channels[user].pageName|escape:"url"}">{tr}view{/tr}</a>
   <a class="link" href="tiki-admin_html_page_content.php?pageName={$channels[user].pageName|escape:"url"}">{tr}content{/tr}</a>
</td>
</tr>
{/if}
{sectionelse}
<tr><td colspan="4" class="odd">{tr}No records found{/tr}</td></tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_html_pages.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_html_pages.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_html_pages.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

