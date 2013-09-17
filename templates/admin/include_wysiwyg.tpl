{* $Id$ *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}WYSIWYG means What You See Is What You Get, and is handled in Tiki by <a href="http://ckeditor.com/">CKEditor</a>{/tr}.{/remarksbox}
<div class="navbar">
{button href="tiki-admin_toolbars.php" _text="{tr}Toolbars{/tr}"}
</div>

<form action="tiki-admin.php?page=wysiwyg" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" class="btn btn-default" name="wysiwygfeatures" value="{tr}Change preferences{/tr}" />
	</div>
	{if $prefs.wysiwyg_htmltowiki neq 'y'}
		{remarksbox type="warning" title="{tr}Page links{/tr}"}{tr}Note that if the SEFURL feature is on, page links created using wysiwyg might not be automatically updated when pages are renamed. This is addressed through the "Use Wiki syntax in WYSIWYG" feature.{/tr}{/remarksbox}
	{/if}

	<fieldset class="admin">
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_wysiwyg visible="always"}
		{preference name=wysiwyg_inline_editing}
		{preference name=wikiplugin_wysiwyg}
	</fieldset>

	<fieldset class="admin">
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
		{preference name=wysiwyg_toolbar_skin}
		{preference name="wysiwyg_fonts"}

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
	</fieldset>

	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" class="btn btn-default" name="wysiwygfeatures" value="{tr}Change preferences{/tr}" />
	</div>
</form>

