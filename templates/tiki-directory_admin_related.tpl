{title help="Directory" url="tiki-directory_admin_related.php?parent=$parent"}{tr}Admin related directory categories{/tr}{/title}

{* Display the title using parent *}
{include file='tiki-directory_admin_bar.tpl'}

{* Navigation bar to admin, admin related, etc *}
<h2>{tr}Parent directory category:{/tr}</h2>
{* Display the path adding manually the top category id=0 *}
<form name="path" method="post" action="tiki-directory_admin_related.php" class="form-horizontal">
	<br>
	<div class="form-group">
		<label class="col-sm-3 control-label">
			{tr}Parent directory category{/tr}
		</label>
		<div class="col-sm-7">
			<select name="parent" onchange="javascript:path.submit();" class="form-control">
				{section name=ix loop=$all_categs}
					<option value="{$all_categs[ix].categId|escape}" {if $parent eq $all_categs[ix].categId}selected="selected"{/if}>{$all_categs[ix].path}</option>
				{/section}
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-7">
			<input type="submit" class="btn btn-default btn-sm" name="go" value="{tr}Go{/tr}">
		</div>
	</div>
</form>
<h2>{tr}Add a related directory category{/tr}</h2>
<form action="tiki-directory_admin_related.php" method="post" class="form-horizontal">
	<br>
	<input type="hidden" name="parent" value="{$parent|escape}">
	<div class="form-group">
		<label class="col-sm-3 control-label">{tr}Directory Category:{/tr}</label>
		<div class="col-sm-7">
			<select name="categId" class="form-control">
				{section name=ix loop=$categs}
					<option value="{$categs[ix].categId|escape}">{$categs[ix].path}</option>
				{/section}
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">{tr}Mutual:{/tr}</label>
		<div class="col-sm-7">
			<input type="checkbox" name="mutual">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-7">
			<input type="submit" class="btn btn-default btn-sm" name="add" value="{tr}Save{/tr}">
		</div>
	</div>
</form>
<br>

<h2>{tr}Related directory categories{/tr}</h2>
{* Display the list of categories (items) using pagination *}
{* Links to edit, remove, browse the categories *}
<form action="tiki-directory_admin_related.php">
	<input type="hidden" name="parent" value="{$parent|escape}">
	<input type="hidden" name="oldcategId" value="{$items[user].relatedTo|escape}">

	<div class="{if $js === 'y'}table-responsive{/if}"> {* table-responsive class cuts off css drop-down menus *}
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>{tr}Directory Category{/tr}</th>
					<th class="text-center">{tr}Remove{/tr}</th>
					<th class="text-center">{tr}Update{/tr}</th>
				</tr>
			</thead>
			<tbody>
				{section name=user loop=$items}
				<tr>
					<td>
						<select name="categId" class="form-control">
							{section name=ix loop=$categs}
								<option value="{$categs[ix].categId|escape}" {if $categs[ix].categId eq $items[user].relatedTo}selected="selected"{/if}>{$categs[ix].path}</option>
							{/section}
						</select>
					</td>
					<td class="text-center"><input type="submit" class="btn btn-default btn-sm" name="remove" value="{tr}Remove{/tr}"/></td>
					<td class="text-center"><input type="submit" class="btn btn-default btn-sm" name="update" value="{tr}Update{/tr}"></td>
				</tr>
				{sectionelse}
					{norecords}
				{/section}
			</tbody>
		</table>
	</div>
</form>
{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
