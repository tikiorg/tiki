{title help="External+Wikis" admpage="textarea"}{tr}Admin External Wikis{/tr}{/title}

<h2>{tr}Create/Edit External Wiki{/tr}</h2>
<form action="tiki-admin_external_wikis.php" method="post" class="form-horizontal" role="form">
	<input type="hidden" name="extwikiId" value="{$extwikiId|escape}">
	<div class="form-group">
        <label for="name" class="col-sm-3 control-label">{tr}Name{/tr}</label>
        <div class="col-sm-9">
            <input type="text" maxlength="255" class="form-control" name="name" value="{$info.name|escape}">
        </div>
    </div>
    <div class="form-group">
        <label for="extwiki" class="col-sm-3 control-label">{tr}URL{/tr}</label>
    	<div class="col-sm-9">
			<input type="text" maxlength="255" class="form-control" name="extwiki" id="extwiki" value="{$info.extwiki|escape}">
            <p class="help-block">{tr}URL (use $page to be replaced by the page name in the URL example: http://www.example.com/tiki-index.php?page=$page):{/tr}</p>
        </div>
	</div>
    <div class="form-group text-center">
		<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
    </div>
</form>

<h2>{tr}External Wiki{/tr}</h2>

<div class="table-responsive">
<table class="table normal">
	<tr>
		<th>
			<a href="tiki-admin_external_wikis.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a>
		</th>
		<th>
			<a href="tiki-admin_external_wikis.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'extwiki_desc'}extwiki_asc{else}extwiki_desc{/if}">{tr}ExtWiki{/tr}</a>
		</th>
		<th>{tr}Action{/tr}</th>
	</tr>

	{section name=user loop=$channels}
		<tr>
			<td class="text">{$channels[user].name}</td>
			<td class="text">{$channels[user].extwiki}</td>
			<td class="action">
				&nbsp;&nbsp;
				<a title="{tr}Edit{/tr}" class="link" href="tiki-admin_external_wikis.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;extwikiId={$channels[user].extwikiId}">{icon _id='page_edit'}</a>
				&nbsp;
				<a title="{tr}Delete{/tr}" class="link" href="tiki-admin_external_wikis.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].extwikiId}" >{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
			</td>
		</tr>
	{sectionelse}
		{norecords _colspan=3}
	{/section}
</table>
</div>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
