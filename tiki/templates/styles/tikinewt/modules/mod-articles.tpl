{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-articles.tpl,v 1.3.12.2 2005/02/23 00:10:20 michael_davey *}

{if $prefs.feature_articles eq 'y'}
{if !isset($tpl_module_title)}{eval assign=tpl_module_title var="{tr}$module_title{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="articles" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$modArticles}
       <li>
        <a class="linkmodule" href="tiki-read_article.php?articleId={$modArticles[ix].articleId}">
          {$modArticles[ix].title}
        </a>
        </li>
    {/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
