<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-mailin.php,v 1.3 2003-09-22 23:25:37 rlpowell Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/mailin/mailinlib.php');

require_once ("lib/webmail/pop3.php");
require_once ("lib/webmail/mimeDecode.php");
include_once ("lib/webmail/class.rc4crypt.php");
include_once ("lib/webmail/htmlMimeMail.php");

function parse_output(&$obj, &$parts, $i) {
	if (!empty($obj->parts)) {
		for ($i = 0; $i < count($obj->parts); $i++)
			parse_output($obj->parts[$i], $parts, $i);
	} else {
		$ctype = $obj->ctype_primary . '/' . $obj->ctype_secondary;

		switch ($ctype) {
		case 'text/plain':
			if (!empty($obj->disposition)AND $obj->disposition == 'attachment') {
				$names = split(';', $obj->headers["content-disposition"]);

				$names = split('=', $names[1]);
				$aux['name'] = $names[1];
				$aux['content-type'] = $obj->headers["content-type"];
				$aux['part'] = $i;
				$parts['attachments'][] = $aux;
			} else {
				$parts['text'][] = $obj->body;
			}

			break;

		case 'text/html':
			if (!empty($obj->disposition)AND $obj->disposition == 'attachment') {
				$names = split(';', $obj->headers["content-disposition"]);

				$names = split('=', $names[1]);
				$aux['name'] = $names[1];
				$aux['content-type'] = $obj->headers["content-type"];
				$aux['part'] = $i;
				$parts['attachments'][] = $aux;
			} else {
				$parts['html'][] = $obj->body;
			}

			break;

		default:
			$names = split(';', $obj->headers["content-disposition"]);

			$names = split('=', $names[1]);
			$aux['name'] = $names[1];
			$aux['content-type'] = $obj->headers["content-type"];
			$aux['part'] = $i;
			$parts['attachments'][] = $aux;
		}
	}
}

// The mailin script is used to get / set wiki pages using an email account

// Get a list of ACTIVE emails accounts configured for mailin procedures
$accs = $mailinlib->list_active_mailin_accounts(0, -1, 'account_desc', '');

print ('<a href="tiki-admin_mailin.php">Admin</a><br/><br/>');

