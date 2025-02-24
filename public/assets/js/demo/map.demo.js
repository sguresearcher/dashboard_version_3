console.log(typeof jvm !== 'undefined' ? "jvm tersedia" : "jvm TIDAK tersedia");
console.log(typeof jvm.WorldMap !== 'undefined' ? "Peta world_mill tersedia" : "Peta world_mill TIDAK tersedia");

var attackLocations = [
    { from: [37.7749, -122.4194], to: [40.7128, -74.0060] }, // San Francisco → New York
    { from: [51.5074, -0.1278], to: [48.8566, 2.3522] }, // London → Paris
    { from: [35.6895, 139.6917], to: [37.7749, -122.4194] }, // Tokyo → San Francisco
    { from: [55.7558, 37.6173], to: [39.9042, 116.4074] }, // Moscow → Beijing
    { from: [40.7128, -74.0060], to: [34.0522, -118.2437] }, // New York → Los Angeles
    { from: [48.8566, 2.3522], to: [35.6895, 139.6917] }, // Paris → Tokyo
    { from: [52.5200, 13.4050], to: [40.7128, -74.0060] }, // Berlin → New York
    { from: [28.6139, 77.2090], to: [37.7749, -122.4194] }, // Delhi → San Francisco
    { from: [1.3521, 103.8198], to: [35.6895, 139.6917] }, // Singapore → Tokyo
    { from: [19.0760, 72.8777], to: [48.8566, 2.3522] }, // Mumbai → Paris
    { from: [41.9028, 12.4964], to: [52.5200, 13.4050] }, // Rome → Berlin
    { from: [34.0522, -118.2437], to: [51.5074, -0.1278] }, // Los Angeles → London
    { from: [39.9042, 116.4074], to: [40.7128, -74.0060] }, // Beijing → New York
    { from: [-33.8688, 151.2093], to: [35.6895, 139.6917] }, // Sydney → Tokyo
    { from: [55.7558, 37.6173], to: [34.0522, -118.2437] }, // Moscow → Los Angeles
    { from: [35.6762, 139.6503], to: [19.0760, 72.8777] }, // Tokyo → Mumbai
    { from: [40.4168, -3.7038], to: [37.7749, -122.4194] }, // Madrid → San Francisco
    { from: [25.276987, 55.296249], to: [35.6895, 139.6917] }, // Dubai → Tokyo
    { from: [-22.9068, -43.1729], to: [48.8566, 2.3522] }, // Rio de Janeiro → Paris
    { from: [30.0444, 31.2357], to: [51.5074, -0.1278] }, // Cairo → London
];

var activeMarkers = [];

function generateRandomAttack() {
    var mapObject = $('#jvectorMap').vectorMap('get', 'mapObject');

    if (!mapObject) {
        console.error("Peta belum tersedia. Tidak bisa menambahkan marker.");
        return;
    }

    var attack = attackLocations[Math.floor(Math.random() * attackLocations.length)];
    var from = attack.from;
    var to = attack.to;

    activeMarkers.push({ latLng: from, name: "Attack Source", style: { initial: { fill: 'red' } } });
    activeMarkers.push({ latLng: to, name: "Attack Target", style: { initial: { fill: 'blue' } } });

    mapObject.addMarkers(activeMarkers, { animation: true });

    drawAttack(from, to);

    setTimeout(function () {
        activeMarkers = [];
        mapObject.removeAllMarkers();
    }, 3000);
}

function drawAttack(from, to) {
    var mapObject = $('#jvectorMap').vectorMap('get', 'mapObject');

    if (!mapObject) {
        console.error("Peta belum dimuat.");
        return;
    }

    var start = mapObject.latLngToPoint ? mapObject.latLngToPoint(from[0], from[1]) : null;
    var end = mapObject.latLngToPoint ? mapObject.latLngToPoint(to[0], to[1]) : null;

    if (!start || !end) {
        console.error("Koordinat tidak valid atau fungsi tidak tersedia.");
        return;
    }

    var svg = mapObject.container.find('svg').get(0);
    if (!svg) {
        console.error("Elemen SVG tidak ditemukan.");
        return;
    }

    var path = document.createElementNS("http://www.w3.org/2000/svg", "path");
    var bezierPath = `M${start.x},${start.y} Q${(start.x + end.x) / 2},${(start.y + end.y) / 2 - 50} ${end.x},${end.y}`;

    path.setAttribute("d", bezierPath);
    path.setAttribute("stroke", "#ff00ff"); // Warna pink neon
    path.setAttribute("stroke-width", "2");
    path.setAttribute("fill", "none");
    path.setAttribute("stroke-linecap", "round");

    // Efek glow pink neon
    path.style.filter = "drop-shadow(0px 0px 5px #ff00ff)";

    // **Animasi Garis**
    var length = path.getTotalLength();
    path.setAttribute("stroke-dasharray", length);
    path.setAttribute("stroke-dashoffset", length);

    svg.appendChild(path);

    // Animasi menggunakan JS
    setTimeout(() => {
        path.style.transition = "stroke-dashoffset 2s linear";
        path.style.strokeDashoffset = "0";
    }, 100);

    // Hapus garis setelah beberapa detik
    setTimeout(function () {
        path.remove();
    }, 4000);
}

// Render Map dan Mulai Animasi Serangan
var handleRenderVectorMap = function () {
    if ($('#jvectorMap').length !== 0) {
        $('#jvectorMap').vectorMap({
            map: 'world_mill',
            normalizeFunction: 'polynomial',
            hoverOpacity: 0.5,
            hoverColor: false,
            zoomOnScroll: false,
            backgroundColor: 'transparent',
            regionStyle: {
                initial: {
                    fill: '#e3e3e3',
                    stroke: 'none',
                    "stroke-width": 0.4,
                    "stroke-opacity": 1
                },
                hover: {
                    "fill-opacity": 0.5
                }
            },
            markerStyle: {
                initial: {
                    fill: 'red',
                    stroke: 'white',
                    "stroke-width": 2,
                    r: 5
                },
                hover: {
                    fill: 'yellow',
                    stroke: 'black'
                }
            },
            series: {
                regions: [{
                    values: {},
                    scale: ['#C8EEFF', '#0071A4'],
                    normalizeFunction: 'polynomial'
                }]
            }
        });

        // Jalankan animasi serangan setiap 3 detik
        setInterval(generateRandomAttack, 3000);
    }
};

/* Controller */
$(document).ready(function () {
    handleRenderVectorMap();
});
