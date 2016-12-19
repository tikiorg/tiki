(function(){

    // Listen for the ready event for any vimeo video players on the page
    var vimeoPlayers = document.querySelectorAll('iframe'),
        player;

    for (var i = 0, length = vimeoPlayers.length; i < length; i++) {
        player = vimeoPlayers[i];
        $f(player).addEvent('ready', ready);
    }

    /**
     * Utility function for adding an event. Handles the inconsistencies
     * between the W3C method for adding events (addEventListener) and
     * IE's (attachEvent).
     */
    function addEvent(element, eventName, callback) {
        if (element.addEventListener) {
            element.addEventListener(eventName, callback, false);
        }
        else {
            element.attachEvent(eventName, callback, false);
        }
    }

    /**
     * Called once a vimeo player is loaded and ready to receive
     * commands. You can add events and make api calls only after this
     * function has been called.
     */
    function ready(player_id) {
        // Keep a reference to Froogaloop for this player
        froogaloop = $f(player_id);

        /**
         * Adds listeners for the events that are checked. Adding an event
         * through Froogaloop requires the event name and the callback method
         * that is called once the event fires.
         */
        function setupEventListeners() {

            function onPlayProgress() {
                var inc = 0;
                froogaloop.addEvent('playProgress', function(data, pid) {
                    if(data.percent.toFixed(2) != inc && (data.percent.toFixed(2) == 0.20 || data.percent.toFixed(2) == 0.40 || data.percent.toFixed(2) == 0.60 || data.percent.toFixed(2) == 0.80)) {
                        inc = data.percent.toFixed(2);
                        post_player_info(data, pid, 'play', 'playProgress');
                    }
                });
            }

            function onPlay() {
                froogaloop.addEvent('play', function(data) {
                   calculate(data, 'play', 'play');
                });
            }

            function onPause() {
                froogaloop.addEvent('pause', function(data) {
                    calculate(data, 'pause', 'pause');
                });
            }

            function onFinish() {
                froogaloop.addEvent('finish', function(data) {
                    calculate(data, 'finish', 'finish');
                });
            }

            function onSeek() {
                froogaloop.addEvent('seek', function(data, player_id) {
                    calculate(player_id, 'seek', 'seek', data);
                });
            }


            function calculate(player_id, action, eventname, seekdata) {
                froogaloops = $f(player_id);
                 froogaloops.api('getCurrentTime', function (value) {
                    froogaloops.api('getDuration', function (value1) {
                        var dataplay = new Object();
                        dataplay.seconds = parseFloat(value);
                        temptime = value/value1;
                        dataplay.percent = parseFloat(temptime.toFixed(3));
                        dataplay.duration = parseFloat(value1);
                        if(eventname == 'seek') {
                            dataplay.secondsto = parseFloat(seekdata.seconds);
                            dataplay.percentto = parseFloat(seekdata.percent);
                            dataplay.durationto = parseFloat(seekdata.duration);
                        }
                        post_player_info(dataplay, player_id, action, eventname);
                    });
                });
            }

            //onLoadProgress();
            onPlayProgress();
            onPlay();
            onPause();
            onFinish();
            onSeek();
        }

        function post_player_info(data, cid, action, eventname){
            $.post('tiki-vimeo-track_'+action, {eventname: eventname, playerId: cid,fileId: $("#"+cid).data('fileid'), url: $("#"+cid).attr('src').split("?")[0], time: JSON.stringify(data)});
        }

        setupEventListeners();
    }
})();