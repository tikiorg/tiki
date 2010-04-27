{if !$tidy}
{remarksbox type="warning" title="{tr}Warning{/tr}"}{tr}Tidy extension not present{/tr}{/remarksbox}
{/if}
{if $http or $curl}
{remarksbox type="notice" title="{tr}Notice{/tr}"}
{if $http}{tr}PECL HTTP extension present{/tr}<br/>{/if}
{if $curl}{tr}cURL extension present{/tr}{/if}
{/remarksbox}
{/if}
{if !$http and !$curl}
{remarksbox type="warning" title="{tr}Notice{/tr}"}{tr}PECL HTTP and cURL extension not present. Replay of the TikiTest will not be possible.{/tr}{/remarksbox}
{/if}
<div class="navbar">
	{if $tiki_p_admin_tikitests eq 'y' or $tiki_p_play_tikitests eq 'y'}
		{button href="tiki_tests/tiki-tests_list.php" _text="{tr}List TikiTests{/tr}"}
	{/if}

	{if $tiki_p_admin_tikitests eq 'y' or $tiki_p_edit_tikitests eq 'y'}
		{button href="tiki_tests/tiki-tests_record.php" _text="{tr}Create a TikiTest{/tr}"}
	{/if}
	{if $filename neq '' and ($tiki_p_admin_tikitests eq 'y' or $tiki_p_play_tikitests eq 'y')}
		{assign var=path value="$tikiroot tiki_tests/tiki-tests_edit.php"|replace:' ':''}
		{if $smarty.server.SCRIPT_NAME eq "$tikiroot tiki_tests/tiki-tests_edit.php"|replace:' ':''}
			{button href="tiki_tests/tiki-tests_replay.php?filename=$filename&amp;action=Config" _text="{tr}Replay the TikiTest{/tr}"}
		{elseif $smarty.server.SCRIPT_NAME eq "$tikiroot tiki_tests/tiki-tests_replay.php"|replace:' ':''}
			{button href="tiki_tests/tiki-tests_edit.php?filename=$filename" _text="{tr}Edit the TikiTest{/tr}"}
		{/if}
	{/if}
</div>
