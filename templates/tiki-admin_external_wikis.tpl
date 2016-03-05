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
	<div class="form-group">
		<label for="indexname" class="col-sm-3 control-label">{tr}Index{/tr}</label>
		<div class="col-sm-9">
			<input type="text" maxlength="20" class="form-control" name="indexname" id="indexname" value="{$info.indexname|escape}">
			<p class="help-block">{tr}<em>[prefix]</em>main, such as tiki_main{/tr}</p>
		</div>
	</div>
	<div class="form-group">
		<label for="groups" class="col-sm-3 control-label">{tr}Search as{/tr}</label>
		<div class="col-sm-9">
			{object_selector_multi _simplename=groups _simpleid=groups _simplevalue=$info.groups type="group" _separator=";"}
			<p class="help-block">{tr}Leave blank to search using currently active groups.{/tr}</p>
		</div>
	</div>
	<div class="form-group text-center">
		<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
	</div>
</form>

<h2>{tr}External Wiki{/tr}</h2>
{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
{if $prefs.javascript_enabled !== 'y'}
	{$js = 'n'}
	{$libeg = '<li>'}
	{$liend = '</li>'}
{else}
	{$js = 'y'}
	{$libeg = ''}
	{$liend = ''}
{/if}
<div class="{if $js === 'y'}table-responsive{/if}"> {* table-responsive class cuts off css drop-down menus *}
	<table class="table table-striped table-hover">
		<tr>
			<th>
				<a href="tiki-admin_external_wikis.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a>
			</th>
			<th>
				<a href="tiki-admin_external_wikis.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'extwiki_desc'}extwiki_asc{else}extwiki_desc{/if}">{tr}ExtWiki{/tr}</a>
			</th>
			<th></th>
		</tr>

		{section name=user loop=$channels}
			<tr>
				<td class="text">{$channels[user].name}</td>
				<td class="text">{$channels[user].extwiki}</td>
				<td class="action">
					{capture name=externalwiki_actions}
						{strip}
							{$libeg}<a href="tiki-admin_external_wikis.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;extwikiId={$channels[user].extwikiId}">
								{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
							</a>{$liend}
							{$libeg}<a href="tiki-admin_external_wikis.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].extwikiId}">
								{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
							</a>{$liend}
						{/strip}
					{/capture}
					{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
					<a
						class="tips"
						title="{tr}Actions{/tr}"
						href="#"
						{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.externalwiki_actions|escape:"javascript"|escape:"html"}{/if}
						style="padding:0; margin:0; border:0"
					>
						{icon name='wrench'}
					</a>
					{if $js === 'n'}
						<ul class="dropdown-menu" role="menu">{$smarty.capture.externalwiki_actions}</ul></li></ul>
					{/if}
				</td>
			</tr>
		{sectionelse}
			{norecords _colspan=3}
		{/section}
	</table>
</div>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
