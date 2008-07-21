<h1><a class="pagetitle" href="tiki-admin_dsn.php">{tr}Admin dsn{/tr}</a> 
  
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Admin+DSN" target="tikihelp" class="tikihelp" title="{tr}Admin DSN{/tr}">{icon _id='help'}</a>
{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_dsn.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}tiki-admin_dsn tpl{/tr}">{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>
{/if}</h1>

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use Admin DSN to define the database to be used by the SQL plugin.{/tr}{/remarksbox}

<h2>{tr}Create/edit dsn{/tr}</h2>
<form action="tiki-admin_dsn.php" method="post">
<input type="hidden" name="dsnId" value="{$dsnId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" maxlength="255" size="10" name="name" value="{$info.name|escape}" /></td></tr>
<tr><td class="formcolor">{tr}DSN{/tr}:</td><td class="formcolor"><input type="text" maxlength="255" size="40" name="dsn" value="{$info.dsn|escape}" /></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>

<h2>{tr}DSN{/tr}</h2>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_dsn.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_dsn.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'dsn_desc'}dsn_asc{else}dsn_desc{/if}">{tr}DSN{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].name}</td>
<td class="{cycle advance=false}">{$channels[user].dsn}</td>
<td class="{cycle}">
   &nbsp;&nbsp;
   <a title="{tr}Edit{/tr}" class="link" href="tiki-admin_dsn.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;dsnId={$channels[user].dsnId}">
   {icon _id='page_edit'}</a> &nbsp;
   <a title="{tr}Delete{/tr}" class="link" href="tiki-admin_dsn.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].dsnId}">
   {icon _id='cross' alt="{tr}Delete{/tr}"}</a>
</td>
</tr>
{sectionelse}
<tr><td class="odd" colspan="3">{tr}No records found{/tr}</td></tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_dsn.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_dsn.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-admin_dsn.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
