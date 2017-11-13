{* $Id$ *}
{title help="Menus" url="tiki-admin_menu_options.php?menuId=$menuId" admpage="general&amp;cookietab=3"}{tr}Menu{/tr}: {$editable_menu_info.name}{/title}

<div class="t_navbar margin-bottom-md">
	<a class="btn btn-link" href="tiki-admin_menus.php">
		{icon name="list"} {tr}List Menus{/tr}
	</a>
	{if $tiki_p_edit_menu eq 'y'}
		<a class="btn btn-default" href="{bootstrap_modal controller=menu action=manage_option menuId=$menuId}">
			{icon name="create"} {tr}Create menu option{/tr}
		</a>
		<a class="btn btn-default" href="{bootstrap_modal controller=menu action=manage menuId=$menuId}">
			{icon name="edit"} {tr}Edit This Menu{/tr}
		</a>
		<a class="btn btn-default" href="{bootstrap_modal controller=menu action=export_menu_options menuId=$menuId}" title="{tr}Export menu options{/tr}">
			{icon name="export"} {tr}Export{/tr}
		</a>
		<a class="btn btn-default no-ajax" href="{bootstrap_modal controller=menu action=import_menu_options menuId=$menuId}" title="{tr}Import menu options{/tr}">
			{icon name="import"} {tr}Import{/tr}
		</a>
	{/if}
</div>

