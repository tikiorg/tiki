<a class="pagetitle" href="tiki-admin_modules.php">{tr}Admin Modules{/tr}</a><br/><br/>
[<a class="link" href="#assign">{tr}assign module{/tr}</a>|
<a class="link" href="#leftmod">{tr}left modules{/tr}</a>|
<a class="link" href="#rightmod">{tr}right modules{/tr}</a>|
<a class="link" href="#editcreate">{tr}edit/create{/tr}</a>|
<a class="link" href="tiki-admin_modules.php?clear_cache=1">{tr}clear cache{/tr}</a>]

<div class="simplebox">
{tr}
<b>Note 1</b>: if you allow your users to configure modules then assigned
modules won't be reflected in the screen until you configure them
from MyTiki->modules.<br/>
<b>Note 2</b>: If you assign modules to groups make sure that you
have turned off the option 'display modules to all groups always'
from Admin->General
{/tr}
</div>

<h3>{tr}User Modules{/tr}</h3>

<table class="normal">
<tr>
<td class="heading">{tr}name{/tr}</td>
<td class="heading">{tr}title{/tr}</td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$user_modules}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$user_modules[user].name}</td>
<td class="odd">{$user_modules[user].title}</td>
<td class="odd"><a class="link" href="tiki-admin_modules.php?um_remove={$user_modules[user].name}">{tr}delete{/tr}</a>
             <a class="link" href="tiki-admin_modules.php?um_edit={$user_modules[user].name}">{tr}edit{/tr}</a>
             <a class="link" href="tiki-admin_modules.php?edit_assign={$user_modules[user].name}">{tr}assign{/tr}</a></td>
</tr>
{else}
<tr>
<td class="even">{$user_modules[user].name}</td>
<td class="even">{$user_modules[user].title}</td>
<td class="even"><a class="link" href="tiki-admin_modules.php?um_remove={$user_modules[user].name}">{tr}delete{/tr}</a>
             <a class="link" href="tiki-admin_modules.php?um_edit={$user_modules[user].name}">{tr}edit{/tr}</a>
             <a class="link" href="tiki-admin_modules.php?edit_assign={$user_modules[user].name}">{tr}assign{/tr}</a></td>
</tr>
{/if}
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br/>
<a name="assign"></a>
<h3>{tr}Assign module{/tr}</h3>
{if $preview eq 'y'}
{tr}Preview{/tr}<br/>
{$preview_data}
{/if}
<form method="post" action="tiki-admin_modules.php#assign">
<table class="normal">
<tr><td class="formcolor">{tr}Module Name{/tr}</td><td class="formcolor">
<select name="assign_name">
{section name=ix loop=$all_modules}
<option value="{$all_modules[ix]}" {if $assign_name eq $all_modules[ix]}selected="selected"{/if}>{$all_modules[ix]}</option>
{/section}
</select>
</td></tr>
<!--<tr><td>{tr}Title{/tr}</td><td><input type="text" name="assign_title" value="{$assign_title}"></td></tr>-->
<tr><td class="formcolor">{tr}Position{/tr}</td><td class="formcolor">
<select name="assign_position">
<option value="l" {if $assign_position eq 'l'}selected="selected"{/if}>{tr}left{/tr}</option>
<option value="r" {if $assign_position eq 'r'}selected="selected"{/if}>{tr}right{/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor">{tr}Order{/tr}</td><td class="formcolor">
<select name="assign_order">
{section name=ix loop=$orders}
<option value="{$orders[ix]}" {if $assign_order eq $orders[ix]}selected="selected"{/if}>{$orders[ix]}</option>
{/section}
</select>
</td></tr>
<tr><td class="formcolor">{tr}Cache Time{/tr} ({tr}secs{/tr})</td><td class="formcolor"><input type="text" name="assign_cache" value="{$assign_cache}" /></td></tr>
<tr><td class="formcolor">{tr}Rows{/tr}</td><td class="formcolor"><input type="text" name="assign_rows" value="{$assign_rows}" /></td></tr>
<tr><td class="formcolor">{tr}Parameters{/tr}</td><td class="formcolor"><input type="text" name="assign_params" value="{$assign_params}" /></td></tr>
<tr><td class="formcolor">{tr}Groups{/tr}</td><td class="formcolor">
<select multiple="multiple" name="groups[]">
{section name=ix loop=$groups}
<option value="{$groups[ix].groupName}" {if $groups[ix].selected eq 'y'}selected="selected"{/if}>{$groups[ix].groupName}</option>
{/section}
</select>
</td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="preview" value="{tr}preview{/tr}"><input type="submit" name="assign" value="{tr}assign{/tr}"></td></tr>
</table>
</form>
<br/>
<h3>{tr}Assigned Modules{/tr}</h3>
<a name="leftmod"></a>
<h3>{tr}Left Modules{/tr}</h3>
<table class="normal">
<tr>
<td class="heading">{tr}name{/tr}</td>
<!--<td class="heading">{tr}title{/tr}</td>-->
<td class="heading">{tr}order{/tr}</td>
<td class="heading">{tr}cache{/tr}</td>
<td class="heading">{tr}rows{/tr}</td>
<td class="heading">{tr}groups{/tr}</td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$left}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$left[user].name}</td>
<!--<td class="odd">{$left[user].title}</td>-->
<td class="odd">{$left[user].ord}</td>
<td class="odd">{$left[user].cache_time}</td>
<td class="odd">{$left[user].rows}</td>
<td class="odd">{$left[user].module_groups}</td>
<td class="odd">
             <a class="link" href="tiki-admin_modules.php?edit_assign={$left[user].name}">{tr}edit{/tr}</a>
             <a class="link" href="tiki-admin_modules.php?modup={$left[user].name}">{tr}up{/tr}</a>
             <a class="link" href="tiki-admin_modules.php?moddown={$left[user].name}">{tr}down{/tr}</a>
             <a class="link" href="tiki-admin_modules.php?unassign={$left[user].name}">{tr}x{/tr}</a></td>
