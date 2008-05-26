<div class="navbar">
	{if $tiki_p_admin_tikitests eq 'y' or $tiki_p_play_tikitests eq 'y'}
	<a class="linkbut" href="tiki_tests/tiki-tests_list.php">{tr}List TikiTests{/tr}</a>
	{/if}
	{if $tiki_p_admin_tikitests eq 'y' or $tiki_p_edit_tikitests eq 'y'}
	<a class="linkbut" href="tiki_tests/tiki-tests_record.php">{tr}Create a TikiTest{/tr}</a>
	{/if}
	{if $filename neq '' and ($tiki_p_admin_tikitests eq 'y' or $tiki_p_play_tikitests eq 'y')}
	{assign var=path value="$tikiroot tiki_tests/tiki-tests_edit.php"|replace:' ':''}
	{if $smarty.server.SCRIPT_NAME eq "$tikiroot tiki_tests/tiki-tests_edit.php"|replace:' ':''}
	<a class="linkbut" href="tiki_tests/tiki-tests_replay.php?filename={$filename}&amp;action={tr}Config{/tr}">{tr}Replay a TikiTest{/tr}</a>
	{elseif $smarty.server.SCRIPT_NAME eq "$tikiroot tiki_tests/tiki-tests_replay.php"|replace:' ':''}
	<a class="linkbut" href="tiki_tests/tiki-tests_edit.php?filename={$filename}">{tr}Edit the TikiTest{/tr}</a>
	{/if}
	{/if}
</div>
