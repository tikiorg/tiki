{title}{tr}Admin HTML Page Dynamic Zones{/tr}{/title}

<h2>{tr}Page:{/tr} {$pageName}</h2>

<div class="t_navbar btn-group">
	{button href="tiki-admin_html_pages.php" class="btn btn-default" _icon_name="cog" _text="{tr}Admin HTML pages{/tr}"}
	{assign var='pname' value=$pageName|escape:"url"}
	{button href="tiki-admin_html_pages.php?pageName=$pname" class="btn btn-default" _icon_name="edit" _text="{tr}Edit this page{/tr}"}
	{button href="tiki-page.php?pageName=$pname" class="btn btn-default" _icon_name="view" _text="{tr}View page{/tr}"}
</div>

{if $zone}
	<h2>{tr}Edit zone{/tr}</h2>
	<form action="tiki-admin_html_page_content.php" method="post" class="form-horizontal">
		<input type="hidden" name="pageName" value="{$pageName|escape}">
		<input type="hidden" name="zone" value="{$zone|escape}">
		<div class="form-group">
			<label class="col-sm-3 control-label">{tr}Zone{/tr}</label>
			<div class="col-sm-7 col-sm-offset-1">
			    <p>{$zone} Teste</p>
		    </div>
	    </div>
	    <div class="form-group">
			<label class="col-sm-3 control-label">{tr}Content:{/tr}</label>
			<div class="col-sm-7 col-sm-offset-1">
			    {if $type eq 'ta'}
					<textarea rows="5" cols="15" name="content" class="form-control">{$content|escape}</textarea>
				{else}
					<input type="text" name="content" value="{$content|escape}" class="form-control">
				{/if}
		    </div>
	    </div>
	    <div class="form-group">
			<div class="col-sm-3"></div>
			<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
				<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
			</div>
		</div>
	</form>
{/if}

<h2>{tr}Dynamic zones{/tr}</h2>

{include file='find.tpl'}

<form action="tiki-admin_html_page_content.php" method="post" class="form-horizontal">
	<input type="hidden" name="pageName" value="{$pageName|escape}">
	<input type="hidden" name="zone" value="{$zone|escape}">
	<table class="table table-striped table-hover">
		<tr>
			<th>
				<a href="tiki-admin_html_page_content.php?pageName={$pageName|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'zone_desc'}zone_asc{else}zone_desc{/if}">{tr}zone{/tr}</a>
			</th>
			<th>
				<a href="tiki-admin_html_page_content.php?pageName={$pageName|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'content_desc'}content_asc{else}content_desc{/if}">{tr}content{/tr}</a>
			</th>
			<th></th>
		</tr>

		{section name=user loop=$channels}
			<tr>
				<td class="text">{$channels[user].zone}</td>
				<td class="text">
					{if $channels[user].type eq 'ta'}
						<textarea name="{$channels[user].zone|escape}" cols="20" rows="4" class="form-control">{$channels[user].content|escape}</textarea>
					{else}
						<input type="text" name="{$channels[user].zone|escape}" value="{$channels[user].content|escape}" class="form-control">
					{/if}
				</td>
				<td class="action text-center">
					<a title=":{tr}Edit{/tr}" class="tips" href="tiki-admin_html_page_content.php?pageName={$pageName|escape:"url"}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;zone={$channels[user].zone}">
						{icon name='edit'}
					</a>
				</td>
			</tr>
		{/section}
	</table>

	<div class="form-group">
		<div class="col-sm-3"></div>
		<div class="col-sm-7 col-sm-offset-2">
			<input type="submit" class="btn btn-default btn-sm" name="editmany" value="{tr}Mass update{/tr}">
		</div>
	</div>
</form>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
