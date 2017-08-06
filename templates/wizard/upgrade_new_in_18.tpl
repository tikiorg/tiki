{* $Id$ *}

<div class="media">
	<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Upgrade Wizard{/tr}" title="Upgrade Wizard">
		<i class="fa fa-arrow-circle-up fa-stack-2x"></i>
		<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
	</span>
	{tr}Main new and improved features and settings in Tiki 18{/tr}.
	<a href="https://doc.tiki.org/Tiki18" target="tikihelp" class="tikihelp" title="{tr}Tiki18:{/tr}
			{tr}This is an LTS version{/tr}.
			{tr}As it is a Long-Term Support (LTS) version, it will be supported for 5 years.{/tr}.
			{tr}Many libraries have been upgraded{/tr}.
			<br/><br/>
			{tr}Click to read more{/tr}
		">
		{icon name="help" size=1}
	</a>
	<br/><br/><br/>
	<div class="media-body">
		<fieldset class="table clearfix featurelist">
			<legend>{tr}New Wiki Plugins{/tr}</legend>
			{preference name=wikiplugin_pdfpage}
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Improved Plugins{/tr}</legend>
			{preference name=wikiplugin_img}
			{preference name=wikiplugin_list}
			{preference name=wikiplugin_listexecute}
			{preference name=wikiplugin_pdf}
			{preference name=wikiplugin_pivottable}
			{preference name=wikiplugin_trackercalendar}
			{preference name=wikiplugin_trackerlist}
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}PDF from URL: mPDF new settings{/tr}</legend>
			{preference name=print_pdf_from_url}
			<div class="adminoptionboxchild print_pdf_from_url_childcontainer mpdf">
				{preference name=print_pdf_mpdf_pagetitle}
				{preference name=print_pdf_mpdf_hyperlinks}
				{preference name=print_pdf_mpdf_columns}
				{preference name=print_pdf_mpdf_watermark}
				{preference name=print_pdf_mpdf_watermark_image}
				{preference name=print_pdf_mpdf_background}
				{preference name=print_pdf_mpdf_background_image}
				{preference name=print_pdf_mpdf_coverpage_text_settings}
				{preference name=print_pdf_mpdf_coverpage_image_settings}
			</div>
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Other new or Extended Features{/tr}</legend>
			<div class="adminoption form-group">
				<label class="col-sm-3 control-label"><b>{tr}Control Panels{/tr}</b>:</label>
				<div class="col-sm-offset-1 col-sm-11">
					{icon name="admin_packages" size=2 iclass="pull-left"}
					{tr}Composer Web Install (<b>Packages</b>){/tr}.
					<a href="https://doc.tiki.org/Packages">{tr}More Information{/tr}...</a><br/>
				</div>
				<div class="col-sm-offset-1 col-sm-11">
					{icon name="admin_rtc" size=2 iclass="pull-left"}
					{tr}Real-time collaboration tools (<b>RTC</b>){/tr}.
					<a href="https://doc.tiki.org/RTC">{tr}More Information{/tr}...</a><br/>
				</div>
			</div>
			<div class="adminoption form-group">
			</div>
			{* {preference name=foo} *}
			{preference name=feature_trackers}
			<div class="adminoptionboxchild" id="feature_trackers_childcontainer">
				<legend>{tr}General{/tr}</legend>
				<div class="col-sm-12">
					{tr}Certain tracker fields can be converted keeping options{/tr}
					<a href="https://doc.tiki.org/Tiki18#Trackers">{tr}More Information{/tr}...</a>
				</div><br/>
				<legend>{tr}Improved Fields{/tr}</legend>
				{preference name=trackerfield_relation}
			</div>
			{preference name=ids_enabled}
			<div class="adminoptionboxchild" id="ids_enabled_childcontainer">
				<div class="form-group adminoptionbox clearfix">
					<div class="col-sm-offset-4 col-sm-8">
						<a href="tiki-admin_ids.php">{tr}Admin IDS custom rules{/tr}</a>
					</div>
				</div>
				{preference name=ids_custom_rules_file}
				{preference name=ids_mode}
				{preference name=ids_threshold}
				{preference name=ids_log_to_file}
				{*{preference name=ids_log_to_database}*}
			</div>
		</fieldset>
		<i>{tr}See the full list of changes{/tr}.</i>
		<a href="https://doc.tiki.org/Tiki18" target="tikihelp" class="tikihelp" title="{tr}Tiki18:{/tr}
			{tr}Click to read more{/tr}
		">
			{icon name="help" size=1}
		</a>
	</div>
</div>
