{* $Id$ *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}WYSIWYG means What You See Is What You Get, and is handled in Tiki by <a href="http://ckeditor.com/">CKEditor</a>{/tr}.{/remarksbox}
<div class="navbar">
{button href="tiki-admin_toolbars.php" _text="{tr}Toolbars{/tr}"}
</div>

<form action="tiki-admin.php?page=wysiwyg" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="wysiwygfeatures" value="{tr}Change preferences{/tr}" />
	</div>
	{if $prefs.feature_wysiwyg ne 'y'}{preference name=feature_wysiwyg}{/if}

	<fieldset class="admin">
		<legend>{icon _id="text_dropcaps"} {tr}Wysiwyg Editor Features{/tr}</legend>
		<div class="adminoptionbox">
			<div class="adminoptionlabel">
				<em>{tr}New{/tr}:</em> {icon _id="star"}
				<div class="adminoptionboxchild">	
					{preference name=wysiwyg_ckeditor}
				</div>
			</div>
		</div>
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
		<input type="submit" name="wysiwygfeatures" value="{tr}Change preferences{/tr}" />
	</div>
</form>

