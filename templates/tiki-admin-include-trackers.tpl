<div class="cbox">
<div class="cbox-title">{tr}Trackers{/tr}</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php?page=trackers" method="post">
<table class="admin">
<tr><td class="form">{tr}Use database to store files{/tr}:</td><td><input type="radio" name="t_use_db" value="y" {if $t_use_db eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Use a directory to store files{/tr}:</td><td><input type="radio" name="t_use_db" value="n" {if $t_use_db eq 'n'}checked="checked"{/if}/> {tr}Path{/tr}:<br /><input type="text" name="t_use_dir" value="{$t_use_dir|escape}" size="50" /> </td></tr>
<tr><td colspan="2" class="button"><input type="submit" name="trkset" value="{tr}Change preferences{/tr}" /></td></tr>    
</table>
</form>
</div>
</div>
</div>

<br />
<div class="admin">
Attachements
<form action="tiki-admin.php?page=trackers" method="post">
<input type="text" name="find" value="{$find|escape}">
<input type="submit" name="action" value="{tr}find{/tr}">
</form>
{cycle values="odd,even" print=false}
<table class="normal">
<tr>
<td class="heading"><a href="tiki-admin.php?page=trackers&amp;sort_mode=user_{if $sort_mode eq 'user'}asc{else}desc{/if}" class="heading">{tr}User{/tr}</td>
<td class="heading"><a href="tiki-admin.php?page=trackers&amp;sort_mode=filename_{if $sort_mode eq 'filename'}asc{else}desc{/if}" class="heading">{tr}Name{/tr}</td>
<td class="heading"><a href="tiki-admin.php?page=trackers&amp;sort_mode=filesize_{if $sort_mode eq 'filesize'}asc{else}desc{/if}" class="heading">{tr}Size{/tr}</td>
<td class="heading"><a href="tiki-admin.php?page=trackers&amp;sort_mode=filetype_{if $sort_mode eq 'filetype'}asc{else}desc{/if}" class="heading">{tr}Type{/tr}</td>
<td class="heading"><a href="tiki-admin.php?page=trackers&amp;sort_mode=downloads_{if $sort_mode eq 'downloads'}asc{else}desc{/if}" class="heading">{tr}dls{/tr}</td>
<td class="heading"><a href="tiki-admin.php?page=trackers&amp;sort_mode=itemId_{if $sort_mode eq 'itemId'}asc{else}desc{/if}" class="heading">{tr}Item{/tr}</td>
<td class="heading"><a href="tiki-admin.php?page=trackers&amp;sort_mode=path_{if $sort_mode eq 'path'}asc{else}desc{/if}" class="heading">{tr}Storage{/tr}</td>
<td class="heading"><a href="tiki-admin.php?page=trackers&amp;sort_mode=created_{if $sort_mode eq 'created'}asc{else}desc{/if}" class="heading">{tr}Created{/tr}</td>
<td class="heading">&nbsp;</td>
</tr>
{section name=x loop=$attachements}
<tr class={cycle}>
<td>{$attachements[x].user}</td>
<td>{$attachements[x].filename}</td>
<td>{$attachements[x].filesize|kbsize}</td>
<td>{$attachements[x].filetype}</td>
<td>{$attachements[x].downloads}</td>
<td>{$attachements[x].itemId}</td>
<td>{if $attachements[x].path}file{else}db{/if}</td>
<td>{$attachements[x].created|tiki_short_date}</td>
<td><a href="tiki-admin.php?page=trackers&amp;attId={$attachements[x].attId}&amp;action={if $attachements[x].path}move2db{else}move2file{/if}">{tr}change{/tr}</a></td>
</tr>
{/section}
</table>
{include file=tiki-pagination.tpl}
</div>
