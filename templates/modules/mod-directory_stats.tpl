{if $feature_directory eq 'y'}
<div class="box">
<div class="box-title">
{tr}Directory Stats{/tr}
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