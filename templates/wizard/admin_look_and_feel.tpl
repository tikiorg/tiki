{* $Id$ *}

<h1>{tr}Set up Look & Feel{/tr}</h1>
<div style="float:left; width:60px"><img src="img/icons/large/gnome-settings-background.png" alt="{tr}Set up Look & Feel{/tr}"></div>
{tr}Configure the Tiki theme and other look & feel preferences{/tr}.
<div align="left" style="margin-top:1em;">
<fieldset>
	<legend>{tr}Look & Feel options{/tr}</legend>
		<div style="position:relative;">
			<div style="position:absolute;right:.5em;top:0.5em;">
				<img src="{$thumbfile}" alt="{tr}Theme Screenshot{/tr}" id="style_thumb" width="160px" height="120px">
			</div>

			<div class="adminoptionbox">
				{preference name=style}
				{preference name=style_option}

				{preference name=site_layout}

				{if $prefs.javascript_enabled eq 'n' or $prefs.feature_jquery eq 'n'}
					<input type="submit" class="btn btn-default" name="changestyle" value="{tr}Go{/tr}" />
				{/if}
			</div>
		</div>
		<br>
		{tr}See also{/tr} <a href="tiki-admin.php?page=look&alt=Look+%26+Feel" target="_blank">{tr}Look & Feel admin panel{/tr}</a>
</fieldset>
<br>
<fieldset>
	<legend>{tr}Logo{/tr}</legend>
	{preference name=sitelogo_src}
	{preference name=sitelogo_bgcolor}
	{preference name=sitelogo_title}
	{preference name=sitelogo_alt}
</fieldset>
<br>
<fieldset>
	<legend>{tr}Favicon{/tr}</legend>
	{preference name=site_favicon}
	{preference name=site_favicon_type}
</fieldset>
</div>
