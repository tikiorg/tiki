{* $Id$ *}

{title help="System+Admin"}{tr}Tiki Cache/System Admin{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}If your Tiki is acting weird, first thing to try is to clear your cache below. Also very important is to clear your cache after an upgrade (by FTP/SSH when needed).{/tr} {tr}Also see "Rebuild Index" in the <a href="tiki-admin.php?page=search">Search Admin Panel</a>{/tr}
{/remarksbox}

<h2>{tr}Exterminator of cached content{/tr}</h2>
{cycle values="even,odd" print=false}
<table class="normal">
	<tr>
		<th>{tr}Directory to exterminate{/tr}</th>
		<th>{tr}Files{/tr}/{tr}Size{/tr}</th>
		<th>{tr}Action{/tr}</th>
	</tr>
	<tr class="{cycle}">
		<td colspan="2"><b>{tr}Clear all Tiki caches{/tr}</b></td>
		<td><a href="tiki-admin_system.php?do=all" class="link" title="{tr}Empty{/tr}">{icon _id='img/icons/del.gif' alt="{tr}Empty{/tr}"}</a></td>
	</tr>
	<tr class="{cycle}">
		<td><b>./templates_c/</b></td>
		<td>({$templates_c.cant} {tr}Files{/tr} / {$templates_c.total|kbsize|default:'0 Kb'})</td>
		<td><a href="tiki-admin_system.php?do=templates_c" class="link" title="{tr}Empty{/tr}">{icon _id='img/icons/del.gif' alt="{tr}Empty{/tr}"}</a></td>
	</tr>
	<tr class="{cycle}">
		<td><b>./modules/cache/</b></td>
		<td>({$modules.cant} {tr}Files{/tr} / {$modules.total|kbsize|default:'0 Kb'})</td>
		<td><a href="tiki-admin_system.php?do=modules_cache" class="link" title="{tr}Empty{/tr}">{icon _id='img/icons/del.gif' alt="{tr}Empty{/tr}"}</a></td>
	</tr>
	<tr class="{cycle}">
		<td><b>./temp/cache/</b></td>
		<td>({$tempcache.cant} {tr}Files{/tr} / {$tempcache.total|kbsize|default:'0 Kb'})</td>
		<td><a href="tiki-admin_system.php?do=temp_cache" class="link" title="{tr}Empty{/tr}">{icon _id='img/icons/del.gif' alt="{tr}Empty{/tr}"}</a></td>
	</tr>
	<tr class="{cycle}">
		<td><b>./temp/public/</b></td>
		<td>({$temppublic.cant} {tr}Files{/tr} / {$temppublic.total|kbsize|default:'0 Kb'})</td>
		<td><a href="tiki-admin_system.php?do=temp_public" class="link" title="{tr}Empty{/tr}">{icon _id='img/icons/del.gif' alt="{tr}Empty{/tr}"}</a></td>
	</tr>
	<tr class="{cycle}">
		<td colspan="2"><b>{tr}All user prefs sessions{/tr}</b></td>
		<td><a href="tiki-admin_system.php?do=prefs" class="link" title="{tr}Empty{/tr}">{icon _id='img/icons/del.gif' alt="{tr}Empty{/tr}"}</a></td>
	</tr>
</table>
<br />

{if count($dirs) && $tiki_p_admin eq 'y'}
	<h2>{tr}Directories to save{/tr}</h2>
	<form  method="post" action="{$smarty.server.PHP_SELF}">
		<p><label>{tr}Full Path to the Zip File:{/tr}<input type="text" name="zipPath" value="{$zipPath|escape}" /></label>
		<input type="submit" name="zip" value="{tr}Generate a zip of those directories{/tr}" /></p>
		{if $zipPath}
			<div class="simplebox highlight">{tr}A zip has been written to {$zipPath}{/tr}</div>
		{/if}
	</form>
	<ul>
		{foreach from=$dirs item=d key=k}
			<li>{$d|escape}{if !$dirsWritable[$k]} <i>({tr}Directory is not writeable{/tr})</i>{/if}</li>
		{/foreach}
	</ul>
{/if}
