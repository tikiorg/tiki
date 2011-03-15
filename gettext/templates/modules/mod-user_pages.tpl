{if isset($modUserPages)}
{tikimodule error=$module_params.error title=$tpl_module_title name="user_pages" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modUserPages nonums=$nonums}
	{section name=ix loop=$modUserPages}
		<li>
			<a class="linkmodule" href="tiki-index.php?page={$modUserPages[ix].pageName|escape:"url"}">
				{$modUserPages[ix].pageName|escape}
			</a>
		</li>
	{/section}
{/modules_list}
{/tikimodule}
{/if}
