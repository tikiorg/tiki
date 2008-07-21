{php}
	$this->assign("tidy",extension_loaded("tidy"));
	$this->assign("http",extension_loaded("http"));
	$this->assign("curl",extension_loaded("curl"));
{/php}
{if !$tidy}
{remarksbox type="warning" title="{tr}Warning{/tr}"}{tr}Tidy extension not present{/tr}{/remarksbox}
{/if}
{if $http or $curl}
{remarksbox type="notice" title="{tr}Notice{/tr}"}
{if $http}{tr}PECL HTPP extension present{/tr}{/if}
{if $curl}{tr}cURL extension present{/tr}{/if}
{/remarksbox}
{/if}
{if !$http and !$curl}
{remarksbox type="warning" title="{tr}Notice{/tr}"}{tr}PECL HTPP and cURL extension not present. Replay of the TikiTest will not be possible.{/tr}{/remarksbox}
{/if}
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
	<a class="linkbut" href="tiki_tests/tiki-tests_replay.php?filename={$filename}&amp;action={tr}Config{/tr}">{tr}Replay the TikiTest{/tr}</a>
	{elseif $smarty.server.SCRIPT_NAME eq "$tikiroot tiki_tests/tiki-tests_replay.php"|replace:' ':''}
	<a class="linkbut" href="tiki_tests/tiki-tests_edit.php?filename={$filename}">{tr}Edit the TikiTest{/tr}</a>
	{/if}
	{/if}
</div>
