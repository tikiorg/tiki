{* $Id$ *}

<h1>{tr}Main features setup{/tr}</h1>

<div style="float:left; width:60px"><img src="img/icons/large/icon-configuration48x48.png" alt="{tr}Main features setup{/tr}"></div>
<div align="left" style="margin-top:1em;">
{tr}Set up the main Tiki features. The wiki and file gallery features are always enabled.{/tr}<br>

<fieldset>
	<legend>{tr}Main Tiki features{/tr}</legend>
	<img src="img/icons/large/boot.png" style="float:right" />	
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
		{preference name=feature_categories}
		{preference name=feature_freetags}
		{preference name=feature_search_fulltext}
	</div>
	<br>
	{tr}Tiki has many more features{/tr}.
	{tr}See also{/tr} <a href="http://doc.tiki.org/Global+Features" target="_blank">{tr}Global Features{/tr} @ doc.tiki.org</a>
</fieldset>

<fieldset>
	<legend>{tr}Watches{/tr}</legend>
	{tr}Enable email notifications when changes occur. For user watches each user can choose to be notified. For group watches all members receive an email notification{/tr}
	<div class="admin clearfix featurelist">
		{preference name=feature_user_watches}
		{preference name=feature_group_watches}
		{if $isMultiLanguage eq true}
			{preference name=feature_user_watches_translations}
			{preference name=feature_user_watches_languages}
		{/if}
	</div>
</fieldset>		

</div>
