<a class="pagetitle" href="tiki-admin_hotwords.php">{tr}Admin Hotwords{/tr}</a>
<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=Hotwords" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin hotwords{/tr}"><img src='img/icons/help.gif' alt='help' /></a>{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_hotwords.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin hotwords tpl{/tr}">
<img src='img/icons/info.gif' alt='edit tpl' /></a>{/if}

<!-- begin -->

<h2>{tr}Add Hotword{/tr}</h2>

<form method="post" action="tiki-admin_hotwords.php">
<table>
<tr><td>{tr}Word{/tr}</td><td><input type="text" name="word"></td></tr>
<tr><td>{tr}URL{/tr}</td><td><input type="text" name="url" /></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="add" value="{tr}Add{/tr}" /></td></tr>
</table>
</form>
<div  align="center">
<table class="findtable">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="tiki-admin_hotwords.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
</div>
<table>
<tr>
<th><a href="tiki-admin_hotwords.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'word_desc'}word_asc{else}word_desc{/if}">{tr}Word{/tr}</a></th>
<th><a href="tiki-admin_hotwords.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}URL{/tr}</a></th>
<th>{tr}action{/tr}</th>
</tr>
{section name=user loop=$words}
{if $smarty.section.user.index % 2}
<tr class="odd">
<td>{$words[user].word}</td>
<td>{$words[user].url}</td>
<td>
<a href="tiki-admin_hotwords.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$words[user].word}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this hotword?{/tr}')" 
title="Click here to delete this hotword"><img alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>
</td>
</tr>
{else}
<tr class="even">
<td>{$words[user].word}</td>
<td>{$words[user].url}</td>
<td>
<a href="tiki-admin_hotwords.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$words[user].word}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this hotword?{/tr}')" 
title="Click here to delete this hotword"><img alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>
</td>
</tr>
{/if}
{sectionelse}
<tr class="odd"><td colspan="2">{tr}No records found{/tr}</td></tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_hotwords.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_hotwords.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_hotwords.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
