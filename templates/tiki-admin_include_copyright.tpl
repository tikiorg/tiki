{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Copyright allows to determine a copyright for all the objects of tikiwiki{/tr}.{/remarksbox}

<form action="tiki-admin.php?page=copyright" method="post">
	<div class="input_submit_container clear" style="text-align: right;">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
	<input type="hidden" name="setcopyright" />

	<fieldset>
		<legend>{tr}Copyright management{/tr}</legend>
		{preference name=wikiLicensePage}
		{preference name=wikiSubmitNotice}

		<div class="adminoptionbox">
			<div class="adminoptionlabel">{tr}Enable copyright management for:{/tr}</div>
			<div class="adminoptionboxchild">
				{preference name=wiki_feature_copyrights}
				{preference name=articles_feature_copyrights}
				{preference name=blogues_feature_copyrights}
				{preference name=faqs_feature_copyrights}
			</div>
		</div>
	</fieldset>

	<div class="input_submit_container clear" style="text-align: center;">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
</form>
