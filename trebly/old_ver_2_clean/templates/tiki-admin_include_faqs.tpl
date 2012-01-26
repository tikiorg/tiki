{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}To add/remove FAQs, look for "Admin FAQs" under "FAQs" on the application menu, or{/tr} <a class="rbox-link" href="tiki-list_faqs.php">{tr}Click Here{/tr}</a>.
{/remarksbox}

<form action="tiki-admin.php?page=faqs" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="faqcomprefs" value="{tr}Change settings{/tr}" />
	</div>
	<fieldset class="admin">
		<legend>{tr}Settings{/tr}</legend>
		{preference name=faq_prefix}
		
		{preference name=feature_faq_comments}
		<div class="adminoptionboxchild" id="feature_faq_comments_childcontainer">
			{preference name=faq_comments_per_page}
			{preference name=faq_comments_default_ordering}
		</div>
	</fieldset>
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" name="faqcomprefs" value="{tr}Change settings{/tr}" />
	</div>
</form>
