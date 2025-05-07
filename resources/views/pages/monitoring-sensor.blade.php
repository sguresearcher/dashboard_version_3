@extends('master')
@section('content')

<h5 class="text-center mt-4">Monitoring Sensor</h5>
<hr>

<div class="row" id="sensor-container">

</div>

@endsection

@push('js')
<script>
    function loadSensorStatus() {
        fetch('/data/tenant/monitor')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('sensor-container');
                container.innerHTML = '';

                data.forEach(sensor => {
                    const card = `
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="text-center">${sensor.name}</h5>
                                </div>
                                <div class="card-body ${sensor.status === 'ACTIVE' ? 'bg-success text-white' : 'bg-danger text-white'}">
                                    <h5 class="text-center">Status</h5>
                                    <h4 class="text-center">${sensor.status}</h4>
                                    ${sensor.timestamp ? `<p class="text-center mt-2 mb-0">Last Seen:</p>
                                    <p class="text-center">${sensor.timestamp}</p>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                    container.innerHTML += card;
                });
            });
    }

    loadSensorStatus();
    setInterval(loadSensorStatus, 30000);
</script>
@endpush