</tr>
{else}
<tr>
<td class="even">{$left[user].name}</td>
<!--<td class="even">{$left[user].title}</td>-->
<td class="even">{$left[user].ord}</td>
<td class="even">{$left[user].cache_time}</td>
<td class="even">{$left[user].rows}</td>
<td class="even">{$left[user].module_groups}</td>
<td class="even">
             <a class="link" href="tiki-admin_modules.php?edit_assign={$left[user].name}">{tr}edit{/tr}</a>
             <a class="link" href="tiki-admin_modules.php?modup={$left[user].name}">{tr}up{/tr}</a>
             <a class="link" href="tiki-admin_modules.php?moddown={$left[user].name}">{tr}down{/tr}</a>
             <a class="link" href="tiki-admin_modules.php?unassign={$left[user].name}">{tr}x{/tr}</a></td>
</tr>
{/if}
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<a name="rightmod"></a>
<h3>{tr}Right Modules{/tr}</h3>

<table class="normal">
<tr>
<td class="heading">{tr}name{/tr}</td>
<!--<td class="heading">{tr}title{/tr}</td>-->
<td class="heading">{tr}order{/tr}</td>
<td class="heading">{tr}cache{/tr}</td>
<td class="heading">{tr}rows{/tr}</td>
<td class="heading">{tr}groups{/tr}</td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$right}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$right[user].name}</td>
<!--<td class="odd">{$right[user].title}</td>-->
<td class="odd">{$right[user].ord}</td>
<td class="odd">{$right[user].cache_time}</td>
<td class="odd">{$right[user].rows}</td>
<td class="odd">{$right[user].module_groups}</td>
<td class="odd">
             <a class="link" href="tiki-admin_modules.php?edit_assign={$right[user].name}">{tr}edit{/tr}</a>
             <a class="link" href="tiki-admin_modules.php?modup={$right[user].name}">{tr}up{/tr}</a>
             <a class="link" href="tiki-admin_modules.php?moddown={$right[user].name}">{tr}down{/tr}</a>
             <a class="link" href="tiki-admin_modules.php?unassign={$right[user].name}">{tr}x{/tr}</a></td>
</tr>
{else}
<tr>
<td class="even">{$right[user].name}</td>
<!--<td class="even">{$right[user].title}</td>-->
<td class="even">{$right[user].ord}</td>
<td class="even">{$right[user].cache_time}</td>
<td class="even">{$right[user].rows}</td>
<td class="even">{$right[user].module_groups}</td>
<td class="even">
             <a class="link" href="tiki-admin_modules.php?edit_assign={$right[user].name}">{tr}edit{/tr}</a>
             <a class="link" href="tiki-admin_modules.php?modup={$right[user].name}">{tr}up{/tr}</a>
             <a class="link" href="tiki-admin_modules.php?moddown={$right[user].name}">{tr}down{/tr}</a>
             <a class="link" href="tiki-admin_modules.php?unassign={$right[user].name}">{tr}x{/tr}</a></td>
