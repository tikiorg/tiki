<h1>Featured links</h1>
<a class="link" href="tiki-admin_links.php?generate=1">Generate positions by hits</a>
<h3>List of featured links</h3>
<table border="1" cellpadding="0" cellspacing="0" width="90%">
<tr>
<td class="heading">{tr}url{/tr}</td>
<td class="heading">{tr}title{/tr}</td>
<td class="heading">{tr}hits{/tr}</td>
<td class="heading">{tr}position{/tr}</td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$links}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$links[user].url}</td>
<td class="odd">{$links[user].title}</td>
<td class="odd">{$links[user].hits}</td>
<td class="odd">{$links[user].position}</td>
<td class="odd"><a class="link" href="tiki-admin_links.php?remove={$links[user].url}">{tr}delete{/tr}</a>
<a class="link" href="tiki-admin_links.php?editurl={$links[user].url}">{tr}edit{/tr}</a>
             </td>
</tr>
{else}
<tr>
<td class="even">{$links[user].url}</td>
<td class="even">{$links[user].title}</td>
<td class="even">{$links[user].hits}</td>
<td class="even">{$links[user].position}</td>
<td class="even"><a class="link" href="tiki-admin_links.php?remove={$links[user].url}">{tr}delete{/tr}</a>
<a class="link" href="tiki-admin_links.php?editurl={$links[user].url}">{tr}edit{/tr}</a>
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
<h3><a class="pagetitle" href="tiki-admin_links.php">Add Featured Link</a></h3>
<form action="tiki-admin_links.php" method="post">
<table>
{if $editurl eq 'n'}
<tr><td>URL</td><td><input type="text" name="url" /></td></tr>
{else}
<tr><td>URL</td><td>{$editurl}
<input type="hidden" name="url" value="{$editurl}" />
</td></tr>
{/if}
<tr><td>Title</td><td><input type="text" name="title" value="{$title}" /></td></tr>
<tr><td>Position</td><td><input type="text" size="3" name="position" value="{$position}" /> (0 {tr}disables the link{/tr})</td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="add" value="add" /></td></tr>
</table>
</form>
