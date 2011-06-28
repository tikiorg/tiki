{title help="Admin+DSN"}{tr}Admin DSN{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Use Admin DSN to define the database to be used by the SQL plugin.{/tr}
{/remarksbox}

<h2>{tr}Create/edit DSN{/tr}</h2>
<form action="tiki-admin_dsn.php" method="post">
	<input type="hidden" name="dsnId" value="{$dsnId|escape}" />
	<table class="formcolor">
		<tr>
		<td>{tr}Name{/tr}:</td>
		 <td>
			<input type="text" maxlength="255" size="10" name="name" value="{$info.name|escape}" />
			</td>
		</tr>
		<tr>
			<td>{tr}DSN{/tr}:</td>
			<td>
				<input type="text" maxlength="255" size="40" name="dsn" value="{$info.dsn|escape}" />
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="submit" name="save" value="{tr}Save{/tr}" />
			</td>
		</tr>
	</table>
</form>

<h2>{tr}DSN{/tr}</h2>
<table class="normal">
	<tr>
		<th>
			<a href="tiki-admin_dsn.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a>
		</th>
		<th>
			<a href="tiki-admin_dsn.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'dsn_desc'}dsn_asc{else}dsn_desc{/if}">{tr}DSN{/tr}</a>
		</th>
		<th>{tr}Action{/tr}</th>
	</tr>
	{cycle values="odd,even" print=false}
	<tr class="{cycle}">
		<td>{tr}Local (Tiki database){/tr}</td>
		<td>{tr}See db/local.php{/tr}</td>
		<td>
			&nbsp;&nbsp;
			<a class="link" href="tiki-objectpermissions.php?objectName=local&amp;objectType=dsn&amp;permType=dsn&amp;objectId=local">{icon _id='key' alt="{tr}Perms{/tr}"}</a>
		</td>
	</tr>
	{section name=user loop=$channels}
		<tr class="{cycle}">
			<td>{$channels[user].name}</td>
			<td>{$channels[user].dsn}</td>
			<td>
				&nbsp;&nbsp;
				<a title="{tr}Edit{/tr}" class="link" href="tiki-admin_dsn.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;dsnId={$channels[user].dsnId}">{icon _id='page_edit'}</a> &nbsp;
				<a title="{tr}Delete{/tr}" class="link" href="tiki-admin_dsn.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].dsnId}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
				<a class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=dsn&amp;permType=dsn&amp;objectId={$channels[user].name|escape:"url"}">{icon _id='key' alt="{tr}Perms{/tr}"}</a>
			</td>
		</tr>
	{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
