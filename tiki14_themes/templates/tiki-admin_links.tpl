{title help="FeaturedLinksAdmin"}{tr}Featured Links{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To use these links, you must assign the featured_links <a class="rbox-link" href="tiki-admin_modules.php">module</a>.{/tr}{/remarksbox}

<div class="t_navbar">
	{button href="tiki-admin_links.php?generate=1" _text="{tr}Generate positions by hits{/tr}"}
</div>

<h2>{tr}List of featured links{/tr}</h2>
<div class="table-responsive">
<table class="table normal">
	<tr>
		<th>{tr}URL{/tr}</th>
		<th>{tr}Title{/tr}</th>
		<th>{tr}Hits{/tr}</th>
		<th>{tr}Position{/tr}</th>
		<th>{tr}Type{/tr}</th>
		<th>{tr}Action{/tr}</th>
	</tr>

	{section name=user loop=$links}
		<tr>
			<td class="text">{$links[user].url}</td>
			<td class="text">{$links[user].title|escape}</td>
			<td class="integer">{$links[user].hits}</td>
			<td class="id">{$links[user].position}</td>
			<td class="text">{$links[user].type}</td>
			<td class="action">
				<a title="{tr}Edit{/tr}" class="link" href="tiki-admin_links.php?editurl={$links[user].url|escape:"url"}">{icon _id='page_edit'}</a>
				<a title="{tr}Delete{/tr}" class="link" href="tiki-admin_links.php?remove={$links[user].url|escape:"url"}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
			</td>
		</tr>
	{sectionelse}
		{norecords _colspan=6}
	{/section}
</table>
</div>

{if $editurl eq 'n'}
	<h2>{tr}Add Featured Link{/tr}</h2>
{else}
	<h2>{tr}Edit this Featured Link:{/tr} {$title}</h2>
	<a href="tiki-admin_links.php">{tr}Create new Featured Link{/tr}</a>
{/if}
<form action="tiki-admin_links.php" method="post">
	<table class="formcolor">
		{if $editurl eq 'n'}
			<tr><td>URL</td><td><input type="text" name="url"></td></tr>
		{else}
			<tr><td>URL</td><td>{$editurl}</td></tr>
			<input type="hidden" name="url" value="{$editurl|escape}">
			<input type="hidden" name="editurl" value="{$editurl|escape}">
		{/if}
		<tr><td>{tr}Title{/tr}</td><td><input type="text" name="title" value="{$title|escape}"></td></tr>
		<tr><td>{tr}Position{/tr}</td><td><input type="text" size="3" name="position" value="{$position|escape}"> (0 {tr}disables the link{/tr})</td></tr>
		<tr><td>{tr}Link type{/tr}</td><td>
			<select name="type">
				<option value="r" {if $type eq 'r'}selected="selected"{/if}>{tr}replace current page{/tr}</option>
				<option value="f" {if $type eq 'f'}selected="selected"{/if}>{tr}framed{/tr}</option>
				<option value="n" {if $type eq 'n'}selected="selected"{/if}>{tr}open new window{/tr}</option>
			</select>
			</td>
		</tr>
		<tr><td>&nbsp;</td><td><input type="submit" class="btn btn-default btn-sm" name="add" value="{tr}Save{/tr}"></td></tr>
	</table>
</form>
