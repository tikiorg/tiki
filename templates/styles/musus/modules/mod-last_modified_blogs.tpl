{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-last_modified_blogs.tpl,v 1.1 2004-01-07 04:31:24 musus Exp $ *}

{if $feature_blogs eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` Modified blogs{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last Modified blogs{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="last_modified_blogs"}
  <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modLastModifiedBlogs}
      <tr>
        {if $nonums != 'y'}<td class="module">{$smarty.section.ix.index_next})</td>{/if}
        <td class="module">
          <a class="linkmodule" href="tiki-view_blog.php?blogId={$modLastModifiedBlogs[ix].blogId}">
            {$modLastModifiedBlogs[ix].title}
          </a>
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
