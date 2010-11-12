{tikimodule error=$module_params.error title=$tpl_module_title name="payment_outstanding" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$outstanding.data nonums=$nonums}
{foreach from=$outstanding.data item=payment}
<li><a href="tiki-payment.php?invoice={$payment.paymentRequestId|escape}">{$payment.description|escape}</a></li>
{/foreach}
{/modules_list}
{/tikimodule}
