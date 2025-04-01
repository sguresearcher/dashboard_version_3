@extends('master')
@section('content')
<div class="row">
    <!-- BEGIN col-6 -->
    <div class="col-xl-4">
        <h4>Summary Per Day</h4>
        <!-- BEGIN card -->
        <div class="card mb-3">
            <!-- BEGIN card-body -->
            <div class="card-body">
                <!-- BEGIN title -->
                <div class="d-flex fw-bold small mb-3">
                    <span class="flex-grow-1">Total Attack</span>
                </div>
                <div class="mb-3">
                   <h2 id="totalAttack">Loading....</h2>
                </div>

                <div class="d-flex fw-bold small mb-3">
                    <span class="flex-grow-1">Average per day</span>
                </div>
                <div class="mb-3">
                   <h2>300,000</h2>
                </div>
            </div>
            <!-- END card-body -->
            
            <!-- BEGIN card-arrow -->
            <div class="card-arrow">
                <div class="card-arrow-top-left"></div>
                <div class="card-arrow-top-right"></div>
                <div class="card-arrow-bottom-left"></div>
                <div class="card-arrow-bottom-right"></div>
            </div>
            <!-- END card-arrow -->
        </div>
        <!-- END card -->
    </div>
    <!-- END col-6 -->
    
    <!-- BEGIN col-6 -->
    <div class="col-xl-8">
        <!-- BEGIN card -->
        <div id="jVectorMap" class="mb-5">
            <h4>Threat Map</h4>
            <div class="card">
                <div class="card-body">
                    <div id="jvectorMap" style="height: 300px;"></div>
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>
        </div>
        <!-- END card -->
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-4 mb-md-0">
        <div class="card">
            <div class="card-body">
                <table id="attackSensor" class="table text-nowrap w-100">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Sensor</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="card-arrow">
                <div class="card-arrow-top-left"></div>
                <div class="card-arrow-top-right"></div>
                <div class="card-arrow-bottom-left"></div>
                <div class="card-arrow-bottom-right"></div>
            </div>
            {{-- <div class="hljs-container">
                <pre><code class="xml" data-url="assets/data/table-plugins/code-1.json"></code></pre>
            </div> --}}
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <table id="top10IpAttacker" class="table text-nowrap w-100">
                    <thead>
                            <th>No</th>
                            <th>Source Ip</th>
                            <th>Destination Ip</th>
                            <th>Sensor</th>
                            <th>Port</th>
                            <th>Total</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="card-arrow">
                <div class="card-arrow-top-left"></div>
                <div class="card-arrow-top-right"></div>
                <div class="card-arrow-bottom-left"></div>
                <div class="card-arrow-bottom-right"></div>
            </div>
            {{-- <div class="hljs-container">
                <pre><code class="xml" data-url="assets/data/table-plugins/code-1.json"></code></pre>
            </div> --}}
        </div>
    </div>
</div>
@endsection
@push('js')
<script>
    function fetchAttackData() {
        $.ajax({
            url: '/data/guest/total-attack',
            method: 'GET',
            success: function(response) {
                if (response.total_attack !== undefined) {
                    $('#totalAttack').text(response.total_attack.toLocaleString());
                } else {
                    $('#totalAttack').text('No data');
                }
            },
            error: function() {
                $('#totalAttack').text('Failed to load');
            }
        });
    }

    $(document).ready(function() {
        fetchAttackData();

        // Set interval setiap 5 jam(millisecond)
        setInterval(fetchAttackData, 18000000);
    });
</script>

<script>
    function fetchTableData() {
        fetch('/data/guest/top-10')
            .then(response => response.json())
            .then(result => {
                const tbody = document.querySelector('#top10IpAttacker tbody');
                tbody.innerHTML = ''; // Kosongkan isi tabel sebelum update

                // Pastikan result.total_attack.data ada dan berbentuk array
                const data = result.total_attack?.data || [];

                data.forEach((item, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                            <td>${index + 1}.</td>
                            <td>${item.source_address || '-'}</td>
                            <td>${item.target_address || '-'}</td>
                            <td>${item.eventid || '-'}</td>
                            <td>${item.target_port || '-'}</td>
                            <td>${item.total_attack || '-'}</td>
                    `;
                    tbody.appendChild(row);

                    if (index === 0) {
                        row.classList.add('highlight-row');
                        setTimeout(() => {
                            row.classList.remove('highlight-row');
                        }, 10000);
                    }
                });
            })
            .catch(error => console.error('Error fetching table data:', error));
    }

    // Fetch data saat halaman pertama kali dimuat
    fetchTableData();

    // Perbarui data setiap 1 menit (60,000 ms)
    setInterval(fetchTableData, 60000);
</script>

<script>
    function fetchTableDataAttackerSensor() {
    fetch('/data/guest/get-attack-sensor')
        .then(response => response.json())
        .then(result => {
            const tbody = document.querySelector('#attackSensor tbody');
            tbody.innerHTML = ''; // Kosongkan isi tabel sebelum update

            const data = result.sensor_attack?.data || [];

            data.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}.</td>
                    <td>${item.sensor || 'unknown'}</td>
                    <td>${item.count || '-'}</td>
                `;
                tbody.appendChild(row);

                if (index === 0) {
                    row.classList.add('highlight-row');
                    setTimeout(() => {
                        row.classList.remove('highlight-row');
                    }, 10000);
                }
            });
        })
        .catch(error => console.error('Error fetching table data:', error));
}

// Fetch data saat halaman pertama kali dimuat
fetchTableDataAttackerSensor();

// Perbarui data setiap 1 menit (60,000 ms)
setInterval(fetchTableDataAttackerSensor, 60000);

</script>


@endpush