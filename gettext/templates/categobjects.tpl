<div class="catblock clearfix"> 
	{if !isset($params.showTitle) or $params.showTitle eq 'y'}
		<div class="cattitle">
			<span class="label">{tr}Category:{/tr}&nbsp&nbsp</span>
			{foreach name=for key=id item=title from=$titles}
				{if $params.categoryshowlink ne 'n'}<a href="tiki-browse_categories.php?parentId={$id}">{/if}
				{$title|tr_if|escape}
				{if $params.categoryshowlink ne 'n'}</a>{/if}
				{if !$smarty.foreach.for.last} &amp; {/if}
			{/foreach}
		</div>
	{/if}
  <div class="catlists">
   {* ajout BTy:B01103 ### see better ? *} <span class='simple_title'>{tr}Other objects of the category can be reached by the following links{/tr} :</span>
    <ul class="{if $params.showtype ne 'n'}catfeatures{elseif $params.one eq 'y'}catitemsone{else}catitems{/if}">
   {foreach key=t item=i from=$listcat}
   	{if $params.showtype ne 'n'}
      <li>
      {tr}{$t}:{/tr}
      <ul class="{if $params.one eq 'y'}catitemsone{else}catitems{/if}">
	{/if}
        {section name=o loop=$i}
        <li>
			{if $params.showlinks ne 'n'}
				{if $prefs.feature_sefurl eq 'y'}
					<a href="{$i[o].itemId|sefurl:$i[o].type}" class="link">{* ###trebly:B01221-13 *}<span class='inline_list_separator '> &nbsp;•&nbsp;</span>
				{else}
					<a href="{$i[o].href}" class="link"><span class='inline_list_separator '> &nbsp;•&nbsp;</span>
				{/if}
			{/if}
			{if $params.showname ne 'n' or empty($i[o].description)}
				{$i[o].name|escape}
				{if $params.showlinks ne 'n'}</a>{/if}
				{if $params.showdescription eq 'y'} <span class='description'>{/if}
			{/if}
			{if $params.showdescription eq 'y'}
				{$i[o].description|escape}
				{if $params.showname ne 'n' or empty($i[o].description)}
					</span>
				{else}
					{if $params.showlinks ne 'n'}</a>{/if}
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
