{* $Id$ *}

{if $prefs.feature_blogs eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` blog comments{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last blog comments{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="blog_last_comments" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$comments}
          <li><a class="linkmodule" href="tiki-view_blog_post.php?postId={$comments[ix].postId}&comzone=show#threadId{$comments[ix].threadId}" title="{$comments[ix].commentDate|tiki_short_datetime}, {tr}by{/tr} {$comments[ix].user}{if $moretooltips eq 'y'}{tr} on blogpost {/tr}{$comments[ix].blogPostTitle}{/if}">
            {if $moretooltips ne 'y'}<b>{$comments[ix].blogPostTitle}:</b>{/if} {$comments[ix].commentTitle}</a>
          </li>
    {/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
