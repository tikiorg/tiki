<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_registermemberpayment_info()
{
	require_once( 'lib/wiki-plugins/wikiplugin_memberpayment.php' );
	$infoFromParent = wikiplugin_memberpayment_info();

	return array(
		'name' => tra('Register Member Payment'),
		'documentation' => 'PluginRegisterMemberPayment',
		'validate' => 'all',
		'description' => tra('Register and make a member payment at the same time'),
		'prefs' => array( 'wikiplugin_registermemberpayment', 'payment_feature' ),
		'body' => tra('NA'),
		'iconname' => 'money',
		'introduced' => 9.1,
		'params' => array_merge(
			$infoFromParent['params'],
			array(
				'fixedperiods' => array(
					'required' => false,
					'name' => tra('Fixed Periods'),
					'description' => tra('Give specific periods that can be chosen with a dropdown list.
						Example:') . " <code>name:value;name:value;value;value;</code>",
					'since' => '9.1',
					'filter' => 'text',
					'default' => 'Number of periods:',
				),
			)
		)
	);
}

function wikiplugin_registermemberpayment($data, $params, $offset)
{
	global $user, $tiki_p_payment_view;
	$headerlib = TikiLib::lib('header');
	$smarty = TikiLib::lib('smarty');

	static $i;
	$i++;

	if ($tiki_p_payment_view != 'y') {
		return tr('Insufficient Privileges');
	}

	if (empty($user) && isset($_REQUEST['wp_member_users']) == true) {
		$user = end(explode('|', $_REQUEST['wp_member_users']));
		$_SESSION['forceanon'] = 'y';
		$_REQUEST['price'] = $_POST['price'] = $_GET['price'] = $params['price'];
	}

	$periodslabel = (isset($params['periodslabel']) ? tr($params['periodslabel']) : 'Number of periods:');
	$fixedperiodsDDL = '';

	$fixedperiods = explode(';', isset($params['fixedperiods']) ? $params['fixedperiods'] : '');
	foreach ($fixedperiods as $fixedperiod) {
		if (!empty($fixedperiod)) {
			$fixedperiod = explode(':', $fixedperiod);
			$name = $fixedperiod[0];
			$value = (!empty($fixedperiod[1]) ? $fixedperiod[1] : $fixedperiod[0]);
			$fixedperiodsDDL .= '<option value="' . trim($value) . '">' . trim($name) . '</option>';
		}
	}

	if (empty($fixedperiodsDDL)) {
		$periods = '<input id="memberDuration' . $i . '" name="duration" value="1" />';
	} else {
		$periods = '<select id="memberDuration' . $i . '" name="duration">' . $fixedperiodsDDL . '</select>';
	}

    //force current user to not be used
    $params['currentuser'] = 'n';
	$memberPayment = TikiLib::lib('parser')->parse_data(wikiplugin_memberpayment($data, $params, $offset), array('is_html' => true));
	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
		if (isset($_POST['msg'])) {
			$_POST['msg'] = addslashes(htmlspecialchars($_POST['msg']));
			$headerlib->add_jq_onready("$.notify('" . $_POST['msg'] . "')");
		}

		if (empty($user) ) {
			include_once('lib/smarty_tiki/function.user_registration.php');
			$register = smarty_function_user_registration(array(), $smarty);
		} else {
			$register = "<table>
				<tr class='registerSubmitTr'>
					<td colspan='2'>
						<input type='hidden' id='name' value='$user' />
						<input type='submit' value='" . tr('Submit') . "' class='registerSubmit'/>
					</td>
				</tr>
			</table>";
		}
	}

	$group = $params['group'];

	$headerlib->add_jq_onready(<<<JS
var reg = $('#memberRegister$i'),
    pay = $('#memberPayment$i'),
    user = "$user";

pay.find('.warning').insertAfter(pay); //just in case there are any warnings

var submitBtn = reg.find('input.registerSubmit'),

    //both with and without trackers
    submitBtnTr = reg.find('tr.registerSubmitTr,div.input_submit_container'),
    trackerForm = $('table.wikiplugin_tracker').parent('form');

if (!user) {
    $('<tr class="grpMmbChk">\
        <td>$group Membership:</td>\
        <td>\
            <input type="checkbox" id="memberType$i" />\
        </td>\
    </tr>').insertBefore(submitBtnTr);
} else {
    $('<tr>\
        <td><b>$group Membership</b></td>\
    </tr>').insertBefore(submitBtnTr);
}

$('<tr style="display: none;">\
    <td>$periodslabel</td>\
    <td>$periods</td>\
</tr>')
    .insertBefore(submitBtnTr);

$('#memberType$i')
    .click(function() {
        $('tr.grpMmbChk').next()
            .stop()
            .fadeToggle();
    })
    .click();

reg
    .bind('continueToPurchase', function() {
        pay.find('input[name="wp_member_users"]').val($('#memberRegister$i #name').val());
        pay.find('input[name="wp_member_periods"]').val($('#memberDuration$i').val());
        pay.find('input:last').click();
    })
    .find('input:last').click(function() {
        var frmData = reg.find('form').serialize(),
            invokeRegistration = function() {
                if (frmData) {
                    $.getJSON($.service('user', 'register') + '&' + frmData + '&noTemplate', function(data) {
                        if (typeof data.result == "string") {
                            if ($('#memberType$i').is(':checked')) {
                                $('<input name="msg" />')
                                    .val(data.result)
                                    .prependTo(pay.find('form'));

                                reg.trigger('continueToPurchase');
                            } else { //registered
                                $.notify(data.result);
                                $.notify(tr('You will be redirected in 5 seconds'));
                                setTimeout(function() {
                                    document.location = 'tiki-index.php';
                                }, 5000);
                            }
                        } else { //errors
                            $.each(data.result, function(i) {
                                $.notify(data.result[i].msg);
                            });
                        }
                    });
                } else {
                    reg.trigger('continueToPurchase');
                }
            };

        //this is from a tracker, lets go ahead and submit the tracker data, then we will submit the other
        if (trackerForm.length > 0) {
            $.post(trackerForm.attr('action'), trackerForm.serialize(), function(data) {
                console.log(data);
                invokeRegistration();
            });
        } else {
            invokeRegistration();
        }

        return false;
    });
JS
);

	$paymentStyle = '';
	$registerStyle = '';
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$registerStyle = 'display: none;';
	} else {
		$paymentStyle = 'display: none;';
	}

	return "~np~
		<div id='memberPayment$i' style='$paymentStyle'>$memberPayment</div>
		<div id='memberRegister$i' style='$registerStyle'>$register</div>
	~/np~";
}
