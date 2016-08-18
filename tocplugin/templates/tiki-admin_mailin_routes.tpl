{* $Id$ *}
{title}{tr}Admin Mail-in routes{/tr}{/title}

{tabset name="user_mailin"}
	{tab name="{tr}Structure Routing{/tr}"}
		<h2>{tr}Structure Routing{/tr}</h2>
		<p>
		Routes are edited in the user's: My Account / Mail-in panel
		</p>
		{if $prefs.feature_wiki_structure eq 'y'}
			<table id="table_user_mailin_routing" class="table normal table-striped table-hover">
				<tr>
				<th>Username</th>
				<th>Email</th>
				<th>Subject pattern</th>
				<th>Body pattern</th>
				<th>Structure</th>
				<th>Parent page name</th>
				<th>Active</th>
				</tr>
				{foreach from=$userStructs item=ustruct name=mstruct}
					<tr>
					<td>{$ustruct.username}</td>
					<td>{$ustruct.email}</td>
					<td>{$ustruct.subj_pattern}</td>
					<td>{$ustruct.body_pattern}</td>
					<td>{$ustruct.structName}</td>
					<td>{$ustruct.pageName}</td>
					<td><input type="checkbox" disabled="disabled" {if $ustruct.is_active eq 'y'}checked="checked"{/if} />
					</td>
					</tr>
				{/foreach}
			</table>
		{else}
			<p>
				{tr}Wiki structures feature is not enabled{/tr}
			</p>
			<a href="tiki-admin.php?page=wiki&highlighted='feature_wiki_structure'">Go to wiki structure setting</a>
		{/if}

	{/tab}
{/tabset}
