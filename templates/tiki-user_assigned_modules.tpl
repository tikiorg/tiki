<a class="pagetitle" href="tiki-user_assigned_modules.php">{tr}User assigned modules{/tr}</a><br/><br/>
[<a href="tiki-user_preferences.php" class="link">{tr}User Preferences{/tr}</a>
{if $feature_user_bookmarks eq 'y'}|<a href="tiki-user_bookmarks.php" class="link">{tr}User Bookmarks{/tr}</a>{/if}
{if $user_assigned_modules eq 'y'}|<a href="tiki-user_assigned_modules.php" class="link">{tr}Configure modules{/tr}</a>{/if}]<br/><br/>
<a class="link" href="tiki-user_assigned_modules.php?recreate=1">{tr}Restore defaults{/tr}</a><br/><br/>
<h2>User assigned modules</h2>
<table class="normal">
<tr>
  <td class="heading">{tr}name{/tr}</td>
  <td class="heading">{tr}order{/tr}</td>
  <td class="heading">{tr}column{/tr}</td>
  <td class="heading">{tr}action{/tr}</td>
</tr>
{assign var="what" value="l"}
{section name=ix loop=$modules}
<tr>
  {if $what eq 'l'}
    <td style="text-align:center;" class="even" colspan="4"><b>{tr}Left column{/tr}</b></td></tr><tr>
    {assign var="what" value="r"}
  {/if}
  {if $what eq 'r' and $modules[ix].position eq 'r'}
    <td style="text-align:center;" class="even" colspan="4"><b>{tr}Right column{/tr}</b></td></tr><tr>
    {assign var="what" value="x"}
  {/if}
  <td class="even">{$modules[ix].name}</td>
  <td class="even">{$modules[ix].ord}</td>
  <td class="even">{$modules[ix].position}</td>
  <td class="even">
  {if $modules[ix].name ne 'application_menu' and $modules[ix].name ne 'login_box'}
  <a class="link" href="tiki-user_assigned_modules.php?unassign={$modules[ix].name}">unassign</a>
  {/if}
  <a class="link" href="tiki-user_assigned_modules.php?up={$modules[ix].name}">up</a>
  <a class="link" href="tiki-user_assigned_modules.php?down={$modules[ix].name}">down</a>
  {if $modules[ix].position eq 'l'}
  <a class="link" href="tiki-user_assigned_modules.php?right={$modules[ix].name}">move</a>
  {else}
  <a class="link" href="tiki-user_assigned_modules.php?left={$modules[ix].name}">move</a>
  {/if}
  </td>
</tr>
{/section}  
</table>
{if $canassign eq 'y'}
<br/>
<form action="tiki-user_assigned_modules.php" method="post">
{tr}Assign module{/tr}
<table class="normal">
<tr><td class="formcolor">{tr}Module{/tr}:</td>
<td class="formcolor">
<select name="module">
{section name=ix loop=$assignables}
<option value="{$assignables[ix].name}">{$assignables[ix].name}</option>
{/section}
</select>
</td>
</tr>
<tr>
<td class="formcolor">{tr}Column{/tr}:</td>
<td class="formcolor">
<select name="position">
<option value="l">left</option>
<option value="r">right</option>
</select>
</td>
</tr>
<tr>
<td class="formcolor">{tr}Order{/tr}:</td>
<td class="formcolor">
<select name="order">
{section name=ix loop=$orders}
<option value="{$orders[ix]}">{$orders[ix]}</option>
{/section}
</select>
</td>
</tr>
<tr>
<td class="formcolor">&nbsp;</td>
<td class="formcolor"><input type="submit" name="assign" value="{tr}assign{/tr}" /></td>
</tr>
</table>
</form>
{/if}