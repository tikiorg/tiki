<a class="pagetitle" href="tiki-admin_external_wikis.php">{tr}Admin External Wikis{/tr}</a>

<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=ExternalWiki" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin ExternalWiki{/tr}"><img border="0" alt="{tr}Help{/tr}" src="img/icons/help.gif" /></a>
{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_external_wikis.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}tiki admin external wikis tpl{/tr}">
<img border="0"  alt="{tr}Edit template{/tr}" src="img/icons/info.gif" /></a>
{/if}

<!-- begin -->
 
<br /><br />
<h2>{tr}Create/Edit External Wiki{/tr}</h2>
<form action="tiki-admin_external_wikis.php" method="post">
<input type="hidden" name="extwikiId" value="{$extwikiId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" maxlength="255" size="10" name="name" value="{$info.name|escape}" /></td></tr>
<tr><td class="formcolor">{tr}URL (use $page to be replaced by the page name in the URL example: http://www.example.com/tiki-index.php?page=$page){/tr}:</td><td class="formcolor"><input type="text" maxlength="255" size="40" name="extwiki" value="{$info.extwiki|escape}" /></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>

<h2>{tr}External Wiki{/tr}</h2>
<!-- second table -->

<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_external_wikis.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_external_wikis.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'extwiki_desc'}extwiki_asc{else}extwiki_desc{/if}">{tr}External Wiki{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].name}</td>
<td class="{cycle advance=false}">{$channels[user].extwiki}</td>
<td class="{cycle}">
   &nbsp;&nbsp;<a class="link" href="tiki-admin_external_wikis.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].extwikiId}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this external wiki?{/tr}')" 
title="Click here to delete this external wiki"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>&nbsp;&nbsp;
   <a class="link" href="tiki-admin_external_wikis.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;extwikiId={$channels[user].extwikiId}"><img border="0" alt="{tr}Edit{/tr}" src="img/icons/edit.gif" /></a>
</td>
</tr>
{/section}
</table>
<div align="center">
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_external_wikis.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_external_wikis.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_external_wikis.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>