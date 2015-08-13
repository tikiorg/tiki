<h1class="pagetitle><a " href="tiki-admin_keywords.php">{tr}Admin keywords{/tr}</a></h1>

{if $keywords_updated}
	<div class="alert alert-warning">
		{if $keywords_updated == 'y'}{tr}Keywords have been updated{/tr}
		{else}{tr}Updating keywords has failed. Page probably doesn't exist.{/tr}{/if}
		{if $keywords_updated_on} ({$keywords_updated_on|escape}){/if}
	</div>
{/if}
{if $edit_on}
	<div id="current_keywords" class="clearfix">
		<h2>{tr}Edit page keywords{/tr} ({$edit_keywords_page|escape})</h2>
		<form action="tiki-admin_keywords.php" method="post" class="form-horizontal">
			<input name="page" value="{$edit_keywords_page|escape}" type="hidden">
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Keywords{/tr}</label>
				<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
					<input name="new_keywords" size="65" value="{$edit_keywords|escape}" class="form-control">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"></label>
				<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
					<input type="submit" class="btn btn-default btn-sm" name="save_keywords" value="{tr}Save{/tr}">
				</div>
			</div>
		</form>
	</div>
{/if}

<h2>{tr}Current Page Keywords{/tr}</h2>
<form method="get" action="tiki-admin_keywords.php" class="form-horizontal">
	<div class="form-group">
		<label class="col-sm-3 control-label">{tr}Search by page:{/tr}</label>
		<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
			<input type="text" name="q" value="{if $smarty.request.q}{$smarty.request.q|escape}{/if}" class="form-control">
		</div>
		<div class="col-sm-1">
			<input type="submit" class="btn btn-default btn-sm" name="search" value="{tr}Go{/tr}">
		</div>
	</div>
</form>
{if $search_on}
	<strong>{$search_cant|escape} {tr}results found!{/tr}</strong>
{/if}

{if $existing_keywords}
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
			<tbody>
				<tr>
					<th>{tr}Page{/tr}</th>
					<th>{tr}Keywords{/tr}</th>
					<th></th>
				</tr>

				{section name=i loop=$existing_keywords}
					<tr>
						<td class="text"><a href="{$existing_keywords[i].page|sefurl}">{$existing_keywords[i].page|escape}</a></td>
						<td class="text">{$existing_keywords[i].keywords|escape}</td>
						<td class="action">
							{capture name=keywords_actions}
								{strip}
									{$libeg}<a href="tiki-admin_keywords.php?page={$existing_keywords[i].page|escape:"url"}">
										{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
									</a>{$liend}
									{$libeg}<a href="tiki-admin_keywords.php?page={$existing_keywords[i].page|escape:"url"}&amp;remove_keywords=1">
										{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
									</a>{$liend}
								{/strip}
							{/capture}
							{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
							<a
								class="tips"
								title="{tr}Actions{/tr}"
								href="#"
								{if $js === 'y'}{popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.keywords_actions|escape:"javascript"|escape:"html"}{/if}
								style="padding:0; margin:0; border:0"
							>
								{icon name='wrench'}
							</a>
							{if $js === 'n'}
								<ul class="dropdown-menu" role="menu">{$smarty.capture.keywords_actions}</ul></li></ul>
							{/if}
						</td>
					</tr>
				{/section}
			</tbody>
		</table>
	</div>
{else}
	<h2>{tr}No pages found{/tr}</h2>
{/if}

{pagination_links cant=$pages_cant step=$prefs.maxRecords offset=$offset}{/pagination_links}
