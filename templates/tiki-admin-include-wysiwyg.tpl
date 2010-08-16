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
		<legend>{tr}Wysiwyg Editor Features{/tr}</legend>
		{preference name=wysiwyg_optional}
		<div class="adminoptionboxchild" id="wysiwyg_optional_childcontainer">
			{preference name=wysiwyg_default}
		</div>

		{preference name=wysiwyg_memo}
		{preference name=wysiwyg_wiki_parsed}
		{preference name=wysiwyg_wiki_semi_parsed}
		{preference name=wysiwyg_htmltowiki}
		{preference name=wysiwyg_toolbar_skin}
		{preference name="wysiwyg_fonts"}

		{preference name=feature_wiki_paragraph_formatting}
		<div class="adminoptionboxchild" id="feature_wiki_paragraph_formatting_childcontainer">
			{preference name=feature_wiki_paragraph_formatting_add_br}
		</div>
		
		<div class="adminoptionbox">
			<div class="adminoptionlabel">
				<em>{tr}Experimental{/tr}:</em> {icon _id=bug_error}
				<div class="adminoptionboxchild">	
					{preference name=wysiwyg_ckeditor}
				</div>
			</div>
		</div>

	</fieldset>
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" name="wysiwygfeatures" value="{tr}Change preferences{/tr}" />
	</div>
</form>

