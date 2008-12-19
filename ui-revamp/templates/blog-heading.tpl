{if $description neq ""}
  <div class="blogdesc">{$description}</div>
{/if}
<div class="bloginfo">
{tr}Created by{/tr} {$creator|userlink} {tr}on{/tr} {$created|tiki_short_datetime}<br />
{tr}Last post{/tr} {$lastModif|tiki_short_datetime}<br />
({$posts} {tr}Posts{/tr} | {$hits} {tr}Visits{/tr} | {tr}Activity={/tr}{$activity|string_format:"%.2f"})
</div>
