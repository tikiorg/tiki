{* navbar menu for admin_navbar.tpl *}
<ul class="nav navbar-nav clearfix">
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">{tr}Access{/tr} <b class="caret"></b></a>
		<ul class="dropdown-menu">
			{if $tiki_p_admin eq "y" and $tiki_p_admin_users eq "y"}
				<li><a href="tiki-adminusers.php">{tr}Users{/tr}</a></li>
			{/if}
			{if $tiki_p_admin eq "y"}
				<li><a href="tiki-admingroups.php">{tr}Groups{/tr}</a></li>
			{/if}
			{if $tiki_p_admin eq "y"}
				<li><a href="tiki-objectpermissions.php">{tr}Permissions{/tr}</a></li>
			{/if}
			{if $prefs.feature_banning eq "y" and $tiki_p_admin_banning eq "y"}
				<li class="divider"></li>
				<li><a href="tiki-admin_banning.php">{tr}Banning{/tr}</a></li>
			{/if}
		</ul>
	</li>
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">{tr}Content{/tr} <b class="caret"></b></a>
		<ul class="dropdown-menu">
			{if $prefs.feature_articles eq "y"}
				<li><a href="tiki-list_articles.php">{tr}Articles{/tr}</a></li>
			{/if}
			{if $prefs.feature_banners eq "y" and $tiki_p_admin_banners eq "y"}
				<li><a href="tiki-list_banners.php">{tr}Banners{/tr}</a></li>
			{/if}
			{if $prefs.feature_blogs eq "y"}
				<li><a href="tiki-list_blogs.php">{tr}Blogs{/tr}</a></li>
			{/if}
			{if $prefs.feature_calendar eq "y"}
				<li><a href="tiki-admin_calendars.php">{tr}Calendars{/tr}</a></li>
			{/if}
			{if $prefs.feature_categories eq "y"}
				<li><a href="tiki-admin_categories.php">{tr}Categories{/tr}</a></li>
			{/if}
			{if $tiki_p_admin_comments eq "y"}
				<li><a href="tiki-list_comments.php">{tr}Comments{/tr}</a></li>
			{/if}
			{if $prefs.feature_directory eq "y" and $tiki_p_admin_directory_cats eq "y"}
				<li><a href="tiki-directory_admin.php">{tr}Directories{/tr}</a></li>
			{/if}
			{if $tiki_p_admin_rssmodules eq "y"}
				<li><a href="tiki-admin_rssmodules.php">{tr}External Feeds{/tr}</a></li>
			{/if}
			{if $prefs.feature_file_galleries eq "y"}
				<li><a href="tiki-list_file_gallery.php">{tr}Files{/tr}</a></li>
			{/if}
			{if $prefs.feature_faqs eq "y" and $tiki_p_view_faqs eq "y"}
				<li><a href="tiki-list_faqs.php">{tr}FAQs{/tr}</a></li>
			{/if}
			{if $prefs.feature_forums eq "y"}
				<li><a href="tiki-admin_forums.php">{tr}Forums{/tr}</a></li>
			{/if}
			{if $prefs.feature_html_pages eq "y" and $tiki_p_edit_html_pages eq "y"}
				<li><a href="tiki-admin_html_pages.php">{tr}HTML Pages{/tr}</a></li>
			{/if}
			{if $prefs.feature_newsletters eq "y" and $tiki_p_admin_newsletters eq "y"}
				<li><a href="tiki-admin_newsletters.php">{tr}Newsletters{/tr}</a></li>
			{/if}
			{if $prefs.feature_polls eq "y" and $tiki_p_admin_polls eq "y"}
				<li><a href="tiki-admin_polls.php">{tr}Polls{/tr}</a></li>
			{/if}
			{if $prefs.feature_quizzes eq "y" and $tiki_p_admin_quizzes eq "y"}
				<li><a href="tiki-edit_quiz.php">{tr}Quizzes{/tr}</a></li>
			{/if}
			{if $prefs.feature_sheet eq "y" and $tiki_p_view_sheet eq "y"}
				<li><a href="tiki-sheets.php">{tr}Spreadsheets{/tr}</a></li>
			{/if}
			{if $prefs.feature_surveys eq "y" and $tiki_p_admin_surveys eq "y"}
				<li><a href="tiki-admin_surveys.php">{tr}Surveys{/tr}</a></li>
			{/if}
			{if $prefs.feature_freetags eq "y"}
				<li><a href="tiki-browse_freetags.php">{tr}Tags{/tr}</a></li>
			{/if}
			{if $prefs.feature_trackers eq "y" and $tiki_p_list_trackers eq "y"}
				<li><a href="tiki-list_trackers.php">{tr}Trackers{/tr}</a></li>
			{/if}
			{if $prefs.feature_wiki eq "y"}
				<li><a href="tiki-listpages.php">{tr}Wiki Pages{/tr}</a></li>
			{/if}
			{if $prefs.feature_wiki eq "y" and $prefs.feature_wiki_structure eq "y" and $tiki_p_view eq "y"}
				<li><a href="tiki-admin_structures.php">{tr}Wiki Structures{/tr}</a></li>
			{/if}
		</ul>
	</li>
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">{tr}System{/tr} <b class="caret"></b></a>
		<ul class="dropdown-menu">
			{if $tiki_p_admin eq "y"}
				<li><a href="{service controller=managestream action=list}">{tr}Activity Rules{/tr}</a></li>
			{/if}
			{if ($prefs.feature_wiki_templates eq "y" or $prefs.feature_cms_templates eq "y") and $tiki_p_edit_content_templates eq "y"}
				<li><a href="tiki-admin_content_templates.php ">{tr}Content Templates{/tr}</a></li>
			{/if}
			{if $prefs.feature_contribution eq "y" and $tiki_p_admin_contribution eq "y"}
				<li><a href="tiki-admin_contribution.php">{tr}Contributions{/tr}</a></li>
			{/if}
			{if $prefs.feature_dynamic_content eq "y" and $tiki_p_admin_dynamic eq "y"}
				<li><a href="tiki-list_contents.php">{tr}Dynamic Content{/tr}</a></li>
			{/if}
			{if $prefs.feature_hotwords eq "y"}
				<li><a href="tiki-admin_hotwords.php">{tr}Hotwords{/tr}</a></li>
			{/if}
			{if $prefs.lang_use_db eq "y" and $tiki_p_edit_languages eq "y"}
				<li><a href="tiki-edit_languages.php">{tr}Languages{/tr}</a></li>
			{/if}
			{if $prefs.feature_live_support eq "y" and $tiki_p_live_support_admin eq "y"}
				<li><a href="tiki-live_support_admin.php">{tr}Live Support{/tr}</a></li>
			{/if}
			{if $prefs.feature_mailin eq "y" and $tiki_p_admin_mailin eq "y"}
				<li><a href="tiki-admin_mailin.php">{tr}Mail-in{/tr}</a></li>
			{/if}
			{if $tiki_p_admin_notifications eq "y"}
				<li><a href="tiki-admin_notifications.php">{tr}Mail Notifications{/tr}</a></li>
			{/if}
			{if $tiki_p_edit_menu eq "y"}
				<li><a href="tiki-admin_menus.php">{tr}Menus{/tr}</a></li>
			{/if}
			{if $tiki_p_admin_modules eq "y"}
				<li><a href="tiki-admin_modules.php">{tr}Modules{/tr}</a></li>
			{/if}
			{if $prefs.feature_perspective eq "y"}
				<li><a href="tiki-edit_perspective.php">{tr}Perspectives{/tr}</a></li>
			{/if}
			{if $prefs.feature_shoutbox eq "y" and $tiki_p_admin_shoutbox eq "y"}
				<li><a href="tiki-shoutbox.php">{tr}Shoutbox{/tr}</a></li>
			{/if}
			{if $prefs.payment_feature eq "y"}
				<li><a href="tiki-admin_credits.php">{tr}User Credits{/tr}</a></li>
			{/if}
			{if $prefs.feature_theme_control eq "y" and $tiki_p_admin eq "y"}
				<li><a href="tiki-theme_control.php">{tr}Theme Control{/tr}</a></li>
			{/if}
			{if $tiki_p_admin_toolbars eq "y"}
				<li><a href="tiki-admin_toolbars.php">{tr}Toolbars{/tr}</a></li>
			{/if}
			{if $tiki_p_admin eq "y"}
				<li><a href="tiki-admin_transitions.php">{tr}Transitions{/tr}</a></li>
			{/if}
			{if $prefs.workspace_ui eq "y" and $tiki_p_admin eq "y"}
				<li><a href="tiki-ajax_services.php?controller=workspace&action=list_templates">{tr}Workspace Templates{/tr}</a></li>
			{/if}
			<li class="divider"></li>
			{if $tiki_p_plugin_approve eq "y"}
				<li><a href="tiki-plugins.php">{tr}Plugin Approval{/tr}</a></li>
			{/if}
			<li class="divider"></li>
			<li><a href="tiki-mods.php">{tr}Mods{/tr}</a></li>
		</ul>
	</li>
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">{tr}Tools{/tr} <b class="caret"></b></a>
		<ul class="dropdown-menu">
			{if $prefs.feature_actionlog eq "y" and $tiki_p_view_actionlog}
				<li><a href="tiki-admin_actionlog.php">{tr}Action Log{/tr}</a></li>
			{/if}
			{if $tiki_p_edit_cookies eq "y"}
				<li><a href="tiki-admin_cookies.php">{tr}Cookies{/tr}</a></li>
			{/if}
			<li><a href="tiki-admin_dsn.php">{tr}DSN/Content Authentication{/tr}</a></li>
			{if $prefs.feature_editcss eq "y" and $tiki_p_create_css eq "y"}
				<li><a href="tiki-edit_css.php">{tr}Edit CSS{/tr}</a></li>
			{/if}
			{if $prefs.feature_view_tpl eq "y" and $prefs.feature_edit_templates eq "y" and $tiki_p_edit_templates eq "y"}
				<li><a href="tiki-edit_templates.php">{tr}Edit TPL{/tr}</a></li>
			{/if}
			{if $prefs.cachepages eq "y" and $tiki_p_admin eq "y"}
				<li><a href="tiki-list_cache.php">{tr}External Pages Cache{/tr}</a></li>
			{/if}
			<li><a href="tiki-admin_external_wikis.php">{tr}External Wikis{/tr}</a></li>
			{if $tiki_p_admin_importer eq "y"}
				<li><a href="tiki-importer.php">{tr}Importer{/tr}</a></li>
			{/if}
			{if $prefs.feature_integrator eq "y" and $tiki_p_admin_integrator eq "y"}
				<li><a href="tiki-admin_integrator.php">{tr}Integrator{/tr}</a></li>
			{/if}
			<li><a href="tiki-phpinfo.php">{tr}PhpInfo{/tr}</a></li>
			{if $prefs.feature_referer_stats eq "y" and $tiki_p_view_referer_stats eq "y"}
				<li><a href="tiki-referer_stats.php">{tr}Referer Statistics{/tr}</a></li>
			{/if}
			{if $prefs.feature_search_stats eq "y" and $tiki_p_admin eq "y"}
				<li><a href="tiki-search_stats.php">{tr}Search Statistics{/tr}</a></li>
			{/if}
			<li><a href="tiki-admin_security.php">{tr}Security Admin{/tr}</a></li>
			<li><a href="tiki-check.php">{tr}Server Check{/tr}</a></li>
			{if $tiki_p_clean_cache eq "y"}
				<li><a href="tiki-admin_system.php">{tr}System Cache{/tr}</a></li>
			{/if}
			<li><a href="tiki-syslog.php">{tr}System Logs{/tr}</a></li>
			<li class="divider"></li>
			<li><a href="tiki-wizard_admin.php">{tr}Wizards{/tr}</a></li>
		</ul>
	</li>
</ul>
