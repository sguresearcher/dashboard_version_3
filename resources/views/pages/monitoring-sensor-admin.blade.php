@extends('master')
@section('content')

<h5 class="text-center mt-4">Monitoring Sensor Tenant</h5>
<hr>

<div id="sensor-container" class="row g-3"></div>

@endsection

@push('js')
<script>
const SENSOR_LIST = ["conpot", "cowrie", "dionaea", "dionaea_ews", "elasticpot", "honeytrap", "rdpy"];
const KNOWN_TENANTS = [
    "ewsdb_hp_atmajaya_1", "ewsdb_hp_upnvj_1", "ewsdb_hp_uii_1",
    "ewsdb_hp_its_1", "ewsdb_hp_unnes_1", "ewsdb_hp_uny_1",
    "ewsdb_hp_ugm_1", "ewsdb_hp_usk_1", "ewsdb_hp_pssn_1",
    "ewsdb_hp_telkom_1", "ewsdb_hp_sgu_1", "ewsdb_hp_instiki_1",
    "ewsdb_hp_iibd_1"
];

let sensorStatusMap = {};
KNOWN_TENANTS.forEach(tenant => {
    sensorStatusMap[tenant] = {};
    SENSOR_LIST.forEach(sensor => {
        sensorStatusMap[tenant][sensor] = null;
    });
});

function mapTenantName(rawTenant) {
    const parts = rawTenant.split('_');
    if (parts.length < 2) return rawTenant;
    return `ewsdb_hp_${parts[1]}_1`;
}

function parseTimestamp(ts) {
    if (!ts) return null;

    try {
        if (typeof ts === "string") {
            if (ts.endsWith('Z')) return new Date(ts);
            if (ts.includes('T')) return new Date(ts + 'Z');
            if (ts.includes(' ')) return new Date(ts.replace(' ', 'T') + 'Z');
        } else if (typeof ts === "number") {
            return new Date(ts);
        }
        return new Date(ts);
    } catch (e) {
        console.warn("Gagal parsing timestamp:", ts);
        return null;
    }
}

function renderSensorStatus() {
    const container = document.getElementById('sensor-container');
    container.innerHTML = '';
    const now = new Date();

    Object.entries(sensorStatusMap).forEach(([tenant, sensors]) => {
        let activeCount = 0;
        const sensorsHTML = SENSOR_LIST.map(sensorName => {
            const timestamp = sensors[sensorName];
            let status = "INACTIVE";
            let formatted = "-";

            if (timestamp) {
                const tsDate = parseTimestamp(timestamp);
                if (tsDate && !isNaN(tsDate)) {
                    const diffMin = (now - tsDate) / 60000;
                    if (diffMin <= 1440) {
                        status = "ACTIVE";
                        activeCount++;
                    }
                    formatted = tsDate.toLocaleString('en-GB');
                }
            }

            const badge = status === "ACTIVE"
                ? `<span class="badge bg-success">ACTIVE</span>`
                : `<span class="badge bg-danger">INACTIVE</span>`;

            return `
                <tr>
                    <td>${sensorName}</td>
                    <td>${badge}</td>
                    <td>${formatted}</td>
                </tr>
            `;
        }).join('');

        const cardColor = activeCount === 0 ? 'bg-danger' : 'bg-primary';

        container.innerHTML += `
            <div class="col-md-6 col-lg-4">
                <div class="card shadow border-0">
                    <div class="card-header ${cardColor} text-white">
                        <strong>${tenant}</strong><br>
                        <small class="text-white-50">Active: ${activeCount} / ${SENSOR_LIST.length}</small>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Sensor</th>
                                    <th>Status</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${sensorsHTML}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    });
}

async function fetchLatestTimestamps() {
    try {
        const response = await fetch("/latest-timestamps");
        const json = await response.json();
        const latestData = json.data || {};

        Object.entries(latestData).forEach(([key, entry]) => {
            const tenantRaw = entry.user || entry.tenant;
            const tenant = mapTenantName(tenantRaw);
            const sensor = entry.sensor;
            const timestamp = entry.timestamp?.$date || entry.timestamp;

            console.log("ENTRY DEBUG:", { tenant, sensor, timestamp });

            if (KNOWN_TENANTS.includes(tenant) && SENSOR_LIST.includes(sensor)) {
                sensorStatusMap[tenant][sensor] = timestamp;
            } else {
                console.warn("🚫 Tidak cocok:", { tenant, sensor });
            }
        });

        renderSensorStatus();
    } catch (err) {
        console.error("Gagal ambil data /latest-timestamps:", err);
    }
}

//const socket = io("http://10.20.100.172:3330");
const socket = io("wss://public2.cscisac.org", {
    path: "/socket.io/",
    transports: ["websocket"],
});
socket.on("connect", () => {
    console.log("WebSocket connected");
});

socket.on("disconnect", () => {
    console.warn("WebSocket disconnected");
});

socket.on("new_log", (msg) => {
    const rawTenant = msg.user || msg.tenant || "unknown_tenant";
    const tenant = mapTenantName(rawTenant);
    const sensor = msg.sensor || "unknown_sensor";
    const data = msg.data || {};
    const timestamp = data.timestamp;

    console.log("ENTRY DEBUG:", { tenant, sensor, timestamp });

    if (!timestamp) return;
    if (!KNOWN_TENANTS.includes(tenant)) return;
    if (!SENSOR_LIST.includes(sensor)) return;

    sensorStatusMap[tenant][sensor] = timestamp;
    renderSensorStatus();
});

setInterval(fetchLatestTimestamps, 60000);
fetchLatestTimestamps();
</script>
@endpush
