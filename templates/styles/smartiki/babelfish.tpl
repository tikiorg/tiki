{* $Id$ *}

{if $prefs.feature_babelfish eq 'y'}

<form>
<select name="babel" class="small">
<option value="#">{tr}Translate in{/tr} ...</option>
{section loop=$babelfish_links name=i}
<option value="{$babelfish_links[i].href}">{$babelfish_links[i].msg}</option>
{/section}
</select>
<input type="submit" name="action" value="{tr}babelfish it{/tr}!">
</form>

{/if}
