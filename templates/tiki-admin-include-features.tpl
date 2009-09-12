{* $Id$ *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Please see the <a class='rbox-link' target='tikihelp' href='http://doc.tikiwiki.org/Features'>evaluation of each feature</a> on Tiki's developer site.{/tr}{/remarksbox}

	<form class="admin" id="features" name="features" action="tiki-admin.php?page=features" method="post">
		<div class="heading input_submit_container" style="text-align: right">
			<input type="submit" name="features" value="{tr}Apply{/tr}" />
			<input type="reset" name="featuresreset" value="{tr}Reset{/tr}" />
		</div>

{tabset name="admin_features"}
{*
 * The following section is typically for features that act like Tikiwiki
 * sections and add a configuration icon to the sections list
 *}
{* ---------- Main features ------------ *}
{tab name="{tr}Main{/tr}"}
			<div class="admin clearfix featurelist">
				{preference name=feature_wiki}
				{preference name=feature_blogs}
				{preference name=feature_galleries}
				{preference name=feature_file_galleries}
				{preference name=feature_articles}
				{preference name=feature_forums}
				{preference name=feature_trackers}
				{preference name=feature_polls}
				{preference name=feature_calendar}
				{preference name=feature_newsletters}
				{preference name=feature_banners}
			</div>
{/tab}
	
{* ---------- Global features ------------ *}
{tab name="{tr}Site Global{/tr}"}
			<div class="admin featurelist">
				{preference name=feature_categories}
				{preference name=feature_score}
				{preference name=feature_freetags}
				{preference name=feature_search}
				{preference name=feature_actionlog}
				{preference name=feature_contribution}
				{preference name=feature_multilingual}
			</div>
{/tab}

{* ---------- Additional features ------------ *}
{tab name="{tr}Additional{/tr}"}
			<div class="admin featurelist">
				{preference name=feature_faqs}
				{preference name=feature_surveys}
				{preference name=feature_directory}
				{preference name=feature_quizzes}
				{preference name=feature_featuredLinks}
				{preference name=feature_copyright}
				{preference name=feature_multimedia}
				{preference name=feature_shoutbox}
				{preference name=feature_maps}
				{preference name=feature_gmap}
				{preference name=feature_live_support}
				{preference name=feature_tell_a_friend}
				{preference name=feature_html_pages}
				{preference name=feature_contact}
				{preference name=feature_minichat}
				{preference name=feature_comments_moderation}
				{preference name=feature_comments_locking}
				{preference name=feature_purifier}
			</div>
{/tab}

{* ---------- User interface enhancement ------------ *}
{tab name="{tr}User Interface{/tr}"}
			<div class="admin featurelist">
				{preference name=feature_jscalendar}
				{preference name=feature_phplayers}
				{preference name=feature_fullscreen}
				{preference name=feature_cssmenus}
				{preference name=feature_shadowbox}
				{preference name=feature_quick_object_perms}
			</div>
{/tab}
			
{* ---------- Experimental features ------------ *}
{tab name="{tr}Experimental{/tr}"}
			<div class="admin featurelist">
				<fieldset>
					<legend class="heading">{icon _id="accept"}<span>{tr}Seem ok{/tr}</span></legend>
					<span class="description">{tr}Features that may change or might be re-worked in the future{/tr}</span>
						{preference name=feature_ajax}
						{preference name=feature_mobile}
						{preference name=feature_morcego}
						{preference name=feature_webmail}
				</fieldset>

				<fieldset>
					<legend class="heading">{icon _id="error"}<span>{tr}Need polish{/tr}</span></legend>
					<span class="description">{tr}Features that need admin help and user patience to work well{/tr}</span>
						{preference name=feature_intertiki}
						{preference name=feature_mailin}
						{preference name=feature_wiki_mindmap}
						{preference name=feature_print_indexed}
						{preference name=feature_sefurl}
						{preference name=feature_sheet}
						{preference name=feature_wysiwyg}
						{preference name=feature_ajax_autosave}
						{preference name='feature_htmlpurifier_output'}
				</fieldset>

				<fieldset>
					<legend class="heading">{icon _id="exclamation"}<span>{tr}Malfunctioning{/tr}</span></legend>
					<span class="description">{tr}These features have critical faults - not recommended{/tr}</span>
						{preference name=feature_workspaces}
				</fieldset>

				<fieldset>
					<legend class="heading">{icon _id="information_gray"}<span>{tr}Neglected{/tr}</span></legend>
					<span class="description">{tr}Old features no longer maintained{/tr}</span>
						{preference name=feature_friends}
				</fieldset>
			</div>
{/tab}

{* ---------- Administration features ------------ *}
{tab name="{tr}Administration{/tr}"}
			<div class="admin featurelist">
				{preference name=feature_banning}
				{preference name=feature_stats}
				{preference name=feature_action_calendar}
				{preference name=feature_referer_stats}
				{preference name=feature_redirect_on_error}
				{preference name=feature_comm}
				{preference name=feature_custom_home}
			</div>
{/tab}
			
{* --- User Features --- *}
{tab name="{tr}User{/tr}"}
			<div class="admin featurelist">
				<div style="width: 49%; text-align: left; float: left">
					<span class="checkbox"><input type="checkbox" name="feature_mytiki" {if $prefs.feature_mytiki eq 'y'}checked="checked"{/if} /></span>
					<span class="label"> {tr}Display 'MyTiki' in the application menu{/tr} </span>
				</div>
				<div class="half_width">
					<span class="checkbox"><input type="checkbox" name="feature_minical" {if $prefs.feature_minical eq 'y'}checked="checked"{/if} /></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Calendar" target="tikihelp" class="tikihelp" title="{tr}Mini Calendar{/tr}">{/if} {tr}Mini Calendar{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
				
				<div style="width: 49%; text-align: left; float: left">
					<span class="checkbox"><input type="checkbox" name="feature_userPreferences" {if $prefs.feature_userPreferences eq 'y'}checked="checked"{/if} /></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}User+Preferences" target="tikihelp" class="tikihelp" title="{tr}User Preferences Screen{/tr}">{/if} {tr}User Preferences Screen{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
				<div class="half_width">
					<span class="checkbox"><input type="checkbox" name="feature_notepad" {if $prefs.feature_notepad eq 'y'}checked="checked"{/if} /></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Notepad" target="tikihelp" class="tikihelp" title="{tr}User Notepad{/tr}">{/if} {tr}User Notepad{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
				
				<div style="width: 49%; text-align: left; float: left">
					<span class="checkbox"><input type="checkbox" name="feature_user_bookmarks"	{if $prefs.feature_user_bookmarks eq 'y'}checked="checked"{/if} /></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Bookmarks" target="tikihelp" class="tikihelp" title="{tr}User Bookmarks{/tr}">{/if} {tr}User Bookmarks{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
				<div class="half_width">
					<span class="checkbox"><input type="checkbox" name="feature_contacts" {if $prefs.feature_contacts eq 'y'}checked="checked"{/if}/></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Contacts" target="tikihelp" class="tikihelp" title="{tr}User Contacts{/tr}">{/if} {tr}User Contacts{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
				
				<div style="width: 49%; text-align: left; float: left">
					<span class="checkbox"><input type="checkbox" name="feature_user_watches" {if $prefs.feature_user_watches eq 'y'}checked="checked"{/if}/></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}User+Watches" target="tikihelp" class="tikihelp" title="{tr}User Watches{/tr}">{/if} {tr}User Watches{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
				<div class="half_width">
					<span class="checkbox"><input type="checkbox" name="feature_group_watches" {if $prefs.feature_group_watches eq 'y'}checked="checked"{/if}/></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Group+Watches" target="tikihelp" class="tikihelp" title="{tr}Group Watches{/tr}">{/if} {tr}Group Watches{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
				<div class="half_width">
					<span class="checkbox"><input type="checkbox" name="feature_daily_report_watches" {if $prefs.feature_daily_report_watches eq 'y'}checked="checked"{/if}/></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Daily+Reports" target="tikihelp" class="tikihelp" title="{tr}Daily Repors for User Watches{/tr}">{/if} {tr}Daily Reports{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
				<div class="half_width">
					<span class="checkbox"><input type="checkbox" name="feature_user_watches_translations"	{if $prefs.feature_user_watches_translations eq 'y'}checked="checked"{/if}/></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}User+Watches" target="tikihelp" class="tikihelp" title="{tr}User Watches Translations{/tr}">{/if} {tr}User Watches Translations{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
				
				<div style="width: 49%; text-align: left; float: left">
					<span class="checkbox"><input type="checkbox" name="feature_usermenu" {if $prefs.feature_usermenu eq 'y'}checked="checked"{/if}/></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}User+Menu" target="tikihelp" class="tikihelp" title="{tr}User Menu{/tr}">{/if} {tr}User Menu{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
				<div class="half_width">
					<span class="checkbox"><input type="checkbox" name="feature_tasks" {if $prefs.feature_tasks eq 'y'}checked="checked"{/if}/></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Task" target="tikihelp" class="tikihelp" title="{tr}User Tasks{/tr}">{/if} {tr}User Tasks{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
				
				<div style="width: 49%; text-align: left; float: left">
					<span class="checkbox"><input type="checkbox" name="feature_messages" {if $prefs.feature_messages eq 'y'}checked="checked"{/if}/></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Inter-User+Messages" target="tikihelp" class="tikihelp" title="{tr}User Messages{/tr}">{/if} {tr}User Messages{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
				<div class="half_width">
					<span class="checkbox"><input type="checkbox" name="feature_userfiles" {if $prefs.feature_userfiles eq 'y'}checked="checked"{/if}/></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}User+Files" target="tikihelp" class="tikihelp" title="{tr}User Files{/tr}">{/if} {tr}User Files{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
				
				<div class="half_width">
					<span class="checkbox"><input type="checkbox" name="feature_userlevels" {if $prefs.feature_userlevels eq 'y'}checked="checked"{/if}/></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}User+Levels" target="tikihelp" class="tikihelp" title="{tr}User Levels{/tr}">{/if} {tr}User Levels{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
				<div style="width: 49%; text-align: left; float: left">
					<span class="checkbox"><input type="checkbox" name="feature_groupalert" {if $prefs.feature_groupalert eq 'y'}checked="checked"{/if}/></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Group+Alert" target="tikihelp" class="tikihelp" title="{tr}Group Alert{/tr}">{/if} {tr}Group Alert{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
			</div>
{/tab}

