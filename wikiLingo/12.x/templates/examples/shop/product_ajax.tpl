{include file='templates/examples/shop/product_list_inner.tpl'}
{pagination_links offset_jsvar='customsearch_0.offset' _onclick="$('#customsearch_0').submit();return false;" resultset=$results}{/pagination_links}
{* Note: the _onclick param in pagination now needs to be single quoted to avoid js compile errors *}