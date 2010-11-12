{tikimodule error=$module_params.error title=$tpl_module_title name="payment_outstanding" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{foreach from=$outstanding.data item=payment}
<a href="tiki-payment.php?invoice={$payment.paymentRequestId|escape}">{$payment.description|escape}</a>
{/foreach}
{/tikimodule}
