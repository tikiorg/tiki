{* $Id$ *}
{title help="Menus" admpage="general&amp;cookietab=3"}{tr}Menus{/tr}{/title}

{if $tiki_p_admin eq 'y'}
	<div class="t_navbar margin-bottom-md">
		<a class="btn btn-default" href="{bootstrap_modal controller=menu action=manage_menu}">
			{icon name="create"} {tr}Create Menu{/tr}
		</a>
		{button href="tiki-admin_modules.php" _icon_name="cogs" _type="link" _text="{tr}Modules{/tr}"}
	</div>
{/if}
{include file='find.tpl'}
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
	<table class="table table-hover">
		<tr>
			<th>{self_link _sort_arg='sort_mode' _sort_field='menuId'}{tr}ID{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='type'}{tr}Type{/tr}{/self_link}</th>
			<th>{tr}Options{/tr}</th>
			<th></th>
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
					{capture name=menu_actions}
						{strip}
							{if $channels[user].menuId neq 42}
								{if $tiki_p_edit_menu eq 'y'}
									{$libeg}<a href="{bootstrap_modal controller=menu action=manage_menu menuId=$channels[user].menuId}">
										{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
									</a>{$liend}
								{/if}
								{if $tiki_p_edit_menu_option eq 'y'}
									{$libeg}<a href="tiki-admin_menu_options.php?menuId={$channels[user].menuId}">
										{icon name="list" _menu_text='y' _menu_icon='y' alt="{tr}Menu options{/tr}"}
									</a>{$liend}
								{/if}
								{if $tiki_p_edit_menu eq 'y'}
									{$libeg}{self_link remove=$channels[user].menuId _menu_text='y' _menu_icon='y' _icon_name="remove"}
										{tr}Delete{/tr}
									{/self_link}{$liend}
								{/if}
							{else}
								{if $tiki_p_admin eq 'y'}
									{$libeg}{button reset="y" menuId=$channels[user].menuId _text="{tr}RESET{/tr}" _auto_args="reset,menuId" _class="btn btn-warning btn-sm"}{$liend}
									<hr>
								{/if}
							{/if}
							{if $tiki_p_edit_menu eq 'y'}
								{$libeg}<a href="{bootstrap_modal controller=menu action=clone_menu menuId=$channels[user].menuId}">
									{icon name="copy" _menu_text='y' _menu_icon='y' alt="{tr}Clone{/tr}"}
								</a>{$liend}
							{/if}
						{/strip}
					{/capture}
					{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
					<a
						class="tips"
						title="{tr}Actions{/tr}"
						href="#"
						{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.menu_actions|escape:"javascript"|escape:"html"}{/if}
						style="padding:0; margin:0; border:0"
					>
						{icon name='wrench'}
					</a>
					{if $js === 'n'}
						<ul class="dropdown-menu" role="menu">{$smarty.capture.menu_actions}</ul></li></ul>
					{/if}
				</td>
			</tr>
		{sectionelse}
			{norecords _colspan=5}
		{/section}
	</table>
</div>
{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
