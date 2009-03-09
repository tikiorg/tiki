<?php
/* Invoke a social network sharing tool.
// Developed by Scot E. Wilcoxon for Tiki CMS
// Based on SHARETHIS by Andrew Hafferman
// Usage:
// {SHARE(<options="values">)}{SHARE}
//
// 2008-11-28 SEWilco
//   Initial version.
*/
function wikiplugin_share_help() {
	return tra("Display a social networking tool.  Requires ADDTHIS, SHARETHIS, or SUBMIT plugin.").":<br />~np~{SHARE(option=>'ADDTHIS'|'SHARETHIS'|'SUBMIT')}{SHARE}~/np~";
}
function wikiplugin_share_info() {
	return array(
		'name' => tra('share'),
		'description' => tra("Insert a ShareThis Button from www.sharethis.com"),
		'prefs' => array( 'wikiplugin_share' ),
		'params' => array(
			'option' => array(
				'required' => false,
				'name' => tra('option'),
				'description' => 'ADDTHIS|SHARE|SUBMIT'
		),
						  )
	 );
}
function wikiplugin_share($data, $params) {
  global $tikilib;

	// Define default parameters here.
  //   $share_option = "ADDTHIS";
  //   $share_option = "SHARETHIS";
  //   $share_option = "SUBMIT";

	extract ($params,EXTR_PREFIX_ALL,'share');

	$sharecode = "~hc~ SHARE BEGIN (".$share_option.") ~/hc~";

  switch ($share_option) {
    case 'ADDTHIS':
    case "'ADDTHIS'":
      if($tikilib->plugin_enabled("addthis",true))
      {
        $sharecode .= wikiplugin_addthis($data, $params);
      } else {
        $sharecode .= "~hc~ SHARE tried to use missing ADDTHIS plugin; trying SUBMIT. ~/hc~";
        if($tikilib->plugin_enabled("submit",true))
        {
          $sharecode .= wikiplugin_submit($data, $params);
        }
      }
      break;
    case 'SHARETHIS':
    case "'SHARETHIS'":
      if($tikilib->plugin_enabled("sharethis",true))
      {
        if(function_exists("wikiplugin_sharethis"))
        {
          $sharecode .= wikiplugin_sharethis($data, $params);
        }
      } else {
        $sharecode .= "~hc~ SHARE tried to use missing SHARETHIS plugin; trying SUBMIT. ~/hc~";
        if($tikilib->plugin_enabled("submit",true))
        {
          if(function_exists("wikiplugin_submit"))
          {
            $sharecode .= wikiplugin_submit($data, $params);
          }
        }
      }
      break;
    case 'SUBMIT':
    case "'SUBMIT'":
      if($tikilib->plugin_enabled("submit",true))
      {
        $sharecode .= wikiplugin_submit($data, $params);
      } else {
        $sharecode .= "~hc~ SHARE tried to use missing SUBMIT plugin. ~/hc~";
      }
      break;
    default:
      $sharecode .= "~hc~ SHARE requires option; trying SUBMIT plugin. ~/hc~";
      if($tikilib->plugin_enabled("submit",true))
      {
        $sharecode .= wikiplugin_submit($data, $params);
      }
      break;
  }

	$sharecode .= "~hc~ SHARE END ~/hc~";

  $result = $sharecode;

  return $result;

}

?>
