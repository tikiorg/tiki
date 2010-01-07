<?php
  /* length: length in pixels of the bar (default 50)
	 value: length in pixels to display at the left in red (if not set all length is display in red)
  */
function smarty_function_quotabar( $params, $smarty ) {
	extract($params, EXTR_SKIP);
	if (empty($length)) {
		$length = 100;
	}
	if (!isset($value)) {
		$value = $length;
	}
	if (empty($value)) {
		$ret = "<img src='img/leftbarlight.gif' alt='&lt;' /><img alt='-' src='img/mainbarlight.gif' height='14' width='$length' /><img src='img/rightbarlight.gif' alt='&gt;' />";
	} elseif ($value >= $length) {
		$ret = "<img src='img/leftbar.gif' alt='&lt;' /><img alt='-' src='img/mainbar.gif' height='14' width='$length' /><img src='img/rightbar.gif' alt='&gt;' />";
	} else {
		$left = $length - $value;
		$ret = "<img src='img/leftbar.gif' alt='&lt;' /><img alt='-' src='img/mainbar.gif' height='14' width='$value' /><img alt='-' src='img/mainbarlight.gif' height='14' width='$left' /><img src='img/rightbarlight.gif' alt='&gt;' />";
	}
	return $ret;
  }
