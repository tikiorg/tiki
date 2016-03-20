{* $Id$ *}
<form role="form" class="form-horizontal" action="tiki-admin.php?page=faqs" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<div class="t_navbar margin-bottom-md clearfix">
		<a role="link" class="btn btn-link" href="tiki-list_faqs.php" title="{tr}List{/tr}">
			{icon name="list"} {tr}FAQs{/tr}
		</a>
		<div class="pull-right">
			<input type="submit" class="btn btn-primary btn-sm tips" name="faqcomprefs" title=":{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
		</div>
	</div>
	<fieldset>
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_faqs visible="always"}
	</fieldset>
	<fieldset class="table">
		<legend>{tr}Settings{/tr}</legend>
		{preference name=faq_prefix}
		{preference name=feature_faq_comments}
		<div class="adminoptionboxchild" id="feature_faq_comments_childcontainer">
			{preference name=faq_comments_per_page}
			{preference name=faq_comments_default_ordering}
		</div>
		{preference name=faq_feature_copyrights}
	</fieldset>
	<div class="t_navbar margin-bottom-md text-center">
		<input type="submit" class="btn btn-primary btn-sm tips" name="faqcomprefs" title=":{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
	</div>
</form>
