{if $feature_trackers eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` Modified Comments{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last Modified Comments{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="last_modif_tracker_comments" flip=$module_params.flip decorations=$mo\dule_params.decorations}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$modLastModifComments}
      <li>
	  	<a class="linkmodule" href="tiki-view_tracker_item.php?itemId={$modLastModifComments[ix].itemId}">
              {$modLastModifComments[ix].title}
          </a>
        </li>
    {/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
