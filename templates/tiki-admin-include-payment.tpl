{* $Id$ *}
<div class="navbar">
	 {button href="tiki-payment.php" _text="{tr}Payments{/tr}"}
</div>
<form action="tiki-admin.php?page=payment" method="post">
	<fieldset class="admin">
		<legend>{tr}Payment{/tr}</legend>
		{preference name=payment_feature}
		
		{remarksbox title="{tr}Choose payment system{/tr}"}
			{tr}You can only use one payment system per Tiki, so only enter info into one section or another.{/tr}<br />
			{tr}Only PayPal is working at the moment. See PayPal.com{/tr}<br />
			{tr}Cclite: Community currency accounting for local exchange trading systems (LETS). See {/tr}<a href="http://sourceforge.net/projects/cclite/">{tr}sourceforge.net{/tr}</a>
		{/remarksbox}
		
		<div class="adminoptionboxchild" id="payment_feature_childcontainer">
			<fieldset class="admin">
				{preference name=payment_system}
				{preference name=payment_currency}
				{preference name=payment_default_delay}
			</fieldset>
			<div id="payment_systems">
				<h2>{tr}PayPal{/tr}</h2>
				<div class="admin payment">
					{preference name=payment_paypal_business}
		
					<div class="adminoptionboxchild">
						{preference name=payment_paypal_environment}
						{preference name=payment_paypal_ipn}
					</div>
				</div>
				<h2>{tr}Cclite{/tr}</h2>
				<div class="admin payment">
					{remarksbox title="{tr}Experimental{/tr}" type="warning" icon="bricks"}
						{tr}Cclite is for creating and managing alternative or complementary trading currencies and groups{/tr}
						{tr}Work in progress for Tiki 6{/tr}
					{/remarksbox}
					{preference name=payment_cclite_registry}
					<div class="adminoptionboxchild">
						{preference name=payment_cclite_gateway}
						{preference name=payment_cclite_merchant_user}
						{preference name=payment_cclite_merchant_key}
						{preference name=payment_cclite_mode}
						{preference name=payment_cclite_hashing_algorithm}
						{preference name=payment_cclite_notify}
					</div>
				</div>
			</div>
		{jq}
if ($jq.ui) {
	var idx = $jq("select[name=payment_system]").attr("selectedIndex");
	$jq("#payment_systems").tiki("accordion", {heading: "h2"});
	if (idx > 0) { $jq("#payment_systems").accordion("option", "active", idx); }
}{/jq}
		</div>
	</fieldset>
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" name="faqcomprefs" value="{tr}Change settings{/tr}" />
	</div>
</form>
