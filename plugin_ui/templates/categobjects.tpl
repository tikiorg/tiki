{* $Id$ *}

<div class="catblock">
  <div class="cattitle">
    {tr}Category{/tr}: {foreach name=for key=id item=title from=$titles}
    <a href="tiki-browse_categories.php?parentId={$id}">{$title|tr_if}</a>
    {if !$smarty.foreach.for.last} &amp; {/if}
    {/foreach}
  </div>
  <div class="catlists">
    <ul class="catfeatures">
   {foreach key=t item=i from=$listcat}
      <li>
      {tr}{$t}{/tr}:
      <ul class="catitems">
        {section name=o loop=$i}
        <li>
          <a href="{$i[o].href}" class="link">{$i[o].name}</a>
          </li>
        {/section}
        </ul>
      </li>
    {/foreach}
  </ul>
  </div>
</div>
