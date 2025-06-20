console.log(typeof jvm !== 'undefined' ? "jvm tersedia" : "jvm TIDAK tersedia");
console.log(typeof jvm.WorldMap !== 'undefined' ? "Peta world_mill tersedia" : "Peta world_mill TIDAK tersedia");
const socket = io("http://10.20.100.172:3330");

var attackLocations = [
    { from: [37.7749, -122.4194], to: [-6.2000, 106.8167] }, 
    { from: [51.5074, -0.1278], to: [-7.2504, 112.7688] }, 
    { from: [35.6895, 139.6917], to: [-6.9147, 107.6098] }, 
    { from: [55.7558, 37.6173], to: [-0.7893, 113.9213] }, 
    { from: [40.7128, -74.0060], to: [1.4556, 124.8442] }, 
    { from: [48.8566, 2.3522], to: [-8.4095, 115.1889] }, 
    { from: [52.5200, 13.4050], to: [3.5952, 98.6722] }, 
    { from: [28.6139, 77.2090], to: [-3.9907, 122.5120] },
    { from: [1.3521, 103.8198], to: [-0.5021, 117.1537] }, 
    { from: [19.0760, 72.8777], to: [-3.3194, 114.5908] },
    { from: [41.9028, 12.4964], to: [-5.1477, 119.4327] }, 
    { from: [34.0522, -118.2437], to: [-2.9761, 104.7754] },
    { from: [39.9042, 116.4074], to: [-7.7972, 110.3688] },
    { from: [-33.8688, 151.2093], to: [0.9131, 104.4783] }, 
    { from: [55.7558, 37.6173], to: [-7.0051, 110.4381] }, 
    { from: [35.6762, 139.6503], to: [2.0833, 102.0833] }, 
    { from: [40.4168, -3.7038], to: [0.7893, 127.3967] }, 
    { from: [25.276987, 55.296249], to: [-2.1900, 113.9145] },
    { from: [-22.9068, -43.1729], to: [1.0646, 104.0305] }, 
    { from: [30.0444, 31.2357], to: [3.3273, 117.5785] },
];


var activeMarkers = [];

function generateRandomAttack() {
    var mapObject = $('#jvectorMap').vectorMap('get', 'mapObject');

    if (!mapObject) {
        console.error("Map not ready yet.");
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
        console.error("Map not ready yet.");
        return;
    }

    var start = mapObject.latLngToPoint ? mapObject.latLngToPoint(from[0], from[1]) : null;
    var end = mapObject.latLngToPoint ? mapObject.latLngToPoint(to[0], to[1]) : null;

    if (!start || !end) {
        console.error("Coordinate unvalid.");
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

        // Jalankan animasi serangan setiap 2 detik
        setInterval(generateRandomAttack, 1000);
    }
};

/* Controller */
$(document).ready(function () {
    handleRenderVectorMap();
});
