{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_submissions.tpl,v 1.10 2003-11-24 01:33:46 zaufi Exp $ *}

{if $feature_submissions eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` submissions{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last submissions{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="last_submissions"}
  <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modLastSubmissions}
      <tr>
      {if $tiki_p_edit_submission eq 'y'}
          {if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
          <td class="module">
            <a class="linkmodule" href="tiki-edit_submission.php?subId={$modLastSubmissions[ix].subId}">
              {$modLastSubmissions[ix].title}
            </a>
          </td>
      {else}
          {if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
          <td class="module">{$modLastSubmissions[ix].title}</td>
      {/if}
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
