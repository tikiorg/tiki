# $Id: tiki_1.7to1.8.sql,v 1.1 2004-10-28 21:28:27 damosoft Exp $

# The following script will update a tiki database from verion 1.7 to 1.8
# 
# To execute this file do the following:
#
# $ mysql -f dbname <tiki_1.7to1.8.sql
#
# where dbname is the name of your tiki database.
#
# For example, if your tiki database is named tiki (not a bad choice), type:
#
# $ mysql -f tiki <tiki_1.7to1.8.sql
# 
# You may execute this command as often as you like, 
# and may safely ignore any error messages that appear.

INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('allowRegister', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('anonCanEdit', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('article_comments_default_ordering', 'points_desc');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('article_comments_per_page', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('art_list_author', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('art_list_date', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('art_list_img', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('art_list_reads', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('art_list_size', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('art_list_title', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('art_list_topic', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('art_list_type','y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('art_list_expire','y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('art_list_visible','y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('auth_create_user_auth', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('auth_create_user_tiki', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('auth_ldap_adminpass', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('auth_ldap_adminuser', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('auth_ldap_basedn', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('auth_ldap_groupattr', 'cn');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('auth_ldap_groupdn', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('auth_ldap_groupoc', 'groupOfUniqueNames');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('auth_ldap_host', 'localhost');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('auth_ldap_memberattr', 'uniqueMember');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('auth_ldap_memberisdn', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('auth_ldap_port', '389');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('auth_ldap_scope', 'sub');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('auth_ldap_userattr', 'uid');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('auth_ldap_userdn', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('auth_ldap_useroc', 'inetOrgPerson');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('auth_method', 'tiki');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('auth_skip_admin', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('blog_comments_default_ordering', 'points_desc');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('blog_comments_per_page', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('blog_list_activity', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('blog_list_created', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('blog_list_description', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('blog_list_lastmodif', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('blog_list_order', 'created_desc');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('blog_list_posts', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('blog_list_title', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('blog_list_user', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('blog_list_visits', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('blog_spellcheck', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('cacheimages', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('cachepages', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('change_language', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('change_theme', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('cms_bot_bar', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('cms_left_column', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('cms_right_column', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('cms_spellcheck', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('cms_top_bar', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('contact_user', 'admin');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('count_admin_pvs', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('directory_columns', '3');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('directory_links_per_page', '20');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('directory_open_links', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('directory_validate_urls', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('direct_pagination', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('display_timezone', 'EST');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('faq_comments_default_ordering', 'points_desc');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('faq_comments_per_page', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_autolinks','y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_maps','n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_article_comments', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_articles', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_babelfish', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_babelfish_logo', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_backlinks', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_banners', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_banning', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_blog_comments', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_blogposts_comments', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_blog_rankings', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_blogs', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_bot_bar', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_calendar', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_categories', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_categoryobjects', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_categorypath', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_challenge', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_charts', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_chat', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_clear_passwords', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_cms_rankings', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_cms_templates', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_comm', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_contact', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_custom_home', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_debug_console', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_debugger_console', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_directory', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_drawings', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_dump', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_dynamic_content', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_editcss', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_edit_templates', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_eph', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_faq_comments', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_faqs', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_featuredLinks', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_file_galleries_comments', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_file_galleries', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_file_galleries_rankings', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_forum_parse', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_forum_quickjump', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_forum_rankings', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_forums', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_forum_topicd', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_galleries', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_gal_rankings', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_games', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_history', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_hotwords_nw', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_hotwords', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_html_pages', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_image_galleries_comments', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_integrator', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_lastChanges', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_left_column', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_likePages', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_listPages', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_live_support', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_menusfolderstyle', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_messages', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_minical', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_modulecontrols', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_newsletters', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_newsreader', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_notepad', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_obzip', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_page_title', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_phpopentracker', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_poll_comments', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_polls', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_quizzes', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_ranking', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_referer_stats', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_right_column', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_sandbox', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_search_fulltext', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_search_stats', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_search', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_shoutbox', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_smileys', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_stats', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_submissions', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_surveys', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_tasks', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_theme_control', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_top_bar', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_trackers', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_user_bookmarks', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_userfiles', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_usermenu', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_userPreferences', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_userVersions', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_user_watches', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_warn_on_edit', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_webmail', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_wiki_allowhtml', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_wiki_attachments', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_wiki_comments', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_wiki_description', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_wiki_discuss', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_wiki_footnotes', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_wiki_monosp', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_wiki_multiprint', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_wiki_notepad', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_wiki_pdf', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_wiki_pictures', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_wiki_rankings', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_wiki_tables', 'old');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_wiki_templates', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_wiki_undo', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_wiki_usrlock', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_wikiwords', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_wiki', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_workflow', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('feature_xmlrpc', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('fgal_list_created', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('fgal_list_description', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('fgal_list_files', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('fgal_list_hits', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('fgal_list_lastmodif', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('fgal_list_name', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('fgal_list_user', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('fgal_match_regex', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('fgal_nmatch_regex', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('fgal_use_db', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('fgal_use_dir', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('file_galleries_comments_default_ordering', 'points_desc');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('file_galleries_comments_per_page', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('forgotPass', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('forum_list_desc', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('forum_list_lastpost', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('forum_list_posts', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('forum_list_ppd', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('forum_list_topics', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('forum_list_visits', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('forums_ordering', 'created_desc');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('gal_list_created', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('gal_list_description', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('gal_list_imgs', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('gal_list_lastmodif', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('gal_list_name', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('gal_list_user', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('gal_list_visits', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('gal_match_regex', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('gal_nmatch_regex', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('gal_use_db', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('gal_use_dir', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('gal_use_lib', 'gd');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('home_file_gallery', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('http_domain', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('http_port', '80');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('http_prefix', '/');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('https_domain', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('https_login', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('https_login_required', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('https_port', '443');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('https_prefix', '/');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('image_galleries_comments_default_orderin', 'points_desc');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('image_galleries_comments_per_page', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('keep_versions', '1');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('language', 'en');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('lang_use_db', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('layout_section', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('long_date_format', '%A %d of %B,  %Y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('long_time_format', '%H:%M:%S %Z');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('maxArticles', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('maxRecords', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('max_rss_directories', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('max_rss_articles', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('max_rss_blog', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('max_rss_blogs', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('max_rss_file_galleries', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('max_rss_file_gallery', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('max_rss_forum', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('max_rss_forums', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('max_rss_mapfiles', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('max_rss_image_galleries', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('max_rss_image_gallery', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('max_rss_wiki', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('maxVersions', '0');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('min_pass_length', '1');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('modallgroups', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('pass_chr_num', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('pass_due', '999');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('poll_comments_default_ordering', 'points_desc');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('poll_comments_per_page', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('popupLinks', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('proxy_host', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('proxy_port', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('record_untranslated', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('registerPasscode', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('rememberme', 'disabled');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('remembertime', '7200');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('rnd_num_reg', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('rss_directories', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('rss_articles', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('rss_blog', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('rss_blogs', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('rss_file_galleries', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('rss_file_gallery', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('rss_forums', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('rss_forum', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('rss_mapfiles', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('rss_image_galleries', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('rss_image_gallery', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('rss_wiki', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('sender_email', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('email_encoding', 'utf-8');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('session_db', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('session_lifetime', '0');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('short_date_format', '%a %d of %b,  %Y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('short_time_format', '%H:%M %Z');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('siteTitle', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('slide_style', 'slidestyle.css');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('style', 'moreneat.css');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('system_os', 'unix');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('tikiIndex', 'tiki-index.php');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('tmpDir', 'temp');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('t_use_db', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('t_use_dir', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('uf_use_db', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('uf_use_dir', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('urlIndex', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('use_proxy', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('user_assigned_modules', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('useRegisterPasscode', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('userfiles_quota', '30');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('useUrlIndex', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('validateUsers', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('eponymousGroups', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('warn_on_edit_time', '2');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('webmail_max_attachment', '1500000');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('webmail_view_html', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('webserverauth', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_bot_bar', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_cache', '0');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_comments_default_ordering', 'points_desc');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_comments_per_page', '10');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_creator_admin', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_feature_copyrights', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_forum', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_forum_id', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wikiHomePage', 'HomePage');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_left_column', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wikiLicensePage', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_list_backlinks', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_list_comment', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_list_creator', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_list_hits', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_list_lastmodif', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_list_lastver', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_list_links', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_list_name', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_list_size', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_list_status', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_list_user', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_list_versions', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_page_regex', 'strict');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_right_column', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_spellcheck', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wikiSubmitNotice', '');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('wiki_top_bar', 'n');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('w_use_db', 'y');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('w_use_dir', '');
DELETE /* IGNORE */ FROM tiki_preferences WHERE name like 'art_view%';

INSERT /* IGNORE */ INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_map_edit', 'Can edit mapfiles', 'editor', 'maps');
INSERT /* IGNORE */ INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_map_create', 'Can create new mapfile', 'admin', 'maps');
INSERT /* IGNORE */ INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_map_delete', 'Can delete mapfiles', 'admin', 'maps');
INSERT /* IGNORE */ INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_map_view', 'Can view mapfiles', 'basic', 'maps');
INSERT /* IGNORE */ INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_access_closed_site', 'Can access site when closed', 'admin', 'tiki');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('map_path', '/var/www/html/map/');
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('default_map', 'pacific.map');

# adding field for group HomePage feature

# \todo lower case this field name: change to group_home
ALTER TABLE `users_groups` ADD `groupHome` VARCHAR( 255 ) AFTER `groupDesc` ;

ALTER TABLE `users_users` DROP `realname`;
ALTER TABLE `users_users` DROP `homePage`;
ALTER TABLE `users_users` DROP `country`;
ALTER TABLE `users_users` ADD `default_group` VARCHAR( 255 ) AFTER `provpass` ;

ALTER TABLE `tiki_structures` ADD `page_alias` VARCHAR( 240 ) DEFAULT '' NOT NULL AFTER `page` ;
ALTER TABLE `tiki_structures` ADD `structID` VARCHAR( 40 ) DEFAULT '' NOT NULL AFTER `page` ;

# Per-forum from address.  -rlpowell
ALTER TABLE `tiki_forums` ADD `outbound_from` VARCHAR( 250 ) AFTER `outbound_address` ;

# Message Ids and In-Reply-To, for strict threading that extends to
# e-mail. -rlpowell
ALTER TABLE `tiki_comments` ADD `message_id` VARCHAR( 250 ) AFTER `smiley` ;
ALTER TABLE `tiki_comments` ADD `in_reply_to` VARCHAR( 250 ) AFTER `message_id` ;

#ALTER TABLE `tiki_comments` MODIFY `message_id` VARCHAR( 250 ) AFTER `smiley` ;
#ALTER TABLE `tiki_comments` MODIFY `in_reply_to` VARCHAR( 250 ) AFTER `message_id` ;

# Add field in tiki_comments for comment_rating - xenfasa
ALTER TABLE `tiki_comments` ADD `comment_rating` TINYINT( 2 ) ;

# Some more indexes for performance 
CREATE INDEX `hash` on `tiki_comments`(`hash`);
CREATE INDEX `in_reply_to` on `tiki_comments`(`in_reply_to`);

CREATE TABLE /* IF NOT EXISTS */ tiki_download (
  id int(11) NOT NULL auto_increment,
  object varchar(255) NOT NULL default '',
  userId int(8) NOT NULL default '0',
  type varchar(20) NOT NULL default '',
  date int(14) NOT NULL default '0',
  IP varchar(50) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY object (object,userId,type),
  KEY userId (userId),
  KEY type (type),
  KEY date (date)
);

ALTER TABLE `tiki_pages` ADD `wiki_cache` int(10) default 0 AFTER `cache` ;

ALTER TABLE tiki_forums ADD forum_last_n int(2);
UPDATE tiki_forums SET forum_last_n = 0;

CREATE  TABLE tiki_dynamic_variables( name varchar( 40  ) not null,  data text,  PRIMARY  KEY ( name )  );
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_edit_dynvar', 'Can edit dynamic variables', 'editors', 'wiki');

ALTER TABLE tiki_newsletters ADD allowUserSub char(1) DEFAULT 'y' AFTER `users`;
ALTER TABLE tiki_newsletters ADD unsubMsg char(1) DEFAULT 'y' AFTER `allowAnySub`;
ALTER TABLE tiki_newsletters ADD validateAddr char(1) DEFAULT 'y' AFTER `unsubMsg`;
UPDATE tiki_newsletters SET allowUserSub = 'y', unsubMsg = 'y', validateAddr = 'y';

# new switches for rss modules
ALTER TABLE tiki_rss_modules ADD showTitle char(1) DEFAULT 'n' AFTER `lastUpdated`;
ALTER TABLE tiki_rss_modules ADD showPubDate char(1) DEFAULT 'n' AFTER `showTitle`;

# remove unused columns from tiki_user_assigned_modules
ALTER TABLE tiki_user_assigned_modules DROP COLUMN title;
ALTER TABLE tiki_user_assigned_modules DROP COLUMN cache_time;
ALTER TABLE tiki_user_assigned_modules DROP COLUMN rows;
ALTER TABLE tiki_user_assigned_modules DROP COLUMN groups;
ALTER TABLE tiki_user_assigned_modules DROP COLUMN params;

#
# Table structure for table `tiki_rss_feeds`
#
# Creation: Oct 14, 2003 at 20:34 PM
# Last update: Oct 14, 2003 at 20:34 PM
#

DROP TABLE IF EXISTS tiki_rss_feeds;
CREATE TABLE tiki_rss_feeds (
  name varchar(30) NOT NULL default '',
  rssVer char(1) NOT NULL default '1',
  refresh int(8) default '300',
  lastUpdated int(14) default NULL,
  cache longblob,
  PRIMARY KEY  (name, rssVer)
) TYPE=MyISAM;
# --------------------------------------------------------

# added a legth field in wiki pages table for db abstraction needs (length() is not common in sql)
ALTER TABLE `tiki_pages` ADD `page_size` int(10) unsigned default 0;
UPDATE tiki_pages set page_size=length(data);

DROP TABLE IF EXISTS tiki_article_types;
CREATE TABLE tiki_article_types (
  type varchar(50) NOT NULL,
  use_ratings varchar(1) default NULL,
  show_pre_publ varchar(1) default NULL,
  show_post_expire varchar(1) default 'y',
  heading_only varchar(1) default NULL,
  allow_comments varchar(1) default 'y',
  show_image varchar(1) default 'y',
  show_avatar varchar(1) default NULL,
  show_author varchar(1) default 'y',
  show_pubdate varchar(1) default 'y',
  show_expdate varchar(1) default NULL,
  show_reads varchar(1) default 'y',
  show_size varchar(1) default 'y',
  creator_edit varchar(1) default NULL,
  comment_can_rate_article varchar(1) default NULL,
  PRIMARY KEY  (type)
) TYPE=MyISAM ;

INSERT IGNORE INTO tiki_article_types(type) VALUES ('Article');
INSERT IGNORE INTO tiki_article_types(type,use_ratings) VALUES ('Review','y');
INSERT IGNORE INTO tiki_article_types(type,show_post_expire) VALUES ('Event','n');
INSERT IGNORE INTO tiki_article_types(type,show_post_expire,heading_only,allow_comments) VALUES ('Classified','n','y','n');

ALTER TABLE tiki_articles ADD COLUMN expireDate int(14) default NULL AFTER `publishDate`;
UPDATE tiki_articles SET expireDate = 1104555540 WHERE expireDate is null;
ALTER TABLE tiki_articles ADD COLUMN state varchar(1) default 's' AFTER `title`;
UPDATE tiki_articles SET state = 'p';

INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_view_categories', 'Can browse categories', 'registered', 'tiki');

INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_view_eph', 'Can view ephemerides', 'registered', 'tiki');

ALTER TABLE tiki_received_articles ADD COLUMN expireDate int(14) default NULL AFTER `publishDate`;
ALTER TABLE tiki_submissions ADD COLUMN expireDate int(14) default NULL AFTER `publishDate`;

ALTER TABLE `tiki_modules` CHANGE `title` `title` VARCHAR( 255 ) DEFAULT NULL;

INSERT /* IGNORE */ INTO tiki_preferences(name,value) VALUES ('rssfeed_default_version','2');
INSERT /* IGNORE */ INTO tiki_preferences(name,value) VALUES ('rssfeed_language','en-us');
INSERT /* IGNORE */ INTO tiki_preferences(name,value) VALUES ('rssfeed_editor','');
INSERT /* IGNORE */ INTO tiki_preferences(name,value) VALUES ('rssfeed_publisher','');
INSERT /* IGNORE */ INTO tiki_preferences(name,value) VALUES ('rssfeed_webmaster','');
INSERT /* IGNORE */ INTO tiki_preferences(name,value) VALUES ('rssfeed_creator','');
INSERT /* IGNORE */ INTO tiki_preferences(name,value) VALUES ('rssfeed_css','y');

# blogs had the same problem than wiki pages : adding a field for size to avoid run-time calculation
ALTER TABLE `tiki_blog_posts` ADD `data_size` int(10) unsigned default 0;
UPDATE `tiki_blog_posts` set `data_size`=length(`data`);

#so admin  and users have option of viewing the tpl for a page
INSERT /* IGNORE */ INTO tiki_preferences(name,value) VALUES ('feature_view_tpl','y');


#
# Table structure for table 'tiki_integrator_reps'
#
DROP TABLE IF EXISTS tiki_integrator_reps;
CREATE TABLE tiki_integrator_reps (
  repID int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  path varchar(255) NOT NULL default '',
  start_page varchar(255) NOT NULL default '',
  css_file varchar(255) NOT NULL default '',
  visibility char(1) NOT NULL default 'y',
  cacheable char(1) NOT NULL default 'y',
  expiration int(11) NOT NULL default '0',
  description text NOT NULL,
  PRIMARY KEY  (repID)
) TYPE=MyISAM;

#
# Dumping data for table 'tiki_integrator_reps'
#
INSERT INTO tiki_integrator_reps VALUES ('1','Doxygened (1.3.4) Documentation','','index.html','doxygen.css','n','y','0','Use this repository as rule source for all your repositories based on doxygened docs. To setup yours just add new repository and copy rules from this repository :)');

#
# Table structure for table 'tiki_integrator_rules'
#
DROP TABLE IF EXISTS tiki_integrator_rules;
CREATE TABLE tiki_integrator_rules (
  ruleID int(11) NOT NULL auto_increment,
  repID int(11) NOT NULL default '0',
  ord int(2) unsigned NOT NULL default '0',
  srch blob NOT NULL,
  repl blob NOT NULL,
  type char(1) NOT NULL default 'n',
  casesense char(1) NOT NULL default 'y',
  rxmod varchar(20) NOT NULL default '',
  enabled char(1) NOT NULL default 'n',
  description text NOT NULL,
  PRIMARY KEY (ruleID),
  KEY repID (repID)
) TYPE=MyISAM;

#
# Dumping data for table 'tiki_integrator_rules'
#
INSERT INTO tiki_integrator_rules VALUES ('1','1','1','.*<body[^>]*?>(.*?)</body.*','\1','y','n','i','y','Extract code between <BODY> tags');
INSERT INTO tiki_integrator_rules VALUES ('2','1','2','img src=(\"|\')(?!http://)','img src=\1{path}/','y','n','i','y','Fix images path');
INSERT INTO tiki_integrator_rules VALUES ('3','1','3','href=(\"|\')(?!(#|(http|ftp)://|mailto:))','href=\1tiki-integrator.php?repID={repID}&file=','y','n','i','y','Relace internal (local) links to integrator. Dont touch an external links.');

#
# Integrator permissions
#
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_admin_integrator', 'Can admin integrator repositories and rules', 'admin', 'tiki');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_view_integrator', 'Can view integrated repositories', 'basic', 'tiki');
 
#
# New Search
#

DROP TABLE IF EXISTS tiki_searchindex;
CREATE TABLE tiki_searchindex(
  searchword varchar(80) NOT NULL default '',
  location varchar(80) NOT NULL default '',
  page varchar(255) NOT NULL default '',
  count int(11) NOT NULL default '1',
  last_update int(11) NOT NULL default '0',
  PRIMARY KEY (searchword,location,page),
  KEY (last_update)
) TYPE=MyISAM;

# searchword caching table for search syllables
DROP TABLE IF EXISTS tiki_searchwords;
CREATE TABLE tiki_searchwords(
  syllable varchar(80) NOT NULL default '',
  searchword varchar(80) NOT NULL default '',
  PRIMARY KEY  (syllable,searchword)
) TYPE=MyISAM;


#
# session stored in db
#
DROP TABLE IF EXISTS sessions;

CREATE TABLE sessions(
  SESSKEY char(32) NOT NULL,
  EXPIRY int(11) unsigned NOT NULL,
  EXPIREREF varchar(64),
  DATA text NOT NULL,
  PRIMARY KEY  (SESSKEY),
  KEY EXPIRY (EXPIRY)
) TYPE=MyISAM;

# 
# Changing language code from 'sp' to 'es'
# 


UPDATE tiki_calendar_items SET lang = 'es' WHERE lang='sp';
UPDATE tiki_language SET lang = 'es' WHERE lang='sp';
UPDATE tiki_languages SET lang = 'es' WHERE lang='sp';
UPDATE tiki_menu_languages SET language = 'es' WHERE language='sp';
UPDATE tiki_untranslated SET lang = 'es' WHERE lang='sp';
UPDATE tiki_preferences SET value = 'es' WHERE value='sp' and name='language';
UPDATE tiki_user_preferences SET value = 'es' WHERE value='sp' and prefName='language';

# added on 2003-11-18 by mose
INSERT /* IGNORE */ INTO tiki_preferences (name, value) VALUES ('shoutbox_autolink', 'n');

# added on 2003-11-19 by mose per dgd wish
CREATE TABLE `tiki_quicktags` (
  `tagId` int(4) unsigned NOT NULL auto_increment,
  `taglabel` varchar(255) default NULL,
  `taginsert` varchar(255) default NULL,
  `tagicon` varchar(255) default NULL,
  PRIMARY KEY  (`tagId`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

INSERT INTO tiki_quicktags (tagId, taglabel, taginsert, tagicon) VALUES (1,'bold','__text__','images/ed_format_bold.gif');
INSERT INTO tiki_quicktags (tagId, taglabel, taginsert, tagicon) VALUES (2,'italic','\'\'text\'\'','images/ed_format_italic.gif');
INSERT INTO tiki_quicktags (tagId, taglabel, taginsert, tagicon) VALUES (3,'underline','===text===','images/ed_format_underline.gif');
INSERT INTO tiki_quicktags (tagId, taglabel, taginsert, tagicon) VALUES (4,'table','||r1c1|r1c2||r2c1|r2c2||','images/insert_table.gif');
INSERT INTO tiki_quicktags (tagId, taglabel, taginsert, tagicon) VALUES (5,'table new','||r1c1|r1c2\nr2c1|r2c2||','images/insert_table.gif');
INSERT INTO tiki_quicktags (tagId, taglabel, taginsert, tagicon) VALUES (6,'external link','[http://example.com|text]','images/ed_link.gif');
INSERT INTO tiki_quicktags (tagId, taglabel, taginsert, tagicon) VALUES (7,'wiki link','((text))','images/ed_copy.gif');
INSERT INTO tiki_quicktags (tagId, taglabel, taginsert, tagicon) VALUES (8,'heading1','!text','images/ed_custom.gif');
INSERT INTO tiki_quicktags (tagId, taglabel, taginsert, tagicon) VALUES (9,'title bar','-=text=-','images/fullscreen_maximize.gif');
INSERT INTO tiki_quicktags (tagId, taglabel, taginsert, tagicon) VALUES (10,'box','^text^','images/ed_about.gif');
INSERT INTO tiki_quicktags (tagId, taglabel, taginsert, tagicon) VALUES (11,'rss feed','{rss id= }','images/ico_link.gif');
INSERT INTO tiki_quicktags (tagId, taglabel, taginsert, tagicon) VALUES (12,'dynamic content','{content id= }','images/book.gif');
INSERT INTO tiki_quicktags (tagId, taglabel, taginsert, tagicon) VALUES (13,'tagline','{cookie}','images/footprint.gif');
INSERT INTO tiki_quicktags (tagId, taglabel, taginsert, tagicon) VALUES (14,'hr','---','images/ed_hr.gif');
INSERT INTO tiki_quicktags (tagId, taglabel, taginsert, tagicon) VALUES (15,'center text','::text::','images/ed_align_center.gif');
INSERT INTO tiki_quicktags (tagId, taglabel, taginsert, tagicon) VALUES (16,'colored text','~~#FF0000:text~~','images/fontfamily.gif');
INSERT INTO tiki_quicktags (tagId, taglabel, taginsert, tagicon) VALUES (17,'dynamic variable','%text%','images/book.gif');
INSERT INTO tiki_quicktags (tagId, taglabel, taginsert, tagicon) VALUES (18,'image','{img src= width= height= align= desc= link= }','images/ed_image.gif');

# added on 2003-11-20 by mose for countering gustavo
# variabilisation of application_menu
# 
ALTER TABLE tiki_menu_options ADD COLUMN section varchar(255) default NULL;
ALTER TABLE tiki_menu_options ADD COLUMN perm varchar(255) default NULL;
ALTER TABLE tiki_menu_options ADD COLUMN groupname varchar(255) default NULL;

INSERT INTO tiki_menus (menuId,name,description,type) VALUES ('42','Application menu','Main extensive navigation menu','d');

delete from tiki_menu_options where menuId=42;
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Home','tiki-index.php',10,'','','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Chat','tiki-chat.php',15,'feature_chat','tiki_p_chat','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Contact us','tiki-contact.php',20,'feature_contact','','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Stats','tiki-stats.php',23,'feature_stats','tiki_p_view_stats','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Categories','tiki-categories.php',25,'feature_categories','tiki_p_view_categories','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Games','tiki-games.php',30,'feature_games','tiki_p_play_games','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Calendar','tiki-calendar.php',35,'feature_calendar','tiki_p_view_calendar','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','(debug)','javascript:toggle(\'debugconsole\')',40,'feature_debug_console','tiki_p_admin','');

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','MyTiki (click!)','tiki-my_tiki.php',50,'','','Registered');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Preferences','tiki-user_preferences.php',55,'feature_userPreferences','','Registered');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Messages','messu-mailbox.php',60,'feature_messages','tiki_p_messages','Registered');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Tasks','tiki-user_tasks.php',65,'feature_tasks','tiki_p_tasks','Registered');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Bookmarks','tiki-user_bookmarks.php',70,'feature_user_bookmarks','tiki_p_create_bookmarks','Registered');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Modules','tiki-user_assigned_modules.php',75,'user_assigned_modules','tiki_p_configure_modules','Registered');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Newsreader','tiki-newsreader_servers.php',80,'feature_newsreader','tiki_p_newsreader','Registered');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Webmail','tiki-webmail.php',85,'feature_webmail','tiki_p_use_webmail','Registered');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Notepad','tiki-notepad_list.php',90,'feature_notepad','tiki_p_notepad','Registered');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','My files','tiki-userfiles.php',95,'feature_userfiles','tiki_p_userfiles','Registered');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','User menu','tiki-usermenu.php',100,'feature_usermenu','','Registered');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Mini calendar','tiki-minical.php',105,'feature_minical','','Registered');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','My watches','tiki-user_watches.php',110,'feature_user_watches','','Registered');

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Workflow','tiki-g-user_processes.php',150,'feature_workflow','tiki_p_use_workflow','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Admin processes','tiki-g-admin_processes.php',155,'feature_workflow','tiki_p_admin_workflow','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Monitor processes','tiki-g-monitor_processes.php',160,'feature_workflow','tiki_p_admin_workflow','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Monitor activities','tiki-g-monitor_activities.php',165,'feature_workflow','tiki_p_admin_workflow','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Monitor instances','tiki-g-monitor_instances.php',170,'feature_workflow','tiki_p_admin_workflow','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','User processes','tiki-g-user_processes.php',175,'feature_workflow','tiki_p_use_workflow','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','User activities','tiki-g-user_activities.php',180,'feature_workflow','tiki_p_use_workflow','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','User instances','tiki-g-user_instances.php',185,'feature_workflow','tiki_p_use_workflow','');

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Wiki','tiki-index.php',200,'feature_wiki','tiki_p_view','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Wiki Home','tiki-index.php',202,'feature_wiki','tiki_p_view','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Last Changes','tiki-lastchanges.php',205,'feature_wiki,feature_lastChanges','tiki_p_view','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Dump','dump/new.tar',210,'feature_wiki,feature_dump','tiki_p_view','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Rankings','tiki-wiki_rankings.php',215,'feature_wiki,feature_wiki_rankings','tiki_p_view','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','List pages','tiki-listpages.php',220,'feature_wiki,feature_listPages','tiki_p_view','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Orphan pages','tiki-orphan_pages.php',225,'feature_wiki,feature_listPages','tiki_p_view','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Sandbox','tiki-editpage.php?page=sandbox',230,'feature_wiki,feature_sandbox','tiki_p_view','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Print','tiki-print_pages.php',235,'feature_wiki,feature_wiki_multiprint','tiki_p_view','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Send pages','tiki-send_objects.php',240,'feature_wiki,feature_comm','tiki_p_view,tiki_p_send_pages','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Received pages','tiki-received_pages.php',245,'feature_wiki,feature_comm','tiki_p_view,tiki_p_admin_received_pages','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Structures','tiki-admin_structures.php',250,'feature_wiki','tiki_p_edit_structures','');

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Image Galleries','tiki-galleries.php',300,'feature_galleries','tiki_p_view_image_gallery','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Galleries','tiki-galleries.php',305,'feature_galleries','tiki_p_view_image_gallery','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Rankings','tiki-galleries_rankings.php',310,'feature_galleries,feature_gal_rankings','tiki_p_view_image_gallery','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Upload image','tiki-upload_image.php',315,'feature_galleries','tiki_p_upload_images','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','System gallery','tiki-list_gallery.php?galleryId=0',320,'feature_galleries','tiki_p_admin_galleries','');

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Articles','tiki-view_articles.php',350,'feature_articles','tiki_p_read_article','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Articles home','tiki-view_articles.php',355,'feature_articles','tiki_p_read_article','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','List articles','tiki-list_articles.php',360,'feature_articles','tiki_p_read_article','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Rankings','tiki-cms_rankings.php',365,'feature_articles,feature_cms_ranking','tiki_p_read_article','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Submit article','tiki-edit_submissions.php',370,'feature_articles,feature_submissions','tiki_p_read_article,tiki_p_submit_article','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','View submissions','tiki-list_submissions.php',375,'feature_articles,feature_submissions','tiki_p_read_article,tiki_p_submit_article','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','View submissions','tiki-list_submissions.php',375,'feature_articles,feature_submissions','tiki_p_read_article,tiki_p_approve_submission','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','View submissions','tiki-list_submissions.php',375,'feature_articles,feature_submissions','tiki_p_read_article,tiki_p_remove_submission','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Edit article','tiki-edit_article.php',380,'feature_articles','tiki_p_read_article,tiki_p_edit_article','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Send articles','tiki-send_objects.php',385,'feature_articles,feature_comm','tiki_p_read_article,tiki_p_send_articles','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Received articles','tiki-send_objects.php',385,'feature_articles,feature_comm','tiki_p_read_article,tiki_p_send_articles','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Admin topics','tiki-admin_topics.php',390,'feature_articles','tiki_p_read_article,tiki_p_admin_cms','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Admin types','tiki-articles_types.php',395,'feature_articles','tiki_p_read_article,tiki_p_admin_cms','');

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Blogs','tiki-list_blogs.php',450,'feature_blogs','tiki_p_read_blog','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','List blogs','tiki-list_blogs.php',455,'feature_blogs','tiki_p_read_blog','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Rankings','tiki-blogs_rankings.php',460,'feature_blogs,feature_blog_rankings','tiki_p_read_blog','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Create/Edit blog','tiki-edit_blog.php',465,'feature_blogs','tiki_p_read_blog,tiki_p_create_blogs','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Post','tiki-blog_post.php',470,'feature_blogs','tiki_p_read_blog,tiki_p_blog_post','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Admin posts','tiki-list_posts.php',475,'feature_blogs','tiki_p_read_blog,tiki_p_blog_admin','');

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Forums','tiki-forums.php',500,'feature_forums','tiki_p_forum_read','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','List forums','tiki-forums.php',505,'feature_forums','tiki_p_forum_read','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Rankings','tiki-forums_rankings.php',510,'feature_forums,feature_forum_rankings','tiki_p_forum_read','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Admin forums','tiki-admin_forums.php',515,'feature_forums','tiki_p_forum_read,tiki_p_admin_forum','');

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Directory','tiki-directory_browse.php',550,'feature_directory','tiki_p_view_directory','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Submit a new link','tiki-directory_add_site.php',555,'feature_directory','tiki_p_view_directory','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Browse directory','tiki-directory_browse.php',560,'feature_directory','tiki_p_view_directory','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Admin directory','tiki-directory_admin.php',565,'feature_directory','tiki_p_view_directory,tiki_p_admin_directory_cats','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Admin directory','tiki-directory_admin.php',565,'feature_directory','tiki_p_view_directory,tiki_p_admin_directory_sites','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Admin directory','tiki-directory_admin.php',565,'feature_directory','tiki_p_view_directory,tiki_p_validate_links','');

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','File Galleries','tiki-file_galleries.php',600,'feature_file_galleries','tiki_p_view_file_gallery','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','List galleries','tiki-file_galleries.php',605,'feature_file_galleries','tiki_p_view_file_gallery','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Rankings','tiki-file_galleries_rankings.php',610,'feature_file_galleries,feature_file_galleries_rankings','tiki_p_view_file_gallery','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Upload  File','tiki-upload_file.php',615,'feature_file_galleries','tiki_p_view_file_gallery,tiki_p_upload_files','');

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','FAQs','tiki-list_faqs.php',650,'feature_faqs','tiki_p_view_faqs','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','List FAQs','tiki-list_faqs.php',665,'feature_faqs','tiki_p_view_faqs','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Admin FAQs','tiki-list_faqs.php',660,'feature_faqs','tiki_p_admin_faqs','');

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Maps','tiki-map.phtml',700,'feature_maps','tiki_p_map_view','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Mapfiles','tiki-map_edit.php',705,'feature_maps','tiki_p_map_view','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Layer management','tiki-map_upload.php',710,'feature_maps','tiki_p_map_edit','');

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Quizzes','tiki-list_quizzes.php',750,'feature_quizzes','','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','List quizzes','tiki-list_quizzes.php',755,'feature_quizzes','','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Quiz stats','tiki-quiz_stats.php',760,'feature_quizzes','tiki_p_view_quiz_stats','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Admin quiz','tiki-edit_quiz.php',765,'feature_quizzes','tiki_p_admin_quizzes','');

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Trackers','tiki-list_trackers.php',800,'feature_trackers','tiki_p_view_trackers','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','List trackers','tiki-list_trackers.php',805,'feature_trackers','tiki_p_view_trackers','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Admin trackers','tiki-admin_trackers.php',810,'feature_trackers','tiki_p_admin_trackers','');

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Surveys','tiki-list_surveys.php',850,'feature_surveys','tiki_p_take_survey','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','List surveys','tiki-list_surveys.php',855,'feature_surveys','tiki_p_take_survey','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Stats','tiki-surveys_stats.php',860,'feature_surveys','tiki_p_view_survey_stats','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Admin surveys','tiki-admin_surveys.php',865,'feature_surveys','tiki_p_admin_surveys','');

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Newsletters','tiki-newsletters.php',900,'feature_newsletters','tiki_p_subscribe_newsletters','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Send newsletters','tiki-send_newsletters.php',905,'feature_newsletters','tiki_p_admin_newsletters','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Admin newsletters','tiki-admin_newsletters.php',910,'feature_newsletters','tiki_p_admin_newsletters','');

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Ephemerides','tiki-eph.php',950,'feature_eph','tiki_p_view_eph','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Admin','tiki-eph_admin.php',955,'feature_eph','tiki_p_eph_admin','');

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Charts','tiki-charts.php',1000,'feature_charts','tiki_p_view_chart','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Admin Charts','tiki-admin_charts.php',1005,'feature_charts','tiki_p_admin_charts','');

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_admin_chat','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_admin_categories','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_admin_banners','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_edit_templates','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_edit_cookies','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_admin_dynamic','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_admin_mailin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_edit_content_templates','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_edit_html_pages','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_view_referer_stats','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_admin_drawings','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_admin_shoutbox','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_admin_live_support','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','user_is_operator','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'feature_integrator','tiki_p_admin_integrator','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Live support','tiki-live_support_admin.php',1055,'feature_live_support','tiki_p_live_support_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Live support','tiki-live_support_admin.php',1055,'feature_live_support','user_is_operator','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Banning','tiki-admin_banning.php',1060,'feature_banning','tiki_p_admin_banning','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Calendar','tiki-admin_calendars.php',1065,'feature_calendar','tiki_p_admin_calendar','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Users','tiki-adminusers.php',1070,'','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Groups','tiki-admingroups.php',1075,'','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Cache','tiki-list_cache.php',1080,'','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Modules','tiki-admin_modules.php',1085,'','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Links','tiki-admin_links.php',1090,'','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Hotwords','tiki-admin_hotwords.php',1095,'','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','RSS modules','tiki-admin_rssmodules.php',1100,'','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Menus','tiki-admin_menus.php',1105,'','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Polls','tiki-admin_polls.php',1110,'','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Backups','tiki-backup.php',1115,'','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Mail notifications','tiki-admin_notifications.php',1120,'','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Search stats','tiki-search_stats.php',1125,'','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Theme control','tiki-theme_control.php',1130,'','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','QuickTags','tiki-admin_quicktags.php',1135,'','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Chat','tiki-admin_chat.php',1140,'','tiki_p_admin_chat','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Categories','tiki-admin_categories.php',1145,'','tiki_p_admin_categories','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Banners','tiki-list_banners.php',1150,'','tiki_p_admin_banners','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Edit templates','tiki-edit_templates.php',1155,'','tiki_p_edit_templates','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Drawings','tiki-admin_drawings.php',1160,'','tiki_p_admin_drawings','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Dynamic content','tiki-list_contents.php',1165,'','tiki_p_admin_dynamic','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Cookies','tiki-admin_cookies.php',1170,'','tiki_p_edit_cookies','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Mail-in','tiki-admin_mailin.php',1175,'','tiki_p_admin_mailin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Content templates','tiki-admin_content_templates.php',1180,'','tiki_p_edit_content_templates','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','HTML pages','tiki-admin_html_pages.php',1185,'','tiki_p_edit_html_pages','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Shoutbox','tiki-shoutbox.php',1190,'','tiki_p_admin_shoutbox','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Referer stats','tiki-referer_stats.php',1195,'','tiki_p_view_referer_stats','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Edit languages','tiki-edit_languages.php',1200,'','tiki_p_edit_languages,lang_use_db','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Integrator','tiki-admin_integrator.php',1205,'feature_integrator','tiki_p_admin_integrator','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Import PHPWiki Dump','tiki-import_phpwiki.php',1210,'','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','phpinfo','tiki-phpinfo.php',1215,'','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','DSN','tiki-admin_dsn.php',1220,'','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','External wikis','tiki-admin_external_wikis.php',1225,'','tiki_p_admin','');

# added on 2003-11-21 by gmuslera
# Updating the tiki-admin_html_pages title from Mail-in to HTML pages
UPDATE tiki_menu_options SET name = 'HTML pages' WHERE position='1185';

# added on 2003-11-24 by mose (typo)
UPDATE tiki_menu_options SET url = 'tiki-edit_submission.php' WHERE url = 'tiki-edit_submissions.php';

# added on 2003-11-24 by mose (strange alien typo field no idea why it was there)
ALTER TABLE `tiki_charts` DROP `hist`;

# added on 2003-11-25 by mose (phplayers pref)
INSERT INTO tiki_preferences (name, value) VALUES ('feature_phplayers', 'n');

# added on 2003-12-01 by mose (jscalendar pref)
INSERT INTO tiki_preferences (name, value) VALUES ('feature_jscalendar', 'n');

# added on 2003-12-02 by mose (make everything optionnal !)
INSERT INTO tiki_preferences (name, value) VALUES ('wiki_uses_slides', 'n');

# added on 2003-12-08 by mose (adding power to trackers)
ALTER TABLE `tiki_tracker_fields` ADD `position` INT( 4 ) AFTER `options` ;

# added on 2003-12-10 by mose (adding options for trackers listing)
ALTER TABLE `tiki_trackers` ADD `showComments` CHAR( 1 ) AFTER `useComments` ;
ALTER TABLE `tiki_trackers` ADD `showAttachments` CHAR( 1 ) AFTER `useAttachments` ;

# added on 2003-12-15 by baptiste (adding anonymous posts discard possibility to mail-in feature)
ALTER TABLE tiki_mailin_accounts ADD anonymous CHAR(1) NOT NULL DEFAULT 'y';

# added on 2003-12-16 by mose (adding power to trackers attachments)
ALTER TABLE `tiki_tracker_item_attachments` ADD `longdesc` blob after `data` ;
ALTER TABLE `tiki_tracker_item_attachments` ADD `version` varchar(40) after `downloads` ;
ALTER TABLE `tiki_tracker_item_attachments` CHANGE `itemId` `itemId` INT( 12 ) DEFAULT '0' NOT NULL ;
ALTER TABLE `tiki_trackers` ADD `orderAttachments` VARCHAR( 255 ) DEFAULT 'filename,created,filesize,downloads,desc' NOT NULL AFTER `showAttachments` ;

# added on 2003-16-19 by mose (typo reported by xenfasa)
UPDATE tiki_menu_options set `url`='tiki-forum_rankings.php' where `url`='tiki-forums_rankings.php';

# added on 2003-12-20 by gmuslera
# Updating the URL for games to tiki-list_games.php instead of tiki-games.php
UPDATE tiki_menu_options SET `url` = 'tiki-list_games.php' WHERE `url`='tiki-games.php';

# added on 2003-12-23 by mose
UPDATE users_permissions SET `level`='editors' WHERE `level`='editor';

# added on 2004-01-02 by mose
DROP TABLE IF EXISTS tiki_searchsyllable;
CREATE TABLE tiki_searchsyllable(
  syllable varchar(80) NOT NULL default '',
  lastUsed int(11) NOT NULL default '0',
  lastUpdated int(11) NOT NULL default '0',
  PRIMARY KEY  (syllable),
  KEY lastUsed (lastUsed)
) TYPE=MyISAM;

# added on 2004-01-02 by xenfasa (typo in file name)
UPDATE tiki_menu_options set `url`='tiki-browse_categories.php' where `url`='tiki-categories.php';

# added on 2004-01-08 by Chealer9 (typo reported by xenfasa)
UPDATE tiki_menu_options set `url`='tiki-article_types.php' where `url`='tiki-articles_types.php';

# added on 2004-01-10 by redflo: new search settings for optimal tuning
INSERT /* IGNORE */ INTO tiki_preferences(name,value) VALUES ('search_refresh_rate','5');
INSERT /* IGNORE */ INTO tiki_preferences(name,value) VALUES ('search_min_wordlength','3');
INSERT /* IGNORE */ INTO tiki_preferences(name,value) VALUES ('search_max_syllwords','100');
INSERT /* IGNORE */ INTO tiki_preferences(name,value) VALUES ('search_lru_purge_rate','5');
INSERT /* IGNORE */ INTO tiki_preferences(name,value) VALUES ('search_lru_length','100');
INSERT /* IGNORE */ INTO tiki_preferences(name,value) VALUES ('search_syll_age','48');

# added on 2004-03-18 by baptiste (adding anonymous posts discard possibility to mail-in feature)
ALTER TABLE tiki_mailin_accounts ADD anonymous CHAR(1) NOT NULL DEFAULT 'y';

# added on 2004-03-26 by baptiste (adding attachments handling to the mail-in feature)
ALTER TABLE tiki_mailin_accounts ADD attachments CHAR(1) NOT NULL DEFAULT 'n';

