{assign var="row" value=$results[0]}
{assign var="maxRows" value=1}
<style type="text/css">
	.product th {
		text-align:right;
		vertical-align: top;
		padding-right: .5em;
	}
</style>
<table class="product">
	<tr itemscope itemtype="http://schema.org/Offer">
		<td rowspan="14" style="padding-right: 2em; min-height: 600px;">
			{* values from the {list} plugin in Smarty here are surrounded with ~np~ tags which are removed later, hence the nonp modifiers *}
			{$img = ','|explode:$row.images|nonp}
			{if $img}
				<div class="item-img-outer" style="min-width:360px;min-height:360px;">
					<a class="item-img" href="display{$img[0]}" title="{$row.title}" data-box="[p]box">
						<img itemprop="image" src="display{$img[0]}?height=350" height="350" />
					</a>
				</div>
			{else}
				<div class="item-noimg" style="width:350px;height:350px;text-align:center;">
					Image coming soon
				</div>
			{/if}
		</td>
		<td colspan="2">
			<h1 itemprop="name">{$row.name}</h1>
		</td>
	</tr>
	<tr>
		<th>
			Product Code:
		</th>
		<td>
			<span itemprop="serialNumber">{$row.object_id}</span>
		</td>
	</tr>
	<tr>
		<th>
			Category:
		</th>
		<td>
			<span itemprop="category">{$row.category}</span>
		</td>
	</tr>
	<tr>
		<th>
			Status:
		</th>
		<td>
			{if $row.stock|nonp gt 0}
				<strong>In stock</strong>
				<meta itemprop="availability" content="InStock">
			{else}
				<em>Awaiting stock</em>
				<meta itemprop="availability" content="OutOfStock">
			{/if}
		</td>
	</tr>
	<tr>
		<th>
			<strong>Price:</strong>
		</th>
		<td>
			<strong itemprop="price">{$row.price}</strong>
			<!-- meta itemprop="priceCurrency" content="USD" can't access prefs here sadly :(-->
		</td>
	</tr>
	<tr>
		<th>
			&nbsp;
		</th>
		<td>
			{if $row.stock|nonp gt 0}
				{include file="templates/examples/shop/add_to_cart.tpl"}
				<meta itemprop="acceptedPaymentMethod" content="http://www.heppnetz.de/ontologies/goodrelations/v1#PayPal">
			{/if}
		</td>
	</tr>
	<tr>
		<th>
			&nbsp;
		</th>
		<td>
			{$row.description}
		</td>
	</tr>
	<tr>
		<td colspan="3">
			{if count($img) > 1}
				{for $i = 1; $i< count($img); $i++}
					<a class="item-thumb" href="display{$img[$i]}" title="{$row.name}" data-box="[p]box">
						<img src="thumbnail{$img[$i]}" width="120" height="120" />
					</a>
				{/for}
			{/if}
		</td>
	</tr>
</table>
{pagination_links resultset=$results}{/pagination_links}
