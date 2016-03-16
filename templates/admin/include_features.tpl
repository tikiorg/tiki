{* $Id$ *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Please see the <a class='alert-link' target='tikihelp' href='http://doc.tiki.org/Features'>evaluation of each feature</a> on Tiki's developer site.{/tr}
{/remarksbox}

<form class="admin form-horizontal" id="features" name="features" action="tiki-admin.php?page=features" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" name="features" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
			</div>
		</div>
	</div>


	{tabset name="admin_features"}
		{*
		 * The following section is typically for features that act like Tiki
		 * sections and add a configuration icon to the sections list
		 *}
		{* ---------- Main features ------------ *}
		{tab name="{tr}Global features{/tr}" key=global}
			<h2>{tr}Global features{/tr}</h2>

			<fieldset>
				<legend>{tr}Main features{/tr}</legend>

				<div class="admin clearfix form-group featurelist">
					{preference name=feature_wiki}
					{preference name=feature_file_galleries}
					{preference name=feature_blogs}
					{preference name=feature_articles}
					{preference name=feature_forums}
					{preference name=feature_trackers}
					{preference name=feature_polls}
					{preference name=feature_sheet}
					{preference name=feature_calendar}
					{preference name=feature_newsletters}
					{preference name=feature_banners}
					{preference name=feature_categories}
					{preference name=feature_freetags}
					{preference name=feature_search}
				</div>

			</fieldset>

			<fieldset>
				<legend>{tr}Additional features{/tr}</legend>

				<div class="admin clearfix form-group featurelist">
					{preference name=feature_surveys}
					{preference name=feature_directory}
					{preference name=feature_quizzes}
					{preference name=feature_shoutbox}
					{preference name=feature_minichat}
					{preference name=feature_live_support}
					{preference name=feature_tell_a_friend}
					{preference name=feature_share}
					{preference name=feature_credits}
					{preference name=feature_time_sheet}
					{preference name=feature_invoice}
					{preference name=feature_accounting}
					{preference name=payment_feature}
					{preference name=feature_draw}
					<div class="adminoptionboxchild" id="feature_draw_childcontainer">
						{preference name=feature_draw_hide_buttons}
						{preference name=feature_draw_separate_base_image}
						<div class="adminoptionboxchild" id="feature_draw_separate_base_image_childcontainer">
							{preference name=feature_draw_in_userfiles}
						</div>
					</div>

					{preference name=feature_docs}
					{preference name=feature_slideshow}
					{preference name=feature_slideshow_pdfexport}
					{preference name=feature_dynamic_content}
					{preference name=feature_perspective}
					{preference name=feature_areas}
					{preference name=feature_sefurl}
					{preference name=feature_webmail}
					{preference name=feature_actionlog}
					{preference name=feature_comm}
					{preference name=feature_contribution}
					{preference name=feature_copyright}
					{preference name=feature_mailin}

					{preference name=feature_faqs}
					{preference name=feature_galleries}
					{preference name=feature_html_pages}

					{preference name=feature_htmlfeed}
					{preference name=feature_futurelinkprotocol}

					{preference name=feature_jcapture}
					<div class="adminoptionboxchild" id="feature_jcapture_childcontainer">
						{preference name=fgal_for_jcapture}
					</div>
					{preference name=feature_reports}
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Interaction with online services or other software{/tr}</legend>
				<div class="admin clearfix featurelist">
					{preference name=connect_feature}
					{preference name=feature_socialnetworks}

					{preference name=feature_kaltura}

					{preference name=zotero_enabled}
					<div class="adminoptionboxchild" id="zotero_enabled_childcontainer">
						{if $prefs.zotero_client_key and $prefs.zotero_client_secret and $prefs.zotero_group_id}
							{remarksbox type=info title="{tr}Configuration completed{/tr}"}<a href="{service controller=oauth action=request provider=zotero}">{tr}Authenticate with Zotero{/tr}</a>{/remarksbox}
						{/if}
						{preference name=zotero_client_key}
						{preference name=zotero_client_secret}
						{preference name=zotero_group_id}
						{preference name=zotero_style}
					</div>

				</div>
				{preference name=feature_htmlfeed}
			</fieldset>


			<fieldset>
				<legend>{tr}Watches{/tr}</legend>

				<div class="admin clearfix featurelist">
					{preference name=feature_user_watches}
					{preference name=feature_group_watches}
					{preference name=feature_daily_report_watches}
					<div class="adminoptionboxchild" id="feature_daily_report_watches_childcontainer">
						{preference name=dailyreports_enabled_for_new_users}
					</div>
					{preference name=feature_user_watches_translations}
					{preference name=feature_user_watches_languages}
					{preference name=feature_groupalert}
				</div>
			</fieldset>

		{/tab}

		{tab name="{tr}Interface{/tr}" key=interface}
			<h2>{tr}Interface{/tr}</h2>
			<fieldset class="table clearfix featurelist">
				<legend> {tr}Ajax{/tr} </legend>
				{preference name=feature_ajax}
				<div class="adminoptionboxchild" id="feature_ajax_childcontainer">
					{preference name=ajax_autosave}
				</div>
			</fieldset>
			<fieldset class="table clearfix featurelist">
				<legend> {tr}jQuery plugins and add-ons{/tr} </legend>
				{preference name=feature_jquery_autocomplete}
				{preference name=feature_jquery_media}
				{preference name=feature_jquery_reflection}
				{preference name=feature_jquery_superfish}
				{preference name=feature_jquery_tooltips}
				{preference name=feature_jquery_tagcanvas}
				{preference name=feature_jquery_ui_theme}
				{preference name=feature_jquery_ui}
				{preference name=feature_jquery_validation}
				{preference name=feature_jquery_zoom}
				{preference name=jquery_ui_chosen}
				{preference name=jquery_fitvidjs}
				{preference name=jquery_timeago}
				<div class="adminoptionboxchild">
					<fieldset>
						<legend>{tr}Experimental{/tr}</legend>
						{preference name=feature_jquery_carousel}
						{preference name=feature_jquery_tablesorter}
					</fieldset>
				</div>
			</fieldset>

			<fieldset class="table clearfix featurelist">
				<legend> {tr}Mobile{/tr} </legend>
					{preference name=mobile_feature}

					<div class="adminoptionboxchild" id="mobile_feature_childcontainer">
						{preference name=mobile_perspectives}
					</div>
			</fieldset>

		{/tab}

		{tab name="{tr}Programmer{/tr}" key=programmer}
			<h2>{tr}Programmer{/tr}</h2>
			<div class="admin clearfix featurelist">
				{preference name=feature_integrator}
				{preference name=feature_xmlrpc}
				{preference name=feature_debug_console}
				{preference name=feature_tikitests}
				{preference name=disableJavascript}
				{preference name=smarty_compilation}
				{preference name=feature_webservices}
				{preference name=feature_dummy}
			</div>

			<div class="admin clearfix featurelist">
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

			<div class="table">
				<fieldset>
					<legend>{tr}Custom Code{/tr}</legend>
					{preference name="header_custom_js"}
					{preference name=smarty_security}
				</fieldset>
			</div>
		{/tab}

		{tab name="{tr}Addons{/tr}" key=addons}
			<h2>{tr}Tiki Addons{/tr}</h2>
			<fieldset>
				<legend>{tr}Activate Addons{/tr}</legend>
				{foreach $addonprefs as $addon}
					{preference name="{$addon|escape}"}
				{/foreach}
			</fieldset>
			<fieldset>
				<legend>{tr}Addon Groups API{/tr}</legend>
				{preference name=feature_community_send_mail_join}
				{preference name=feature_community_send_mail_leave}
			</fieldset>
		{/tab}
	{/tabset}

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" name="features" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
			</div>
		</div>
	</div>
</form>
