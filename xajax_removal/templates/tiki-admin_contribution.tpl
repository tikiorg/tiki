{* $Id$ *}

{title help="Contribution"}{tr}Admin Contributions{/tr}{/title}

{if $contribution}
	<h2>{tr}Edit the contribution:{/tr} {$contribution.name|escape}</h2>
	<form enctype="multipart/form-data" action="tiki-admin_contribution.php" method="post">
		<input type="hidden" name="contributionId" value="{$contribution.contributionId}" />
		<table class="formcolor">
			<tr>
				<td>{tr}Name{/tr}</td>
				<td>
					<input type="text" name="name"{if $contribution.name} value="{$contribution.name|escape}"{/if} />
				</td>
			</tr>
			<tr>
				<td>{tr}Description{/tr}</td>
				<td>
					<input type="text" name="description" size="80" maxlength="250"{if $contribution.description} value="{$contribution.description|escape}"{/if} />
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="submit" name="replace" value="{tr}Save{/tr}" />
				</td>
			</tr>
		</table>
	</form>
{/if}

<h2>{tr}Settings{/tr}</h2>
<form action="tiki-admin_contribution.php?page=features" method="post">
	{cycle print=false values="even,odd"}
	<table class="formcolor">
		<tr class="{cycle}">
			<td>
				<input type="checkbox" name="feature_contribution_mandatory" {if $prefs.feature_contribution_mandatory eq 'y'}checked="checked"{/if}/>
			</td>
			<td>
				{tr}Contributions are mandatory in wiki pages{/tr}
			</td>
		</tr>
		<tr class="{cycle}">
			<td>
				<input type="checkbox" name="feature_contribution_mandatory_forum" {if $prefs.feature_contribution_mandatory_forum eq 'y'}checked="checked"{/if}/>
			</td>
			<td>
				{tr}Contributions are mandatory in forums{/tr}
			</td>
		</tr>
		<tr class="{cycle}">
			<td>
				<input type="checkbox" name="feature_contribution_mandatory_comment" {if $prefs.feature_contribution_mandatory_comment eq 'y'}checked="checked"{/if}/>
			</td>
			<td>
				{tr}Contributions are mandatory in comments{/tr}
			</td>
		</tr>
		<tr class="{cycle}">
			<td>
				<input type="checkbox" name="feature_contribution_mandatory_blog" {if $prefs.feature_contribution_mandatory_blog eq 'y'}checked="checked"{/if}/>
			</td>
			<td>
				{tr}Contributions are mandatory in blogs{/tr}
			</td>
		</tr>
		<tr class="{cycle}">
			<td>
				<input type="checkbox" name="feature_contribution_display_in_comment" {if $prefs.feature_contribution_display_in_comment eq 'y'}checked="checked"{/if}/>
			</td>
			<td>
				{tr}Contributions are displayed in the comment/post{/tr}
			</td>
		</tr>
		<tr class="{cycle}">
			<td>
				<input type="checkbox" name="feature_contributor_wiki" {if $prefs.feature_contributor_wiki eq 'y'}checked="checked"{/if}/>
			</td>
			<td>{tr}Contributors{/tr}
			</td>
		</tr>
		<tr class="{cycle}">
			<td>&nbsp;</td>
			<td>
				<input type="submit" name="setting" value="{tr}Save{/tr}" />
			</td>
		</tr>
	</table>
</form>


<h2>{tr}Create a new contribution{/tr}</h2>

<form enctype="multipart/form-data" action="tiki-admin_contribution.php" method="post">
	<table class="formcolor">
		<tr>
			<td>{tr}Name{/tr}</td>
			<td><input type="text" name="name" /></td>
		</tr>
		<tr>
			<td>{tr}Description{/tr}</td>
			<td>
				<input type="text" name="description" size="80" maxlength="250" />
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="submit" name="add" value="{tr}Add{/tr}" />
			</td>
		</tr>
	</table>
</form>

<h2>{tr}List of contributions{/tr}</h2>
<table class="normal">
	<tr>
		<th>{tr}Name{/tr}</th>
		<th>{tr}Description{/tr}</th>
		<th>{tr}Actions{/tr}</th>
	</tr>
	{cycle print=false values="even,odd"}
	{section name=ix loop=$contributions}
		<tr class="{cycle}">
			<td class="text">{$contributions[ix].name}</td>
			<td class="text">{$contributions[ix].description|truncate|escape}</td>
			<td class="action">
				<a class="link" href="tiki-admin_contribution.php?contributionId={$contributions[ix].contributionId}">{icon _id='shape_square_edit'}</a> &nbsp;
				<a class="link" href="tiki-admin_contribution.php?remove={$contributions[ix].contributionId}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
			</td>
		</tr>
	{sectionelse}
		<tr class="even"><td colspan="3" class="norecords">{tr}No records found{/tr}</td></tr>
	{/section}
</table>
