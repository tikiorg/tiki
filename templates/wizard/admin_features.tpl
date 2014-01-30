{* $Id$ *}

<div class="adminWizardIconleft"><img src="img/icons/large/wizard_admin48x48.png" alt="{tr}Admin Wizard{/tr}" title="{tr}Admin Wizard{/tr}" /></div><div class="adminWizardIconright"><img src="img/icons/large/boot48x48.png" alt="{tr}Main features setup{/tr}" /></div>
<div class="adminWizardContent">
{tr}Set up the main Tiki features. The wiki and file gallery features are always enabled.{/tr}<br>

<fieldset>
	<legend>{tr}Main Tiki features{/tr}</legend>
		{* preference name=feature_wiki *}
		{* preference name=feature_file_galleries *}
	<div class="admin clearfix featurelist">
		{preference name=feature_blogs}
		{preference name=feature_articles}
		{preference name=feature_forums}
		{preference name=feature_trackers}
		{preference name=feature_polls}
		{preference name=feature_sheet}
		{preference name=feature_calendar}
		{preference name=feature_newsletters}
		{preference name=feature_banners}
		{preference name=feature_freetags}

	</div>
	<br>
	<em>{tr}Tiki has many more features{/tr}.
	{tr}See also{/tr} <a href="http://doc.tiki.org/Global+Features" target="_blank">{tr}Global Features{/tr} @ doc.tiki.org</a></em>
</fieldset>

<fieldset>
	<legend>{tr}Watches{/tr}</legend>
	<img src="img/icons/large/mywatches.gif" class="adminWizardIconright" />
	{tr}Enable email notifications to users when changes in the content of specific items (pages, posts, trackers, etc.) are made{/tr}.
	<div class="admin clearfix featurelist">
		{preference name=feature_user_watches}
		{preference name=feature_group_watches}
		{preference name=feature_daily_report_watches}
		<div class="adminoptionboxchild" id="feature_daily_report_watches_childcontainer">
			{preference name=dailyreports_enabled_for_new_users}
		</div>
		{if $isMultiLanguage eq true}
			{preference name=feature_user_watches_translations}
			{preference name=feature_user_watches_languages}
		{/if}
	</div>
</fieldset>

</div>
