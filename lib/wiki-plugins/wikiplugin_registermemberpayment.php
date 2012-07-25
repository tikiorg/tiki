<?php

function wikiplugin_registermemberpayment_info()
{
	require_once( 'lib/wiki-plugins/wikiplugin_memberpayment.php' );
	$infoFromParent = wikiplugin_memberpayment_info();

	return array(
		'name' => tra('Register Member Payment'),
		'documentation' => '',
		'validate' => 'all',
		'description' => tra('Allows user to register and make member payment at the same time'),
		'prefs' => array( 'wikiplugin_registermemberpayment' ),
		'body' => tra('NA'),
		'icon' => 'img/icons/text_replace.png',
		'params' => array_merge($infoFromParent['params'], array(
			'fixedperiods' => array(
				'required' => false,
				'name' => tra('Fixed Periods'),
				'description' => tra('Give specific periods that can be chosen with a dropdown list.  Example - "name:value;name:value;value;value;"'),
				'filter' => 'text',
				'default' => 'Number of periods:',
			),
		))
	);
}

function wikiplugin_registermemberpayment($data, $params, $offset)
{
	global $headerlib, $user, $smarty, $tiki_p_payment_view;
	static $i;
	$i++;

	if ($tiki_p_payment_view != 'y') {
		return tr('Insufficient Privileges');
	}

	$fakeUser = false;
	if (empty($user)) {
		$fakeUser = true;
		$user = end(explode('|', $_REQUEST['wp_member_users']));
		$_SESSION['forceanon'] = 'y';
		$_REQUEST['price'] = $_POST['price'] = $_GET['price'] = 30.00;
	}

	$periodslabel = (isset($params['periodslabel']) ? tr($params['periodslabel']) : 'Number of periods:');
	$fixedperiodsDDL = '';

	$fixedperiods = explode(';',isset($params['fixedperiods']) ? $params['fixedperiods'] : '');
	foreach($fixedperiods as $fixedperiod) {
		if (!empty($fixedperiod)) {
			$fixedperiod = explode(':', $fixedperiod);
			$name = $fixedperiod[0];
			$value = (!empty($fixedperiod[1]) ? $fixedperiod[1] : $fixedperiod[0]);
			$fixedperiodsDDL .= '<option value="' . trim($value) . '">' . trim($name) . '</option>\\';
		}
	}

	$memberPayment = TikiLib::lib('parser')->parse_data(wikiplugin_memberpayment( $data, $params, $offset ), array('is_html' => true));
	if ($fakeUser == true) unset($user);

	include_once('lib/smarty_tiki/function.user_registration.php');
	$register = smarty_function_user_registration(array(), $smarty);

	if (isset($_POST['msg'])) {
		$_POST['msg'] = addslashes(htmlspecialchars($_POST['msg']));
		$headerlib->add_jq_onready("$.notify('" . $_POST['msg'] . "')");
	}

	$headerlib->add_jq_onready(<<<JQ
		var reg = $('#memberRegister$i');
		var submitBtn = reg.find('input.registerSubmit'),submitBtnTr = reg.find('tr.registerSubmitTr');

		$('<tr>\
			<td>Membership Type:</td>\
			<td>\
				<select id="memberType$i" name="group">\
					<option value="Premium Registered">Premium</option>\
					<option value="Registered">Free</option>\
				</select>\
			</td>\
		</tr><tr>\
			<td>$periodslabel</td>\
			<td>\
				<select id="memberDuration$i" name="duration">\
					$fixedperiodsDDL
				</select>\
			</td>\
		</tr>').insertBefore(submitBtnTr);

		var frm = $('#memberRegister$i form');
		frm
			.find('input:last').click(function() {
				$.post($.service('user', 'register') + '&' + frm.serialize(), function(data) {
					data = $.parseJSON(data);
					if (typeof data == "string") {
						var memberPayment = $('#memberPayment$i form');
						if ($('#memberType$i').val() == 'Premium Registered') {
							$('<input name="msg" />')
								.val(data)
								.prependTo(memberPayment);

							memberPayment.find('input[name="wp_member_users"]').val($('#memberRegister$i #name').val());
							memberPayment.find('input[name="wp_member_periods"]').val($('#memberDuration$i').val());
							memberPayment.find('input:last').click();
						} else {
							$.notify(data);
						}
					} else {
						$.each(data, function(i) {
							$.notify(data[i]);
						});
					}
				});
				return false;
			});
JQ
);

	$paymentStyle = '';
	$registerStyle = '';
	if (isset($_REQUEST['wp_member_users'])) {
		$registerStyle = 'display: none;';
	} else {
		$paymentStyle = 'display: none;';
	}

	return "~np~
		<div id='memberPayment$i' style='$paymentStyle'>$memberPayment</div>
		<div id='memberRegister$i' style='$registerStyle'>$register</div>
	~/np~";
}