{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="quickadmin" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if $tiki_p_admin == "y"}
		<div id="quickadmin" style="text-align: left; padding-left: 12px;" data-role="controlgroup" data-type="horizontal">
{*			<ul style="line-height: 30px;">
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
*}{* recent prefs removed from mobile since they take way too much space *}
			<a data-role="button" href="tiki-admin.php" title="{tr}Configuration Panels{/tr}">{icon _id=house alt="{tr}Configuration Panels{/tr}"}</a>
			<a data-role="button" href="tiki-admin.php?page=look&amp;cookietab=2" title="{tr}Modify the look &amp; feel (logo, theme, etc.){/tr}">{icon _id=wrench alt="{tr}Modify the look &amp; feel (logo, theme, etc.){/tr}"} </a> 
			<a data-role="button" href="tiki-adminusers.php" title="{tr}Users{/tr}">{icon _id=user alt="{tr}Users{/tr}"}</a> 
			<a data-role="button" href="tiki-admingroups.php" title="{tr}Groups{/tr}">{icon _id=group alt="{tr}Groups{/tr}"}</a> 			
			<a data-role="button" href="tiki-objectpermissions.php" title="{tr}Permissions{/tr}">{icon _id=key alt="{tr}Permissions{/tr}"}</a> 
			<a data-role="button" href="tiki-admin_menus.php" title="{tr}Menus{/tr}">{icon _id=application_side_tree alt="{tr}Menus{/tr}"}</a> 
			{if $prefs.lang_use_db eq "y"}
				{if isset($smarty.session.interactive_translation_mode) && $smarty.session.interactive_translation_mode eq "on"}
					<a data-role="button" href="tiki-interactive_trans.php?interactive_translation_mode=off" title="{tr}Toggle interactive translation off{/tr}">{icon _id=world_edit alt="{tr}Toggle interactive translation off{/tr}"}</a> 
				{else}
					<a data-role="button" href="tiki-interactive_trans.php?interactive_translation_mode=on" title="{tr}Toggle interactive translation on{/tr}">{icon _id=world_edit alt="{tr}Toggle interactive translation on{/tr}"}</a> 
				{/if}
			{/if}
			{if $prefs.themegenerator_feature eq "y" and !empty($prefs.themegenerator_theme)}
				<a data-role="button" href="#" onclick="openThemeGenDialog();return false;" title="{tr}Theme Generator Editor{/tr}">{icon _id="palette" alt="{tr}Theme Generator Editor{/tr}"}</a> 
			{/if}
			{if $prefs.feature_comments_moderation eq "y"}
				<a data-role="button" href="tiki-list_comments.php" title="{tr}Comments Moderation{/tr}">{icon _id=comments alt="{tr}Comments Moderation{/tr}"}</a> 
			{/if}
			<a data-role="button" href="tiki-admin_system.php?do=all" title="{tr}Clear all Tiki caches{/tr}">{icon _id=database_refresh alt="{tr}Clear all Tiki caches{/tr}"}</a> 
			<a data-role="button" href="tiki-admin.php?page=search&amp;rebuild=now" title="{tr}Rebuild Search index{/tr}">{icon _id=table_refresh alt="{tr}Rebuild Search index{/tr}"}</a> 
			<a data-role="button" href="tiki-plugins.php" title="{tr}Plugin Approval{/tr}">{icon _id=plugin alt="{tr}Plugin Approval{/tr}"}</a> 
			<a data-role="button" href="tiki-syslog.php" title="{tr}SysLogs{/tr}">{icon _id=book alt="{tr}SysLogs{/tr}"}</a> 
			<a data-role="button" href="tiki-admin_modules.php" title="{tr}Modules{/tr}">{icon _id=module alt="{tr}Modules{/tr}"}</a> 
			{if $prefs.feature_debug_console}
				<a data-role="button" href="{query _type='relative' show_smarty_debug=1}" title="{tr}Open Smarty debug window{/tr}">{icon _id=bug alt="{tr}Open Smarty debug window{/tr}"}</a> 
			{/if}
			{if $prefs.feature_jcapture eq "y"}
				<a data-role="button" href="#" onclick="openJCaptureDialog('none', '{$page}', event);return false;" title="{tr}Screen capture{/tr}">{icon _id=camera alt="{tr}Screen capture{/tr}"}</a> 
			{/if}
		</div>
	{/if}
{/tikimodule}
