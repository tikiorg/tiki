<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*************************************************************************/
#  Mailbox 0.9.2a   by Sivaprasad R.L (http://netlogger.net)             #
#  eMailBox 0.9.3   by Don Grabowski  (http://ecomjunk.com)              #
#          --  A pop3 client addon for phpnuked websites --              #
#                                                                        #
# This program is distributed in the hope that it will be useful,        #
# but WITHOUT ANY WARRANTY; without even the implied warranty of         #
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          #
# GNU General Public License for more details.                           #
#                                                                        #
# You should have received a copy of the GNU General Public License      #
# along with this program; if not, write to the Free Software            #
# Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.              #
#                                                                        #
#             Copyright (C) by Sivaprasad R.L                            #
#            Script completed by Ecomjunk.com 2001                       #
/*************************************************************************/

// Class Made By Mukul Sabharwal [mukulsabharwal@yahoo.com]
// http://www.devhome.net/php/
// On October 21, 2000
class rc4crypt
{
	function endecrypt($pwd, $data, $case) {
		if ($case == 'de') {
			$data = urldecode($data);
		}

		$key[] = "";
		$box[] = "";
		$temp_swap = "";
		$pwd_length = 0;
		$pwd_length = strlen($pwd);

		for ($i = 0; $i < 255; $i++) {
			$key[$i] = ord(substr($pwd, ($i % $pwd_length) + 1, 1));

			$box[$i] = $i;
		}

		$x = 0;

		for ($i = 0; $i < 255; $i++) {
			$x = ($x + $box[$i] + $key[$i]) % 256;

			$temp_swap = $box[$i];
			$box[$i] = $box[$x];
			$box[$x] = $temp_swap;
		}

		$temp = "";
		$k = "";
		$cipherby = "";
		$cipher = "";
		$a = 0;
		$j = 0;

		for ($i = 0, $istrlen_data = strlen($data); $i < $istrlen_data; $i++) {
			$a = ($a + 1) % 256;

			$j = ($j + $box[$a]) % 256;
			$temp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $temp;
			$k = $box[(($box[$a] + $box[$j]) % 256)];
			$cipherby = ord(substr($data, $i, 1)) ^ $k;
			$cipher .= chr($cipherby);
		}

		if ($case == 'de') {
			$cipher = urldecode(urlencode($cipher));
		} else {
			$cipher = urlencode($cipher);
		}

		return $cipher;
	}
}
