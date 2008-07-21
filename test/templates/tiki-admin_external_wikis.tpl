<h1><a class="pagetitle" href="tiki-admin_external_wikis.php">{tr}Admin external wikis{/tr}</a>
  
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}External+Wikis" target="tikihelp" class="tikihelp" title="{tr}Admin External Wikis{/tr}">{icon _id='help'}</a>
{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_external_wikis.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}tiki admin external wikis template{/tr}">
{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>
{/if}</h1>

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
<td class="heading"><a class="tableheading" href="tiki-admin_external_wikis.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'extwiki_desc'}extwiki_asc{else}extwiki_desc{/if}">{tr}ExtWiki{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].name}</td>
<td class="{cycle advance=false}">{$channels[user].extwiki}</td>
<td class="{cycle}">
   &nbsp;&nbsp;
   <a title="{tr}Edit{/tr}" class="link" href="tiki-admin_external_wikis.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;extwikiId={$channels[user].extwikiId}">
   {icon _id='page_edit'}</a> &nbsp;
   <a title="{tr}Delete{/tr}" class="link" href="tiki-admin_external_wikis.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].extwikiId}" >
   {icon _id='cross' alt='{tr}Delete{/tr}'}</a>
</td>
</tr>
{sectionelse}
<tr><td class="odd" colspan="3">{tr}No records found{/tr}</td></tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_external_wikis.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_external_wikis.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-admin_external_wikis.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
