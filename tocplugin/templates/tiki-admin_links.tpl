{title help="FeaturedLinksAdmin"}{tr}Featured Links{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To use these links, you must assign the featured_links <a class="rbox-link" href="tiki-admin_modules.php">module</a>.{/tr}{/remarksbox}

<div class="t_navbar">
	{button href="tiki-admin_links.php?generate=1" _icon_name="ranking" _text="{tr}Generate positions by hits{/tr}"}
</div>

<h2>{tr}List of featured links{/tr}</h2>
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
			<th>{tr}URL{/tr}</th>
			<th>{tr}Title{/tr}</th>
			<th>{tr}Hits{/tr}</th>
			<th>{tr}Position{/tr}</th>
			<th>{tr}Type{/tr}</th>
			<th></th>
		</tr>

		{section name=user loop=$links}
			<tr>
				<td class="text">{$links[user].url}</td>
				<td class="text">{$links[user].title|escape}</td>
				<td class="integer">{$links[user].hits}</td>
				<td class="id">{$links[user].position}</td>
				<td class="text">{$links[user].type}</td>
				<td class="action">
					{capture name=links_actions}
						{strip}
							{$libeg}<a href="tiki-admin_links.php?editurl={$links[user].url|escape:"url"}">
								{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
							</a>{$liend}
							{$libeg}<a href="tiki-admin_links.php?remove={$links[user].url|escape:"url"}">
								{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
							</a>{$liend}
						{/strip}
					{/capture}
					{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
					<a
						class="tips"
						title="{tr}Actions{/tr}"
						href="#"
						{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.links_actions|escape:"javascript"|escape:"html"}{/if}
						style="padding:0; margin:0; border:0"
					>
						{icon name='wrench'}
					</a>
					{if $js === 'n'}
						<ul class="dropdown-menu" role="menu">{$smarty.capture.links_actions}</ul></li></ul>
					{/if}
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
<form action="tiki-admin_links.php" method="post" class="form-horizontal">
	<div class="form-group">
		<label class="col-sm-3 control-label">URL</label>
		<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
			{if $editurl eq 'n'}
				<input type="text" name="url" class="form-control">
			{else}
				{$editurl}
				<input type="hidden" name="url" value="{$editurl|escape}">
				<input type="hidden" name="editurl" value="{$editurl|escape}">
			{/if}
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">{tr}Title{/tr}</label>
		<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
			<input type="text" name="title" value="{$title|escape}" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">{tr}Position{/tr}</label>
		<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
			<input type="text" size="3" name="position" value="{$position|escape}" class="form-control">
			<div class="small-hint">
				(0 {tr}disables the link{/tr})
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">{tr}Link type{/tr}</label>
		<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
			<select name="type" class="form-control">
				<option value="r" {if $type eq 'r'}selected="selected"{/if}>{tr}replace current page{/tr}</option>
				<option value="f" {if $type eq 'f'}selected="selected"{/if}>{tr}framed{/tr}</option>
				<option value="n" {if $type eq 'n'}selected="selected"{/if}>{tr}open new window{/tr}</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
			<input type="submit" class="btn btn-default btn-sm" name="add" value="{tr}Save{/tr}">
		</div>
	</div>
</form>
