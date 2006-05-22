<h1><a class="pagetitle" href="tiki-backup.php">{tr}Backups{/tr}</a>
  
      {if $feature_help eq 'y'}
<a href="{$helpurl}Backups" target="tikihelp" class="tikihelp" title="{tr}admin backups{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>{/if}

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-backup.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin admin tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}edit tpl{/tr}' /></a>{/if}</h1>

<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}Use of this feature is NOT recommended. Please use phpMyAdmin or mysqldump instead.{/tr}</div>
</div>
<br /> 

<h2>{tr}List of available backups{/tr}</h2>

<table class="normal">
<tr>
<td class="heading">{tr}Filename{/tr}</td>
<td class="heading">{tr}Created{/tr}</td>
<td class="heading">{tr}Size{/tr}</td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$backups}
<tr>
<td class="{cycle advance=false}"><a class="link" href="backups/{if $tikidomain}{$tikidomain}/{/if}{$backups[user].filename}" title="{$backups[user].filename}">{$backups[user].filename|truncate:20:"...":true}</a></td>
<td class="{cycle advance=false}">{$backups[user].created|tiki_short_datetime}</td>
<td class="{cycle advance=false}">{$backups[user].size|string_format:"%.2f"} Mb</td>
<td class="{cycle}">
   <a class="link" href="tiki-backup.php?remove={$backups[user].filename}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-backup.php?restore={$backups[user].filename}">{tr}restore{/tr}</a>
</td>
</tr>
{/section}
</table>
<br />
{if $restore eq 'y'}
<div class="simplebox">
<strong>{tr}Restoring a backup{/tr}</strong>
<br />
<strong>{tr}Warning!{/tr}: </strong>
<i>{tr}Restoring a backup destoys all the data in your Tiki database. All your tables will be replaced with the information in the backup.{/tr}</i>
<br /><br />
<a href="tiki-backup.php?rrestore={$restorefile}" class="link">{tr}Click here to confirm restoring{/tr}</a>
</div>
<br />
{/if}
<div class="simplebox">
<strong>{tr}Create new backup{/tr}</strong><br />
<i>{tr}Creating backups may take a long time. If the process is not completed you will see a blank screen. If so you need to increment the maximum script execution time from your php.ini file{/tr}</i><br />
{if $gal_use_db eq "n"}
<br />
{tr}Your image gallery storage is set to "{$gal_use_dir}", you will need to backup this using FTP or SCP.{/tr}<br />
{/if}
{if $fgal_use_db eq "n"}
<br />
{tr}Your file gallery storage is set to "{$fgal_use_dir}", you will need to backup this using FTP or SCP.{/tr}<br />
{/if}
<br />
{tr}If any of your forums have attachments stored in the directory you will need to backup these using FTP or SCP.{/tr}<br />
<br /><br />
<a href="tiki-backup.php?generate=1" class="linkbut">{tr}Create new backup{/tr}</a>
</div>
<br />
<div class="simplebox">
<strong>{tr}Upload a backup{/tr}</strong><br />
<form enctype="multipart/form-data" action="tiki-backup.php" method="post">
<table class="normalnoborder">
<tr><td class="form">{tr}Upload backup{/tr}:</td><td class="form">
<input type="hidden" name="MAX_FILE_SIZE" value="10000000000" />
<input name="userfile1" type="file" /></td></tr>
<tr><td class="form">&nbsp;</td><td class="form"><input type="submit" name="upload" value="{tr}upload{/tr}" /></td></tr>
</table>
</form>
</div>
