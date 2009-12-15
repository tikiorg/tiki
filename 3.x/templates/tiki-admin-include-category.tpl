{* $Id$ *}
<div class="navbar">
{button href="tiki-browse_categories.php" _text="{tr}Browse categories{/tr}"}
{button href="tiki-admin_categories.php" _text="{tr}Administer categories{/tr}"}
</div>

<form action="tiki-admin.php?page=category" method="post">
<input type="hidden" name="categorysetup" />
<div class="cbox">
<table class="admin"><tr><td>
<div style="padding:1em;" align="center"><input type="submit" value="{tr}Change preferences{/tr}" /></div>

<fieldset><legend>{tr}Features{/tr}{if $prefs.feature_help eq 'y'} {help url="Category"}{/if}</legend>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_categorypath" name="feature_categorypath" {if $prefs.feature_categorypath eq 'y'}checked="checked" {/if}onclick="flip('catpathomit');" /></div>
	<div class="adminoptionlabel"><label for="feature_categorypath">{tr}Category path{/tr}</label></div>
<div class="adminoptionboxchild" id="catpathomit" style="display:{if $prefs.feature_categorypath eq 'y'}block{else}none{/if};">
	<div class="adminoptionlabel"><label for="categorypath_excluded">{tr}Exclude these categories{/tr}: </label><input type="text" id="categorypath_excluded" name="categorypath_excluded" value="{$prefs.categorypath_excluded}" size="15" /><br /><em>{tr}Separate category IDs with a comma (,){/tr}.</em></div>
</div>	
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_categoryobjects" name="feature_categoryobjects" {if $prefs.feature_categoryobjects eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="feature_categoryobjects">{tr}Show category objects{/tr}.</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_category_use_phplayers" name="feature_category_use_phplayers" {if $prefs.feature_category_use_phplayers eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="feature_category_use_phplayers">{tr}Use PHPLayers for category browser{/tr}.</label>
	{if $prefs.feature_phplayers ne 'y'}<br />{icon _id=information} <em>{tr}PHPLayers is disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.</em>{/if}
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="categories_used_in_tpl" name="categories_used_in_tpl" {if $prefs.categories_used_in_tpl eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="categories_used_in_tpl">{tr}Categories used in templates (TPL){/tr}</label></div>
</div>
</fieldset>

<fieldset><legend>{tr}Permissions{/tr}</legend>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_search_show_forbidden_cat" name="feature_search_show_forbidden_cat" {if $prefs.feature_search_show_forbidden_cat eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_search_show_forbidden_cat">{tr}Ignore category viewing restrictions{/tr}.</label>{if $prefs.feature_help eq 'y'} {help url="WYSIWYCA+Search"}{/if}<br /><em>{tr}Will improve performance, but may show forbidden results{/tr}.</em></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_category_reinforce" name="feature_category_reinforce" {if $prefs.feature_category_reinforce eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_category_reinforce">{tr}Permission to all (not just any) of an object's categories is required for access{/tr}.</label></div>
</div>
</fieldset>

<div style="padding:1em;" align="center"><input type="submit" value="{tr}Change preferences{/tr}" /></div>
</td></tr></table>
</div>
</form>
