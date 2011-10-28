
(function () {
	var mapNumber = 0;

	function getBaseLayers()
	{
		var layers = [], tiles = jqueryTiki.mapTileSets, factories = {
			openstreetmap: function () {
				return new OpenLayers.Layer.OSM();
			},
			openaerialmap: function () {
				return new OpenLayers.Layer.XYZ(
					"OpenAerialMap",
					"http://tile.openaerialmap.org/tiles/1.0.0/openaerialmap-900913/${z}/${x}/${y}.png",
					{sphericalMercator: true}
				);
			},
			google_street: function () {
				return new OpenLayers.Layer.Google(
					"Google Streets",
					{}
				);
			},
			google_satellite: function () {
				return new OpenLayers.Layer.Google(
					"Google Satellite",
					{type: google.maps.MapTypeId.SATELLITE}
				);
			},
			google_hybrid: function () {
				return new OpenLayers.Layer.Google(
					"Google Hybrid",
					{type: google.maps.MapTypeId.HYBRID}
				);
			},
			google_physical: function () {
				return new OpenLayers.Layer.Google(
					"Google Physical",
					{type: google.maps.MapTypeId.TERRAIN}
				);
			/* Needs additional testing
			},
			visualearth_road: function () {
				return new OpenLayers.Layer.VirtualEarth(
					"Virtual Earth Roads",
					{'type': VEMapStyle.Road}
				);
			},
			yahoo_street: function () {
				return new OpenLayers.Layer.Yahoo(
					"Yahoo Street",
					{}
				);
			},
			yahoo_satellite: function () {
				return new OpenLayers.Layer.Yahoo(
					"Yahoo Satellite",
					{'type': YAHOO_MAP_SAT}
				);
			},
			yahoo_hybrid: function () {
				return new OpenLayers.Layer.Yahoo(
					"Yahoo Hybrid",
					{'type': YAHOO_MAP_HYB}
				);
			*/
			}
		};

		if (tiles.length === 0) {
			tiles.push('openstreetmap');
		}

		$.each(tiles, function (k, name) {
			var f = factories[name];

			if (f) {
				layers.push(f());
			}
		});

		return layers;
	}

	$.fn.createMap = function () {
		this.each(function () {
			var id = $(this).attr('id'), container = this;
			var setupHeight = function () {
				var height = $(container).height();
				if (0 === height) {
					height = $(container).width() / 4.0 * 3.0;
				}

				$(container).closest('.height-size').each(function () {
					height = $(this).data('available-height');
					$(this).css('padding', 0);
					$(this).css('margin', 0);
				});

				$(container).height(height);
			};
			setupHeight();

			$(window).resize(setupHeight);

			if (! id) {
				++mapNumber;
				id = 'openlayers' + mapNumber;
				$(this).attr('id', id);
			}

			setTimeout(function () {
				OpenLayers.ImgPath = "img/openlayers/dark/";
				var map = container.map = new OpenLayers.Map(id);
				var layers = getBaseLayers();
				container.layer = layers[0];
				container.markers = new OpenLayers.Layer.Markers("Markers");
				container.uniqueMarkers = {};
				map.addLayers(layers);
				map.addLayer(container.markers);
				map.zoomToMaxExtent();

				if (layers.length > 1) {
					map.addControl(new OpenLayers.Control.LayerSwitcher());
				}

				container.markerIcons = {
					"default": new OpenLayers.Icon('http://www.openlayers.org/dev/img/marker.png', new OpenLayers.Size(21,25), new OpenLayers.Pixel(-10, -25)),
					"selection": new OpenLayers.Icon('http://www.openlayers.org/dev/img/marker-gold.png', new OpenLayers.Size(21,25), new OpenLayers.Pixel(-10, -25))
				};

				// add or alter icons for map markers
				if ($(container).data('icon-name') && $(container).data('icon-src')) {
					var iconSize = $(container).data('icon-size');
					if (!iconSize) {
						iconSize = [25,25];
					}
					var iconOffset = $(container).data('icon-offset');
					if (!iconOffset) {
						iconOffset = [-(iconSize[0]/2), -iconSize[1]];
					}
					container.markerIcons[$(container).data('icon-name')] = new OpenLayers.Icon(
							$(container).data('icon-src'),
							new OpenLayers.Size(iconSize[0],iconSize[1]),
							new OpenLayers.Pixel(iconOffset[0], iconOffset[1])
					)
				}

				if (navigator.geolocation && navigator.geolocation.getCurrentPosition) {
					$(container).after($('<a/>')
						.css('display', 'block')
						.attr('href', '')
						.click(function () {
							navigator.geolocation.getCurrentPosition(function (position) {
								var lonlat = new OpenLayers.LonLat(position.coords.longitude, position.coords.latitude).transform(
									new OpenLayers.Projection("EPSG:4326"),
									map.getProjectionObject()
								);

								map.setCenter(lonlat);
								map.zoomToScale(position.coords.accuracy * OpenLayers.INCHES_PER_UNIT.m);

								$(container).addMapMarker({
									lat: position.coords.latitude,
									lon: position.coords.longitude,
									unique: 'selection'
								});
							});
							return false;
						})
						.text(tr('To My Location')));
				}

				$(container).after($('<a/>')
					.css('display', 'block')
					.attr('href', '')
					.click(function () {
						var address = prompt(tr('What address are you looking for?'));

						if (address) {
							$.getJSON('tiki-ajax_services.php', {geocode: address}, function (data) {
								var lonlat = new OpenLayers.LonLat(data.lon, data.lat).transform(
									new OpenLayers.Projection("EPSG:4326"),
									map.getProjectionObject()
								);

								map.setCenter(lonlat);
								map.zoomToScale(data.accuracy * OpenLayers.INCHES_PER_UNIT.m);

								$(container).addMapMarker({
									lat: data.lat,
									lon: data.lon,
									unique: 'selection'
								});
							});
						}
						return false;
					})
					.text(tr('Search Location')));

				var field = $(container).data('target-field');
				var central = null;

				if (field) {
					field = $($(container).closest('form')[0][field]);
					if (! field.attr('disabled')) {
						$(container).bind('selectionChange', function (e, lonlat) {
							field.val(lonlat.lon + ',' + lonlat.lat + ',' + map.getZoom()).change();
						});
						map.events.register('zoomend', map, function (e, lonlat) {
							var coords = field.val().split(","), lon = 0, lat = 0;
							if (coords.length > 1) {
								lon = coords[0];
								lat = coords[1];
							}
							field.val(lon + ',' + lat + ',' + map.getZoom()).change();
						});

						var ClickHandler = OpenLayers.Class(OpenLayers.Control, {                
							defaultHandlerOptions: {
								'single': true,
								'double': false,
								'pixelTolerance': 0,
								'stopSingle': false,
								'stopDouble': false
							},
							initialize: function(options) {
								this.handlerOptions = OpenLayers.Util.extend({}, this.defaultHandlerOptions);
								OpenLayers.Control.prototype.initialize.apply(this, arguments); 
								this.handler = new OpenLayers.Handler.Click(
									this,
									{
										'click': this.trigger
									},
									this.handlerOptions
								);
							}, 
							trigger: function(e) {
								var lonlat = map.getLonLatFromViewPortPx(e.xy).transform(
									map.getProjectionObject(),
									new OpenLayers.Projection("EPSG:4326")
								);
								$(container).addMapMarker({
									lat: lonlat.lat,
									lon: lonlat.lon,
									unique: 'selection'
								});
							}
						});

						var control = new ClickHandler();
						map.addControl(control);
						control.activate();
					}

					var value = field.val();
					var matching = value.match(/^(-?[0-9]*(\.[0-9]+)?),(-?[0-9]*(\.[0-9]+)?)(,(.*))?$/);
					
					if (matching) {
						var lat = parseFloat(matching[3]);
						var lon = parseFloat(matching[1]);
						var zoom = matching[6] ? parseInt(matching[6], 10) : 0;

						central = {lat: lat, lon: lon, zoom: zoom};
					}
				}

				if ($(container).data('marker-filter')) {
					var filter = $(container).data('marker-filter');
					$(filter).each(function () {
						var lat = $(this).data('geo-lat');
						var lon = $(this).data('geo-lon');
						var zoom = $(this).data('geo-zoom');
						var extent = $(this).data('geo-extent');
						var icon = $(this).data('icon-name');
						var content = $(this).clone().data({}).wrap('<span/>').parent().html();

						if (! extent) {
							if ($(this).hasClass('primary') || this.href === document.location.href) {
								central = {lat: lat, lon: lon, zoom: zoom ? zoom : 0};
							} else {
								$(container).addMapMarker({
									lon: lon,
									lat: lat,
									content: content,
									icon: icon ? icon : null
								});
							}
						} else if ($(this).is('img')) {
							var graphic = new OpenLayers.Layer.Image(
								$(this).attr('alt'),
								$(this).attr('src'),
								OpenLayers.Bounds.fromString(extent),
								new OpenLayers.Size($(this).width(), $(this).height())
							);

							graphic.isBaseLayer = false;
							graphic.alwaysInRange = true;
							container.map.addLayer(graphic);
						}
					});
				}

				if (central) {
					var lonlat = new OpenLayers.LonLat(central.lon, central.lat).transform(
						new OpenLayers.Projection("EPSG:4326"),
						map.getProjectionObject()
					);

					map.setCenter(lonlat, central.zoom);
					$(container).addMapMarker({
						lon: central.lon,
						lat: central.lat,
						unique: 'selection'
					});
				}

				if (jqueryTiki.googleStreetView) {
					var streetViewHandler, streetViewLink = $('<a/>')
						.attr('href', '')
						.text(tr('Google StreetView'));

					var StreetViewHandler = OpenLayers.Class(OpenLayers.Control, {
						defaultHandlerOptions: {
							'single': true,
							'double': false,
							'pixelTolerance': 0,
							'stopSingle': false,
							'stopDouble': false
						},
						initialize: function(options) {
							this.handlerOptions = OpenLayers.Util.extend({}, this.defaultHandlerOptions);
							OpenLayers.Control.prototype.initialize.apply(this, arguments); 
							this.handler = new OpenLayers.Handler.Click(
								this,
								{
									'click': this.trigger
								},
								this.handlerOptions
							);
						}, 
						trigger: function(e) {
							var width = 600, height = 500;

							streetViewHandler.deactivate();
							var lonlat = map.getLonLatFromViewPortPx(e.xy).transform(
								map.getProjectionObject(),
								new OpenLayers.Projection("EPSG:4326")
							);

							var canvas = $('<div/>')[0];
							$(canvas)
								.appendTo('body')
								.dialog({
									title: tr('Panorama'),
									width: width,
									height: height + 30,
									modal: true,
									close: function () {
										$(canvas).dialog('destroy');
									},
									buttons: {
										'Get Image URL': function () {
											var pov =  canvas.panorama.getPov();
											var pos =  canvas.panorama.getPosition();
											var base = 'http://maps.googleapis.com/maps/api/streetview?'
												+ 'size=500x400&'
												+ 'location=' + escape(pos.toUrlValue()) + '&'
												+ 'heading=' + escape(pov.heading) + '&'
												+ 'pitch=' + escape(pov.pitch) + '&'
												+ 'sensor=false'
											;
											$(canvas).dialog('close');
											alert(base);
										},
										'Cancel': function () {
											$(canvas).dialog('close');
										}
									}
								});

							canvas.panorama = new google.maps.StreetViewPanorama(canvas, {
								position: new google.maps.LatLng(lonlat.lat, lonlat.lon),
								zoomControl: false,
								scrollwheel: false,
								disableDoubleClickZoom: true
							});
							var timeout = setTimeout(function () {
								$(canvas).dialog('close');
							}, 5000);
							google.maps.event.addListener(canvas.panorama, 'pano_changed', function () {
								if (! canvas.panorama.getPano()) {
									$(canvas).dialog('close');
								}
								clearTimeout(timeout);
							});
						}
					});

					streetViewHandler = new StreetViewHandler();
					map.addControl(streetViewHandler);

					streetViewLink.click(function () {
						streetViewHandler.activate();
						return false;
					});

					$(container).after(streetViewLink);
				}

				$(container).closest('.tab, #appframe').find('form.search-box')
					.unbind('submit')
					.submit(function () {
						$.post('tiki-searchindex.php?filter~geo_located=y', $(this).serialize(), function (data) {
							$.each(data, function (k, i) {
								$(container).addMapMarker({
									coordinates: i.geo_location,
									title: i.title
								});
							});
						}, 'json');
						return false;
					});
			}, 250);
		});

		return this;
	};

	$.fn.addMapMarker = function (options) {
		this.each(function () {
			var container = this,
				lonlat,
				iconModel = container.markerIcons["default"],
				marker;

			if (options.unique && container.markerIcons[options.unique]) {
				iconModel = container.markerIcons[options.unique];
			}

			if (options.icon && container.markerIcons[options.icon]) {
				iconModel = container.markerIcons[options.icon];
			}

			if (options.coordinates) {
				var parts = options.coordinates.split(',');
				if (parts.length >= 2) {
					options.lon = parts[0];
					options.lat = parts[1];
				}
			}
			
			if (options.lat && options.lon) {
				lonlat = new OpenLayers.LonLat(options.lon, options.lat).transform(
					new OpenLayers.Projection("EPSG:4326"),
					container.map.getProjectionObject()
				);
			}

			marker = new OpenLayers.Marker(lonlat, iconModel.clone());
			container.markers.addMarker(marker);
			
			if (options.unique) {
				if (container.uniqueMarkers[options.unique]) {
					container.markers.removeMarker(container.uniqueMarkers[options.unique]);
				}

				container.uniqueMarkers[options.unique] = marker;
				$(container).trigger(options.unique + 'Change', options);
			}

			if (options.content) {
				marker.events.register('click', marker, function () {
					if (container.activePopup) {
						container.map.removePopup(container.activePopup);
					}

					container.activePopup = new OpenLayers.Popup('marker', lonlat, null, options.content);
					container.activePopup.autoSize = true;
					container.map.addPopup(container.activePopup);
				});
			}
		});

		return this;
	}
})();

