{tikimodule error=$module_params.error title=$tpl_module_title name="last_modif_tracker_comments" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modLastModifComments nonums=$nonums}
	{section name=ix loop=$modLastModifComments}
		<li>
			<a class="linkmodule" href="tiki-view_tracker_item.php?itemId={$modLastModifComments[ix].itemId}">
				{$modLastModifComments[ix].title|escape}
			</a>
		</li>
	{/section}
{/modules_list}
{/tikimodule}
