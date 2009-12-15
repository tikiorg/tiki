{* $Id$ *}

{if $prefs.feature_blogs eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` blog comments{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last blog comments{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule error=$module_params.error title=$tpl_module_title name="blog_last_comments" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$comments}
          <li><a class="linkmodule" href="tiki-view_blog_post.php?postId={$comments[ix].postId}&amp;comzone=show#threadId{$comments[ix].threadId}" title="{$comments[ix].commentDate|tiki_short_datetime}, {tr}by{/tr} {$comments[ix].userName}{if $moretooltips eq 'y'} {tr}on blogpost{/tr} {$comments[ix].title}{/if}">
		{if $moretooltips ne 'y'}<b>{$comments[ix].title}:</b>{/if}
		{$comments[ix].commentTitle}
		{if $module_params.nodate neq 'y'}
			<small class="description">{$comments[ix].commentDate|tiki_short_datetime}</small>
		{/if}
          </a></li>
    {/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
