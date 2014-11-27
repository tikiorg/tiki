{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="quickadmin" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if $tiki_p_admin == "y"}
		<div id="quickadmin" class="btn-group">
			<div class="btn-group">
				{icon name="sort-down" title="{tr}Recent preferences{/tr}" class="btn btn-link btn-sm dropdown-toggle" data-toggle="dropdown"}
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
			{icon name="wizard" class="btn btn-link btn-sm" title="{tr}Wizards{/tr}" href="tiki-wizard_admin.php?stepNr=0&amp;url=index.php"}
			{icon name="administer" class="btn btn-link btn-sm" title="{tr}Control Panels{/tr}" href="tiki-admin.php"}
			{icon name="theme" class="btn btn-link btn-sm" title="{tr}Themes{/tr}" href="tiki-admin.php?page=look"}
			{icon name="user" class="btn btn-link btn-sm" title="{tr}Users{/tr}" href="tiki-adminusers.php"}
			{icon name="group" class="btn btn-link btn-sm" title="{tr}Groups{/tr}" href="tiki-admingroups.php"}
			{permission_link mode=icon label="{tr}Permissions{/tr}"}
			{icon name="menu" class="btn btn-link btn-sm" title="{tr}Menus{/tr}" href="tiki-admin_menus.php"}
			{if $prefs.lang_use_db eq "y"}
				{if isset($smarty.session.interactive_translation_mode) && $smarty.session.interactive_translation_mode eq "on"}
					{icon name="translate" class="btn btn-link btn-sm" title="{tr}Toggle interactive translation off{/tr}" href="tiki-interactive_trans.php?interactive_translation_mode=off" class="btn btn-warning btn-sm"}
				{else}
					{icon name="translate" class="btn btn-link btn-sm" title="{tr}Toggle interactive translation on{/tr}" href="tiki-interactive_trans.php?interactive_translation_mode=on"}
				{/if}
			{/if}
			{if $prefs.themegenerator_feature eq "y" and !empty($prefs.themegenerator_theme)}
				{icon name="themegenerator" class="btn btn-link btn-sm" title="{tr}Theme Generator{/tr}" href="#" onclick="openThemeGenDialog();return false;"}
			{/if}
			{if $prefs.feature_comments_moderation eq "y"}
				{icon name="comments" class="btn btn-link btn-sm" title="{tr}Comment Moderation{/tr}" href="tiki-list_comments.php"}
			{/if}
			{icon name="trash" class="btn btn-link btn-sm" title="{tr}Clear all caches{/tr}" href="tiki-admin_system.php?do=all"}
			{icon name="index" class="btn btn-link btn-sm" title="{tr}Rebuild Search index{/tr}" href="{bootstrap_modal controller=search action=rebuild}"}
			{icon name="plugin" class="btn btn-link btn-sm" title="{tr}Plugin Approval{/tr}" href="tiki-plugins.php"}
			{icon name="log" class="btn btn-link btn-sm" title="{tr}Logs{/tr}" href="tiki-syslog.php"}
			{icon name="module" class="btn btn-link btn-sm" title="{tr}Modules{/tr}" href="tiki-admin_modules.php"}
			{if $prefs.feature_debug_console}
				{icon name="bug" class="btn btn-link btn-sm" title="{tr}Smarty debug window{/tr}" href="{query _type='relative' show_smarty_debug=1}"}
			{/if}
			{if $prefs.feature_jcapture eq "y"}
				{icon name="screencapture" class="btn btn-link btn-sm" title="{tr}Screen capture{/tr}" href="#" onclick="openJCaptureDialog('none', '{$page}', event);return false;"}
			{/if}
		</div>
	{/if}
{/tikimodule}
