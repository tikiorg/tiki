<a class="pagetitle" href="tiki-admin_dsn.php">{tr}Admin dsn{/tr}</a> 
<!-- the help link info -->
{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=AdminDSN" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}AdminDSN{/tr}">{$helpIcon $helpIconDesc}</a>
{/if}
<!-- link to tpl -->
      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_dsn.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}tiki-admin_dsn tpl{/tr}"><img alt="{tr}Edit template{/tr}" src="img/icons/info.gif" /></a>
{/if}
<!-- begin -->
<br /><br />
<h2>{tr}Create/edit dsn{/tr}</h2>
<form action="tiki-admin_dsn.php" method="post">
<input type="hidden" name="dsnId" value="{$dsnId|escape}" />
<table>
<tr><td><label>{tr}name{/tr}:</label></td><td><input type="text" maxlength="255" size="10" name="name" value="{$info.name|escape}" /></td></tr>
<tr><td><label>{tr}dsn{/tr}:</label></td><td><input type="text" maxlength="255" size="40" name="dsn" value="{$info.dsn|escape}" /></td></tr>
<tr><td >&nbsp;</td><td><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>{tr}dsn{/tr}</h2>
<table>
<tr>
<th><a class="tableheading" href="tiki-admin_dsn.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></th>
<th><a class="tableheading" href="tiki-admin_dsn.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'dsn_desc'}dsn_asc{else}dsn_desc{/if}">{tr}dsn{/tr}</a></th>
<th>{tr}action{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].name}</td>
<td class="{cycle advance=false}">{$channels[user].dsn}</td>
<td class="{cycle}">
     <a href="tiki-admin_dsn.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].dsnId}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this dsn?{/tr}')" 
title="Click here to delete this dsn"><img alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>
   <a href="tiki-admin_dsn.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;dsnId={$channels[user].dsnId}"><img alt="{tr}Edit{/tr}" src="img/icons/edit.gif" /></a>
</td>
</tr>
{/section}
</table>
<div align="center">
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_dsn.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>] 
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
 [<a class="prevnext" href="tiki-admin_dsn.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_dsn.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a> 
{/section}
{/if}
</div>
</div>
