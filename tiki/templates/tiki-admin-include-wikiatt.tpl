<div class="simplebox">
{tr}Wiki attachments{/tr}
<form method="post" action="tiki-admin.php?page=wikiatt">
<table class="admin">
<tr><td class="form">{tr}Wiki attachments{/tr}:</td><td><input type="checkbox" name="feature_wiki_attachments" {if $feature_wiki_attachments eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Use database to store files{/tr}:</td><td><input type="radio" name="w_use_db" value="y" {if $w_use_db eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Use a directory to store files{/tr}:</td><td class="form"><input type="radio" name="w_use_db" value="n" {if $w_use_db eq 'n'}checked="checked"{/if}/> {tr}Path{/tr}:<input type="text" name="w_use_dir" value="{$w_use_dir|escape}" /> </td></tr>
<tr><td colspan="2" class="button"><input type="submit" name="wikiattprefs" value="{tr}Change preferences{/tr}" /></td></tr>
</table>
</form>
</div>

<br />
<div class="admin">
{tr}Attachements{/tr}
<form action="tiki-admin.php?page=wikiatt" method="post">
<input type="text" name="find" value="{$find|escape}">
<input type="submit" name="action" value="{tr}find{/tr}">
</form>
{cycle values="odd,even" print=false}
<table class="normal">
<tr>
<td class="heading"><a href="tiki-admin.php?page=wikiatt&amp;sort_mode=user_{if $sort_mode eq 'user'}asc{else}desc{/if}" class="heading">{tr}User{/tr}</td>
<td class="heading"><a href="tiki-admin.php?page=wikiatt&amp;sort_mode=page_{if $sort_mode eq 'page'}asc{else}desc{/if}" class="heading">{tr}Page{/tr}</td>
<td class="heading"><a href="tiki-admin.php?page=wikiatt&amp;sort_mode=filename_{if $sort_mode eq 'filename'}asc{else}desc{/if}" class="heading">{tr}Name{/tr}</td>
<td class="heading"><a href="tiki-admin.php?page=wikiatt&amp;sort_mode=filesize_{if $sort_mode eq 'filesize'}asc{else}desc{/if}" class="heading">{tr}Size{/tr}</td>
<td class="heading"><a href="tiki-admin.php?page=wikiatt&amp;sort_mode=filetype_{if $sort_mode eq 'filetype'}asc{else}desc{/if}" class="heading">{tr}Type{/tr}</td>
<td class="heading"><a href="tiki-admin.php?page=wikiatt&amp;sort_mode=path_{if $sort_mode eq 'path'}asc{else}desc{/if}" class="heading">{tr}Storage{/tr}</td>
<td class="heading"><a href="tiki-admin.php?page=wikiatt&amp;sort_mode=created_{if $sort_mode eq 'created'}asc{else}desc{/if}" class="heading">{tr}Created{/tr}</td>
<td class="heading"><a href="tiki-admin.php?page=wikiatt&amp;sort_mode=downloads_{if $sort_mode eq 'downloads'}asc{else}desc{/if}" class="heading">&gt;</td>
<td class="heading">&nbsp;</td>
</tr>
{section name=x loop=$attachements}
<tr class={cycle}>
<td>{$attachements[x].user}</td>
<td>{$attachements[x].page}</td>
<td>{$attachements[x].filename}</td>
<td>{$attachements[x].filesize|kbsize}</td>
<td>{$attachements[x].filetype}</td>
<td>{if $attachements[x].path}file{else}db{/if}</td>
<td>{$attachements[x].created|tiki_short_date}</td>
<td>{$attachements[x].downloads}</td>
<td><a href="tiki-admin.php?page=wikiatt&amp;attId={$attachements[x].attId}&amp;action={if $attachements[x].path}move2db{else}move2file{/if}">{tr}change{/tr}</a></td>
</tr>
{/section}
</table>
{include file=tiki-pagination.tpl}
</div>
<table><tr><td>
<form action="tiki-admin.php?page=wikiatt" method="post">
<input type="hidden" name="all2db" value="1">
<input type="submit" name="action" value="{tr}Change all to db{/tr}">
</form>
</td><td>
<form action="tiki-admin.php?page=wikiatt" method="post">
<input type="hidden" name="all2file" value="1">
<input type="submit" name="action" value="{tr}Change all to file{/tr}">
</form>
</td></tr></table>