{tabset name="admin_menu_options"}
{tab name="{tr}Manage menu{/tr} {$editable_menu_info.name}"}
	<div>
		<a id="options"></a>

		<h2>{tr}Menu options{/tr} <span class="badge">{$cant_pages}</span></h2>
		{if $options or ($find ne '')}
			{include file='find.tpl' find_show_num_rows='y'}
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
		<form method="get" action="tiki-admin_menu_options.php">
			<input type="hidden" name="find" value="{$find|escape}">
			<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
			<input type="hidden" name="menuId" value="{$menuId}">
			<input type="hidden" name="offset" value="{$offset}">

			<div class="{if $js === 'y'}table-responsive{/if}"> {* table-responsive class cuts off css drop-down menus *}
				<table class="table table-striped table-hover">
					{assign var=numbercol value=0}
					<tr>
						<th>
							{assign var=numbercol value=$numbercol+1}
							{if $options}
								{select_all checkbox_names='checked[]'}
							{/if}
						</th>
						{assign var=numbercol value=$numbercol+1}
						<th>{self_link _sort_arg='sort_mode' _sort_field='optionId'}{tr}ID{/tr}{/self_link}</th>
						{assign var=numbercol value=$numbercol+1}
						<th>{self_link _sort_arg='sort_mode' _sort_field='position'}{tr}Position{/tr}{/self_link}</th>
						{assign var=numbercol value=$numbercol+1}
						<th>{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</th>
						{assign var=numbercol value=$numbercol+1}
						<th>{self_link _sort_arg='sort_mode' _sort_field='type'}{tr}Type{/tr}{/self_link}</th>
						{if $prefs.feature_userlevels eq 'y'}
							{assign var=numbercol value=$numbercol+1}
							<th>{self_link _sort_arg='sort_mode' _sort_field='userlevel'}{tr}Level{/tr}{/self_link}</th>
						{/if}
						{assign var=numbercol value=$numbercol+1}
						<th></th>
					</tr>

					{foreach $options as $option}
						<tr>
							<td class="checkbox-cell">
								<input type="checkbox" name="checked[]" value="{$option.optionId|escape}"
									{if $smarty.request.checked and in_array($option.optionId,$smarty.request.checked)}checked="checked"{/if}>
							</td>
							<td class="id">{$option.optionId}</td>
							<td class="id">{$option.position}</td>
							<td class="text">
								<a href="{bootstrap_modal controller=menu action=manage_option menuId=$menuId optionId=$option.optionId}" class="tips" title=":{tr}Edit{/tr}">
									{$option.name|escape}
								</a>
								<span class="help-block">
									{if $option.url}
										{tr}URL:{/tr}
										<a href="{$option.sefurl|escape}" class="link tips" target="_blank" title=":{$option.canonic|escape}" >
											{$option.canonic|truncate:40:' ...'|escape}
										</a>
									{/if}
									{if $option.section}
										<br>
										{tr}Sections:{/tr} {$option.section}
									{/if}
									{if $option.perm}
										<br>
										{tr}Permissions:{/tr} {$option.perm}
									{/if}
									{if $option.groupname}
										<br>
										{tr}Groups:{/tr} {$option.groupname|escape}
									{/if}
									{if $option.class}
										<br>
										{tr}Class:{/tr} {$option.class|escape}
									{/if}
								</span>
							</td>
							<td class="text">{$option.type_description}</td>

							{if $prefs.feature_userlevels eq 'y'}
								{assign var=it value=$option.userlevel}
								<td>{$prefs.userlevels.$it}</td>
							{/if}

							<td class="action">
								{capture name=menu_options_actions}
									{strip}
										{if !$smarty.section.user.first}
											{$libeg}<a href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;up={$option.optionId}&amp;maxRecords={$maxRecords}#options">
												{icon name="up" _menu_text='y' _menu_icon='y' alt="{tr}Switch with previous option{/tr}"}
											</a>{$liend}
										{/if}
										{if !$smarty.section.user.last}
											{$libeg}<a href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;down={$option.optionId}&amp;maxRecords={$maxRecords}#options">
												{icon name="down" _menu_text='y' _menu_icon='y' alt="{tr}Switch with next option{/tr}"}
											</a>{$liend}
										{/if}
										{$libeg}<a href="{bootstrap_modal controller=menu action=manage_option menuId=$menuId optionId=$option.optionId}">
											{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
										</a>{$liend}
										{$libeg}<a href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$option.optionId}&amp;maxRecords={$maxRecords}">
											{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
										</a>{$liend}
									{/strip}
								{/capture}
								{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
								<a
									class="tips"
									title="{tr}Actions{/tr}"
									href="#"
									{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.menu_options_actions}{/if}
									style="padding:0; margin:0; border:0"
								>
									{icon name='wrench'}
								</a>
								{if $js === 'n'}
									<ul class="dropdown-menu" role="menu">{$smarty.capture.menu_options_actions}</ul></li></ul>
								{/if}
							</td>
						</tr>
					{foreachelse}
						{norecords _colspan=$numbercol}
					{/foreach}
				</table>
			</div>

			{if $options}
				<div align="left">
					{tr}Perform action with checked:{/tr}
					<input type="image" name="delsel" src='img/icons/cross.png' alt="{tr}Delete{/tr}" title="{tr}Delete{/tr}">
				</div>
			{/if}
		</form>

		{pagination_links cant=$cant_pages step=$maxRecords offset=$offset}{/pagination_links}


	</div>
{/tab}
{tab name="{tr}Preview{/tr}"}
	<h2>{tr}Preview menu{/tr}</h2>
	<form action="tiki-admin_menu_options.php" class="form-inline">
		<input type="hidden" name="menuId" value="{$menuId}">
		<div class="form-group">
			<label for="preview_type" class="control-label">{tr}Type{/tr}:</label>
			<select id="preview_type" class="form-control" name="preview_type" onchange="this.form.submit()">
				<option value="vert"{if $preview_type eq 'vert'} selected{/if}>{tr}Vertical{/tr}</option>
				<option value="horiz"{if $preview_type eq 'horiz'} selected{/if}>{tr}Horizontal{/tr}</option>
			</select>
		</div>
		<div class="checkbox">
			<label for="preview_css">
			<input type="checkbox" id="preview_css" name="preview_css" onchange="this.form.submit()"{if $preview_css eq 'y'} checked="checked"{/if}>
				CSS</label>
		</div>
	</form>

	<h2>Smarty Code</h2>
	<pre id="preview_code">
	{ldelim}menu id={$menuId} css={$preview_css} type={$preview_type}{rdelim
					}</pre>{* <pre> cannot have extra spaces for indenting *}
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">{$editable_menu_info.name|escape}</h3>
		</div>
		<div class="panel-body clearfix">
			{menu id=$menuId css=$preview_css type=$preview_type}
		</div>
	</div>

{/tab}
{/tabset}
