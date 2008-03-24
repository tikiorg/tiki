<div class="cbox">
  <div class="cbox-title">{tr}Wiki attachments{/tr}</div>
  <div class="cbox-data">
<div class="admin">
<form action="tiki-admin.php?page=wikiatt" method="post">
<input type="text" name="find" value="{$find|escape}" />
<input type="submit" name="action" value="{tr}Find{/tr}">
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
<td class="heading"><a href="tiki-admin.php?page=wikiatt&amp;sort_mode=hits_{if $sort_mode eq 'hits'}asc{else}desc{/if}" class="heading">&gt;</td>
<td class="heading">&nbsp;</td>
</tr>
{section name=x loop=$attachements}
<tr class={cycle}>
<td>{$attachements[x].user}</td>
<td>{$attachements[x].page}</td>
<td><a href="tiki-download_wiki_attachment.php?attId={$attachements[x].attId}">{$attachements[x].filename}</a></td>
<td>{$attachements[x].filesize|kbsize}</td>
<td>{$attachements[x].filetype}</td>
<td>{if $attachements[x].path}file{else}db{/if}</td>
<td>{$attachements[x].created|tiki_short_date}</td>
<td>{$attachements[x].hits}</td>
<td><a href="tiki-admin.php?page=wikiatt&amp;attId={$attachements[x].attId}&amp;action={if $attachements[x].path}move2db{else}move2file{/if}">{tr}Change{/tr}</a></td>
</tr>
{/section}
</table>
{include file=tiki-pagination.tpl}
</div>
<table><tr><td>
<form action="tiki-admin.php?page=wikiatt" method="post">
<input type="hidden" name="all2db" value="1" />
<input type="submit" name="action" value="{tr}Change all to db{/tr}">
</form>
</td><td>
<form action="tiki-admin.php?page=wikiatt" method="post">
<input type="hidden" name="all2file" value="1" />
<input type="submit" name="action" value="{tr}Change all to file{/tr}">
</form>
</td></tr></table>
</div>
</div>
