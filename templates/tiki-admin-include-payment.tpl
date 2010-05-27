{* $Id$ *}
<div class="navbar">
	 {button href="tiki-payment.php" _text="{tr}Payments{/tr}"}
</div>
<form action="tiki-admin.php?page=payment" method="post">
	<fieldset class="admin">
		<legend>{tr}Payment{/tr}</legend>
		{preference name=payment_feature}
		
		<div class="adminoptionboxchild" id="payment_feature_childcontainer">
			{preference name=payment_currency}
			{preference name=payment_default_delay}
			{preference name=payment_paypal_business}

			<div class="adminoptionboxchild">
				{preference name=payment_paypal_environment}
				{preference name=payment_paypal_ipn}
			</div>
		</div>
	</fieldset>
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" name="faqcomprefs" value="{tr}Change settings{/tr}" />
	</div>
</form>
