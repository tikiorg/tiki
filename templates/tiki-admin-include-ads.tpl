{* $Id$ *}
{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Select <a href="tiki-list_banners.php" title="Banners">Admin &gt; Banners</a> from the menu to create and edit banner zones.{/tr}
{/remarksbox}

<form action="tiki-admin.php?page=ads"  onreset="return(confirm('{tr}Cancel Edit{/tr}'))" class="admin" method="post">
<div class="cbox">
<table class="admin"><tr><td>
<div style="padding:1em;" align="center"><input type="submit" value="{tr}Change preferences{/tr}" /></div>
<input type="hidden" name="adssetup" />
<fieldset><legend>{tr}Site Ads and Banners{/tr}{if $prefs.feature_help eq 'y'} {help url="Look+and+Feel+Admin"}{/if}</legend>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="sitead">{tr}Content{/tr}:</label><br /><textarea name="sitead" rows="6" style="width: 90%" id="sitead">{$prefs.sitead|escape}</textarea>
	<br /><em>{tr}Example{/tr}: {literal}{banner zone='{/literal}{tr}Test{/tr}{literal}'}{/literal}.</em></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_sitead" id="feature_sitead"{if $prefs.feature_sitead eq 'y'} checked="checked"{/if} onclick="flip('activatebanner');" /></div>
	<div class="adminoptionlabel"><label for="feature_sitead">{tr}Activate{/tr}</label></div>
<div class="adminoptionboxchild" id="activatebanner" style="display:{if $prefs.feature_sitead eq 'y'}block{else}none{/if};">
{remarksbox type="note" title="{tr}Note{/tr}"}{tr}<strong>Activate</strong> will display content for Admin only. Select <strong>Publish</strong> to display for all users.{/tr}{/remarksbox}
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="sitead_publish" id="sitead_publish"{if $prefs.sitead_publish eq 'y'} checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="sitead_publish">{tr}Publish{/tr}</label></div>
</div>
</div>
</fieldset>

<div style="padding:1em;" align="center"><input type="submit" value="{tr}Change preferences{/tr}" /></div>
</td></tr></table>
</div>
</form>
