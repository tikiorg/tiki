{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-directory_stats.tpl,v 1.5 2003-11-20 23:49:04 mose Exp $ *}

{if $feature_directory eq 'y'}
<div class="box">
<div class="box-title">
{include file="module-title.tpl" module_title="{tr}Directory Stats{/tr}" module_name="directory_stats"}
</div>
<div class="box-data">
{tr}Sites{/tr}: {$modDirStats.valid}<br />
{tr}Sites to validate{/tr}: {$modDirStats.invalid}<br />
{tr}Categories{/tr}: {$modDirStats.categs}<br />
{tr}Searches{/tr}: {$modDirStats.searches}<br />
{tr}Visited links{/tr}: {$modDirStats.visits}<br />
</div>
</div>
{/if}