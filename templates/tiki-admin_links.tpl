<a href="tiki-admin_links.php" class="pagetitle">{tr}Featured links{/tr}</a><br/><br/>
<a class="link" href="tiki-admin_links.php?generate=1">{tr}Generate positions by hits{/tr}</a>
<h3>List of featured links</h3>
<table class="normal">
<tr>
<td class="heading">{tr}url{/tr}</td>
<td class="heading">{tr}title{/tr}</td>
<td class="heading">{tr}hits{/tr}</td>
<td class="heading">{tr}position{/tr}</td>
<td class="heading">{tr}type{/tr}</td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$links}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$links[user].url}</td>
<td class="odd">{$links[user].title}</td>
<td class="odd">{$links[user].hits}</td>
<td class="odd">{$links[user].position}</td>
<td class="odd">{$links[user].type}</td>
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
<td class="even">{$links[user].type}</td>
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
<h3>{tr}Add Featured Link{/tr}</h3>
<form action="tiki-admin_links.php" method="post">
<table class="normal">
{if $editurl eq 'n'}
<tr><td class="formcolor">URL</td><td class="formcolor"><input type="text" name="url" /></td></tr>
{else}
<tr><td class="formcolor">URL</td><td class="formcolor">{$editurl}
<input type="hidden" name="url" value="{$editurl}" />
</td></tr>
{/if}
<tr><td class="formcolor">{tr}Title{/tr}</td><td class="formcolor"><input type="text" name="title" value="{$title}" /></td></tr>
<tr><td class="formcolor">{tr}Position{/tr}</td><td class="formcolor"><input type="text" size="3" name="position" value="{$position}" /> (0 {tr}disables the link{/tr})</td></tr>
<tr><td class="formcolor">{tr}Link type{/tr}</td><td class="formcolor">
<select name="type">
<option value="r" {if $type eq 'r'}selected="selected"{/if}>{tr}replace current page{/tr}</option>
<option value="f" {if $type eq 'f'}selected="selected"{/if}>{tr}framed{/tr}</option>
<option value="n" {if $type eq 'n'}selected="selected"{/if}>{tr}open new window{/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="add" value="add" /></td></tr>
</table>
</form>
