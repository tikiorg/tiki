<h1>Featured links</h1>
<h3>List of featured links</h3>
<table border="1" cellpadding="0" cellspacing="0" width="90%">
<tr>
<td class="heading">{tr}url{/tr}</td>
<td class="heading">{tr}title{/tr}</td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$links}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$links[user].url}</td>
<td class="odd">{$links[user].title}</td>
<td class="odd"><a class="link" href="tiki-admin_links.php?remove={$links[user].url}">{tr}delete{/tr}</a>
             </td>
</tr>
{else}
<tr>
<td class="even">{$links[user].url}</td>
<td class="even">{$links[user].title}</td>
<td class="even"><a class="link" href="tiki-admin_links.php?remove={$links[user].url}">{tr}delete{/tr}</a>
             </td>
</tr>
{/if}
{sectionelse}
<tr><td colspan="2">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br/>
<h3>Add Featured Link</h3>
<form action="tiki-admin_links.php" method="post">
<table>
<tr><td>URL</td><td><input type="text" name="url" /></td></tr>
<tr><td>Title</td><td><input type="text" name="title" /></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="add" value="add" /></td></tr>
</table>
</form>
