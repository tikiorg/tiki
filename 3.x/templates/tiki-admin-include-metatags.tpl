
<form action="tiki-admin.php?page=metatags" method="post">
<div class="cbox">
<table class="admin"><tr><td>
<div align="center" style="padding:1em;"><input type="submit" name="metatags" value="{tr}Change Preferences{/tr}" /></div>

<fieldset><legend>{tr}Meta tags{/tr}{if $prefs.feature_help eq 'y'} {help url="Meta+Tags+Config"}{/if}</legend>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="metatag_keywords">{tr}Keywords{/tr}:</label><br /> <textarea rows="5" cols="40" id="metatag_keywords" name="metatag_keywords" style="width:95%;">{$prefs.metatag_keywords}</textarea></div>

<div class="adminoptionboxchild">
<div class="adminoptionbox">
<div class="adminoption"><input type="checkbox" id="metatag_freetags" name="metatag_freetags" 
		{if $prefs.metatag_freetags eq 'y'} checked="checked" {/if} /></div>
	<div class="adminoptionlabel"><label for="metatag_freetags">{tr}Include freetags{/tr}.</label>
	{if $prefs.feature_freetags ne 'y'}<br />{icon _id=information}{tr}Feature is disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.{/if}
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="metatag_threadtitle" name="metatag_threadtitle" {if $prefs.metatag_threadtitle eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="prefs_metatag_threadtitle">{tr}Use thread title instead{/tr}.</label>
	{if $prefs.feature_forums ne 'y'}<br />{icon _id=information}{tr}Feature is disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.{/if}
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="metatag_imagetitle" name="metatag_imagetitle" {if $prefs.metatag_imagetitle eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="prefs_metatag_imagetitle">{tr}Use image title instead{/tr}.</label>
	{if $prefs.feature_galleries ne 'y'}<br />{icon _id=information}{tr}Feature is disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.{/if}
	</div>
</div>

</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="metatag_description">{tr}Description{/tr}:</label><br /> <textarea id="metatag_description" name="metatag_description" rows="5" cols="40" style="width:95%">{$prefs.metatag_description}</textarea></div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="metatag_author">{tr}Author{/tr}:</label> <input id="metatag_author" type="text" name="metatag_author" value="{$prefs.metatag_author}" size="50" /></div>
</div>
</fieldset>


<fieldset><legend>{tr}Geo URL{/tr} {if $prefs.feature_help eq 'y'}<a target="_blank" href="http://www.geourl.org/">{icon _id='help'}</a>{/if}</legend>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="metatag_geoposition">{tr}geo.position{/tr}:</label> <input type="text" name="metatag_geoposition" id="metatag_geoposition" value="{$prefs.metatag_geoposition}" size="50" /></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="metatag_georegion">{tr}geo.region{/tr}:</label> <input id="metatag_georegion" type="text" name="metatag_georegion" value="{$prefs.metatag_georegion}" size="50" /></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="metatag_geoplacename">{tr}geo.placename{/tr}:</label> <input id="metatag_geoplacename" type="text" name="metatag_geoplacename" value="{$prefs.metatag_geoplacename}" size="50" /></div>
</div>
</fieldset>

<fieldset><legend>{tr}Robots{/tr}</legend>
{* Need to show site_metatag_robots as real metatags are overridden at runtime  *}
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="metatag_robots">{tr}Meta robots{/tr}:</label> <input id="metatag_robots" type="text" name="metatag_robots" value="{$prefs.site_metatag_robots}" size="50" /></div>
</div> 
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="metatag_revisitafter">{tr}Revisit after{/tr}:</label> <input id="metatag_revisitafter" type="text" name="metatag_revisitafter" value="{$prefs.metatag_revisitafter}" size="50" /></div>
</div>
</fieldset>

<div align="center" style="padding:1em;"><input type="submit" name="metatags" value="{tr}Change Preferences{/tr}" /></div>
</td></tr></table>
</div>
</form>


