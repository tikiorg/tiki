<?php
/* small file just for testing mail.php */

include('../lib/webmail/mail.php');

$mail = new TikiMail('imap', 'localhost', 143, 'user', 'pass', 'INBOX', 'novalidate-cert');

$mail->connect();

$boxes = $mail->mailboxes_list();
echo implode("<br />\n", $boxes);

echo "<br> new messages: ".$mail->mailbox_check()."<br>";

echo "<br> All folders new msg's <br>";
$boxes_new = $mail->mailboxes_check();
reset($boxes_new);
foreach($boxes_new as $box) {
	echo "Box: ".$box["mailbox"]." new: ".$box["unseen"]."<br>";
}

$mail->mailbox_create("testing");
$mail->mailbox_create("second_testing");
$boxes = $mail->mailboxes_list();
echo implode("<br />\n", $boxes);

$mail->mailbox_rename("second_testing", "third");
$mail->mailbox_delete("testing");
$boxes = $mail->mailboxes_list();
echo implode("<br />\n", $boxes);

$mail->mailbox_delete("third");
$boxes = $mail->mailboxes_list();
echo implode("<br />\n", $boxes);

?>
