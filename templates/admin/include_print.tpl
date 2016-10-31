{* $Id: include_general.tpl 59571 2016-09-01 07:37:31Z yonixxx $ *}
<form class="form-horizontal" action="tiki-admin.php?page=print" class="admin" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<input type="hidden" name="new_prefs" />
			<fieldset>
				<legend>{tr}PDF Settings{/tr}</legend>
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
			    <fieldset>
			
                <legend>{tr}Print Version Settings{/tr}</legend>
				
                {preference name=print_wiki_authors}
				{preference name=print_original_url_wiki}
				{preference name=print_original_url_tracker}
				{preference name=print_original_url_forum}
			</fieldset>
	<div class="t_navbar margin-bottom-md text-center">
		<input type="submit" class="btn btn-primary btn-sm tips" title=":{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
	</div>
</form>
