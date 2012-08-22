<div id="{$trackercalendar.id|escape}"></div>
{jq}
	var data = {{$trackercalendar|json_encode}};
	$('#' + data.id).each(function () {
		var cal = this;
		var storeEvent = function(event) {
			var request = {
				itemId: event.id,
				trackerId: data.trackerId
			};
			request['fields~' + data.begin] = event.start.getTime() / 1000;
			request['fields~' + data.end] = event.end.getTime() / 1000;

			$.post($.service('tracker', 'update_item'), request);
		};

		$(this).fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: data.views
			},
			editable: true,
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
			monthNamesShort: [ "{tr}Jan{/tr}", "{tr}Feb{/tr}", "{tr}Mar{/tr}", "{tr}Apr{/tr}", "{tr}May{/tr}", "{tr}Jun{/tr}", "{tr}Jul{/tr}", "{tr}Aug{/tr}", "{tr}Sep{/tr}", "{tr}Oct{/tr}", "{tr}Nov{/tr}", "{tr}Dec{/tr}"], 
			dayNames: ["{tr}Sunday{/tr}", "{tr}Monday{/tr}", "{tr}Tuesday{/tr}", "{tr}Wednesday{/tr}", "{tr}Thursday{/tr}", "{tr}Friday{/tr}", "{tr}Saturday{/tr}"],
			dayNamesShort: ["{tr}Sun{/tr}", "{tr}Mon{/tr}", "{tr}Tue{/tr}", "{tr}Wed{/tr}", "{tr}Thu{/tr}", "{tr}Fri{/tr}", "{tr}Sat{/tr}"],
			buttonText: {
				today:    "{tr}today{/tr}",
				month:    "{tr}month{/tr}",
				week:     "{tr}week{/tr}",
				day:      "{tr}day{/tr}"
			},
			allDayText: "{tr}all-day{/tr}",
			firstDay: data.firstDayofWeek,
			slotMinutes: {{$prefs.calendar_timespan}},
			defaultView: 'month',
			eventAfterRender : function( event, element, view ) {
				element.attr('title',event.title +'|'+event.description);
				element.cluetip({arrows: true, splitTitle: '|', clickThrough: true});
			},
			eventClick: function(event) {
				if (event.editable) {
					var info = {
						trackerId: data.trackerId,
						itemId: event.id
					};
					$('<a href="#"/>').attr('href', $.service('tracker', 'update_item', info)).serviceDialog({
						title: event.title,
						success: function () {
							$(cal).fullCalendar('refetchEvents');
						}
					});
				}

				return false;
			},
			dayClick: function(date, allDay, jsEvent, view) {
				if (data.canInsert) {
					var info = {
						trackerId: data.trackerId,
					};
					info[data.beginFieldName] = date.getTime() / 1000;
					info[data.endFieldName] = date.getTime() / 1000 + 3600;
					$('<a href="#"/>').attr('href', $.service('tracker', 'insert_item', info)).serviceDialog({
						title: data.addTitle,
						success: function () {
							$(cal).fullCalendar('refetchEvents');
						}
					});
				}

				return false;
			},
			eventResize: storeEvent,
			eventDrop: storeEvent
		});
	});
{/jq}
