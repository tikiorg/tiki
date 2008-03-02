{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-last_submissions.tpl,v 1.10.10.2 2005/02/23 14:51:54 michael_davey *}

{if $feature_submissions eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="<a href=\"tiki-list_submissions.php\">{tr}Last `$module_rows` submissions{/tr}</a>" assign="tpl_module_title"}
{else}
{eval var="<a href=\"tiki-list_submissions.php\">{tr}Last submissions{/tr}</a>" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="last_submissions" flip=$module_params.flip decorations=$module_params.decorations}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$modLastSubmissions}
      <li>
      {if $tiki_p_edit_submission eq 'y'}
          
            <a class="linkmodule" href="tiki-edit_submission.php?subId={$modLastSubmissions[ix].subId}">
              {$modLastSubmissions[ix].title}
            </a>
       {else}
          
          <span class="module">{$modLastSubmissions[ix].title}</span>
      {/if}
      </li>
    {/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
