{* $Id$ *}

<div class="catblock clearfix">
  <div class="cattitle">
    <span class="label">{tr}Category{/tr}: </span>{foreach name=for key=id item=title from=$titles}
    <a href="tiki-browse_categories.php?parentId={$id}">{$title|tr_if}</a>
    {if !$smarty.foreach.for.last} &amp; {/if}
    {/foreach}
  </div>
  <div class="catlists">
    <ul class="{if $params.showtype ne 'n'}catfeatures{else}catitems{/if}">
   {foreach key=t item=i from=$listcat}
   	{if $params.showtype ne 'n'}
      <li>
      {tr}{$t}{/tr}:
      <ul class="catitems">
	{/if}
        {section name=o loop=$i}
        <li>
			{if $prefs.feature_sefurl eq 'y'}
				<a href="{$i[o].itemId|sefurl:$i[o].type}" class="link">
			{else}
				<a href="{$i[o].href}" class="link">
			{/if}
			{if $params.showname ne 'n' or empty($i[o].description)}
				{$i[o].name|escape}</a>
				{if $params.showdescription eq 'y'} <span class='description'>{/if}
			{/if}
			{if $params.showdescription eq 'y'}
				{$i[o].description|escape}
				{if $params.showname ne 'n' or empty($i[o].description)}
					</span>
				{else}
					</a>
				{/if}
			{/if}
          </li>
        {/section}
	{if $params.showtype ne 'n'}
        </ul>
      </li>
	{/if}
    {/foreach}
  </ul>
  </div>
</div>
