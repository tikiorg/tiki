{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_modified_blogs.tpl,v 1.8 2003-11-23 03:53:04 zaufi Exp $ *}

{if $feature_blogs eq 'y'}
{tikimodule title="{tr}Last Modified blogs{/tr}" name="last_modified_blogs"}
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
