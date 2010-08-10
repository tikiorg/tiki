<h1>{$title|escape}</h1>
{if $description neq ""}
  <div class="description">{$description|escape}</div>
{/if}
{* example code to add more info to the default blog heading if desired
 * remove the line above (starting curly bracket then asterisk) and the last line to enable
<div class="bloginfo">
{tr}Created by{/tr} {$creator|userlink} {tr}on{/tr} {$created|tiki_short_datetime}<br />
{tr}Last post{/tr} {$lastModif|tiki_short_datetime}<br />

({$posts} {tr}Posts{/tr} | {$hits} {tr}Visits{/tr} | {tr}Activity={/tr}{$activity|string_format:"%.2f"})
</div>
*}