{* $Id$ *}

{if $prefs.feature_submissions eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="<a href=\"tiki-list_submissions.php\">{tr}Last `$module_rows` submissions{/tr}</a>" assign="tpl_module_title"}
{else}
{eval var="<a href=\"tiki-list_submissions.php\">{tr}Last submissions{/tr}</a>" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="last_submissions" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
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
