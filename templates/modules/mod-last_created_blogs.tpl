{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_created_blogs.tpl,v 1.15 2007-10-14 17:51:00 mose Exp $ *}

{if $prefs.feature_blogs eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` Created blogs{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last Created blogs{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="last_created_blogs" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modLastCreatedBlogs}
      <tr>
        {if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
        <td class="module">&nbsp;
          <a class="linkmodule" href="tiki-view_blog.php?blogId={$modLastCreatedBlogs[ix].blogId}" title="{$modLastCreatedBlogs[ix].created|tiki_short_datetime}, {tr}by{/tr} {if $modLastCreatedBlogs[ix].user ne ''}{$modLastCreatedBlogs[ix].user}{else}{tr}Anonymous{/tr}{/if}">
            {$modLastCreatedBlogs[ix].title}
          </a>
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
