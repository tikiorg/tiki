{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="quickadmin" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if $tiki_p_admin == "y"}
		<div id="quickadmin" style="text-align: left; padding-left: 12px;"><small>{tr}Quick Admin{/tr}</small>:
			{icon _id=database_refresh title="{tr}Clear all Tiki caches{/tr}" href="tiki-admin_system.php?do=all"}
			{icon _id=wrench title="{tr}Modify the look &amp; feel (logo, theme, etc.){/tr}" href="tiki-admin.php?page=look&amp;cookietab=2"} 
			{if $prefs.lang_use_db eq "y"}
				{if isset($smarty.session.interactive_translation_mode) && $smarty.session.interactive_translation_mode eq "on"}
					{icon _id=world_edit title="{tr}Toggle interactive translation off{/tr}" href="tiki-interactive_trans.php?interactive_translation_mode=off"}
				{else}
					{icon _id=world_edit title="{tr}Toggle interactive translation on{/tr}" href="tiki-interactive_trans.php?interactive_translation_mode=on"}
				{/if}
			{/if}
			{if $prefs.themegenerator_feature eq "y" and !empty($prefs.themegenerator_theme)}
				{icon _id="palette" title="{tr}Theme Genrator Editor{/tr}" href="#" onclick="openThemeGenDialog();return false;"}
			{/if}
		</div>  
	{/if}
{/tikimodule}