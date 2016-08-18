{title help="Dynamic Content"}{tr}Dynamic Content System{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To use content blocks in a text area (Wiki page, etc), a <a class="rbox-link" href="tiki-admin_modules.php">module</a> or a template, use {literal}{content id=x}{/literal}, where x is the ID of the content block.{/tr} {tr}You can also use {literal}{content label=x}{/literal}, where x is the label of the content block.{/tr}{/remarksbox}
{tabset}
	{tab name="{tr}Available content blocks{/tr}"}
		<h2>{tr}Available content blocks{/tr}</h2>

		{if $listpages or $find neq ''}
			{include file='find.tpl'}
		{/if}
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
					<th>{self_link _sort_arg='sort_mode' _sort_field='contentId'}{tr}Id{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='contentLabel'}{tr}Label{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='data'}{tr}Current Value{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='actual'}{tr}Current ver{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='next'}{tr}Next ver{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='future'}{tr}Future vers{/tr}{/self_link}</th>
					<th></th>
				</tr>

				{section name=changes loop=$listpages}
					<tr>
						<td class="id">{$listpages[changes].contentId}</td>
						<td class="text">
							{if $listpages[changes].contentLabel neq ''}
								<b>{$listpages[changes].contentLabel}</b>
							{/if}
							{if $listpages[changes].description neq ''}
								<div class="subcomment">{$listpages[changes].description}</div>
							{/if}
						</td>
						<td class="text">{$listpages[changes].data|escape:'html'|nl2br}</td>
						<td class="date">{$listpages[changes].actual|tiki_short_datetime}</td>
						<td class="date">{$listpages[changes].next|tiki_short_datetime}</td>
						<td class="text">{$listpages[changes].future}</td>
						<td class="action">
							{capture name=content_actions}
								{strip}
									{$libeg}{self_link _icon_name='edit' _menu_text='y' _menu_icon='y' edit=$listpages[changes].contentId cookietab=2}
										{tr}Edit{/tr}
									{/self_link}{$liend}
									{$libeg}<a href="tiki-edit_programmed_content.php?contentId={$listpages[changes].contentId}" title="{tr}Program{/tr}">
										{icon name='cog' _menu_text='y' _menu_icon='y' alt="{tr}Program{/tr}"}
									</a>{$liend}
									{$libeg}{self_link _icon_name='remove' _menu_text='y' _menu_icon='y' _template='confirm.tpl' remove=$listpages[changes].contentId}
										{tr}Remove{/tr}
									{/self_link}{$liend}
								{/strip}
							{/capture}
							{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
							<a
								class="tips"
								title="{tr}Actions{/tr}"
								href="#"
								{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.content_actions|escape:"javascript"|escape:"html"}{/if}
								style="padding:0; margin:0; border:0"
							>
								{icon name='wrench'}
							</a>
							{if $js === 'n'}
								<ul class="dropdown-menu" role="menu">{$smarty.capture.content_actions}</ul></li></ul>
							{/if}
						</td>
					</tr>
				{sectionelse}
					{norecords _colspan=7}
				{/section}
			</table>
		</div>
		{pagination_links cant=$cant step=$prefs.maxRecords offset=$offset}{/pagination_links}
	{/tab}

	{tab name="{tr}Create/Edit content block{/tr}"}
		<h2>
		{if $contentId}
			{tr}Edit content block{/tr}
		{else}
			{tr}Create content block{/tr}
		{/if}
		</h2>

		{if $contentId ne ''}
			<div class="t_navbar">{button href="tiki-list_contents.php" class="btn btn-default" _text="{tr}Create New Block{/tr}"}</div>
		{/if}
		<br>
		<form action="tiki-list_contents.php" method="post" class="form-horizontal">
			{query _type='form_input'}
			<input type="hidden" name="contentId" value="{$contentId|escape}">

			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Label{/tr}</label>
				<div class="col-sm-7">
			      	<input type="text" name="contentLabel" style="width:40%" value="{$contentLabel|escape}" class="form-control">
			    </div>
		    </div>
		    <div class="form-group">
				<label class="col-sm-3 control-label">{tr}Description{/tr}</label>
				<div class="col-sm-7">
			      	<textarea rows="5" cols="40" name="description" style="width:95%" class="form-control">{$description|escape}</textarea>
			    </div>
		    </div>
		    <div class="form-group">
				<label class="col-sm-3 control-label"></label>
				<div class="col-sm-7">
			      	<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
			    </div>
		    </div>
		</form>
	{/tab}
{/tabset}
