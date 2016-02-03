{* $Id$ *}
{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}WYSIWYG means What You See Is What You Get, and is handled in Tiki by <a class="alert-link" href="http://ckeditor.com/">CKEditor</a>{/tr}.{/remarksbox}
<form class="form-horizontal" action="tiki-admin.php?page=wysiwyg" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<div class="t_navbar margin-bottom-md">
		<a role="button" class="btn btn-link" href="tiki-admin_toolbars.php" title="{tr}Toolbars{/tr}">
			{icon name="settings"} {tr}Toolbars{/tr}
		</a>
		<div class="pull-right">
			<input type="submit" class="btn btn-primary btn-sm" name="wysiwygfeatures" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
		</div>
	</div>
	{if $prefs.wysiwyg_htmltowiki neq 'y'}
		{remarksbox type="warning" title="{tr}Page links{/tr}"}{tr}Note that if the SEFURL feature is on, page links created using wysiwyg might not be automatically updated when pages are renamed. This is addressed through the "Use Wiki syntax in WYSIWYG" feature.{/tr}{/remarksbox}
	{/if}
	<fieldset>
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_wysiwyg visible="always"}
		{preference name=wikiplugin_wysiwyg}
	</fieldset>
	<fieldset>
		<legend>{tr}WYSIWYG Editor Features{/tr}</legend>
		{preference name=wysiwyg_optional}
		<div class="adminoptionboxchild" id="wysiwyg_optional_childcontainer">
			{preference name=wysiwyg_default}
			{preference name=wysiwyg_memo}
		</div>
		{preference name=wysiwyg_wiki_parsed}
		<div class="adminoptionboxchild" id="wysiwyg_wiki_parsed_childcontainer">
			{preference name=wysiwyg_wiki_semi_parsed}
		</div>
		{preference name=wysiwyg_htmltowiki}
		{preference name=wysiwyg_inline_editing}
		{preference name=wysiwyg_toolbar_skin}
		{preference name="wysiwyg_fonts"}
		{preference name="wysiwyg_extra_plugins"}
	</fieldset>
	<fieldset>
		<legend class="heading">{tr}Related features{/tr}</legend>
		{preference name=feature_wiki_paragraph_formatting}
		<div class="adminoptionboxchild" id="feature_wiki_paragraph_formatting_childcontainer">
			{preference name=feature_wiki_paragraph_formatting_add_br}
		</div>
		{preference name=feature_ajax}
		<div class="adminoptionboxchild" id="feature_ajax_childcontainer">
			{preference name=ajax_autosave}
		</div>
	</fieldset>
	<div class="t_navbar margin-bottom-md">
		<div class="text-center">
			<input type="submit" class="btn btn-primary btn-sm" name="wysiwygfeatures" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
		</div>
	</div>
</form>