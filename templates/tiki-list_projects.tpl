<a class="pagetitle" href="tiki-list_projects.php">{tr}Projects{/tr}</a><br /><br />
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=projects"><img src='img/icons/config.gif' border='0'  alt="{tr}configure listing{/tr}" title="{tr}configure listing{/tr}" /></a>
{/if}
<br /><br />
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-list_projects.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">

<tr>
	<td class="heading"><a class="heading" href="tiki-list_projects.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'projectName_desc'}projectName_asc{else}projectName_desc{/if}">{tr}Name{/tr}</a></td>
	<td class="heading">{tr}Description{/tr}</td>
	<td class="heading">{tr}Number Members{/tr}</td>
	<td class="heading">{tr}Number Admins{/tr}</td>
	<td class="heading"><a class="heading" href="tiki-list_projects.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'Created_desc'}Created_asc{else}Created_desc{/if}">{tr}Created{/tr}</a></td>
	<td class="heading">{tr}Action{/tr}</td>
</tr>

{cycle values="odd,even" print=false}
{section name=changes loop=$listprojects}
{if ($tiki_p_admin eq 'y') or ($listprojects[changes].individual eq 'n') or ($listprojects[changes].individual_tiki_p_project_view eq 'y' ) }
    <tr>
	<td class="{cycle advance=false}"><a class="projectlink" href="tiki-project.php?projectId={$listprojects[changes].projectId}" title="{$listprojects[changes].projectName}">{$listprojects[changes].projectName|truncate:20:"...":true}</a></td>
	<td class="{cycle advance=false}">{$listprojects[changes].projectDescription}</td>
	<td class="{cycle advance=false}">&nbsp;</td>
	<td class="{cycle advance=false}">&nbsp;</td>
	<td class="{cycle advance=false}">{$listprojects[changes].Created|tiki_short_datetime}</td>
	<td class="{cycle advance=true}">&nbsp;</td>
    </tr>
{/if}
{sectionelse}
<tr><td colspan="6" class="odd">
{tr}No projects found{/tr}
</td></tr>
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="projectprevnext" href="tiki-list_projects.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="projectprevnext" href="tiki-list_projects.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-list_projects.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

