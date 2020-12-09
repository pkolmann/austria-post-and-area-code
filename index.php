<!DOCTYPE html>
<html lang="de-AT">
<head>
    <title>Ortsvorwahlen Österreich</title>

    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="leaflet/leaflet.css">
    <script src="leaflet/leaflet.js"></script>
    <script src="leaflet/leaflet.ajax.min.js"></script>
    <script src="leaflet/leaflet-hash.js"></script>
    <script src="leaflet/leaflet.CenterCross-v0.0.8.js"></script>


    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        #map {
            width: 100vw;
            height: 100%;
        }

    </style>
</head>
<body>

<div id='map'></div>

<script>
    let minX = Infinity;
    let minY = Infinity;
    let maxX = -Infinity;
    let maxY = -Infinity;

    const osm = L.tileLayer('https://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });

    const basemap = L.tileLayer('https://maps{s}.wien.gv.at/basemap/geolandbasemap/{type}/google3857/{z}/{y}/{x}.{format}', {
        maxZoom: 18,
        attribution: 'Datenquelle: <a href="https://www.basemap.at">basemap.at</a>',
        subdomains: ["", "1", "2", "3", "4"],
        type: 'normal',
        format: 'png',
        bounds: [[46.35877, 8.782379], [49.037872, 17.189532]]
    });

    const basemapOrtho = L.tileLayer('https://maps{s}.wien.gv.at/basemap/{type}/normal/google3857/{z}/{y}/{x}.{format}', {
        maxZoom: 18,
        attribution: 'Datenquelle: <a href="https://www.basemap.at">basemap.at</a>',
        subdomains: ["", "1", "2", "3", "4"],
        type: 'bmaporthofoto30cm',
        format: 'jpeg',
        bounds: [[46.35877, 8.782379], [49.037872, 17.189532]]
    });

    const map = L.map('map', {
        center: [47.5, 13.4],
        zoom: 8,
        layers: [osm]
    });
    map.doubleClickZoom.disable();


    const mapLayers = {
        "Open Street Map": osm,
        "Basemap.at": basemap,
        "Basemap.at Orthofoto": basemapOrtho,
    };

    const hash = new L.Hash(map);
    // const popUpPromises = [];

    function popUp(feature, layer) {
        // popUpPromises.push(new Promise((resolve, reject) => {
            const out = [];
            if (feature.hasOwnProperty('properties')) {
                for (const key in feature.properties) {
                    if (key === 'color') continue;
                    if (key === 'color1') continue;
                    if (key === 'plzcolor') continue;
                    if (key === 'plzcolor1') continue;
                    if (feature.properties.hasOwnProperty(key) && typeof feature.properties[key] == 'object' &&
                        feature.properties[key] !== null
                    ) {
                        let valStr = '';
                        if (Object.keys(feature.properties[key]).length > 1) {
                            valStr += '<ul>';
                            for (const val in feature.properties[key]) {
                                if (feature.properties[key].hasOwnProperty(val)) {
                                    valStr += '<li>';
                                    valStr += val + ': ' + feature.properties[key][val];
                                    valStr += '</li>';
                                }
                            }
                            valStr += '</ul>';
                        } else {
                            for (const val in feature.properties[key]) {
                                if (feature.properties[key].hasOwnProperty(val)) {
                                    valStr += val + ': ' + feature.properties[key][val];
                                }
                            }
                        }
                        if (key === 'iso') {
                            out.push("<b>GemKZ:</b> " + valStr);
                        } else {
                            out.push("<b>" + key + "</b>: " + valStr);
                        }
                    } else {
                        if (key === 'iso') {
                            out.push("<b>GemKZ</b>: " + feature.properties[key]);
                        } else {
                            out.push("<b>" + key + "</b>: " + feature.properties[key]);
                        }
                    }
                }
                layer.bindPopup(out.join("<br />"), {maxHeight: 300});
            }

            if (feature.hasOwnProperty('featureBounds')) {
                if (feature['featureBounds']['minX'] < minX) {
                    minX = feature['featureBounds']['minX'];
                }
                if (feature['featureBounds']['maxX'] > maxX) {
                    maxX = feature['featureBounds']['maxX'];
                }
                if (feature['featureBounds']['minY'] < minY) {
                    minY = feature['featureBounds']['minY'];
                }
                if (feature['featureBounds']['maxY'] > maxY) {
                    maxY = feature['featureBounds']['maxY'];
                }
            }
        // }))
    }

    const vorwahlLayer = new L.GeoJSON.AJAX("data/vorwahlen+plz.json",
        {
            onEachFeature: popUp,
            style: function (feature) {
                if (feature.properties.color === undefined) {
                    return {
                        'fillOpacity': 0,
                        'color': 'rgba(0,0,0,0)'
                    }
                } else {
                    return {
                        'color': feature.properties.color
                    }
                }
            }
        }
    );
    vorwahlLayer.getAttribution = function () {
        return 'RTR-GmbH – data.rtr.at';
    };
    vorwahlLayer.addTo(map);

    const plzLayer = new L.GeoJSON.AJAX("data/vorwahlen+plz.json",
        {
            onEachFeature: popUp,
            style: function (feature) {
                if (feature.properties['plzcolor'] === undefined) {
                    return {
                        'fillOpacity': 0,
                        'color': 'rgba(0,0,0,0)'
                    }
                } else {
                    return {
                        'color': feature.properties['plzcolor']
                    }
                }
            }
        }
    );
    plzLayer.getAttribution = function () {
        return 'RTR-GmbH – data.rtr.at';
    };
    plzLayer.addTo(map);

    // Promise.all(popUpPromises)
    //     .then(response => {
    //         console.log(response)
    //         console.log("popUpPromises: " + minX + "/" + minY + " - " + maxX + "/" + maxY);
    //         if (minX < Infinity && maxX > -Infinity && minY < Infinity && maxY > -Infinity) {
    //             map.fitBounds([
    //                 [maxY, minX],
    //                 [minY, maxX]
    //             ]);
    //         }
    //     }
    // )



    if (minX < Infinity && maxX > -Infinity && minY < Infinity && maxY > -Infinity) {
        map.fitBounds([
            [maxY, minX],
            [minY, maxX]
        ]);
    }

    const gemeindenLayer = new L.GeoJSON.AJAX("data/gemeinden_95_geo.json", {onEachFeature: popUp});
    const bezirkLayer = new L.GeoJSON.AJAX("data/bezirke_995_geo.json");
    const wienBezirkLayer = new L.GeoJSON.AJAX("data/BezirksgrenzenWien.json", {onEachFeature: popUp});

    const overlayLayers = {
        "Vorwahlen": vorwahlLayer,
        "Postleitzahlen": plzLayer,
        "Gemeinden": gemeindenLayer,
        "Bezirke": bezirkLayer,
        "Wiener Bezirke": wienBezirkLayer

    };

    const layerControl = L.control.layers(mapLayers, overlayLayers, {sortLayers: true}).addTo(map);

    function onLocationFound(e) {
        const radius = e.accuracy / 2;

        L.marker(e.latlng).addTo(map)
            .bindPopup("You are within " + radius + " meters from this point").openPopup();

        L.circle(e.latlng, radius).addTo(map);
    }

    function onLocationError(e) {
        alert(e.message);
    }

    map.on('locationfound', onLocationFound);
    map.on('locationerror', onLocationError);

    //	map.locate({setView: true, maxZoom: 16});

    function searchFunction(val) {
        minX = Infinity;
        minY = Infinity;
        maxX = -Infinity;
        maxY = -Infinity;
        const lowCaseVal = val.toLowerCase();

        const promises = [];
        vorwahlLayer.eachLayer(function (layer) {
            promises.push(new Promise(function(resolve, reject) {
                if ('vorwahl' in layer.feature.properties) {
                    let show = false;
                    if ('name' in layer.feature.properties && layer.feature.properties['name'].toLowerCase().startsWith(lowCaseVal)) {
                        show = true;
                    }
                    for (const vorwahl in layer.feature.properties['vorwahl']) {
                        if (vorwahl !== undefined && vorwahl.toString().startsWith(val)) {
                            console.log("Vorwahl " + vorwahl + " found...");
                            show = true;
                            break;
                        }
                        if (
                            layer.feature.properties['vorwahl'].hasOwnProperty(vorwahl) &&
                            layer.feature.properties['vorwahl'][vorwahl].toString(10).toLowerCase().startsWith(lowCaseVal)
                        ) {
                            console.log("Vorwahl " + vorwahl + " for " + val + " found...");
                            show = true;
                            break;
                        }
                    }

                    if (show) {
                        if (val.length === 1) {
                            layer.setStyle({
                                'fillOpacity': 0.6,
                                'color': layer.feature.properties['color1']
                            });
                        } else {
                            layer.setStyle({
                                'fillOpacity': 0.6,
                                'color': layer.feature.properties['color']
                            });
                        }

                        if (layer.feature.hasOwnProperty('featureBounds')) {
                            if (layer.feature.featureBounds['minX'] < minX) {
                                minX = layer.feature.featureBounds['minX'];
                            }
                            if (layer.feature.featureBounds['maxX'] > maxX) {
                                maxX = layer.feature.featureBounds['maxX'];
                            }
                            if (layer.feature.featureBounds['minY'] < minY) {
                                minY = layer.feature.featureBounds['minY'];
                            }
                            if (layer.feature.featureBounds['maxY'] > maxY) {
                                maxY = layer.feature.featureBounds['maxY'];
                            }
                        }

                    } else {
                        layer.setStyle({
                            'fillOpacity': 0,
                            'color': 'rgba(0,0,0,0)'
                        });
                    }
                }
            }))
        });

        plzLayer.eachLayer(function (layer) {
            promises.push(new Promise(function (resolve, reject) {
                if ('plz' in layer.feature.properties) {
                    let show = false;
                    if ('name' in layer.feature.properties && layer.feature.properties['name'].toLowerCase().startsWith(lowCaseVal)) {
                        show = true;
                    }
                    for (const plz in layer.feature.properties['plz']) {
                        if (plz !== undefined && plz.toString(10).startsWith(val)) {
                            console.log("PLZ " + plz + " found...");
                            show = true;
                            break;
                        }
                        if (
                            layer.feature.properties['plz'].hasOwnProperty(plz) &&
                            layer.feature.properties['plz'][plz].toString(10).toLowerCase().startsWith(lowCaseVal)
                        ) {
                            console.log("PLZ " + plz + " for " + val + " found...");
                            show = true;
                            break;
                        }
                    }

                    if (show) {
                        layer.setStyle({
                            'fillOpacity': 0.6,
                            'color': layer.feature.properties['plzcolor']
                        });

                        if (layer.feature.hasOwnProperty('featureBounds')) {
                            if (layer.feature.featureBounds['minX'] < minX) {
                                minX = layer.feature.featureBounds['minX'];
                            }
                            if (layer.feature.featureBounds['maxX'] > maxX) {
                                maxX = layer.feature.featureBounds['maxX'];
                            }
                            if (layer.feature.featureBounds['minY'] < minY) {
                                minY = layer.feature.featureBounds['minY'];
                            }
                            if (layer.feature.featureBounds['maxY'] > maxY) {
                                maxY = layer.feature.featureBounds['maxY'];
                            }
                        }
                    } else {
                        layer.setStyle({
                            'fillOpacity': 0,
                            'color': 'rgba(0,0,0,0)'
                        });
                    }
                }
            }));
        });

        setTimeout(function() {
            console.log("promises.len: " + promises.length)
            if (minX < Infinity && maxX > -Infinity && minY < Infinity && maxY > -Infinity) {
                map.fitBounds([
                    [maxY, minX],
                    [minY, maxX]
                ]);
            }
        }, 500)

        Promise.all(promises)
            .then((response) => {
                console.log("promises: " + minX + "/" + minY + " - " + maxX + "/" + maxY);
                console.log(response)
                if (minX < Infinity && maxX > -Infinity && minY < Infinity && maxY > -Infinity) {
                    map.fitBounds([
                        [maxY, minX],
                        [minY, maxX]
                    ]);
                }
            })
            .catch(error => console.log(`Error in executing ${error}`)) // Promise.all throws an error.
    }

    L.Control.textbox = L.Control.extend({
        onAdd: function (map) {
            const text = L.DomUtil.create('div');
            text.id = 'searchDiv';
            text.innerHTML = '<input id="searchInput" size=10 onkeyup="searchFunction(this.value)"/>';
            return text;
        },

        onRemove: function (map) {
        }
    });

    const textbox = function (opts) {
        return new L.Control.textbox(opts);
    };
    textbox({position: 'topleft'}).addTo(map);

</script>


</body>
</html>

