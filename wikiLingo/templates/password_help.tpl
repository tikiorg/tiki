{* $Id$ *}
					{if $prefs.min_pass_length > 1}<div class="highlight"><em>{tr _0=$prefs.min_pass_length}Minimum %0 characters long{/tr}</em></div>{/if}
					{if $prefs.pass_chr_num eq 'y'}<div class="highlight"><em>{tr}Password must contain both letters and numbers{/tr}</em></div>{/if}
					{if $prefs.pass_chr_case eq 'y'}<div class="highlight"><em>{tr}Password must contain at least one alphabetical character in lower case like a and one in upper case like A.{/tr}</em></div>{/if}
					{if $prefs.pass_chr_special eq 'y'}<div class="highlight"><em>{tr}Password must contain at least one special character in lower case like " / $ % ? & * ( ) _ + ...{/tr}</em></div>{/if}
					{if !empty($prefs.pass_chr_repetition) and $prefs.pass_chr_repetition eq 'y'}<div class="highlight"><em>{tr}Password must contain no consecutive repetition of the same character as 111 or aab{/tr}</em></div>{/if}
