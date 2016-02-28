<div id="{$trackercalendar.id|escape}"></div>
{jq}
	var data = {{$trackercalendar|json_encode}};
	$('#' + data.id).each(function () {
		var cal = this;
		var storeEvent = function(event) {
			var request = {
				itemId: event.id,
				trackerId: data.trackerId
			}, end = event.end;

			if (! end) {
				end = event.start;
			}

			request['fields~' + data.begin] = event.start.getTime() / 1000;
			request['fields~' + data.end] = end.getTime() / 1000;

			$.post($.service('tracker', 'update_item'), request);
		};

		$(this).fullCalendar({
			timeFormat: {
				'': data.timeFormat
			},
			header: {
				left: 'prevYear,prev,next,nextYear today',
				center: 'title',
				right: data.views
			},
			editable: true,
			timezone: '{{$prefs.server_timezone}}',
			//theme: true, TODO: add support of jQuery UI theme to the plugin's PHP
			events: $.service('tracker_calendar', 'list', {
				trackerId: data.trackerId,
				beginField: data.begin,
				endField: data.end,
				resourceField: data.resource,
				coloringField: data.coloring,
				filters: data.body
			}),
			resources: data.resourceList,
			year: data.viewyear,
			month: data.viewmonth-1,
			day: data.viewday,
			minTime: data.minHourOfDay,
			maxTime: data.maxHourOfDay,
			monthNames: [ "{tr}January{/tr}", "{tr}February{/tr}", "{tr}March{/tr}", "{tr}April{/tr}", "{tr}May{/tr}", "{tr}June{/tr}", "{tr}July{/tr}", "{tr}August{/tr}", "{tr}September{/tr}", "{tr}October{/tr}", "{tr}November{/tr}", "{tr}December{/tr}"], 
			monthNamesShort: [ "{tr}Jan.{/tr}", "{tr}Feb.{/tr}", "{tr}Mar.{/tr}", "{tr}Apr.{/tr}", "{tr}May{/tr}", "{tr}June{/tr}", "{tr}July{/tr}", "{tr}Aug.{/tr}", "{tr}Sep.{/tr}", "{tr}Oct.{/tr}", "{tr}Nov.{/tr}", "{tr}Dec.{/tr}"], 
			dayNames: ["{tr}Sunday{/tr}", "{tr}Monday{/tr}", "{tr}Tuesday{/tr}", "{tr}Wednesday{/tr}", "{tr}Thursday{/tr}", "{tr}Friday{/tr}", "{tr}Saturday{/tr}"],
			dayNamesShort: ["{tr}Sun{/tr}", "{tr}Mon{/tr}", "{tr}Tue{/tr}", "{tr}Wed{/tr}", "{tr}Thu{/tr}", "{tr}Fri{/tr}", "{tr}Sat{/tr}"],
			buttonText: {
				resourceDay:    "{tr}resource day{/tr}",
				resourceMonth:    "{tr}resource month{/tr}",
				resourceWeek:    "{tr}resource week{/tr}",
				today:    "{tr}today{/tr}",
				month:    "{tr}month{/tr}",
				week:     "{tr}week{/tr}",
				day:      "{tr}day{/tr}"
			},
			allDayText: "{tr}all-day{/tr}",
			firstDay: data.firstDayofWeek,
			weekends: data.weekends,
			slotMinutes: {{$prefs.calendar_timespan}},
			defaultView: data.dView,
			eventAfterRender : function( event, element, view ) {
				element.popover({trigger: 'hover focus', title: event.title, content: event.description, html: true, container: 'body'});
			},
			eventClick: function(event) {
				if (data.url) {
					var actualURL = data.url;
					actualURL += actualURL.indexOf("?") === -1 ? "?" : "&";

					if (data.trkitemid === "y" && data.addAllFields === "n") {	// "simple" mode
						actualURL +=  "itemId=" + event.id;
					} else {
						var lOp='';
						var html = $.parseHTML( event.description ) || [];

						// Store useful data values to the URL for Wiki Argument Variable
						// use and to javascript session storage for JQuery use
						actualURL += "trackerid=" + event.trackerId;
						if( data.trkitemid == 'y' ) {
							actualURL = actualURL + "&itemId=" + event.id;
						}
						else {
							actualURL = actualURL + "&itemid=" + event.id;
						}
						actualURL = actualURL + "&title=" + event.title;
						actualURL = actualURL + "&end=" + event.end;
						actualURL = actualURL + "&start=" + event.start;
						if (data.useSessionStorage) {
							sessionStorage.setItem( "trackerid", event.trackerId);
							sessionStorage.setItem( "title", event.title);
							sessionStorage.setItem( "start", event.start);
							sessionStorage.setItem( "itemid", event.id);
							sessionStorage.setItem( "end", event.end);
							sessionStorage.setItem( "eventColor", event.color);
						}

						// Capture the description HTML as variables
						// with the label being the variable name
						$.each( html, function( i, el ) {
							if( isEven( i ) == true ) {
								lOp = el.textContent.replace( ' ', '_' );
							}
							else {
								actualURL = actualURL + "&" + lOp + "=" + el.textContent;
								if (data.useSessionStorage) {
									sessionStorage.setItem( lOp, el.textContent);
								}
							}
						});
					}

					location.href=actualURL;
					return false;
				}
				else if (event.editable && event.trackerId) {
					var info = {
						trackerId: event.trackerId,
						itemId: event.id
					};
					$('<a href="#"/>').attr('href', $.service('tracker', 'update_item', info)).serviceDialog({
						title: event.title,
						success: function () {
							$(cal).fullCalendar('refetchEvents');
						}
					});
					return false;
				} else {
					return true;
				}

			},
			dayClick: function(date, allDay, jsEvent, view) {
				if (data.canInsert) {
					var info = {
						trackerId: data.trackerId
					};
					info[data.beginFieldName] = date.getTime() / 1000;
					info[data.endFieldName] = date.getTime() / 1000 + 3600;
					if (data.url) {
						$('<a href="#"/>').attr('href', data.url);
					}
					else {
						$('<a href="#"/>').attr('href', $.service('tracker', 'insert_item', info)).serviceDialog({
							title: data.addTitle,
							success: function () {
								$(cal).fullCalendar('refetchEvents');
							}
						});
					}
				}

				return false;
			},
			eventResize: storeEvent,
			eventDrop: storeEvent
		});
		$(this).fullCalendar( 'gotoDate', data.viewyear, data.viewmonth-1, data.viewday );
	});

	function isEven(x) { return (x%2)==0; }
{/jq}
