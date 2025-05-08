@extends('master')
@section('content')

<h5 class="text-center mt-4">Monitoring Sensor Tenant</h5>
<hr>

<div id="sensor-container" class="row g-3">

</div>

@endsection

@push('js')
<script>
function loadSensorStatus() {
    fetch('/data/all-tenant/monitor')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('sensor-container');
            container.innerHTML = '';

            data.forEach(tenant => {
                let sensorsHTML = tenant.sensors.map(sensor => {
                    let badge = sensor.status === 'ACTIVE'
                        ? `<span class="badge bg-success">ACTIVE</span>`
                        : `<span class="badge bg-danger">UNACTIVE</span>`;
                    
                    return `
                        <tr>
                            <td>${sensor.name}</td>
                            <td>${badge}</td>
                            <td>${sensor.timestamp ?? '-'}</td>
                        </tr>
                    `;
                }).join('');

                container.innerHTML += `
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow border-0">
                        <div class="card-header bg-primary text-white">
                            <strong>${tenant.tenant}</strong>
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
        })
        .catch(error => {
            console.error("Gagal memuat data sensor:", error);
        });
}

// Load pertama kali & auto refresh setiap 30 detik
loadSensorStatus();
setInterval(loadSensorStatus, 30000);
</script>
@endpush
