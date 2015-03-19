{* $Id$ *}
{title help="Menus" admpage="general&amp;cookietab=3"}{tr}Menus{/tr}{/title}

{if $tiki_p_admin eq 'y'}
	<div class="t_navbar  margin-bottom-md">
		<a class="btn btn-default" href="{bootstrap_modal controller=menu action=manage_menu}">
			{icon name="create"} {tr}Create Menu{/tr}
		</a>
		{button href="tiki-admin_modules.php" _text="{icon name="administer"} {tr}Modules{/tr}"}
	</div>
{/if}
{include file='find.tpl'}
<div class="table-responsive">
	<table class="table table-hover">
		<tr>
			<th>{self_link _sort_arg='sort_mode' _sort_field='menuId'}{tr}ID{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='type'}{tr}Type{/tr}{/self_link}</th>
			<th>{tr}Options{/tr}</th>
			<th>{tr}Action{/tr}</th>
		</tr>

		{section name=user loop=$channels}
			<tr>
				<td class="id">{$channels[user].menuId}</td>
				<td class="text">
					{if $tiki_p_edit_menu_option eq 'y' and $channels[user].menuId neq 42}
						<a class="link tips" href="tiki-admin_menu_options.php?menuId={$channels[user].menuId}" title=":{tr}Menu Options{/tr}">{$channels[user].name|escape}</a>
					{else}
						{$channels[user].name|escape}
					{/if}
					<span class="help-block">
						{$channels[user].description|escape|nl2br}
					</span>
				</td>
				<td class="text">{$channels[user].type}</td>
				<td><span class="badge">{$channels[user].options}</span></td>
				<td class="action">
					{if $channels[user].menuId neq 42}
						{if $tiki_p_edit_menu eq 'y'}
							<a class="tips" href="{bootstrap_modal controller=menu action=manage_menu menuId=$channels[user].menuId}" title=":{tr}Edit{/tr}">
								{icon name="edit"}
							</a>
						{/if}
						{if $tiki_p_edit_menu_option eq 'y'}
							<a class="tips" href="tiki-admin_menu_options.php?menuId={$channels[user].menuId}" title=":{tr}Menu Options{/tr}">{icon name="list"}</a>
						{/if}
						{if $tiki_p_edit_menu eq 'y'}
							{self_link remove=$channels[user].menuId _title=":{tr}Delete{/tr}" _class="tips"}{icon name="delete"}{/self_link}
						{/if}
					{else}
						{if $tiki_p_admin eq 'y'}
							{button reset="y" menuId=$channels[user].menuId _text="{tr}RESET{/tr}" _auto_args="reset,menuId" _class="btn btn-warning btn-sm"}
						{/if}
					{/if}
					{if $tiki_p_edit_menu eq 'y'}
						<a class="tips" href="{bootstrap_modal controller=menu action=clone_menu menuId=$channels[user].menuId}" title=":{tr}Clone{/tr}">
							{icon name="copy"}
						</a>
					{/if}
				</td>
			</tr>
		{sectionelse}
			{norecords _colspan=5}
		{/section}
	</table>
</div>
{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
