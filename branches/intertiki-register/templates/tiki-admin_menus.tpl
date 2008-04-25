<h1><a class="pagetitle" href="tiki-admin_menus.php">{tr}Admin Menus{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Menus" target="tikihelp" class="tikihelp" title="{tr}Admin Menu Builder{/tr}">{icon _id='help'}</a>
{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_menus.tpl" target="tikihelp" class="tikihelp" title="{tr}Edit template{/tr}: {tr}Admin Menus Template{/tr}">{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>
{/if}</h1>

<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}To use menus in a <a class="rbox-link" href="tiki-admin_modules.php">module</a>, <a class="rbox-link" href="tiki-admin.php?page=siteid">Site identity</a> or a template, use {literal}{menu id=x}{/literal}, where x is the ID of the menu. To use <a class="rbox-link" target="tikihelp" href="http://phplayersmenu.sourceforge.net/">phplayersmenu</a>, you can use one of the three following syntaxes:{/tr} 
{literal}{phplayers id=47}{/literal},
{literal}{phplayers id=47 type=horiz}{/literal},
{literal}{phplayers id=47 type=vert}{/literal}.
{tr}This will work well (or not!) depending on your theme. To learn more about <a class="rbox-link" target="tikihelp" href="http://themes.tikiwiki.org">themes</a>{/tr}

</div>
</div>
<br />



{if $menuId > 0}
<h2>{tr}Edit this Menu:{/tr} {$name}</h2>
<a href="tiki-admin_menus.php" class="linkbut">{tr}Create new Menu{/tr}</a>
{else}
<h2>{tr}Create new Menu{/tr}</h2>
{/if}
<form action="tiki-admin_menus.php" method="post">
<input type="hidden" name="menuId" value="{$menuId|escape}" />
<table class="normal">
<tr><td class="formcolor"><label for="menus_name">{tr}Name{/tr}:</label></td><td class="formcolor"><input type="text" name="name" id="menus_name" value="{$name|escape}" /></td></tr>
<tr><td class="formcolor"><label for="menus_desc">{tr}Description{/tr}:</label></td><td class="formcolor"><textarea name="description" id="menus_desc" rows="4" cols="40">{$description|escape}</textarea></td></tr>
<tr><td class="formcolor"><label for="menus_type">{tr}Type{/tr}:</label></td><td class="formcolor">
<select name="type" id="menus_type">
<option value="d" {if $type eq 'd'}selected="selected"{/if}>{tr}dynamic collapsed{/tr}</option>
<option value="e" {if $type eq 'e'}selected="selected"{/if}>{tr}dynamic extended{/tr}</option>
<option value="f" {if $type eq 'f'}selected="selected"{/if}>{tr}fixed{/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>{tr}Menus{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td><label for="menus_find">{tr}Find{/tr}</label></td>
   <td>
   <form method="get" action="tiki-admin_menus.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" id="menus_find" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_menus.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'menuId_desc'}menuId_asc{else}menuId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_menus.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_menus.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_menus.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}Type{/tr}</a></td>
<td class="heading">{tr}Options{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].menuId}</td>
<td class="{cycle advance=false}"><a href="tiki-admin_menus.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;menuId={$channels[user].menuId}" title="{tr}Edit{/tr}">{$channels[user].name}</a></td>
<td class="{cycle advance=false}">{$channels[user].description}</td>
<td class="{cycle advance=false}">{$channels[user].type}</td>
<td class="{cycle advance=false}">{$channels[user].options}</td>
<td class="{cycle advance=true}">
	<a class="link" href="tiki-admin_menus.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;menuId={$channels[user].menuId}" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>
	{if $tiki_p_edit_menu_option eq 'y'}<a class="link" href="tiki-admin_menu_options.php?menuId={$channels[user].menuId}" title="{tr}Configure/Options{/tr}">{icon _id='table' alt='{tr}Configure/Options{/tr}'}</a> &nbsp;{/if}
    <a class="link" href="tiki-admin_menus.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].menuId}" title="{tr}Delete{/tr}">{icon _id='cross' alt='{tr}Delete{/tr}'}</a>
</td>
</tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_menus.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>][
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_menus.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-admin_menus.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>
{/section}
{/if}
</div>
</div>
