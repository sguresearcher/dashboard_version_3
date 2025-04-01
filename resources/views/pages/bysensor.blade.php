@extends('master')
@section('content')
<h5 class="text-center mt-4">{{ $sensor }}</h5>
<hr>
<div class="row">
    <!-- BEGIN col-6 -->
    <div class="col-md-6">
        <!-- BEGIN card -->
        <div class="card mb-3">
            <!-- BEGIN card-body -->
            <div class="card-body">
                <!-- BEGIN title -->
                <div id="chartJsLineChart" class="mb-5">
                    <div class="card">
                        <div class="card-body">
                            <h6>Attack</h6>
                            <canvas id="lineChart"></canvas>
                        </div>
                        <div class="card-arrow">
                            <div class="card-arrow-top-left"></div>
                            <div class="card-arrow-top-right"></div>
                            <div class="card-arrow-bottom-left"></div>
                            <div class="card-arrow-bottom-right"></div>
                        </div>
                    </div>
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
    <div class="col-md-6">
        <!-- BEGIN card -->
        <div class="card mb-3" style="height: 95%">
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
                    <span class="flex-grow-1">Last 24 Hours</span>
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
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div id="chartJsDoughnutChart">
                    <div class="card">
                        <div class="card-body">
                            <h6>Most Used ASN</h6>
                            <div class="h-300px w-300px mx-auto">
                                <canvas id="doughnutChart"></canvas>
                            </div>
                        </div>
                        <div class="card-arrow">
                            <div class="card-arrow-top-left"></div>
                            <div class="card-arrow-top-right"></div>
                            <div class="card-arrow-bottom-left"></div>
                            <div class="card-arrow-bottom-right"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-arrow">
                <div class="card-arrow-top-left"></div>
                <div class="card-arrow-top-right"></div>
                <div class="card-arrow-bottom-left"></div>
                <div class="card-arrow-bottom-right"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-4">Top 10 Attacker IP</h6>
                <canvas id="top10IpTenant"></canvas>
            </div>
            <div class="card-arrow">
                <div class="card-arrow-top-left"></div>
                <div class="card-arrow-top-right"></div>
                <div class="card-arrow-bottom-left"></div>
                <div class="card-arrow-bottom-right"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')

<script>
    function fetchAttackData() {
        $.ajax({
            url: '/data/{{ $sensor }}/h',
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
    function fetchDataAndUpdateChart() {
        fetch('/data/{{ $sensor }}/top-10-ip')
            .then(response => response.json())
            .then(result => {
                const data = result.data;
                const labels = Object.keys(data); // IP addresses
                const values = Object.values(data); // totalAttack

                if (window.barChart) {
                    // Update chart data
                    window.barChart.data.labels = labels;
                    window.barChart.data.datasets[0].data = values;
                    window.barChart.update();
                } else {
                    // Initialize chart if it doesn't exist
                    const ctx2 = document.getElementById('top10IpTenant').getContext('2d');
                    window.barChart = new Chart(ctx2, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Jumlah Serangan per IP',
                                data: values,
                                backgroundColor: 'rgba(100, 149, 237, 0.25)',
                                borderColor: 'rgba(100, 149, 237, 1)',
                                borderWidth: 1.5
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Total Attack'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'IP Address'
                                    }
                                }
                            }
                        }
                    });
                }
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    // Fetch data immediately on page load
    fetchDataAndUpdateChart();

    // Set interval to update chart every 1 minute (60,000 ms)
    setInterval(fetchDataAndUpdateChart, 60000);
</script>


@endpush