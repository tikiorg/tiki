{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="quickadmin" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if $tiki_p_admin == "y"}
		<div id="quickadmin" class="btn-group">
			<div class="btn-group">
				{icon name="sort-down" title="{tr}Recent preferences{/tr}" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"}
				<ul class="dropdown-menu recent-prefs" role="menu">
					{foreach $recent_prefs as $p}
						<li>
							<a href="tiki-admin.php?lm_criteria={$p|stringfix:"_":"%20AND%20"}">{$p|stringfix}</a>
						</li>
					{foreachelse}
						<li>{tr}None{/tr}</li>
					{/foreach}
				</ul>
			</div>
			{icon name="wizard" title="{tr}Wizards{/tr}" href="tiki-wizard_admin.php?stepNr=0&amp;url=index.php"}
			{icon name="administer" title="{tr}Configuration Panels{/tr}" href="tiki-admin.php"}
			{icon name="theme" title="{tr}Themes{/tr}" href="tiki-admin.php?page=look"}
			{icon name="user" title="{tr}Users{/tr}" href="tiki-adminusers.php"}
			{icon name="group" title="{tr}Groups{/tr}" href="tiki-admingroups.php"}
			{permission_link mode=icon label="{tr}Permissions{/tr}"}
			{icon name="menu" title="{tr}Menus{/tr}" href="tiki-admin_menus.php"}
			{if $prefs.lang_use_db eq "y"}
				{if isset($smarty.session.interactive_translation_mode) && $smarty.session.interactive_translation_mode eq "on"}
					{icon name="translate" title="{tr}Toggle interactive translation off{/tr}" href="tiki-interactive_trans.php?interactive_translation_mode=off" class="btn btn-warning btn-sm"}
				{else}
					{icon name="translate" title="{tr}Toggle interactive translation on{/tr}" href="tiki-interactive_trans.php?interactive_translation_mode=on"}
				{/if}
			{/if}
			{if $prefs.themegenerator_feature eq "y" and !empty($prefs.themegenerator_theme)}
				{icon name="themegenerator" title="{tr}Theme Generator{/tr}" href="#" onclick="openThemeGenDialog();return false;"}
			{/if}
			{if $prefs.feature_comments_moderation eq "y"}
				{icon name="comments" title="{tr}Comment Moderation{/tr}" href="tiki-list_comments.php"}
			{/if}
			{icon name="trash" title="{tr}Clear all caches{/tr}" href="tiki-admin_system.php?do=all"}
			{icon name="index" title="{tr}Rebuild Search index{/tr}" href="{bootstrap_modal controller=search action=rebuild}"}
			{icon name="plugin" title="{tr}Plugin Approval{/tr}" href="tiki-plugins.php"}
			{icon name="log" title="{tr}Logs{/tr}" href="tiki-syslog.php"}
			{icon name="module" title="{tr}Modules{/tr}" href="tiki-admin_modules.php"}
			{if $prefs.feature_debug_console}
				{icon name="bug" title="{tr}Smarty debug window{/tr}" href="{query _type='relative' show_smarty_debug=1}"}
			{/if}
			{if $prefs.feature_jcapture eq "y"}
				{icon name="screencapture" title="{tr}Screen capture{/tr}" href="#" onclick="openJCaptureDialog('none', '{$page}', event);return false;"}
			{/if}
		</div>
	{/if}
{/tikimodule}
