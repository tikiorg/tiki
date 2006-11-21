{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-assignpermission.tpl,v 1.66 2006-11-21 05:22:56 bluestrain Exp $ *}
{ *TODO: Must fix even/odd table rows detection byusing Smarty 'cycle' *}
<h1><a href="tiki-assignpermission.php?group={$group}" class="pagetitle">{tr}Assign permissions to group{/tr}: {$group}</a>

{if $feature_help eq 'y'}
<a href="{$helpurl}Permissions+Settings" target="tikihelp" class="tikihelp" title="{tr}Help for Assigning Permissions{/tr}">
<img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>
{/if}
{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-assignpermission.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}edit article tpl{/tr}">
<img src="pics/icons/shape_square_edit.png" border="0" width="16" height="16" alt='{tr}Edit template{/tr}' /></a>
{/if}</h1>

<a href="tiki-admingroups.php" class="linkbut">{tr}Back to groups{/tr}</a><br />

<h2>{tr}Group Information{/tr}</h2>
<table class="normal" id="groupinformation">
<tr><td class="even">{tr}Name{/tr}:</td><td class="odd">{$group_info.groupName}</td></tr>
<tr><td class="even">{tr}Desc{/tr}:</td><td class="odd">{$group_info.groupDesc}</td></tr>
<tr><td class="even" style="vertical-align:top;">{tr}Permissions{/tr}:</td><td class="odd">
{section name=grp loop=$group_info.perms}
{$group_info.perms[grp]}{if $group_info.perms[grp] != "Anonymous"}(<a class="link" href="tiki-assignpermission.php?type={$type}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;permission={$group_info.perms[grp]}&amp;group={$group}&amp;action=remove">x</a>){/if}&nbsp;<br />
{/section}
</td></tr>
</table>
<br />
<div class="advanced">{tr}advanced feature{/tr}: {tr}level configuration{/tr}:
{if $advanced_features ne 'y'}
<a href="tiki-assignpermission.php?find={$find}&amp;type={$type}&amp;group={$group}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;advanced_features=y">{tr}show{/tr}</a>
{else}
<a href="tiki-assignpermission.php?find={$find}&amp;type={$type}&amp;group={$group}&amp;offset={$offset}&amp;sort_mode={$sort_mode}">{tr}hide{/tr}</a>
{/if}<br /><br />

<div {if $advanced_features ne 'y'}style="display:none;"{else}style="display:block;"{/if}>
<h2>{tr}Create level{/tr}</h2>
<form method="post" action="tiki-assignpermission.php">
<input type="hidden" name="group" value="{$group|escape}" />
<input type="hidden" name="type" value="{$type|escape}" />
{tr}Create level{/tr}: <input type="text" name="level" /><input type="submit" name="createlevel" value="{tr}create{/tr}" />
</form>
<br />
<br />
<form method="post" action="tiki-assignpermission.php">
<input type="hidden" name="group" value="{$group|escape}" />
<input type="hidden" name="type" value="{$type|escape}" />
<select name="oper">
<option value="assign">{tr}assign{/tr}</option>
<option value="remove">{tr}remove{/tr}</option>
</select>
{tr}all permissions in level{/tr}:
<select name="level">
{html_options output=$levels values=$levels selected=$perms[user].level}
</select>
<input type="submit" name="allper" value="{tr}update{/tr}" />
</form>
</div>
</div>
<br />
<a name="assign" ></a>
<h2>{tr}Assign Permissions{/tr}</h2>
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
<td class="findtable">
<form method="post" action="tiki-assignpermission.php#assign" name="permselects">
<input type="text" name="find" value="{$find|escape}" />
<input type="submit" value="{tr}find{/tr}" name="search" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<select name="type" onchange="permselects.submit()">
<option value="">{tr}all{/tr}</a>
{sortlinks}
{section name=v loop=$types}
<option value="{$types[v]}"{if $type eq $types[v]} selected="selected"{/if}>{tr}{$types[v]}{/tr}</a>
{/section}
{/sortlinks}
</select>
<select name="group" onchange="permselects.submit()">
{section name=v loop=$groups}
<option value="{$groups[v].groupName}"{if $group eq $groups[v].groupName} selected="selected"{/if}>{$groups[v].groupName}</a>
{/section}
</select>
</form>
</td></tr></table>

<form name="tiki-assignpermission.php" method="post">
<input type="hidden" name="group" value="{$group|escape}" />
<input type="hidden" name="type" value="{$type|escape}" />
<input type="submit" name="update" value="{tr}update{/tr}" /><br />
<table class="sortable" id="assignperms">
<tr>
<th class="heading">&nbsp;</td>
<th class="heading">{tr}name{/tr}</th>
<th class="heading" {if $advanced_features ne 'y'}style="display:none;"{else}style="display:block;"{/if}>{tr}level{/tr}</th>
<th class="heading">{tr}type{/tr}</th>
<th class="heading">{tr}desc{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$perms}
<input type="hidden" name="permName[{$perms[user].permName}]" />
<tr>
<td class="{cycle advance=false}"><input type="checkbox" name="perm[{$perms[user].permName}]" 
{assign var=has_inherited_one_perm value='n'}
{assign var=has_inherited_perm value=''}
{foreach key=gr item=it from=$inherited_groups_perms}
{if $it[user].hasPerm eq 'y'}{assign var=has_inherited_one_perm value='y'}{/if}
{/foreach}
{if $perms[user].hasPerm eq 'y' or $inherited_from_anon[user].hasPerm eq 'y'or $inherited_from_reg[user].hasPerm eq 'y' or $has_inherited_one_perm eq 'y'}checked="checked" {/if}
{if $inherited_from_anon[user].hasPerm eq 'y' or $inherited_from_reg[user].hasPerm eq 'y' or $has_inherited_one_perm eq 'y'}disabled="disabled" {/if}/>
</td>
<td class="{cycle advance=false}">{$perms[user].permName}</td>
<td class="{cycle advance=false}" {if $advanced_features ne 'y'}style="display:none;"{else}style="display:block;"{/if}><select name="level[{$perms[user].permName}]">{html_options output=$levels values=$levels selected=$perms[user].level}</select></td>
<td class="{cycle advance=false}">{tr}{$perms[user].type}{/tr}</td>
<td class="{cycle}">
{if $inherited_from_anon[user].hasPerm eq 'y'}<span style="float:right;font-size:80%;padding:1px 5px;border:1px solid #999;color:#262;background-color:#ada;">{tr}inherited from{/tr}<a href="tiki-assignpermission.php?group=Anonymous"> Anonymous</a></span>{/if}
{if $inherited_from_reg[user].hasPerm eq 'y'}<span style="float:right;font-size:80%;padding:1px 5px;border:1px solid #999;color:#258;background-color:#acd;">{tr}inherited from{/tr}<a href="tiki-assignpermission.php?group=Registered"> Registered</a></span>{/if}
{if $has_inherited_one_perm eq 'y'}<span style="float:right;font-size:80%;padding:1px 5px;border:1px solid #999;color:#852;background-color:#dca;">{tr}inherited{/tr}</span>{/if}
{tr}{$perms[user].permDesc}{/tr}</td>
</tr>
{/section}
</table>
<input type="submit" name="update" value="{tr}update{/tr}" />
</form>
<br />
<div align="center">
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-assignpermission.php?find={$find}&amp;type={$type}&amp;group={$group}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-assignpermission.php?find={$find}&amp;type={$type}&amp;group={$group}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-assignpermission.php?find={$find}&amp;type={$type}&amp;group={$group}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
