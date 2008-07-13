{* $Id$ *}

{if $prefs.feature_articles eq 'y'}
{if !isset($tpl_module_title)}{eval assign=tpl_module_title var="{tr}$module_title{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="article_topics" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{if $listTopics[ix].arts > 0}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$listTopics}
    <li><a class="linkmodule" href="tiki-view_articles.php?topic={$listTopics[ix].topicId}">
          {$listTopics[ix].name}
        </a>
    </li>
    {/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/if}
{/tikimodule}
{/if}
