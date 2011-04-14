{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="quick_edit" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
<form method="post" action="{$qe_action|escape}">
<div>
{if $templateId}<input type="hidden" name="templateId" value="{$templateId}" />{/if}
{if $customTip}<input type="hidden" name="customTip" value="{$customTip}" />{/if}
{if $customTipTitle}<input type="hidden" name="customTipTitle" value="{$customTipTitle}" />{/if}
{if $wikiTplHeader}<input type="hidden" name="wikiTplHeader" value="{$wikiTplHeader}" />{/if}
{if $mod_quickedit_heading}<div class="box-data">{$mod_quickedit_heading}</div>{/if}
{if $enterdescription==1 or $chooseCateg==1 or $pastetext==1}<label>{tr}Page name{/tr}</label>{/if}
<input id="{$qefield}" size="{$size}" type="text" name="page" />
{if $enterdescription==1}{if $prefs.feature_wiki_description eq 'y' or $prefs.metatag_pagedesc eq 'y'}
<div>
<label>{if $prefs.metatag_pagedesc eq 'y'}{tr}Description (used for metatags):{/tr}{else}{tr}Description:{/tr}{/if}</label>
{if $prefs.disableJavascript != 'y'}<a id="flipperqdescription" href="javascript:flipWithSign('qdescription')">[+]</a>{/if}
<input id="qdescription" {if $prefs.disableJavascript != 'y'} style="display: none;"{/if} type="text" size="{$size}" name="description" />
</div>{/if}{/if}
{if $chooseCateg==1}
<div>
<input type="hidden" name="cat_categorize" value="on" />
<label>{tr}Category{/tr}</label>
{if $prefs.disableJavascript != 'y'}<a id="flipperqcat" href="javascript:flipWithSign('qcat')">[+]</a>{/if}
<select id="qcat" style="width:97%;{if $prefs.disableJavascript != 'y'} display: none;{/if}" name="cat_categories[]">
<option></option>
{foreach from=$qcats item="cat"}<option value="{$cat.categId}"{if $cat.categId==$categId} selected="selected"{/if}>{$cat.name}</option>
{/foreach}
</select>
</div>
{else}
{if $categId}<input type="hidden" name="categId" value="{$categId}" />{/if}
{/if}
{if $addcategId}<input type="hidden" name="cat_categories[]" value="{$addcategId}" />
<input type="hidden" name="cat_categorize" value="on" />{/if}
{if $pastetext==1}<label>{tr}Paste content here{/tr}</label><textarea name="copypaste" style="width:96%;" cols="{$size}" rows="2"></textarea>{/if}
<input type="submit" name="qedit" value="{$submit}" />
</div>
</form>
{if $prefs.javascript_enabled eq 'y' and $prefs.feature_jquery_autocomplete eq 'y'}
{jq}
	$("#{{$qefield}}").tiki("autocomplete", "pagename");
{/jq}
{/if}
{/tikimodule}
