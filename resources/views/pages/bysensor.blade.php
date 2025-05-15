@extends('master')
@section('content')

<h5 class="text-center mt-4">{{ $sensor }}</h5>
<hr>

<div class="row">
    <div class="col-md-6">

        <div class="card border-0 mb-3" style="height: 95%">
            <div class="card-body">
                <div class="d-flex fw-bold small mb-3">
                    <span class="flex-grow-1">Total Attack</span>
                </div>
                <div class="mb-3">
                   <h2 id="totalAttack">Loading....</h2>
                </div>

                <div class="d-flex fw-bold small mb-3">
                    <span class="flex-grow-1">Average</span>
                </div>
                <div class="mb-3">
                   <div id="totalAttackAverage"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 mb-3" style="height: 95%">
            <div class="card-body">
                    <div class="card border-0">
                        <div class="card-body">
                           <div style="display: flex; align-items: flex-start; gap: 30px;">
                            <canvas id="doughnutChartAttack" width="200" height="200"></canvas>
                            <div id="doughnutLabels" style="font-family: sans-serif; font-size: 14px;"></div>
                        </div>

                        </div>
                    </div>
            </div>
        </div>
    </div>

   
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-body">
                <h6 class="mb-4">Top 10 Attacker IP</h6>
                <canvas id="top10IpTenant" style="height: 350px; width:100%"></canvas>
            </div>
        </div>
    </div>

    {{-- <div class="col-md-6">

        <div class="card mb-3">
            <div class="card-body">
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
            <div class="card-arrow">
                <div class="card-arrow-top-left"></div>
                <div class="card-arrow-top-right"></div>
                <div class="card-arrow-bottom-left"></div>
                <div class="card-arrow-bottom-right"></div>
            </div>
        </div> --}}
    </div>
</div>

@endsection

@push('js')
<script>
    function fetchAttackData() {
        fetch('/data/tenant/sensor/{{ $sensor }}/detail')
            .then(response => response.json())
            .then(result => {
                const total = result.data.reduce((sum, item) => sum + (item.total || 0), 0);
                $('#totalAttack').text(total.toLocaleString());
                $('#totalAttackAverage').text((total / 24).toFixed(2));
            })
            .catch(() => {
                $('#totalAttack').text('Failed');
                $('#totalAttackAverage').text('Failed');
            });
    }

    function fetchTop10AttackerChart() {
        fetch('/data/tenant/sensor/{{ $sensor }}/top10')
            .then(response => response.json())
            .then(result => {
                const data = result.data;

                if (!data || data.length === 0) {
                    console.warn("Data kosong");
                    return;
                }

                const labels = data.map(item => item.source_address);
                const values = data.map(item => item.total);

                const ctx2 = document.getElementById('top10IpTenant').getContext('2d');

                if (window.barChart) {
                    window.barChart.data.labels = labels;
                    window.barChart.data.datasets[0].data = values;
                    window.barChart.update();
                } else {
                    window.barChart = new Chart(ctx2, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Total Attack per IP',
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
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    }

    $(document).ready(function () {
        fetchAttackData();
        fetchTop10AttackerChart();

        setInterval(fetchAttackData, 18000000);           // setiap 5 jam
        setInterval(fetchTop10AttackerChart, 60000);      // setiap 1 menit
    });
</script>

<script>
    $(document).ready(function () {
        // Chart Doughnut untuk IP Attacker
        var ctx6 = document.getElementById('doughnutChartAttack');
        if (!ctx6) {
            console.error("Canvas #doughnutChartAttack tidak ditemukan.");
            return;
        }

        var doughnutChart = new Chart(ctx6.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        '#ff6384', '#36a2eb', '#cc65fe', '#ffce56', '#2ecc71',
                        '#f39c12', '#3498db', '#e74c3c', '#1abc9c', '#9b59b6'
                    ],
                    hoverBackgroundColor: [
                        '#ff6384', '#36a2eb', '#cc65fe', '#ffce56', '#2ecc71',
                        '#f39c12', '#3498db', '#e74c3c', '#1abc9c', '#9b59b6'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false, // Menonaktifkan legend default
                    }
                }
            }
        });

        // Ambil data chart dari backend
        fetch('/data/{{ $sensor }}/doughnut-chart')
            .then(response => response.json())
            .then(data => {
                doughnutChart.data.labels = data.ip_attack_chart.labels;
                doughnutChart.data.datasets[0].data = data.ip_attack_chart.data;
                doughnutChart.update();

                // Menambahkan label di samping kanan chart
                const labelsContainer = document.getElementById('doughnutLabels');
                labelsContainer.innerHTML = ''; // Kosongkan dulu container

                // Iterasi untuk menambahkan label baru
                data.ip_attack_chart.labels.forEach((label, index) => {
                    const value = data.ip_attack_chart.data[index];
                    const labelItem = document.createElement('div');
                    labelItem.style.display = 'flex';
                    labelItem.style.alignItems = 'center';
                    labelItem.style.marginBottom = '10px';

                    // Menambahkan warna kotak kecil sesuai dengan warna background chart
                    const colorBox = document.createElement('span');
                    colorBox.style.backgroundColor = doughnutChart.data.datasets[0].backgroundColor[index];
                    colorBox.style.width = '20px';
                    colorBox.style.height = '20px';
                    colorBox.style.marginRight = '10px';

                    // Menambahkan teks label dan total
                    const labelText = document.createElement('span');
                    labelText.innerText = `${label}: ${value}`;

                    labelItem.appendChild(colorBox);
                    labelItem.appendChild(labelText);
                    labelsContainer.appendChild(labelItem);
                });
            })
            .catch(error => {
                console.error('Error loading chart data:', error);
            });
    });
</script>

@endpush
