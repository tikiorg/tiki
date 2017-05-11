{* $Id$ *}
{if $prefs.feature_jquery_validation eq 'n'}
	{if $prefs.min_pass_length gt 1}
		<div class="highlight"><em>{tr _0=$prefs.min_pass_length}Minimum %0 characters long{/tr}</em></div>
	{/if}
	{if $prefs.pass_chr_num eq 'y'}
		<div class="highlight"><em>{tr}Password must contain both letters and numbers{/tr}</em></div>
	{/if}
	{if $prefs.pass_chr_case eq 'y'}
		<div class="highlight"><em>{tr}Password must contain at least one lowercase alphabetical character like "a" and one uppercase character like "A".{/tr}</em></div>
	{/if}
	{if $prefs.pass_chr_special eq 'y'}
		<div class="highlight"><em>{tr}Password must contain at least one special character like " / $ % ? & * ( ) _ + ...{/tr}</em></div>
	{/if}
	{if $prefs.pass_blacklist == 'y'}
		<div class="highlight"><em>{tr}Password is too common.{/tr}</em></div>
	{/if}
	{if !empty($prefs.pass_chr_repetition) and $prefs.pass_chr_repetition eq 'y'}
		<div class="highlight"><em>{tr}Password must not contain a consecutive repetition of the same character such as "111" or "aab"{/tr}</em></div>
	{/if}
{/if}
