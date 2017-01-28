{* $Id$ *}

<div class="media">
	<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Upgrade Wizard{/tr}" title="Upgrade Wizard">
		<i class="fa fa-arrow-circle-up fa-stack-2x"></i>
		<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
	</span>
	{tr}Main new features and settings in Tiki 16{/tr}.
	<a href="https://doc.tiki.org/Tiki16" target="tikihelp" class="tikihelp" title="{tr}Tiki16:{/tr}
			{tr}New PHP minimum requirement for Tiki 16.x is PHP 5.6{/tr}.
			{tr}Many libraries have been updated{/tr}.
			<br/><br/>
			{tr}Click to read more{/tr}
		">
		{icon name="help" size=1}
	</a>
	<br/><br/><br/>
	<div class="media-body">
		<fieldset class="table clearfix featurelist">
			<legend>{tr}New method to print in PDF{/tr}</legend>
			{tr}Choose <strong>mPDF</strong> from the dropdown below:{/tr}
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
			</div>
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}New Wiki Plugins{/tr}</legend>
			{preference name=wikiplugin_chartjs}
			{*preference name=wikiplugin_fluidgrid*} {*to be backported after 16.0 is released*}
			{*preference name=wikiplugin_pivottable*} {*to be backported after 16.0 is released*}
			{preference name=wikiplugin_wikidiff}
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Improved and extended features{/tr}</legend>
				{preference name=feature_trackers}
				{preference name=wikiplugin_iframe}
				{preference name=wikiplugin_list}
				{preference name=wikiplugin_listexecute}
				{preference name=wikiplugin_toc}
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Removed features{/tr}</legend>
			{tr}None{/tr}
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}New Profiles{/tr}</legend>
			<ul>
				<li>{tr}Bug_Tracker_16{/tr}</li>
				<li>{tr}Execute on list{/tr}</li>
				<li>{tr}GeoCMS_Maps{/tr}</li>
				<li>{tr}Work_Custom_Pricing{/tr}</li>
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
