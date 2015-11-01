<div class="clearfix">
	<ul class="product-list searchresults">
		{foreach $results as $row}
			<li style="float:left;width:220px;height:450px;padding: 4px;">{*<!-- itemId: "{$row.object_id}" -->*}
				<div style="padding-right:0.5em;width:120px;margin:0 auto;">
					{$img = ','|explode:$row.images|nonp}
					{if count({$img})}
						<a class="list-thumb" href="cart+product?itemId={$row.object_id}" title="{$row.title}">
							<img src="thumbnail{$img[0]}" width="120" height="120" />
						</a>
					{else}
						<div class="list-nothumb" style="width:120px;height:120px;text-align:center;">
							Image coming soon
						</div>
					{/if}
				</div>
				<div style="padding: 0 .5em;min-height:36px;text-align:center;">
					<h3 class="list-title"><a href="cart+product?itemId={$row.object_id}">{$row.name}</a></h3>
					<p itemprop="category" class="list-cat">{$row.category}</p>
				</div>
				<div style="padding:0 .5em;text-align:center;min-height:69px;">
					<p style="font-size: 1.1em;"><strong>{$row.price}</strong></p>
					{if $row.stock|nonp gt 0}
						{include file="templates/examples/shop/add_to_cart.tpl"}
						<meta itemprop="acceptedPaymentMethod" content="http://purl.org/goodrelations/v1#PayPal">
						<meta itemprop="availability" content="InStock">
					{else}
						<em>Awaiting stock</em>
						<meta itemprop="availability" content="OutOfStock">
					{/if}
				</div>
			</li>
		{/foreach}
	</ul>
</div>
<br class="clearfix">
