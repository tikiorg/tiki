{* $Id$ *}

<img class="pull-right" src="img/icons/large/gnome-camera-video-48.png" alt="{tr}jCapture setup{/tr}" />
<div class="media">
<img class="pull-left" src="img/icons/large/wizard_admin48x48.png" alt="{tr}Configuration Wizard{/tr}" title="{tr}Configuration Wizard{/tr}" />
<div class="media-body">
    <p>
        {tr}When activating jCapture <img src="img/icons/camera.png" />, token access is also activated. It is required to use jCapture.{/tr}<br>
        {tr}Learn more about <a href="https://doc.tiki.org/Token%20Access" target="_blank">Token Access at doc.tiki.org</a>{/tr}.<br>
    </p>
    <fieldset>
	    <legend>{tr}jCapture options and related features{/tr}</legend>
	    {tr}Choose the file gallery that jCapture will use to store its images in{/tr}.<br><br>
	    {tr}Gallery name{/tr}:
	    {if empty($jcaptureFileGalleryName)}
		    <input type="text" name="jcaptureFileGalleryName" value="{$jcaptureFileGalleryName}" /><br>
		    <div class="adminoptionboxchild">
			    <span style="margin-left:44px">{tr}If the gallery doesn't exist, it will be created under the gallery root{/tr}.</span>
		    </div>
	    {else}
	        <div class="adminoptionboxchild">
		        <span style="margin-left:44px"><b>{$jcaptureFileGalleryName}</b></span>
	        </div>
	    {/if}
	    <br>
	    <br>
    	{preference name=feature_draw}
	    <div class="adminoptionboxchild">
		    {tr}Enable drawing directly on captured images from your web page{/tr}
	    </div>
	    <br>
	    <br>
	    <em>{tr}See also{/tr} <a href="http://doc.tiki.org/Screencast" target="_blank">{tr}Screencast{/tr} @ doc.tiki.org</a></em>
    </fieldset>
    </div>
</div>