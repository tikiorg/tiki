<a class="pagetitle" href="tiki-backup.php">Backups</a><br/><br/>
<h2>{tr}List of available backups{/tr}</h2>

<table class="normal">
<tr>
<td class="heading">{tr}Filename{/tr}</td>
<td class="heading">{tr}Created{/tr}</td>
<td class="heading">{tr}Size{/tr}</td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$backups}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd"><a class="link" href="backups/{$backups[user].filename}">{$backups[user].filename|truncate:20:"(...)":true}</a></td>
<td class="odd">{$backups[user].created|tiki_short_datetime}</td>
<td class="odd">{$backups[user].size|string_format:"%.2f"} Mb</td>
<td class="odd">
   <a class="link" href="tiki-backup.php?remove={$backups[user].filename}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-backup.php?restore={$backups[user].filename}">{tr}restore{/tr}</a>
</td>
</tr>
{else}
<tr>
<td class="even"><a class="link" href="backups/{$backups[user].filename}">{$backups[user].filename|truncate:20:"(...)":true}</a></td>
<td class="even">{$backups[user].created|tiki_short_datetime}</td>
<td class="even">{$backups[user].size|string_format:"%.2f"} Mb</td>
<td class="even">
   <a class="link" href="tiki-backup.php?remove={$backups[user].filename}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-backup.php?restore={$backups[user].filename}">{tr}restore{/tr}</a>
</td>
</tr>
{/if}
{/section}
</table>
<br/>
{if $restore eq 'y'}
<div class="simplebox">
<strong>{tr}Restoring a backup{/tr}</strong><br/>
<small><strong>{tr}Warning!{/tr}: </strong><i>{tr}Restoring a backup destoys all the data in your Tiki database. All your tables will be replaced with the information in the backup.{/tr}</i></small><br/><br/>
<a href="tiki-backup.php?rrestore={$restorefile}" class="link">{tr}Click here to confirm restoring{/tr}</a>
</div>
<br/>
{/if}
<div class="simplebox">
<strong>{tr}Create new backup{/tr}</strong><br/>
<small><i>{tr}Creating backups may take a long time. If the process is not completed you will see a blank screen. If so you need to increment the maximum script execution time from your php.ini file{/tr}</i></small><br/><br/>
<a href="tiki-backup.php?generate=1" class="link">{tr}Click here to create a new backup{/tr}</a>
</div>
<br/>
<div class="simplebox">
<strong>{tr}Upload a backup{/tr}</strong><br/>
<form enctype="multipart/form-data" action="tiki-backup.php" method="post">
<table class="normalnoborder">
<tr><td class="form">{tr}Upload backup{/tr}:</td><td class="form">
<input type="hidden" name="MAX_FILE_SIZE" value="10000000000">
<input name="userfile1" type="file"></td></tr>
<tr><td class="form">&nbsp;</td><td class="form"><input type="submit" name="upload" value="{tr}upload{/tr}" /></td></tr>
</table>
</form>
</div>
