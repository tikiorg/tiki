{if $prefs.feature_page_title eq 'y'}<h1><a  href="tiki-backlinks.php?page={$page}" title="{tr}backlinks to{/tr} {$page}" class="pagetitle">{$page}</a></h1>{/if}
<div class="wikitext">{$parsed}</div>
{if !isset($smarty.request.clean) or $smarty.request.clean neq 'y'}
  {if isset($prefs.wiki_authors_style) && $prefs.wiki_authors_style eq 'business'}
  <p class="editdate">
    {tr}Last edited by{/tr} {$lastUser}
    {section name=author loop=$contributors}
    {if $smarty.section.author.first}, {tr}based on work by{/tr}
    {else}
      {if !$smarty.section.author.last},
      {else} {tr}and{/tr}
      {/if}
    {/if}
    {$contributors[author]}
    {/section}.<br />                                         
    {tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime}.
  </p>
  {elseif isset($prefs.wiki_authors_style) &&  $prefs.wiki_authors_style eq 'collaborative'}
  <p class="editdate">
    {tr}Contributors to this page{/tr}: {$lastUser}
    {section name=author loop=$contributors}
    {if !$smarty.section.author.last},
    {else} {tr}and{/tr}
    {/if}
    {$contributors[author]}
    {/section}.<br />
    {tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime}.
  </p>
  {elseif isset($prefs.wiki_authors_style) &&  $prefs.wiki_authors_style eq 'none'}
  {else}
  <p class="editdate">
    {tr}Created by{/tr}: {$creator}
    {tr}Last Modification{/tr}: {$lastModif|tiki_long_datetime} {tr}by{/tr} {$lastUser|userlink}
  </p>
  {/if}

  {if (!$prefs.page_bar_position or $prefs.page_bar_position eq 'bottom' or $prefs.page_bar_position eq 'both') and $machine_translate_to_lang == ''}
	  {include file='tiki-page_bar.tpl'}
  {/if}
{/if}
