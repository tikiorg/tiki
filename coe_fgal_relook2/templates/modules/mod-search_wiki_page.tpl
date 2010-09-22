{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="search_wiki_page_new" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{jq}$(".pagename").tiki("autocomplete", "pagename");{/jq}
  <form class="forms" method="post" action="tiki-listpages.php">
    <input type="hidden" name="lang" value=""/>
    <input name="find" size="14" type="text" accesskey="s" class="pagename"{if isset($find)} value="{$find}"{/if} />
    {tr}Exact&nbsp;match{/tr}<input type="checkbox" name="exact_match" {if !isset($exact_match) or $exact_match ne 'n'}checked="checked"{/if}/>
    <input type="submit" class="wikiaction" name="search" value="{tr}Go{/tr}"/> 
  </form>
{/tikimodule}
