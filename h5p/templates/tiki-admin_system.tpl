{* $Id$ *}

{title help="System Admin"}{tr}System Administration{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}If your Tiki is acting weird, first thing to try is to clear your cache below. Also very important is to clear your cache after an upgrade (by FTP/SSH when needed).{/tr} {tr}Also see "Rebuild Index" in the <a class="alert-link" href="tiki-admin.php?page=search">Search Control Panel</a>{/tr}
{/remarksbox}

<h2>{tr}Clear cached content{/tr}</h2>
<div class="text-center margin-bottom-md">
	<a href="tiki-admin_system.php?do=all" class="btn btn-primary" title="{tr}Empty{/tr}">{icon name="trash"} {tr}Clear all caches{/tr}</a>
</div>
<table class="table table-striped table-hover">
	<tr>
		<th>{tr}Directory{/tr}</th>
		<th>{tr}Files{/tr}/{tr}Size{/tr}</th>
		<th></th>
	</tr>
	<tr>
		<td><b>./templates_c/</b></td>
		<td>({$templates_c.cant} {tr}Files{/tr} / {$templates_c.total|kbsize|default:'0 Kb'})</td>
		<td><a href="tiki-admin_system.php?do=templates_c" class="tips" title=":{tr}Empty{/tr}">{icon name="trash"}</a></td>
	</tr>
	<tr>
		<td><b>./modules/cache/</b></td>
		<td>({$modules.cant} {tr}Files{/tr} / {$modules.total|kbsize|default:'0 Kb'})</td>
		<td><a href="tiki-admin_system.php?do=modules_cache" class="tips" title=":{tr}Empty{/tr}">{icon name="trash"}</a></td>
	</tr>
	<tr>
		<td><b>./temp/cache/</b></td>
		<td>({$tempcache.cant} {tr}Files{/tr} / {$tempcache.total|kbsize|default:'0 Kb'})</td>
		<td><a href="tiki-admin_system.php?do=temp_cache" class="tips" title=":{tr}Empty{/tr}">{icon name="trash"}</a></td>
	</tr>
	<tr>
		<td><b>./temp/public/</b></td>
		<td>({$temppublic.cant} {tr}Files{/tr} / {$temppublic.total|kbsize|default:'0 Kb'})</td>
		<td><a href="tiki-admin_system.php?do=temp_public" class="tips" title=":{tr}Empty{/tr}">{icon name="trash"}</a></td>
	</tr>
	<tr>
		<td colspan="2"><b>{tr}All user preference sessions{/tr}</b></td>
		<td><a href="tiki-admin_system.php?do=prefs" class="tips" title=":{tr}Empty{/tr}">{icon name="trash"}</a></td>
	</tr>
</table>
<br>

{if count($dirs) && $tiki_p_admin eq 'y'}
	<h2>{tr}Save directories{/tr}</h2>
	{remarksbox type="tip" title="{tr}Directories to save{/tr}" close="n"}
		<ul>
			{foreach from=$dirs item=d key=k}
				<li>{$d|escape}{if !$dirsWritable[$k]} <i>({tr}Directory is not writeable{/tr})</i>{/if}</li>
			{/foreach}
		</ul>
	{/remarksbox}
	<form method="post" action="{$smarty.server.PHP_SELF|escape}" role="form" class"form">
		<div class="input-group">
			<input type="text" name="zipPath" value="{$zipPath|escape}" class="form-control" placeholder="{tr}Full Path to the Zip File{/tr}">
			<span class="input-group-btn">
				<button type="submit" class="btn btn-primary" name="zip" title="{tr}ZIP{/tr}">{icon name="zip"} {tr}Generate zip{/tr}</button>
			</span>
		</div>
		{if $zipPath}
			<div class="alert alert-warning">{tr _0=$zipPath}A zip has been written to %0{/tr}</div>
		{/if}
	</form>
{/if}

{if !empty($lostGroups)}
	<h2>{tr}Clean{/tr}</h2>
	{tr}Groups still used in the database but no more defined.{/tr} {self_link clean="y"}{tr}Click to remove.{/tr}{/self_link}
	<ul>
	{foreach item=g from=$lostGroups}
		<li>{$g|escape}</li>
	{/foreach}
	</ul>
{/if}
