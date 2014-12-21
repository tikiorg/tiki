{* $Id$ *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}WYSIWYG means What You See Is What You Get, and is handled in Tiki by <a class="alert-link" href="http://ckeditor.com/">CKEditor</a>{/tr}.{/remarksbox}


<form action="tiki-admin.php?page=wysiwyg" method="post">
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<a role="button" class="btn btn-default btn-sm" href="tiki-admin_toolbars.php" title="{tr}Admin Toolbars{/tr}">
				{icon name="admin"} {tr}Toolbars{/tr}
			</a>
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" name="wysiwygfeatures" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
			</div>
		</div>
	</div>

	{if $prefs.wysiwyg_htmltowiki neq 'y'}
		{remarksbox type="warning" title="{tr}Page links{/tr}"}{tr}Note that if the SEFURL feature is on, page links created using wysiwyg might not be automatically updated when pages are renamed. This is addressed through the "Use Wiki syntax in WYSIWYG" feature.{/tr}{/remarksbox}
	{/if}

	<fieldset class="table">
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_wysiwyg visible="always"}
		{preference name=wikiplugin_wysiwyg}
	</fieldset>

	<fieldset class="table">
		<legend>{icon _id="text_dropcaps"} {tr}Wysiwyg Editor Features{/tr}</legend>
		{preference name=wysiwyg_optional}
		<div class="adminoptionboxchild" id="wysiwyg_optional_childcontainer">
			{preference name=wysiwyg_default}
			{preference name=wysiwyg_memo}
		</div>

		{preference name=wysiwyg_wiki_parsed}
		<div class="adminoptionboxchild" id="wysiwyg_wiki_parsed_childcontainer">
			{preference name=wysiwyg_wiki_semi_parsed}
			{preference name=wysiwyg_htmltowiki}
		</div>
		{preference name=wysiwyg_inline_editing}
		{preference name=wysiwyg_toolbar_skin}
		{preference name="wysiwyg_fonts"}
		{preference name="wysiwyg_extra_plugins"}

	</fieldset>
	<fieldset>
		<legend class="heading">{icon _id="bricks"} <span>{tr}Related features{/tr}</span></legend>

		{preference name=feature_wiki_paragraph_formatting}
		<div class="adminoptionboxchild" id="feature_wiki_paragraph_formatting_childcontainer">
			{preference name=feature_wiki_paragraph_formatting_add_br}
		</div>

		<p class="description">
			{preference name=feature_ajax}
			<div class="adminoptionboxchild" id="feature_ajax_childcontainer">
				{preference name=ajax_autosave}
			</div>
		</p>
	</fieldset>

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" name="wysiwygfeatures" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
			</div>
		</div>
	</div>
</form>

