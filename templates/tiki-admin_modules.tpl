<h1><a class="wiki" href="tiki-admin_modules.php">{tr}Admin Modules{/tr}</a></h1>
<a class="link" href="#assign">{tr}assign module{/tr}</a>
<a class="link" href="#leftmod">{tr}left modules{/tr}</a>
<a class="link" href="#rightmod">{tr}right modules{/tr}</a>
<a class="link" href="#editcreate">{tr}edit/create{/tr}</a>
<a class="link" href="tiki-admin_modules.php?clear_cache=1">{tr}clear cache{/tr}</a>
<h3>{tr}User Modules{/tr}</h3>

<table border="1" cellpadding="0" cellspacing="0" width="90%">
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
<td class="even"><a class="link" href="tiki-admin_modules.php?action=remove&amp;module={$user_modules[user].name}">{tr}delete{/tr}</a>
             <a class="link" href="tiki-admin_modules.php?action=edit&amp;modules={$user_modules[user].name}">{tr}edit{/tr}</a>
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
<h3>Assign module</h3>

<form method="post" action="tiki-admin_modules.php">
<table>
<tr><td>Module Name</td><td>
<select name="assign_name">
{section name=ix loop=$all_modules}
<option value="{$all_modules[ix]}" {if $assign_name eq $all_modules[ix]}selected="selected"{/if}>{$all_modules[ix]}</option>
{/section}
</select>
</td></tr>
<!--<tr><td>Title</td><td><input type="text" name="assign_title" value="{$assign_title}"></td></tr>-->
<tr><td>Position</td><td>
<select name="assign_position">
<option value="l" {if $assign_position eq 'l'}selected="selected"{/if}>left</option>
<option value="r" {if $assign_position eq 'r'}selected="selected"{/if}>right</option>
</select>
</td></tr>
<tr><td>Order</td><td>
<select name="assign_order">
{section name=ix loop=$orders}
<option value="{$orders[ix]}" {if $assign_order eq $orders[ix]}selected="selected"{/if}>{$orders[ix]}</option>
{/section}
</select>
</td></tr>
<tr><td>Cache Time(secs)</td><td><input type="text" name="assign_cache" value="{$assign_cache}" /></td></tr>
<tr><td>Rows</td><td><input type="text" name="assign_rows" value="{$assign_rows}" /></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="assign" value="assign"></td></tr>
</table>
</form>
<br/>
<h3>Assigned Modules</h3>
<a name="leftmod"></a>
<h3>Left Modules</h3>

<table border="1" cellpadding="0" cellspacing="0" width="90%">
<tr>
<td class="heading">{tr}name{/tr}</td>
<!--<td class="heading">{tr}title{/tr}</td>-->
<td class="heading">{tr}order{/tr}</td>
<td class="heading">{tr}cache{/tr}</td>
<td class="heading">{tr}rows{/tr}</td>
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
<h3>Right Modules</h3>

<table border="1" cellpadding="0" cellspacing="0" width="90%">
<tr>
<td class="heading">{tr}name{/tr}</td>
<!--<td class="heading">{tr}title{/tr}</td>-->
<td class="heading">{tr}order{/tr}</td>
<td class="heading">{tr}cache{/tr}</td>
<td class="heading">{tr}rows{/tr}</td>
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
<h3>Edit/Create user module</h3>
<form method="post" action="tiki-admin_modules.php">
<table>
<tr><td>Name</td><td><input type="text" name="um_name" value="{$um_name}" /></td></tr>
<tr><td>Title</td><td><input type="text" name="um_title" value="{$um_title}" /></td></tr>
<tr><td>Data</td><td><textarea name="um_data" rows="10" cols="40">{$um_data}</textarea></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="um_update" value="{tr}create/edit{/tr}" /></td></tr>
</table>
</form>
