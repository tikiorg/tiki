// $Header: /cvsroot/tikiwiki/tiki/lib/chat/im.js,v 1.1 2006-02-12 22:19:33 lfagundes Exp $

// Instant messaging communication layer

var im_cp;
var im_ui;
im_init();

function im_init() {
    im_cp = new cpaint();
    im_cp.set_use_cpaint_api(true);

    //cp.set_debug(2);

    im_cp.set_persistent_connection(true);

    im_ui = new IM_UI(document.getElementById('im'));

    setInterval('im_getMsgs()',1000);
    setInterval('im_getFriends()',2000);
}

function im_getMsgs() {
    im_cp.call('tiki-im_ajax.php', 'list_new_messages', im_receiveMsgs);
}

function im_receiveMsgs(result) {
    var messages = ajaxExtractArray(result, 'message', new Array('messageId','poster','message'));
    if (messages.length > 0) {
	alert(messages[0]['message']);
    }
}

function im_getFriends() {
    im_cp.call('tiki-im_ajax.php', 'list_online_friends', im_receiveFriends);
}

function im_receiveFriends(result) {
    var friends = ajaxExtractArray(result, 'friend', new Array('login'));

    im_ui.setFriends(friends);    
}


