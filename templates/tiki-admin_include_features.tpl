{* $Id$ *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Please see the <a class='rbox-link' target='tikihelp' href='http://doc.tiki.org/Features'>evaluation of each feature</a> on Tiki's developer site.{/tr}{/remarksbox}

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
{tab name="{tr}Global features{/tr}"}

		<fieldset>
			<legend>{tr}Main feature{/tr}</legend>

			<div class="admin clearfix featurelist">
				{preference name=feature_wiki}
				{preference name=feature_file_galleries}
				{preference name=feature_blogs}
				{preference name=feature_articles}
				{preference name=feature_forums}
				{preference name=feature_trackers}
				{preference name=feature_polls}
				{preference name=feature_calendar}
				{preference name=feature_newsletters}
				{preference name=feature_banners}
				{preference name=feature_categories}
				{preference name=feature_freetags}
				{preference name=feature_search_fulltext}
			</div>

		</fieldset>

		<fieldset>
			<legend>{tr}Additional{/tr}</legend>

			<div class="admin featurelist">
				{preference name=feature_surveys}
				{preference name=feature_directory}
				{preference name=feature_quizzes}
				{preference name=feature_shoutbox}
				{preference name=feature_maps}
				{preference name=feature_gmap}
				{preference name=feature_live_support}
				{preference name=feature_tell_a_friend}
				{preference name=feature_share}
				{preference name=feature_minichat}
				{preference name=feature_score}
				{preference name=feature_fullscreen}
				{preference name=feature_dynamic_content}
			</div>
		</fieldset>

		<fieldset>
			<legend>{tr}Watches{/tr}</legend>

			<div class="admin featurelist">
				{preference name=feature_user_watches}
				{preference name=feature_group_watches}
				{preference name=feature_daily_report_watches}
				{preference name=feature_user_watches_translations}
				{preference name=feature_user_watches_languages}
				{preference name=feature_groupalert}				
			</div>
		</fieldset>		
		
{/tab}
			
{tab name="{tr}Interface{/tr}"}
			<fieldset class="admin featurelist">
				<legend> {tr}Ajax{/tr} </legend>	
				{preference name=feature_ajax}
				<div class="adminoptionboxchild half_width" id="feature_ajax_childcontainer">
					{preference name=ajax_autosave}
				</div>
			</fieldset>
			<fieldset class="admin featurelist">
				<legend> {tr}jQuery plugins and add-ons{/tr} </legend>
				{preference name=feature_jquery_autocomplete}
				{preference name=feature_jquery_media}
				{preference name=feature_jquery_reflection}
				{preference name=feature_jquery_superfish}
				{preference name=feature_jquery_tooltips}
				{preference name=feature_jquery_ui_theme}
				{preference name=feature_jquery_ui}
				{preference name=feature_jquery_validation}
				<hr />
				<div class="floatleft">
					<div class="adminoptionbox">
						<em>{tr}Experimental:{/tr}</em> {icon _id=bug_error}
					</div>
					<div class="adminoptionboxchild">	
						{preference name=feature_jquery_carousel}
						{preference name=feature_jquery_jqs5}
						{preference name=feature_jquery_tablesorter}
					</div>
				</div>
			</fieldset>
{/tab}

{tab name="{tr}Programmer{/tr}"}
			<div class="admin featurelist clearfix">
				{preference name=feature_integrator}
				{preference name=feature_xmlrpc}
				{preference name=feature_debug_console}
				{preference name=feature_tikitests}
				{preference name=disableJavascript}
				{preference name=smarty_compilation}
			</div>

			<div class="adminoptionbox">
				<fieldset>
					<legend>{tr}Logging and Reporting{/tr}</legend>
					<div class="adminoptionbox">
						{preference name=error_reporting_level}
						<div class="adminoptionboxchild">
							{preference name=error_reporting_adminonly label="{tr}Visible to admin only{/tr}"}
							{preference name=smarty_notice_reporting label="{tr}Include Smarty notices{/tr}"}
						</div>
					</div>

					{preference name=log_mail}
					{preference name=log_sql}
					<div class="adminoptionboxchild" id="log_sql_childcontainer">
						{preference name=log_sql_perf_min}
					</div>
					{preference name=log_tpl}
				</fieldset>
			</div>

			<div class="admin">
				<fieldset>
					<legend>{tr}Custom Code{/tr}</legend>
					{preference name="header_custom_js"}
					{preference name=smarty_security}
				</fieldset>
			</div>
			
{/tab}

{* ---------- New features ------------ *}
{tab name="{tr}New{/tr}"}
			<div class="admin featurelist">
				<fieldset>
					<legend class="heading">{icon _id="star"} <span>{tr}New{/tr}</span></legend>
					<p class="description">{tr}These features are relatively new, or recently underwent major renovations. You should expect growing pains and possibly a lack of up to date documentation, as you would of a version 1.0 application{/tr}</p>
						{preference name=bigbluebutton_feature}
						<div class="adminoptionboxchild" id="bigbluebutton_feature_childcontainer">
							{preference name=bigbluebutton_server_location}
							{preference name=bigbluebutton_server_salt}
						</div>
						{preference name=feature_invite}
						{preference name=feature_kaltura}
						{preference name=feature_perspective}
						{preference name=feature_print_indexed}
						{preference name=feature_quick_object_perms}
						{preference name=feature_sefurl}
						{preference name=feature_sheet}
						{preference name=feature_webdav}
						{preference name=feature_webmail}
						{preference name=feature_webservices}
						{preference name=feature_wiki_mindmap}
						{preference name=feature_wysiwyg}
						{preference name=feature_accounting}
						{preference name=payment_feature}
				</fieldset>
			</div>

{/tab}


{* ---------- Experimental features ------------ *}
{tab name="{tr}Experimental{/tr}"}
			<div class="admin featurelist">

				<!--<fieldset>
					<legend class="heading">{icon _id="bricks"} <span>{tr}Getting there...{/tr}</span></legend>
					<p class="description">{tr}Most of these features are hoped to make it into the "new" section for Tiki 6.{/tr}</p>
				</fieldset>-->

				<fieldset>
					<legend class="heading">{icon _id="new"} <span>{tr}Fresh out of the oven{/tr}</span></legend>
					{preference name=feature_credits}
					{preference name=feature_loadbalancer}
					{preference name=feature_socialnetworks}
					{preference name=feature_watershed}
					{preference name=feature_file_galleries_save_draft}
					{preference name=feature_file_galleries_templates}
					{preference name=feature_syntax_highlighter}
				</fieldset>

				<fieldset>
					<legend class="heading">{icon _id="accept"} <span>{tr}Seem ok but...{/tr}</span></legend>
					<p class="description">{tr}These features are not reported to be broken, but they are not actively developed and/or widely used.{/tr}</p>
					{preference name=feature_actionlog}
					{preference name=feature_comm}
					{preference name=feature_contribution}
					{preference name=feature_copyright}
					{preference name=feature_custom_home}
					{preference name=feature_friends}
					{preference name=feature_intertiki}
					{preference name=feature_mailin}
					{preference name=feature_morcego}
				</fieldset>

				<fieldset>
					<legend class="heading">{icon _id="information_gray"} <span>{tr}Will be phased out{/tr}</span></legend>
					<p class="description">{tr}These features generally work but will probably be phased out in the future, because they are superseded by other features or because of evolution in Web technology.{/tr}</p>
					{preference name=feature_faqs}
					{preference name=feature_galleries}
					{preference name=feature_html_pages}
				</fieldset>

			</div>

{/tab}
{/tabset}


	<div class="input_submit_container" style="margin-top: 5px; text-align: center">
		<input type="submit" name="features" value="{tr}Apply{/tr}" />
	</div>
</form>