// foreach account
foreach ($accs['data'] as $acc) {
	print ("<b>Processing account</b><br/>");

	print ("Account :" . $acc['account'] . "<br/>");
	print ("Type    :" . $acc['type'] . "<br/>");
	print ("--------------------------<br/>");
	$pop3 = new POP3($acc["pop"], $acc["username"], $acc["pass"]);
	$pop3->Open();
	$s = $pop3->Stats();
	$mailsum = $s["message"];

	for ($i = 1; $i <= $mailsum; $i++) {
		$aux = $pop3->ListMessage($i);

		$aux["msgid"] = $i;
		$aux["realmsgid"] = $pop3->GetMessageID($i);
		$message = $pop3->GetMessage($i);
		print ("Reading a request. From: " . $aux["sender"]["email"] . "Subject: " . $aux["subject"] . "<br/>");

		if (empty($aux["sender"]["name"]))
			$aux["sender"]["name"] = $aux["sender"]["email"];

		// Now determine account type
		if ($acc['type'] == 'wiki-get') {
			// A wiki-get account sends a copy of the page to the sender
			// and also sends the source of the page
			$page = trim($aux['subject']);

			$mail = new htmlMimeMail();
			$mail->setFrom($acc["account"]);
			$mail->setSubject($page);

			if ($tikilib->page_exists($page)) {
				$info = $tikilib->get_page_info($page);

				$data = $tikilib->parse_data($info["data"]);
				// Now we should attach the source here
				$mail->addAttachment($info['data'], 'source.txt', 'plain/txt');
			} else {
				$data = 'Page not found';
			}

			$mail->setSMTPParams($acc["smtp"], $acc["smtpPort"], '', $acc["useAuth"], $acc["username"], $acc["pass"]);
			$mail->setHTML($data, strip_tags($data));
			$res = $mail->send(array($aux["sender"]["email"]), 'mail');
			print ("Response sent<br/>");
		// Send the email
		}

		if ($acc['type'] == 'wiki-put') {
			// This is used to UPDATE wiki pages
			$page = trim($aux['subject']);

			$full = $message["full"];
			$params = array(
				'input' => $full,
				'crlf' => "\r\n",
				'include_bodies' => TRUE,
				'decode_headers' => TRUE,
				'decode_bodies' => TRUE
			);

			$output = Mail_mimeDecode::decode($params);
			parse_output($output, $parts, 0);

			if (isset($parts["text"][0]))
				$body = $parts["text"][0];

			if (isset($body)) {
				if (!$tikilib->page_exists($page)) {
					print ("Page: $page has been created");

					$tikilib->create_page($page,
						0, $body, date('U'), "Created by email from " . $acc["account"], $aux["sender"]["email"], '0.0.0.0', '');
				} else {
					$tikilib->update_page($page, $body, "Created by email from " . $acc["account"], $aux["sender"]["email"],
						'0.0.0.0', '');

					print ("Page: $page has been updated");
				}
			}
		}

		if ($acc['type'] == 'wiki-append') {
			// This is used to UPDATE wiki pages
			$page = trim($aux['subject']);

			$full = $message["full"];
			$params = array(
				'input' => $full,
				'crlf' => "\r\n",
				'include_bodies' => TRUE,
				'decode_headers' => TRUE,
				'decode_bodies' => TRUE
			);

			$output = Mail_mimeDecode::decode($params);
			parse_output($output, $parts, 0);

			if (isset($parts["text"][0]))
				$body = $parts["text"][0];

			if (isset($body)) {
				if (!$tikilib->page_exists($page)) {
					print ("Page: $page has been created");

					$tikilib->create_page($page,
						0, $body, date('U'), "Created by email from " . $acc["account"], $aux["sender"]["email"], '0.0.0.0', '');
				} else {
					$info = $tikilib->get_page_info($page);

					$tikilib->update_page($page, $info['data'] . $body,
						"Created by email from " . $acc["account"], $aux["sender"]["email"], '0.0.0.0', '');
					print ("Page: $page has been updated");
				}
			}
		}

		if ($acc['type'] == 'wiki') {
			// This is used to GET/SET wiki pages depending on the body
			$p_page = trim($aux['subject']);

			$parts = explode(':', $p_page);

			if (!isset($parts[1])) {
				$parts[1] = $parts[0];

				$parts[0] = 'GET';
			}

			$method = $parts[0];
			$page = $parts[1];
			$full = $message["full"];
			$params = array(
				'input' => $full,
				'crlf' => "\r\n",
				'include_bodies' => TRUE,
				'decode_headers' => TRUE,
				'decode_bodies' => TRUE
			);

			$output = Mail_mimeDecode::decode($params);
			parse_output($output, $parts, 0);

			if (isset($parts["text"][0]))
				$body = $parts["text"][0];

			if ($method == 'PUT') {
				if (!$tikilib->page_exists($page)) {
					print ("Page: $page has been created");

					$tikilib->create_page($page,
						0, $body, date('U'), "Created by email from " . $acc["account"], $aux["sender"]["email"], '0.0.0.0', '');
				} else {
					$tikilib->update_page($page, $body, "Created by email from " . $acc["account"], $aux["sender"]["email"],
						'0.0.0.0', '');

					print ("Page: $page has been updated");
				}
			} elseif ($method == 'GET') {
				$mail = new htmlMimeMail();

				$mail->setFrom($acc["account"]);
				$mail->setSubject($page);

				if ($tikilib->page_exists($page)) {
					$info = $tikilib->get_page_info($page);

					$data = $tikilib->parse_data($info["data"]);
					// Now we should attach the source here
					$mail->addAttachment($data, $page . '.html', 'plain/html');

					$mail->setSMTPParams($acc["smtp"], $acc["smtpPort"], '', $acc["useAuth"], $acc["username"], $acc["pass"]);
					$mail->setHTML($info['data'], strip_tags($data));
					$res = $mail->send(array($aux["sender"]["email"]), 'mail');
					print ("Response sent<br/>");
				} else {
					$data = 'Page not found';
				}
			} elseif ($method == 'APPEND') {
				if (!$tikilib->page_exists($page)) {
					print ("Page: $page has been created");

					$tikilib->create_page($page,
						0, $body, date('U'), "Created by email from " . $acc["account"], $aux["sender"]["email"], '0.0.0.0', '');
				} else {
					$info = $tikilib->get_page_info($page);

					$tikilib->update_page($page, $info['data'] . $body,
						"Created by email from " . $acc["account"], $aux["sender"]["email"], '0.0.0.0', '');
					print ("Page: $page has been updated");
				}
			} else {
				$mail = new htmlMimeMail();

				$mail->setFrom($acc["account"]);
				$mail->setSubject(tra('Tiki mail-in instructions'));
				$mail->setSMTPParams($acc["smtp"], $acc["smtpPort"], '', $acc["useAuth"], $acc["username"], $acc["pass"]);
				$mail->setText("Use the subject to indicate the operation to apply:\nGET:WikiName to get a wiki page\nPUT:WikiName to update/create a wiki page (use the body for the page data)\nAPPEND:WikiName to append data to a Wiki page (use the body for the data to add)");
				$res = $mail->send(array($aux["sender"]["email"]), 'mail');
			}
		}

		// Remove the email from the pop3 server      
		$pop3->DeleteMessage($i);
	}

	$pop3->close();
}

?>
