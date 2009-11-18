{* $Id$ *}

{title help="System+Admin"}{tr}Tiki Cache/System Admin{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}If your Tiki is acting weird, first thing to try is to clear your cache below. Also very important is to clear your cache after an upgrade (by FTP/SSH when needed).{/tr}{/remarksbox}

<h2>{tr}Exterminator of cached content{/tr}</h2>
{cycle values="even,odd" print=false}
<table class="normal">
	<tr>
		<th>{tr}Directory to exterminate{/tr}</th>
		<th>{tr}Files{/tr}/{tr}Size{/tr}</th>
		<th>{tr}Action{/tr}</th>
	</tr>
	<tr class="{cycle advance=false}">
		<td colspan="2"><b>{tr}Clear all Tiki caches{/tr}</b></td>
		<td><a href="tiki-admin_system.php?do=all" class="link" title="{tr}Empty{/tr}">{icon _id=img/icons/del.gif alt="{tr}Empty{/tr}"}</a></td>
	</tr>
	<tr class="{cycle advance=false}">
		<td><b>./templates_c/</b></td>
		<td>({$templates_c.cant} {tr}Files{/tr} / {$templates_c.total|kbsize|default:'0 Kb'})</td>
		<td><a href="tiki-admin_system.php?do=templates_c" class="link" title="{tr}Empty{/tr}">{icon _id=img/icons/del.gif alt="{tr}Empty{/tr}"}</a></td>
	</tr>
	<tr class="{cycle advance=false}">
		<td><b>./modules/cache/</b></td>
		<td>({$modules.cant} {tr}Files{/tr} / {$modules.total|kbsize|default:'0 Kb'})</td>
		<td><a href="tiki-admin_system.php?do=modules_cache" class="link" title="{tr}Empty{/tr}">{icon _id=img/icons/del.gif alt="{tr}Empty{/tr}"}</a></td>
	</tr>
	<tr class="{cycle advance=false}">
		<td><b>./temp/cache/</b></td>
		<td>({$tempcache.cant} {tr}Files{/tr} / {$tempcache.total|kbsize|default:'0 Kb'})</td>
		<td><a href="tiki-admin_system.php?do=temp_cache" class="link" title="{tr}Empty{/tr}">{icon _id=img/icons/del.gif alt="{tr}Empty{/tr}"}</a></td>
	</tr>
	<tr class="{cycle advance=false}">
		<td><b>./temp/public/</b></td>
		<td>({$temppublic.cant} {tr}Files{/tr} / {$temppublic.total|kbsize|default:'0 Kb'})</td>
		<td><a href="tiki-admin_system.php?do=temp_public" class="link" title="{tr}Empty{/tr}">{icon _id=img/icons/del.gif alt="{tr}Empty{/tr}"}</a></td>
	</tr>
	<tr class="{cycle}">
		<td colspan="2"><b>{tr}All user prefs sessions{/tr}</b></td>
		<td><a href="tiki-admin_system.php?do=prefs" class="link" title="{tr}Empty{/tr}">{icon _id=img/icons/del.gif alt="{tr}Empty{/tr}"}</a></td>
	</tr>
</table>
<br />

{if count($dirs) && $tiki_p_admin eq 'y'}
	<h2>{tr}Directories to save{/tr}</h2>
	<form  method="post" action="{$smarty.server.PHP_SELF}">
		<label>{tr}Full Path to the Zip File:{/tr}<input type="text" name="zipPath" value="{$zipPath|escape}" />
		<input type="submit" name="zip" value="{tr}Generate a zip of those directories{/tr}" /></label>
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

{if $tiki_p_admin eq 'y'}
	{remarksbox type="warning" title="{tr}Advanced feature{/tr}"}
		{tr}Fix UTF-8 Errors in Tables{/tr} <a href="javascript:flip('fixutf8')">: {tr}Show{/tr}/{tr}Hide{/tr}</a>
		<div id="fixutf8" {if $advanced_features ne 'y'}style="display:none;"{else}style="display:block;"{/if}>
			<h2>{tr}Fix UTF-8 Errors in Tables{/tr}</h2>
			<table class="normal">
				<tr>
					<td>{tr}Warning: Make a backup of your Database before using this function!{/tr}</td>
				</tr>
				<tr>
					<td colspan="4">{tr}Warning: If you try to convert large tables, raise the maximum execution time in your php.ini!{/tr}</td>
				</tr>
				<tr>
					<td colspan="4">{tr}This function converts ISO-8859-1 encoded strings in your tables to UTF-8{/tr}</td>
				</tr>
				<tr>
					<td colspan="4">{tr}This may be necessary if you created content with tiki &lt; 1.8.4 and Default Charset settings in apache set to ISO-8859-1{/tr}</td>
				</tr>
				<tr>
					<td colspan="4">&nbsp;</td>
				</tr>
				{if isset($utf8it)}
					<tr>
						<td>{$utf8it}</td>
						<td>{$utf8if}</td>
							<td colspan="2">{$investigate_utf8}</td>
					</tr>
				{/if}
				{if isset($utf8ft)}
					<tr>
						<td>{$utf8ft}</td>
							<td>{$utf8ff}</td>
						<td colspan="2">{$errc} {tr}UTF-8 Errors fixed{/tr}</td>
					</tr>
				{/if}
			</table>

			<table class="sortable" id="tablefix" width="100%">
				<tr>
					<th>{tr}Table{/tr}</th>
					<th>{tr}Field{/tr}</th>
					<th>{tr}Investigate{/tr}</th>
					<th>{tr}Fix it{/tr}</th>
				</tr>
				{cycle values="even,odd" print=false}
				{foreach key=key item=item from=$tabfields}
					<tr class="{cycle}">
						<td>{$item.table}</td>
							<td>{$item.field}</td>
						<td>
							<a href="tiki-admin_system.php?utf8it={$item.table}&amp;utf8if={$item.field}" class="link">{tr}Investigate{/tr}</a>
						</td>
						<td>
							<a href="tiki-admin_system.php?utf8ft={$item.table}&amp;utf8ff={$item.field}" class="link">{tr}Fix it{/tr}</a>
						</td>
					</tr>
				{/foreach}
			</table>
		</div>
	{/remarksbox}
{/if}
