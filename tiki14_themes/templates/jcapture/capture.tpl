{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}

	<applet type="application/x-java-applet"
			archive="vendor/jcapture-applet/jcapture-applet/lib/jcapture.jar"
			code = "com.hammurapi.jcapture.JCaptureApplet"
			id="jCapture" height="387" width="482"
	>
		<param name = "scriptable"	value = "true">
		<param name = "mayscript"	value = "true">
		<param name = "pageName"	value = "{$page}">
		<param name = "edid"		value = "{$edit_area}">
		<param name = "uploadUrl"	value = "{$uploader}">
		{tr}Applet failed to run. No Java plug-in was found.{/tr}
	</applet>
{/block}