</tr>
{/if}
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br/>
<a name="editcreate"></a>
<table class="normal"><tr><td valign="top" class="odd">
<h3>{tr}Edit/Create user module{/tr} 
	{if $wysiwyg eq 'n'}
		<a class="link" href="tiki-admin_modules.php?wysiwyg='y'">{tr}Use wysiwyg editor{/tr}</a>
	{else}
		<a class="link" href="tiki-admin_modules.php?wysiwyg='n'">{tr}Use normal editor{/tr}</a>
	{/if}
	
</h3>
<form name='editusr' method="post" action="tiki-admin_modules.php">
<table>
<tr><td class="form">{tr}Name{/tr}</td><td><input type="text" name="um_name" value="{$um_name}" /></td></tr>
<tr><td class="form">{tr}Title{/tr}</td><td><input type="text" name="um_title" value="{$um_title}" /></td></tr>
<tr><td class="form">{tr}Data{/tr}</td><td>


<textarea id='usermoduledata' name="um_data" rows="10" cols="40">{$um_data}</textarea>

{if $wysiwyg eq 'y'}
	<script type="text/javascript" src="lib/htmlarea/htmlarea.js"></script>
	<script type="text/javascript" src="lib/htmlarea/htmlarea-lang-en.js"></script>
	<script type="text/javascript" src="lib/htmlarea/dialog.js"></script>
	<style type="text/css">
		@import url(lib/htmlarea/htmlarea.css);
	</style>
	<script defer='defer'>(new HTMLArea(document.forms['editusr']['um_data'])).generate();</script>
{/if}

</td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="um_update" value="{tr}create/edit{/tr}" /></td></tr>
</table>
</form>
</td><td valign="top" class="even">
<h3>{tr}Objects that can be included{/tr}</h3>
<table>
<tr>
  <td class="form">
    {tr}Available polls{/tr}:
  </td>
  <td>
    <select name="polls" id='list_polls'>
    {section name=ix loop=$polls}
    <option value="{literal}{{/literal}poll id={$polls[ix].pollId}{literal}}{/literal}">{$polls[ix].title}</option>   
    {/section}
    </select>
  </td>
  <td class="form">
    <a class="link" href="javascript:setUserModuleFromCombo('list_polls');">{tr}use poll{/tr}</a>
  </td>
</tr>

<tr>
  <td class="form">
   {tr}Random image from{/tr}:
  </td>
  <td>
   <select name="galleries" id='list_galleries'>
   <option value="{literal}{{/literal}gallery id=-1 showgalleryname=1{literal}}{/literal}">{tr}All galleries{/tr}</option>
   {section name=ix loop=$galleries}
   <option value="{literal}{{/literal}gallery id={$galleries[ix].galleryId} showgalleryname=0{literal}}{/literal}">{$galleries[ix].name}</option>
   {/section}
  </td>
  <td class="form">
   <a class="link" href="javascript:setUserModuleFromCombo('list_galleries');">{tr}use gallery{/tr}</a>
  </td>
</tr>


<tr>
  <td class="form">
    {tr}Dynamic content blocks{/tr}:
  </td>
  <td>
    <select name="contents" id='list_contents'>
    {section name=ix loop=$contents}
    <option value="{literal}{{/literal}content id={$contents[ix].contentId}{literal}}{/literal}">{$contents[ix].description|truncate:20:"(...)":true}</option>   
    {/section}
    </select>
  </td>
  <td class="form">
    <a class="link" href="javascript:setUserModuleFromCombo('list_contents');">{tr}use dynamic  content{/tr}</a>
  </td>
</tr>
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
    <a class="link" href="javascript:setUserModuleFromCombo('list_rsss');">{tr}use rss module{/tr}</a>
  </td>
</tr>

<tr>
  <td class="form">
    {tr}Menus{/tr}:
  </td>
  <td>
    <select name="menus" id='list_menus'>
    {section name=ix loop=$menus}
    <option value="{literal}{{/literal}menu id={$menus[ix].menuId}{literal}}{/literal}">{$menus[ix].menuId}</option>   
    {/section}
    </select>
  </td>
  <td class="form">
    <a class="link" href="javascript:setUserModuleFromCombo('list_menus');">{tr}use menu{/tr}</a>
  </td>
</tr>

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
    <a class="link" href="javascript:setUserModuleFromCombo('list_banners');">{tr}use banner zone{/tr}</a>
  </td>
</tr>
</table>
</td></tr></table>