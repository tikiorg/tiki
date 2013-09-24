{* $Id$ *}

<h1>{tr}jCapture setup{/tr}</h1>

<div class="adminWizardIconleft"><img src="img/icons/large/gnome-camera-video-48.png" alt="{tr}jCapture setup{/tr}" /></div>
<div class="adminWizardContent">
<p>
{tr}When activating jCapture <img src="img/icons/camera.png" />, token access is also activated. It is required to use jCapture.{/tr}<br>
{tr}Learn more about <a href="https://doc.tiki.org/Token%20Access" target="_blank">Token Access at doc.tiki.org</a>{/tr}.<br>
<br>
</p>
<fieldset>
	<legend>{tr}jCapture options and related features{/tr}</legend>
	{tr}jCapture stores the capture files in the file gallery{/tr}.<br>
	{tr}Gallery name{/tr}: 
	{if empty($jcaptureFileGalleryName)}
		<input type="text" name="jcaptureFileGalleryName" value="{$jcaptureFileGalleryName}" /><br>
		{tr}It will be created under the gallery root. If the gallery already exists, jCapture will use that gallery{/tr}.
	{else}
		{$jcaptureFileGalleryName}
	{/if}
	<br>
	<br>

	{preference name=feature_draw}
	{tr}Enable drawing directly on captured images from your web page{/tr}
	<br>
	<br>
	{tr}See also{/tr} <a href="http://doc.tiki.org/Screencast" target="_blank">{tr}Screencast{/tr} @ doc.tiki.org</a>
</fieldset>

</div>
