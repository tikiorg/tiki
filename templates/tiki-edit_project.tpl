{*  Template for tiki-edit_project.tpl
    Damian aka Damosoft
*}
<h3>{$project.projectName}</h3>
{include file=tiki-project_header.tpl}
<br />
<div class="cbox">
<div class="cbox-data">{tr}Project Object Created Successfully{/tr}</div>
</div>
<br />
<form action="tiki-edit_project.php" method="post">
<input type="hidden" name="projectId" value="{$project.projectId}" />
<input type="hidden" name="edit" value="update" />
<table class="normal">
<tr class="formcolor"><td>{tr}Project Name:{/tr}</td><td>{$project.projectName}</td></tr>
<tr class="formcolor"><td>{tr}Project Description:{/tr}</td><td><textarea name="projectDescription" rows=10 cols=40>{$project.projectDescription}</textarea></td></tr>
<tr class="formcolor"><td>&nbsp;</td><td><input type="checkbox" name="active" {if $project.active eq 'y'}checked{/if} /> {tr}Project Active{/tr}</td></tr>
<tr class="formcolor"><td>{tr}Current Objects:{/tr}</td><td></td></tr>
{if $tiki_p_categorise eq 'y'}
{include file=categorize.tpl}
{/if}
<tr class="formcolor"><td colspan="2">&nbsp;</td></tr>
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" value="save" name="update" /></td></tr>
</table>
</form>
{if $newobject eq "filegal" && $tiki_p_project_admin eq 'y'}
<br />
<h4>{tr}Add a File Gallery{/tr}</h4>
<form action="tiki-edit_project.php" method="post">
<input type="hidden" name="projectId" value="{$project.projectId}" />
<input type="hidden" name="add" value="filegal" />
<table class="normal">
<tr class="formcolor"><td colspan="1">{tr}Description{/tr}</td><td colspan="8"><textarea name="fgalDescription"></textarea></td></tr>
<tr>
<td class="formcolor" colspan="1">{tr}Listing configuration{/tr}</td>
<td class="formcolor" colspan="8">
<table >
<tr>
<td class="formcolor">{tr}icon{/tr}</td>
<td class="formcolor">{tr}id{/tr}</td>
<td class="formcolor">{tr}name{/tr}</td>
<td class="formcolor">{tr}size{/tr}</td>
<td class="formcolor">{tr}description{/tr}</td>
<td class="formcolor">{tr}created{/tr}</td>
<td class="formcolor">{tr}downloads{/tr}</td>
</tr>
<tr>
<td class="formcolor"><input type="checkbox" name="show_icon" checked="checked" /></td>
<td class="formcolor"><input type="checkbox" name="show_id" checked="checked" /></td>
<td class="formcolor">
<select name="show_name">
<option value="a">{tr}Name-filename{/tr}</option>
<option value="n">{tr}Name{/tr}</option>
<option value="f" selected="selected">{tr}Filename only{/tr}</option>
</select>
</td>
<td class="formcolor"><input type="checkbox" name="show_size" checked="checked" /></td>
<td class="formcolor"><input type="checkbox" name="show_description" checked="checked" /></td>
<td class="formcolor"><input type="checkbox" name="show_created" checked="checked" /></td>
<td class="formcolor"><input type="checkbox" name="show_dl" checked="checked" /></td>
</tr>
</table>
</td>
</tr>
<tr class="formcolor"><td>{tr}Permission{/tr}</td><td>{tr}Description{/tr}</td><td>{tr}Project Admin{/tr}</td><td>{tr}Project Members{/tr}</td><td>{tr}Registered{/tr}</td><td>{tr}Anonymous{/tr}</td><td>{tr}Not Set{/tr}</td></tr>
{section name=prm loop=$perms}
{if $perms[prm].permName ne "tiki_p_create_file_galleries" && $perms[prm].permName ne "tiki_p_admin_file_galleries"}
<tr class="formcolor">
 <td>{$perms[prm].permName|escape}</td>
 <td>{$perms[prm].permDesc}</td>
 <td><input type="radio" name="fgal:{$perms[prm].permName}" value="{$feature_project_group_prefix_admin}{$project.projectName}" /></td>
 <td><input type="radio" name="fgal:{$perms[prm].permName}" value="{$feature_project_group_prefix}{$project.projectName}" /></td>
 <td><input type="radio" name="fgal:{$perms[prm].permName}" value="Registered" /></td>
 <td><input type="radio" name="fgal:{$perms[prm].permName}" value="Anonymous" /></td>
 <td><input type="radio" name="fgal:{$perms[prm].permName}" value="na" checked /></td>
</tr>
{/if}
{/section}
<tr class="formcolor"><td colspan="2">&nbsp;</td>
<td colspan="5"><input type="submit" name="create" value="Create Gallery" /></td></tr>
</table>
</form>
{/if}
