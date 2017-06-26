{* $Id$ *}

<div class="media">
	<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Upgrade Wizard{/tr}" title="Upgrade Wizard">
		<i class="fa fa-arrow-circle-up fa-stack-2x"></i>
		<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
	</span>
	{tr}Main new and improved features and settings in Tiki 16{/tr}.
	<a href="http://doc.tiki.org/Tiki16" target="tikihelp" class="tikihelp" title="{tr}Tiki16:{/tr}
			{tr}Tiki16 is a standard non-LTS version{/tr}.
			{tr}It will be supported until 17.1 is released{/tr}.
			{tr}New PHP minimum requirement for Tiki 16 is PHP 5.6{/tr}.
			{tr}Many libraries have been upgraded; most notably, jQuery 3.0, Bootstrap Tour v0.11.0 among others{/tr}.
			<br/><br/>
			{tr}Click to read more{/tr}
		">
		{icon name="help" size=1}
	</a>
	<br/><br/><br/>
	<div class="media-body">
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Trackers{/tr}</legend>
			{preference name=feature_trackers}
				<div class="adminoptionboxchild" id="feature_trackers_childcontainer">
					<legend>{tr}Tracker Force-Fill Feature{/tr}</legend>
					{preference name=tracker_force_fill}
					<div class="adminoptionboxchild" id="tracker_force_fill_childcontainer">
						{preference name=tracker_force_tracker_id}
						{preference name=tracker_force_mandatory_field}
						{preference name=tracker_force_tracker_fields}
						{preference name=user_force_avatar_upload}
					</div>
					<legend>{tr}Improved Fields{/tr}</legend>
					{preference name=trackerfield_groupselector}
					{preference name=trackerfield_icon}
					{preference name=trackerfield_itemlink}
					{preference name=trackerfield_itemslist}
					{preference name=trackerfield_location}
					{preference name=trackerfield_math}
					{preference name=trackerfield_statictext}
					{preference name=trackerfield_userselector}
				</div>
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}New Wiki Plugins{/tr}</legend>
			{preference name=wikiplugin_chartjs}
			{preference name=wikiplugin_paymentlist}
			{preference name=wikiplugin_pivottable}
			{preference name=wikiplugin_wikidiff}
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Improved Plugins and extended features{/tr}</legend>
				{preference name=wikiplugin_archivebuilder}
				{preference name=wikiplugin_fade}
				{preference name=wikiplugin_iframe}
				{preference name=wikiplugin_list}
				{preference name=wikiplugin_listexecute}
				{preference name=wikiplugin_toc}
				{preference name=wikiplugin_tracker}
				{preference name=wikiplugin_wikidiff}
				<b>{tr}Console{/tr}</b>:
				{tr}There are a few new console.php commands to set scheduled cron tasks{/tr}.
				<a href="https://doc.tiki.org/Console">{tr}More Information{/tr}...</a><br/>
				<b>{tr}Filtering to Index-based Plugins{/tr}</b>:
				{tr}Filter UI added in any wiki plugin using unified search index{/tr}.
				<a href="https://doc.tiki.org/Tiki16#Filter_UI_in_any_plugin_using_unified_search_index">{tr}More Information{/tr}...</a>
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}PDF Settings (Control Panel > Print){/tr}</legend>
			<b>{tr}New method to create PDF: mPDF{/tr}</b>:
			<a href="https://doc.tiki.org/mPDF">{tr}More Information{/tr}...</a><br/>
			{preference name=print_pdf_from_url}
			<div class="adminoptionboxchild print_pdf_from_url_childcontainer webkit">
				{preference name=print_pdf_webkit_path}
			</div>
			<div class="adminoptionboxchild print_pdf_from_url_childcontainer weasyprint">
				{preference name=print_pdf_weasyprint_path}
			</div>
			<div class="adminoptionboxchild print_pdf_from_url_childcontainer webservice">
				{preference name=print_pdf_webservice_url}
			</div>
			<div class="adminoptionboxchild print_pdf_from_url_childcontainer mpdf">
				{preference name=print_pdf_mpdf_path}
				{preference name=print_pdf_mpdf_printfriendly}
				{preference name=print_pdf_mpdf_orientation}
				{preference name=print_pdf_mpdf_size}
				{preference name=print_pdf_mpdf_header}
				{preference name=print_pdf_mpdf_footer}
				{preference name=print_pdf_mpdf_margin_left}
				{preference name=print_pdf_mpdf_margin_right}
				{preference name=print_pdf_mpdf_margin_top}
				{preference name=print_pdf_mpdf_margin_bottom}
				{preference name=print_pdf_mpdf_margin_header}
				{preference name=print_pdf_mpdf_margin_footer}
				<input style="display:none">{* This seems to be required for the Chromium browser to prevent autofill the password with some password stored in the user's browser *}
				<input type="password" style="display:none" name="print_pdf_mpdf_password_autocomplete_off">{* This seems to be required for the Chromium browser to prevent autofill password with some password stored in the user's browser *}
				{preference name=print_pdf_mpdf_password}
			</div>
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}New Profiles{/tr}</legend>
			<ul>
				<li>{tr}Bug_Tracker_16{/tr}
					<a href="https://profiles.tiki.org/Bug_Tracker_16" target="tikihelp" class="tikihelp" title="{tr}Bug_Tracker_16:{/tr}
						{tr}Click to read more{/tr}">{icon name="help" size=1}
					</a>
				</li>
				<li>{tr}Execute on list{/tr}
					<a href="https://profiles.tiki.org/Execute+on+list" target="tikihelp" class="tikihelp" title="{tr}Execute on list:{/tr}
						{tr}Click to read more{/tr}">{icon name="help" size=1}
					</a>
				</li>
				<li>{tr}GeoCMS_Maps{/tr}
					<a href="https://profiles.tiki.org/GeoCMS_Maps" target="tikihelp" class="tikihelp" title="{tr}GeoCMS_Maps:{/tr}
						{tr}Click to read more{/tr}">{icon name="help" size=1}
					</a>
				</li>
				<li>{tr}Work_Custom_Pricing{/tr}
					<a href="https://profiles.tiki.org/Work_Custom_Pricing" target="tikihelp" class="tikihelp" title="{tr}Work_Custom_Pricing:{/tr}
						{tr}Click to read more{/tr}">{icon name="help" size=1}
					</a>
				</li>
			</ul>
		</fieldset>
		<i>{tr}See the full list of changes{/tr}.</i>
		<a href="https://doc.tiki.org/Tiki16" target="tikihelp" class="tikihelp" title="{tr}Tiki16:{/tr}
			{tr}Click to read more{/tr}
		">
			{icon name="help" size=1}
		</a>
	</div>
</div>