{tab name="{tr}Programmer{/tr}"}
			<div class="admin featurelist">
				<div style="width: 49%; text-align: left; float: left">
					<span class="checkbox"><input type="checkbox" name="feature_integrator" {if $prefs.feature_integrator eq 'y'}checked="checked"{/if}/></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Tiki+Integrator" target="tikihelp" class="tikihelp" title="{tr}Integrator{/tr}">{/if} {tr}Integrator{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
				<div class="half_width">
					<span class="checkbox"><input type="checkbox" name="feature_xmlrpc"	{if $prefs.feature_xmlrpc eq 'y'}checked="checked"{/if}/></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Xmlrpc" target="tikihelp" class="tikihelp" title="{tr}XMLRPC API{/tr}">{/if} {tr}XMLRPC API{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
				<div style="width: 49%; text-align: left; float: left">
					<span class="checkbox"><input type="checkbox" name="feature_debug_console" {if $prefs.feature_debug_console eq 'y'}checked="checked"{/if}/></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Debugger+Console" target="tikihelp" class="tikihelp" title="{tr}Debugger Console{/tr}">{/if} {tr}Debugger Console{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
				<div class="half_width">
					<span class="checkbox"><input type="checkbox" name="feature_tikitests"{if $php_major_version lt 5} disabled="disabled"{/if}{if $prefs.feature_tikitests eq 'y'} checked="checked"{/if}/></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}TikiTests" target="tikihelp" class="tikihelp" title="{tr}TikiTests{/tr}">{/if} {tr}TikiTests{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}{if $php_major_version lt 5} ({tr}requires PHP5 or more{/tr}) {/if}</span>
				</div>
				<div style="width: 49%; text-align: left; float: left">
					<span class="checkbox"><input type="checkbox" name="feature_workflow" {if $prefs.feature_workflow eq 'y'}checked="checked"{/if}/></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Workflow" target="tikihelp" class="tikihelp" title="{tr}Workflow{/tr}">{/if} {tr}Workflow engine{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
				<div style="width: 49%; text-align: left; float: left">
					<span class="checkbox"><input type="checkbox" name="use_minified_scripts" {if $prefs.use_minified_scripts eq 'y'}checked="checked"{/if}/></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}MinifiedScripts" target="tikihelp" class="tikihelp" title="{tr}Use Minified Scripts{/tr}">{/if} {tr}Use Minified Scripts{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
				<div style="width: 49%; text-align: left; float: left">
					<span class="checkbox"><input type="checkbox" name="debug_ignore_xdebug" {if $prefs.debug_ignore_xdebug eq 'y'}checked="checked"{/if}/></span>
					<span class="label"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}DebugIgnoreXDebug" target="tikihelp" class="tikihelp" title="{tr}Ignore XDebug: Don't use XDebug debugging info if installed. Try this if you use xdebug and are geting blank pages.{/tr}">{/if} {tr}Ignore XDebug{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</span>
				</div>
			</div>
{/tab}
{/tabset}

	<div class="input_submit_container" style="margin-top: 5px; text-align: center">
		<input type="submit" name="features" value="{tr}Apply{/tr}" />
	</div>
</form>
