{* $Id$ *}
{popup_init src="lib/overlib.js"}

<h1><a class="pagetitle" href="tiki-admin_modules.php">{tr}Admin Modules{/tr}</a>

{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=module">{icon _id='wrench' alt="{tr}Admin Feature{/tr}" style="vertical-align:bottom"}</a>
{/if}

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Modules+Admin" target="tikihelp" class="tikihelp" title="{tr}Admin Modules{/tr}">
{icon _id='help' style="vertical-align:bottom"}</a>{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_modules.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}Admin Modules Template{/tr}">
{icon _id='shape_square_edit' style="vertical-align:bottom"}</a>{/if}</h1>

<div class="navbar">
<a class="linkbut" href="#assign">{tr}Assign Module{/tr}</a>
<a class="linkbut" href="#leftmod">{tr}Left Modules{/tr}</a>
<a class="linkbut" href="#rightmod">{tr}Right Modules{/tr}</a>
<a class="linkbut" href="#editcreate">{tr}Edit/Create{/tr}</a>
<a class="linkbut" href="tiki-admin_modules.php?clear_cache=1">{tr}Clear Cache{/tr}</a>
</div>

<h2>{tr}User Modules{/tr}</h2>
<table class="normal">
<tr>
<td class="heading">{tr}Name{/tr}</td>
<td class="heading">{tr}Title{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle print=false values="even,odd"}
{section name=user loop=$user_modules}
<tr>
<td class="{cycle advance=false}">{$user_modules[user].name|escape}</td>
<td class="{cycle advance=false}">{$user_modules[user].title|escape}</td>
<td class="{cycle}"><a class="link" href="tiki-admin_modules.php?um_edit={$user_modules[user].name|escape:'url'}#editcreate" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>
             <a class="link" href="tiki-admin_modules.php?edit_assign={$user_modules[user].name|escape:'url'}#assign" title="{tr}Assign{/tr}">{icon _id='add' alt='{tr}Assign{/tr}'}</a>
             <a class="link" href="tiki-admin_modules.php?um_remove={$user_modules[user].name|escape:'url'}" title="{tr}Delete{/tr}">{icon _id='cross' alt='{tr}Delete{/tr}'}</a></td>
</tr>
{sectionelse}
<tr><td colspan="3" class="odd">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br />
<a name="assign"></a>
{if $assign_name eq ''}
<h2>{tr}Assign new module{/tr}</h2>
{else}
<h2>{tr}Edit this assigned module:{/tr} {$assign_name}</h2>
<div class="navbar"><a href="tiki-admin_modules.php?#assign" class="linkbut">{tr}Assign new module{/tr}</a></div>
{/if}
{if $preview eq 'y'}
<h3>{tr}Preview{/tr}</h3>
{$preview_data}
{/if}
<form method="post" action="tiki-admin_modules.php#assign">
{if !empty($info.moduleId)}<input type="hidden" name="moduleId" value="{$info.moduleId}" />{elseif !empty($moduleId)}<input type="hidden" name="moduleId" value="{$moduleId}" />{/if}
<table class="normal">
<tr><td class="formcolor">{tr}Module Name{/tr}</td><td class="formcolor">
<select name="assign_name">
{section name=ix loop=$all_modules}
<option value="{$all_modules[ix]|escape}" {if $assign_name eq $all_modules[ix] || $assign_selected eq $all_modules[ix]}selected="selected"{/if}>{$all_modules[ix]}</option>
{/section}
</select>
</td></tr>
<!--<tr><td>{tr}Title{/tr}</td><td><input type="text" name="assign_title" value="{$assign_title|escape}" /></td></tr>-->
<tr><td class="formcolor">{tr}Position{/tr}</td><td class="formcolor">
<select name="assign_position">
<option value="l" {if $assign_position eq 'l'}selected="selected"{/if}>{tr}Left{/tr}</option>
<option value="r" {if $assign_position eq 'r'}selected="selected"{/if}>{tr}Right{/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor">{tr}Order{/tr}</td><td class="formcolor">
<select name="assign_order">
{section name=ix loop=$orders}
<option value="{$orders[ix]|escape}" {if $assign_order eq $orders[ix]}selected="selected"{/if}>{$orders[ix]}</option>
{/section}
</select>
</td></tr>
<tr><td class="formcolor">{tr}Cache Time{/tr} ({tr}secs{/tr})</td><td class="formcolor"><input type="text" name="assign_cache" value="{$assign_cache|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Rows{/tr}</td><td class="formcolor"><input type="text" name="assign_rows" value="{$assign_rows|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Parameters{/tr}</td><td class="formcolor"><input type="text" name="assign_params" value="{$assign_params|escape}" />
<a {popup text="{tr}Params: specific params to the module and/or general params ('lang', 'flip', 'title', 'decorations', 'section', 'overflow', 'page', 'nobox', 'bgcolor', 'color', 'theme'). Separator between params:'&'. E.g. maxlen=15&nonums=y.{/tr}" width=100 center=true}>{icon _id='help' style="vertical-align:middle"}</a></td></tr>
<tr><td class="formcolor">{tr}Groups{/tr}</td><td class="formcolor">
{remarksbox type="tip" title="Tip"}{tr}Use Ctrl+Click to select multiple groups.{/tr}{/remarksbox}
<select multiple="multiple" name="groups[]">
{section name=ix loop=$groups}
<option value="{$groups[ix].groupName|escape}" {if $groups[ix].selected eq 'y'}selected="selected"{/if}>{$groups[ix].groupName|escape}</option>
{/section}
</select>
{if $prefs.modallgroups eq 'y'}
<div class="simplebox">{icon _id=information.png style="vertical-align:middle;float:left"} {tr}The{/tr} <a class="rbox-link" href="tiki-admin.php?page=module">{tr}Display Modules to All Groups{/tr}</a> {tr}setting will override your selection of specific groups.{/tr}</div><br />{/if}
</td></tr>
{if $prefs.user_assigned_modules eq 'y'}
<tr><td class="formcolor">{tr}Visibility{/tr}</td><td class="formcolor">
<select name="assign_type">
<option value="D" {if $assign_type eq 'D'}selected="selected"{/if}>{tr}Displayed now for all eligible users even with personal assigned modules{/tr}</option>
<option value="d" {if $assign_type eq 'd'}selected="selected"{/if}>{tr}Displayed for the eligible users with no personal assigned modules{/tr}</option>
<option value="P" {if $assign_type eq 'P'}selected="selected"{/if}>{tr}Displayed now, can't be unassigned{/tr}</option>
<option value="h" {if $assign_type eq 'h'}selected="selected"{/if}>{tr}Not displayed until a user chooses it{/tr}</option>
</select>
<div class="simplebox">
{icon _id=information.png style="vertical-align:middle;float:left;"}{tr}Because <a class="rbox-link" href="tiki-admin.php?page=module">Users can Configure Modules</a>, select either{/tr} &quot;{tr}Displayed now for all eligible users even with personal assigned modules{/tr}&quot;{tr} or {/tr}&quot;{tr}Displayed now, can't be unassigned{/tr}&quot; {tr}to make sure users will notice any newly assigned modules.{/tr}</div>
</td></tr>
{/if}
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="preview" value="{tr}Preview{/tr}" /><input type="submit" name="assign" value="{tr}Assign{/tr}" /></td></tr>
</table>
</form>
<br />
<h2>{tr}Assigned Modules{/tr}</h2>
<a name="leftmod"></a>
<table class="normal">
<caption>{tr}Left Modules{/tr}</caption>
<tr>
<td class="heading">{tr}Name{/tr}</td>
<td class="heading">{tr}Order{/tr}</td>
<td class="heading">{tr}Cache{/tr}</td>
<td class="heading">{tr}Rows{/tr}</td>
<td class="heading">{tr}Parameters{/tr}</td>
<td class="heading">{tr}Groups{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle print=false values="even,odd"}
{section name=user loop=$left}
<tr>
<td class="{cycle advance=false}">{$left[user].name|escape}</td>
<td class="{cycle advance=false}">{$left[user].ord}</td>
<td class="{cycle advance=false}">{$left[user].cache_time}</td>
<td class="{cycle advance=false}">{$left[user].rows}</td>
<td class="{cycle advance=false}">{$left[user].params|escape}</td>
<td class="{cycle advance=false}">{$left[user].module_groups}</td>
<td class="{cycle}">
             <a class="link" href="tiki-admin_modules.php?edit_assign={$left[user].moduleId}#assign" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>
{if $left[0].moduleId ne $left[user].moduleId}
             <a class="link" href="tiki-admin_modules.php?modup={$left[user].moduleId}#leftmod" title="{tr}Move Up{/tr}">{icon _id='resultset_up'}</a>
{/if}
{if $left[user.index_next].moduleId}
             <a class="link" href="tiki-admin_modules.php?moddown={$left[user].moduleId}#leftmod" title="{tr}Move Down{/tr}">{icon _id='resultset_down'}</a>
{/if}
             <a class="link" href="tiki-admin_modules.php?modright={$left[user].moduleId}#rightmod" title="{tr}Move to Right Column{/tr}">{icon _id='arrow_right'}</a>
             <a class="link" href="tiki-admin_modules.php?unassign={$left[user].moduleId}#leftmod" title="{tr}Unassign{/tr}">{icon _id='cross' alt='{tr}x{/tr}'}</a></td>
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br />
<br />
<a name="rightmod"></a>
<table class="normal">
<caption>{tr}Right Modules{/tr}</caption>
<tr>
<td class="heading">{tr}Name{/tr}</td>
<td class="heading">{tr}Order{/tr}</td>
<td class="heading">{tr}Cache{/tr}</td>
<td class="heading">{tr}Rows{/tr}</td>
<td class="heading">{tr}Parameters{/tr}</td>
<td class="heading">{tr}Groups{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle print=false values="even,odd"}
{section name=user loop=$right}
<tr>
<td class="{cycle advance=false}">{$right[user].name|escape}</td>
<td class="{cycle advance=false}">{$right[user].ord}</td>
<td class="{cycle advance=false}">{$right[user].cache_time}</td>
<td class="{cycle advance=false}">{$right[user].rows}</td>
<td class="{cycle advance=false}">{$right[user].params|escape}</td>
<td class="{cycle advance=false}">{$right[user].module_groups}</td>
<td class="{cycle}">
             <a class="link" href="tiki-admin_modules.php?edit_assign={$right[user].moduleId}#assign" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>
{if $right[0].moduleId ne $right[user].moduleId}
             <a class="link" href="tiki-admin_modules.php?modup={$right[user].moduleId}#rightmod" title="{tr}Move Up{/tr}">{icon _id='resultset_up'}</a>
{/if}
{if $right[user.index_next].moduleId}
             <a class="link" href="tiki-admin_modules.php?moddown={$right[user].moduleId}#rightmod" title="{tr}Move Down{/tr}">{icon _id='resultset_down'}</a>
{/if}
             <a class="link" href="tiki-admin_modules.php?modleft={$right[user].moduleId}#leftmod" title="{tr}Move to Left Column{/tr}">{icon _id='arrow_left'}</a>
             <a class="link" href="tiki-admin_modules.php?unassign={$right[user].moduleId}#rightmod" title="{tr}Unassign{/tr}">{icon _id='cross' alt='{tr}x{/tr}'}</a></td>
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br />

<a name="editcreate"></a>
{if $um_name eq ''}
<h2>{tr}Create new user module{/tr}</h2>
{else}
<h2>{tr}Edit this user module:{/tr} {$um_name}</h2>
{/if}
{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Create your new custom module below. Make sure to preview first and make sure all is OK before <a href="#assign">assigning it</a>. Using html, you will be fine. However, if you improperly use wiki syntax or Smarty code, you could lock yourself out of the site.{/tr}{/remarksbox}

<table class="normal">
 <tr valign="top">
  <td valign="top" class="odd">
{if $um_name ne ''}
<a href="tiki-admin_modules.php#editcreate">{tr}Create new user module{/tr}</a>
{/if}
<form name='editusr' method="post" action="tiki-admin_modules.php">
<table>
<tr><td class="form">{tr}Name{/tr}</td><td><input type="text" name="um_name" value="{$um_name|escape}" /></td></tr>
<tr><td class="form">{tr}Title{/tr}</td><td><input type="text" name="um_title" value="{$um_title|escape}" /></td></tr>
<tr><td class="form">{tr}Data{/tr}</td><td>
<textarea id='usermoduledata' name="um_data" rows="10" cols="40" style="width:95%">{$um_data|escape}</textarea>
</td></tr>
<tr><td class="form"></td><td class="form"><input type="checkbox" name="um_parse" value="y" {if $um_parse eq "y"}checked="checked"{/if} /> {tr}Must be wiki parsed{/tr}</td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="um_update" value="{if $um_title eq ''}{tr}Create{/tr}{else}{tr}Edit{/tr}{/if}" /></td></tr>
</table>
</form>

</td><td class="even" style="vertical-align:top">
<h3>{tr}Objects that can be included{/tr}</h3>
<table>
{if $polls}
<tr>
  <td class="form">
    {tr}Available polls{/tr}:
  </td>
  <td>
    <select name="polls" id='list_polls'>
	<option value="{literal}{{/literal}poll{literal}}{/literal}">--{tr}Random active poll{/tr}--</option>
	<option value="{literal}{{/literal}poll id=current{literal}}{/literal}">--{tr}Random current poll{/tr}--</option>
    {section name=ix loop=$polls}
    <option value="{literal}{{/literal}poll id={$polls[ix].pollId}{literal}}{/literal}">{$polls[ix].title}</option>
    {/section}
    </select>

  </td>
  <td class="form">
    <a class="link" href="javascript:setUserModuleFromCombo('list_polls');" title="{tr}Use Poll{/tr}">{icon _id='add' alt='{tr}Use{/tr}'}</a>
  </td><td class="form">
	<a {popup text="Params: id= rate=" width=100 center=true}>{icon _id='help'}</a>
  </td>
</tr>
{/if}
{if $galleries}
<tr>
  <td class="form">
   {tr}Random image from{/tr}:
  </td>
  <td>
   <select name="galleries" id='list_galleries'>
   <option value="{literal}{{/literal}gallery id=-1{literal}}{/literal}">{tr}All galleries{/tr}</option>
   {section name=ix loop=$galleries}
   <option value="{literal}{{/literal}gallery id={$galleries[ix].galleryId}{literal}}{/literal}">{$galleries[ix].name}</option>
   {/section}
   </select>
  </td>
  <td class="form">
   <a class="link" href="javascript:setUserModuleFromCombo('list_galleries');" title="{tr}Use Gallery{/tr}">{icon _id='add' alt='{tr}Use{/tr}'}</a>
  </td><td class="form">
	<a {popup text="Params: id= showgalleryname=1 hideimgname=1 hidelink=1" width=100 center=true}>{icon _id='help'}</a>
  </td>
</tr>
{/if}
{if $contents}
<tr>
  <td class="form">
    {tr}Dynamic content blocks{/tr}:
  </td>
  <td>
    <select name="contents" id='list_contents'>
    {section name=ix loop=$contents}
    <option value="{literal}{{/literal}content id={$contents[ix].contentId}{literal}}{/literal}">{$contents[ix].description|truncate:20:"...":true}</option>
    {/section}
    </select>
  </td>
  <td class="form">
    <a class="link" href="javascript:setUserModuleFromCombo('list_contents');" title="{tr}Use Dynamic Content{/tr}">{icon _id='add' alt='{tr}Use{/tr}'}</a>
  </td><td class="form">
	<a {popup text="Params: id=" width=100 center=true}>{icon _id='help'}</a>
  </td>
</tr>
{/if}
{if $rss}
<tr>
  <td class="form">
    {tr}RSS modules{/tr}:
  </td>
  <td>
    <select name="rsss" id='list_rsss'>
    {section name=ix loop=$rsss}
    <option value="{literal}{{/literal}rss id={$rsss[ix].rssId}{literal}}{/literal}">{$rsss[ix].name}</option>
    {/section}
    </select>
  </td>
  <td class="form">
    <a class="link" href="javascript:setUserModuleFromCombo('list_rsss');" title="{tr}Use RSS Module{/tr}">{icon _id='add' alt='{tr}Use{/tr}'}</a>
  </td><td class="form">
	<a {popup text="Params: id= max= skip=x,y " width=100 center=true}>{icon _id='help'}</a>
  </td>
</tr>
{/if}
{if $menus}
<tr>
  <td class="form">
    {tr}Menus{/tr}:
  </td>
  <td>
    <select name="menus" id='list_menus'>
    {section name=ix loop=$menus}
    <option value="{literal}{{/literal}menu id={$menus[ix].menuId}{literal}}{/literal}">{$menus[ix].name}</option>
    {/section}
    </select>
  </td>
  <td class="form">
    <a class="link" href="javascript:setUserModuleFromCombo('list_menus');" title="{tr}Use Menu{/tr}">{icon _id='add' alt='{tr}Use{/tr}'}</a>


  </td><td class="form">
	<a {popup text="Params: id= css= link_on_section=y type=vert|horiz" width=100 center=true}>{icon _id='help'}</a>  </td>
</tr>
{if $prefs.feature_phplayers eq "y"}
<tr>
  <td class="form">
    {tr}phpLayersMenus{/tr}:
  </td>
  <td>
    <select name="phpmenus" id='list_phpmenus'>
    {section name=ix loop=$menus}
    <option value="{literal}{{/literal}phplayers id={$menus[ix].menuId}{literal}}{/literal}">{$menus[ix].name}</option>
    {/section}
    </select>
  </td>
  <td class="form">
    <a class="link" href="javascript:setUserModuleFromCombo('list_phpmenus');" title="{tr}Use phplayermenu{/tr}">{icon _id='add' alt='{tr}Use{/tr}'}</a>
  </td><td class="form">
	<a {popup text="Params: id= type=tree|phptree|plain|horiz|vert file= sectionLevel=" width=100 center=true}>{icon _id='help'}</a>

  </td>
</tr>
{/if}
{/if}
{if $banners}
<tr>
  <td class="form">
    {tr}Banner zones{/tr}:
  </td>
  <td>
    <select name="banners" id='list_banners'>
    {section name=ix loop=$banners}
    <option value="{literal}{{/literal}banner zone={$banners[ix].zone}{literal}}{/literal}">{$banners[ix].zone}</option>
    {/section}
    </select>
  </td>
  <td class="form">
    <a class="link" href="javascript:setUserModuleFromCombo('list_banners');" title="{tr}Use Banner Zone{/tr}">{icon _id='add' alt='{tr}Use{/tr}'}</a>
  </td><td class="form">
	<a {popup text="Params: zone= target=_blank|_self|" width=100 center=true}>{icon _id='help'}</a>
  </td>
</tr>
{/if}
{if $wikistructures}
<tr>
  <td class="form">
    {tr}Wiki{/tr} {tr}Structures{/tr}:
  </td>
  <td>
    <select name="structures" id='list_wikistructures'>
    {section name=ix loop=$wikistructures}
    <option value="{literal}{{/literal}wikistructure id={$wikistructures[ix].page_ref_id}{literal}}{/literal}">{$wikistructures[ix].pageName}</option>
    {/section}
    </select>
  </td>
  <td class="form">
    <a class="link" href="javascript:setUserModuleFromCombo('list_wikistructures');" title="{tr}Use Wiki Structure{/tr}">{icon _id='add' alt='{tr}Use{/tr}'}</a>
  </td><td class="form">
	<a {popup text="Params: id=" width=100 center=true}>{icon _id='help'}</a>
  </td>
</tr>
{/if}
</table>
</td></tr></table>
