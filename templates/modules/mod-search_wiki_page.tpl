{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="search_wiki_page" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{jq}$(".pagename").tiki("autocomplete", "pagename");{/jq}
  <form class="forms" method="post" action="tiki-listpages.php">
    <input type="hidden" name="lang" value=""/>
    <input name="find" size="14" type="text" accesskey="s" class="pagename"{if isset($find)} value="{$find|escape}"{/if} />
    <label><span style="white-space: nowrap">{tr}Exact match{/tr}</span><input type="checkbox" name="exact_match"{if $exact eq 'y'} checked="checked"{/if}/></label>
    <input type="submit" class="wikiaction btn btn-default" name="search" value="{tr}Go{/tr}"/>
  </form>
{/tikimodule}
