<form action="tiki-admin.php?page=video" method="post">

{tabset name="admin_video"}
{tab name="{tr}Kaltura{/tr}"}
{remarksbox type="info" title="{tr}Kaltura Registration{/tr}" }{tr}To get a Kaltura Partner ID:{/tr} {tr}Setup your own instance of Kaltura Community Edition (CE){/tr} or <a href="http://corp.kaltura.com/about/signup">{tr}get an account via Kaltura.com{/tr}</a> {/remarksbox}

	{button _text="{tr}List Media Entries{/tr}" href="tiki-list_kaltura_entries.php?list=media" }
	{button _text="{tr}List Remix Entries{/tr}" href="tiki-list_kaltura_entries.php" }
	{button _text="{tr}Add New Media{/tr}" href="tiki-kaltura_upload.php" }

<fieldset class="admin">
<legend>{tr}Activate the feature{/tr}</legend>
	{preference name=feature_kaltura}
</fieldset>

<fieldset class="admin">
<legend>{tr}Kaltura / Tiki config{/tr}</legend>
	{preference name=kuser}
	{preference name=kServiceUrl}
	{preference name=default_kaltura_editor}
</fieldset>

<fieldset class="admin">
<legend>{tr}Kaltura Partner Settings{/tr}</legend>
	{preference name=partnerId}
	{preference name=secret}
	{preference name=adminSecret}
</fieldset>

<br/>

<fieldset class="admin">
<legend>{tr}Kaltura Dynamic Player{/tr}</legend>
	{preference name=kdpUIConf}
	{preference name=kdpWidget}
</fieldset>

<br/>

<fieldset class="admin">
<legend>{tr}Kaltura Contribution Wizard{/tr}</legend>
	{preference name=kcwUIConf}
</fieldset>

<br/>

<fieldset class="admin">
<legend>{tr}Kaltura Remix Editors{/tr}</legend>
	{preference name=kseUIConf}
	{preference name=kaeUIConf}
</fieldset>

<br/>

<div align="center" style="padding:1em;"><input type="submit" name="kaltura" value="{tr}Save{/tr}" /></div>
</form>
{/tab}
{tab name="{tr}Ustream Watershed{/tr}"}
{remarksbox type="info" title="{tr}Ustream Watershed Registration{/tr}" }{tr}If you don't have a Watershed account, {/tr}<a href="https://watershed.ustream.tv/">{tr}you can find out more about it here{/tr}.</a>{/remarksbox}
<fieldset class="admin">
<legend>{tr}Activate the feature{/tr}</legend>
	{preference name=feature_watershed}
	{preference name=watershed_log_errors}
</fieldset>
{remarksbox type="info" title="{tr}Configuration within Watershed{/tr}" }{tr}Set the webservice to point to tiki-watershed_service.php on your site, and turn on Authentication Lock.{/tr}{/remarksbox}
{remarksbox type="info" title="{tr}Watershed Wiki plugins{/tr}" }{tr}Use Wiki plugins WatershedBroadcaster, WatershedViewer and WatershedChat to embed your broadcaster, viewer or chat.{/tr}{/remarksbox}

<fieldset class="admin">
<legend>{tr}Basic tracker settings{/tr}</legend>
{remarksbox type="info" title="{tr}Tracker{/tr}" }{tr}Information for each channel is stored in a tracker. Tracker item view/modify permissions will determine which channels users will be able to view or broadcast to respectively. You can find the Brand Id and Channel Code from the embed codes provided by Watershed, looking for the cid variable which will be "brandId%2Fchannelcode"{/tr}{/remarksbox}
	{preference name=watershed_channel_trackerId}
	{preference name=watershed_brand_fieldId}
	{preference name=watershed_channel_fieldId}
</fieldset>

<fieldset class="admin">
<legend>{tr}Archive settings{/tr}</legend>
{remarksbox type="info" title="{tr}Tracker{/tr}" }{tr}Information on archived clips are stored in a tracker. Tracker item view permissions will determine which archives users will be able to view. Note that recordings set to private in the broadcaster are not added to Tiki. Also, there is a delay for information to be received which could be quite long (30 min).{/tr}{/remarksbox}
	{preference name=watershed_archive_trackerId}
	{preference name=watershed_archive_fieldId}
	{preference name=watershed_archive_brand_fieldId}
	{preference name=watershed_archive_channel_fieldId}
	{preference name=watershed_archive_rtmpurl_fieldId}
	{preference name=watershed_archive_flvurl_fieldId}
</fieldset>

<fieldset class="admin">
<legend>{tr}Archive settings (optional){/tr}</legend>
	{preference name=watershed_archive_date_fieldId}
	{preference name=watershed_archive_duration_fieldId}
	{preference name=watershed_archive_filesize_fieldId}
	{preference name=watershed_archive_title_fieldId}
	{preference name=watershed_archive_desc_fieldId}
	{preference name=watershed_archive_tags_fieldId}
</fieldset>

<fieldset class="admin">
<legend>{tr}Flash Media Encoder{/tr}</legend>
{remarksbox type="info" title="{tr}Flash Media Encoder{/tr}" }{tr}This is only needed if you are using Flash Media Encoder for higher-quality broadcasting which support only shared secret authentication.{/tr}{/remarksbox}
	{preference name=watershed_fme_key}
</fieldset>

<div align="center" style="padding:1em;"><input type="submit" name="video" value="{tr}Save{/tr}" /></div>
{/tab}
{/tabset}
