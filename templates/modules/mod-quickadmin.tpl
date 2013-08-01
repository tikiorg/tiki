{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="quickadmin" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if $tiki_p_admin == "y"}
		<div id="quickadmin" style="text-align: left; padding-left: 12px;">
			<ul class="cssmenu_horiz clearfix sf-arrows" style="display: inline-block;line-height: 30px;">
				<li>
					{if $prefs.feature_jquery_superfish eq "y"}
						<a>&nbsp;</a>
					{else}
						{icon _id=arrow_down title="{tr}Recent preferences{/tr}" href="#"}
					{/if}
					<ul class="recent-prefs">
						<li style="line-height: 2em;"><em>{tr}Recent:{/tr}</em></li>
						{foreach $recent_prefs as $p}
							<li>
								<a href="tiki-admin.php?lm_criteria={$p|stringfix:"_":" AND "}">{$p|stringfix}</a>
							</li>
						{foreachelse}
							<li>{tr}None{/tr}</li>
						{/foreach}
					</ul>
				</li>
			</ul>
			{icon _id=house title="{tr}Admin home{/tr}" href="tiki-admin.php"} 
			{icon _id=wrench title="{tr}Modify the look &amp; feel (logo, theme, etc.){/tr}" href="tiki-admin.php?page=look&amp;cookietab=2"} 
			{icon _id=user title="{tr}Users{/tr}" href="tiki-adminusers.php"}
			{icon _id=group title="{tr}Groups{/tr}" href="tiki-admingroups.php"}			
			{icon _id=key title="{tr}Permissions{/tr}" href="tiki-objectpermissions.php"}
			{icon _id=application_side_tree title="{tr}Menus{/tr}" href="tiki-admin_menus.php"}
			{if $prefs.lang_use_db eq "y"}
				{if isset($smarty.session.interactive_translation_mode) && $smarty.session.interactive_translation_mode eq "on"}
					{icon _id=world_edit title="{tr}Toggle interactive translation off{/tr}" href="tiki-interactive_trans.php?interactive_translation_mode=off"}
				{else}
					{icon _id=world_edit title="{tr}Toggle interactive translation on{/tr}" href="tiki-interactive_trans.php?interactive_translation_mode=on"}
				{/if}
			{/if}
			{if $prefs.themegenerator_feature eq "y" and !empty($prefs.themegenerator_theme)}
				{icon _id="palette" title="{tr}Theme Generator Editor{/tr}" href="#" onclick="openThemeGenDialog();return false;"}
			{/if}
			{if $prefs.feature_comments_moderation eq "y"}
				{icon _id=comments title="{tr}Comments Moderation{/tr}" href="tiki-list_comments.php"}
			{/if}
			{icon _id=database_refresh title="{tr}Clear all Tiki caches{/tr}" href="tiki-admin_system.php?do=all"}
			{icon _id=table_refresh title="{tr}Rebuild Search index{/tr}" href="tiki-admin.php?page=search&amp;rebuild=now"}
			{icon _id=plugin title="{tr}Plugin Approval{/tr}" href="tiki-plugins.php"}
			{icon _id=book title="{tr}SysLogs{/tr}" href="tiki-syslog.php"}
			{icon _id=module title="{tr}Modules{/tr}" href="tiki-admin_modules.php"}
			{if $prefs.feature_debug_console}
				{icon _id=bug title="{tr}Open Smarty debug window{/tr}" href="{query _type='relative' show_smarty_debug=1}"}
			{/if}
			{if $prefs.feature_jcapture eq "y"}
				{icon _id=camera title="{tr}Screen capture{/tr}" href="#" onclick="openJCaptureDialog('none', '{$page}', event);return false;"}
			{/if}
		</div>
	{/if}
{/tikimodule}
