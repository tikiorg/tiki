{* $Id$ *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Copyright allows to determine a copyright for all the objects of tikiwiki{/tr}.{/remarksbox}

<form action="tiki-admin.php?page=copyright" method="post">
<div class="cbox">
<table class="admin"><tr><td>
<div style="padding:1em;" align="center"><input type="submit" value="{tr}Change preferences{/tr}" /></div>
<input type="hidden" name="setcopyright" />

<fieldset><legend>{tr}Copyright management{/tr}</legend>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="wikiLicensePage">{tr}License page{/tr}: </label> <input type="text" name="wikiLicensePage" id="wikiLicensePage" value="{$prefs.wikiLicensePage|escape}" /></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="wikiSubmitNotice">{tr}Submit notice{/tr}:</label> <input type="text" name="wikiSubmitNotice" id="wikiSubmitNotice" value="{$prefs.wikiSubmitNotice|escape}" /></div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel">{tr}Enable copyright management for{/tr}:</div>
<div class="adminoptionboxchild">
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_feature_copyrights" name="wiki_feature_copyrights" {if $prefs.wiki_feature_copyrights eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="wiki_feature_copyrights">{tr}Wiki{/tr}</label>
	{if $prefs.feature_wiki ne'y'}{icon _id=information} {tr}Feature disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Feature{/tr}">{tr}Enable now{/tr}</a>.{/if}	
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="articles_feature_copyrights" name="articles_feature_copyrights" {if $prefs.articles_feature_copyrights eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="articles_feature_copyrights">{tr}Articles{/tr}</label>
	{if $prefs.feature_articles ne 'y'}{icon _id=information} {tr}Feature disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Feature{/tr}">{tr}Enable now{/tr}</a>.{/if}
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="blogues_feature_copyrights" name="blogues_feature_copyrights" {if $prefs.blogues_feature_copyrights eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="blogues_feature_copyrights">{tr}Blogs{/tr}</label>
	{if $prefs.feature_blogs ne 'y'}{icon _id=information} {tr}Feature disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Feature{/tr}">{tr}Enable now{/tr}</a>.{/if}	
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="faqs_feature_copyrights" name="faqs_feature_copyrights" {if $prefs.faqs_feature_copyrights eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="faqs_feature_copyrights">FAQs</label>
	{if $prefs.feature_faqs ne 'y'}{icon _id=information} {tr}Feature disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Feature{/tr}">{tr}Enable now{/tr}</a>.{/if}	
	</div>
</div>
</div>
</div>

</fieldset>


<div style="padding:1em;" align="center"><input type="submit" value="{tr}Change preferences{/tr}" /></div>
</td></tr></table>
</div>
</form>


<div class="adminoptionbox">
	<div class="adminoption"></div>
	<div class="adminoptionlabel"><label for=""></label></div>
</div>
