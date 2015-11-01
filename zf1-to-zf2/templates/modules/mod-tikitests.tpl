{* $Id$ *}

{if $tiki_p_admin_tikitests eq 'y' or $tiki_p_play_tikitests eq 'y' or $tiki_p_edit_tikitests eq 'y'}
	{if !isset($tpl_module_title)}
		{eval var="{tr}TikiTests Menu{/tr}" assign="tpl_module_title"}
	{/if}
	{tikimodule error=$module_params.error title=$tpl_module_title name="tikitests" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
		{if $tiki_p_admin_tikitests eq 'y' or $tiki_p_play_tikitests eq 'y'}
			<div class="option"><a class="linkmodule" href="tiki_tests/tiki-tests_list.php">{tr}List Tests{/tr}</a></div>
		{/if}
		{if $tiki_p_admin_tikitests eq 'y' or $tiki_p_edit_tikitests eq 'y'}
			<div class="option"><a class="linkmodule" href="tiki_tests/tiki-tests_record.php">{tr}Create Test{/tr}</a></div>
		{/if}
	{/tikimodule}
{/if}

