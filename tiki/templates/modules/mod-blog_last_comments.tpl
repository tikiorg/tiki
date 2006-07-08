{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-blog_last_comments.tpl,v 1.2 2006-07-08 00:34:05 amette Exp $ *}

{if $feature_blogs eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` blog comments{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last blog comments{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="blog_last_comments" flip=$module_params.flip decorations=$module_params.decorations}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$comments}
          <li><a class="linkmodule" href="tiki-view_blog_post.php?postId={$comments[ix].postId}&amp;show_comments=1" title="{$comments[ix].commentDate|tiki_short_datetime}, {tr}by{/tr} {$comments[ix].user}{if $moretooltips eq 'y'}{tr} on blogpost {/tr}{$comments[ix].blogPostTitle}{/if}">
            {if $moretooltips ne 'y'}<b>{$comments[ix].blogPostTitle}:</b>{/if} {$comments[ix].commentTitle}</a>
          </li>
    {/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
