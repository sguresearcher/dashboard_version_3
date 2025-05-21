@extends('master')
@section('content')

<div class="row">
    <!-- BEGIN col-6 -->
    <div class="col-md-3">
        <h4>Attack Information</h4>
        <!-- BEGIN card -->
        <div class="card border-0 mb-2">
            <!-- BEGIN card-body -->
            <div class="card-body">
                <!-- BEGIN title -->
                <div class="d-flex fw-bold small mb-3">
                    <span class="flex-grow-1" id="titleShow"></span>
                </div>
                <div class="mb-3">
                   <h2 id="totalAttack">Loading....</h2>
                </div>
                <div class="mb-3">
                    <div class="mb-3">
                        <div id="totalAttackAverage"></div>
                     </div>
                </div>
                <div class="d-flex">
                    <div class="mr-2">
                        <label for="">Average</label>
                        <input type="checkbox" id="showAverage">
                    </div>
                    <div class="ms-2">
                        <label for="">Total</label>
                        <input type="checkbox" id="showTotal" checked>
                    </div>
                </div>
            </div>
        </div>
        <!-- END card -->
    </div>
    <!-- END col-6 -->
    
    <!-- BEGIN col-6 -->
    <div class="col-md-7">
        <!-- BEGIN card -->
        <div id="jVectorMap" class="mb-5">
            <h4>Threat Map</h4>
            <div class="card border-0">
                <div class="card-body">
                    <div id="jvectorMap" style="height: 300px;"></div>
                </div>
            </div>
        </div>
        <!-- END card -->
    </div>

    <div class="col-md-2">
        <!-- BEGIN card -->
       <div class="d-flex">
        <div class="mr-2">
            <label for="">Day</label>
            <input type="checkbox" id="showDay" checked>
        </div>
        <div class="ms-2">
            <label for="">Hour</label>
            <input type="checkbox" id="showHour">
        </div>
       </div>
        <!-- END card -->
    </div>
</div>
<div class="row">
    <div class="col-md-3 mb-4 mb-md-0">
        <div class="card border-0">
            <div class="card-body table-scroll">
                <table id="attackSensor" class="table w-100">
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
        </div>
    </div>

    <div class="col-md-3 mb-4 mb-md-0">
        <div class="card border-0">
            <div class="card-body table-scroll">
                <table id="attackSourceIP" class="table w-100">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Source IP</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4 mb-md-0">
        <div class="card border-0">
            <div class="card-body table-scroll">
                <table id="top10IpAttacker" class="table w-100">
                    <thead>
                            <th>No</th>
                            <th>Protocol</th>
                            <th>Port</th>
                            <th>Total</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')

@auth
@if (auth()->user()->role == 'superadmin')
<script>
$(document).ready(function() {
    function updateDisplay() {
        const isDay = $('#showDay').is(':checked');
        const isHour = $('#showHour').is(':checked');
        const isAverage = $('#showAverage').is(':checked');
        const isTotal = $('#showTotal').is(':checked');

        $.ajax({
            url: 'data/guest/get-total-attack-sensor-average',
            method: 'GET',
            success: function(response) {
                const container = $('#totalAttack');
                container.empty();

                let value = null;

                if (isDay && isAverage) {
                    value = response.average_total_attack_per_day;
                    container.text(`${value}`);
                } else if (isDay && isTotal) {
                    value = response.total_attack;
                    container.text(`${value}`);
                } else if (isHour && isTotal) {
                    value = response.average_total_attack_per_day;
                    container.text(`${value}`);
                } else if (isHour && isAverage) {
                    value = response.average_total_attack_per_minute;
                    container.text(`${value}`);
                } else {
                    container.text('No data to show');
                }
            },
            error: function() {
                $('#totalAttack').text('Failed to load');
            }
        });
    }

    // Ensure exclusivity
    $('#showAverage').on('change', function () {
        if (this.checked) $('#showTotal').prop('checked', false);
        updateDisplay();
    });

    $('#showTotal').on('change', function () {
        if (this.checked) $('#showAverage').prop('checked', false);
        updateDisplay();
    });

    $('#showHour').on('change', function () {
        if (this.checked) $('#showDay').prop('checked', false);
        updateDisplay();
    });

    $('#showDay').on('change', function () {
        if (this.checked) $('#showHour').prop('checked', false);
        updateDisplay();
    });

    // Load default view
    updateDisplay();
});


   function fetchTableData() {
    const isDay = document.querySelector('#showDay').checked;
    const isHour = document.querySelector('#showHour').checked;
    const isAverage = document.querySelector('#showAverage').checked;
    const isTotal = document.querySelector('#showTotal').checked;

    fetch('/data/guest/top-10')
        .then(response => response.json())
        .then(result => {
            const tbody = document.querySelector('#top10IpAttacker tbody');
            tbody.innerHTML = '';

            const data = result.total_attack?.data || [];

            data.forEach((item, index) => {
                let displayValue = '';

                if (isDay && isAverage) {
                    displayValue = item.average_day;
                } else if (isHour && isAverage) {
                    displayValue = item.average_hour;
                } else if (isDay && isTotal || isHour && isTotal) {
                    displayValue = item.total_attack;
                } else {
                    displayValue = '-';
                }

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}.</td>
                    <td>${item.eventid || '<span style="opacity:0.5">-</span>'}</td>
                    <td>${item.target_port || '<span style="opacity:0.5">-</span>'}</td>
                    <td>${displayValue}</td>
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
        .catch(error => {
            console.error('Error fetching top 10 data:', error);
            document.getElementById('top10Summary').textContent = 'Failed to load data.';
        });
    }

    fetchTableData();
    setInterval(fetchTableData, 60000);

    document.querySelectorAll('#showDay, #showHour, #showAverage, #showTotal')
    .forEach(el => el.addEventListener('change', fetchTableData));



function fetchTableDataSourceIp() {
    const isDay = document.querySelector('#showDay').checked;
    const isHour = document.querySelector('#showHour').checked;
    const isAverage = document.querySelector('#showAverage').checked;
    const isTotal = document.querySelector('#showTotal').checked;

    fetch('/data/guest/top-10')
        .then(response => response.json())
        .then(result => {
            const tbody = document.querySelector('#attackSourceIP tbody');
            tbody.innerHTML = '';

            const data = result.total_attack?.data || [];

            data.forEach((item, index) => {
                let displayValue = '';

                if (isDay && isAverage) {
                    displayValue = item.average_day;
                } else if (isHour && isAverage) {
                    displayValue = item.average_hour;
                } else if (isDay && isTotal || isHour && isTotal) {
                    displayValue = item.total_attack;
                } else {
                    displayValue = '-';
                }

                 const row = document.createElement('tr');
                    row.innerHTML = `
                            <td>${index + 1}.</td>
                            <td>${item.source_address || '<span style="opacity:0.5">-</span>'}</td>
                            <td>${displayValue || '<span style="opacity:0.5">-</span>'}</td>
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
        .catch(error => {
            console.error('Error fetching top 10 data:', error);
            document.getElementById('top10Summary').textContent = 'Failed to load data.';
        });
    }

    fetchTableDataSourceIp();
    setInterval(fetchTableDataSourceIp, 60000);

    document.querySelectorAll('#showDay, #showHour, #showAverage, #showTotal')
    .forEach(el => el.addEventListener('change', fetchTableDataSourceIp));
</script>

<script>

$(document).ready(function() {
    function updateAverage() {
        const isDay = $('#showDay').is(':checked');
        const isHour = $('#showHour').is(':checked');
        const isAverage = $('#showAverage').is(':checked');
        const isTotal = $('#showTotal').is(':checked');

        $.ajax({
            url: '/data/guest/get-attack-sensor-average',
            method: 'GET',
            success: function(response) {
                const container = $('#totalAttackAverage');
                container.empty();

                const data = response.sensor_attack?.data || [];

                if (isDay && isAverage) {
                    const ul = $('<ul style="text-align: right"></ul>').addClass('list-unstyled mb-0');

                    data.forEach(item => {
                        const sensorName = item.sensor || 'Unknown';
                        const averagePerHour = item.average_per_hour ?? 0;
                        const li = $(`
                                            <li style="display: flex; justify-content: space-between;">
                                                <span>${sensorName}</span>
                                                <span>${averagePerHour}</span>
                                            </li>
                                        `);

                        ul.append(li);
                    });
                    container.append(ul);

                } else if (isDay && isTotal) {

                    const ul = $('<ul style="text-align: right"></ul>').addClass('list-unstyled mb-0');

                    data.forEach(item => {
                        const sensorName = item.sensor || 'Unknown';
                        const total = item.total_per_day ?? 0;
                        const li = $(`
                                            <li style="display: flex; justify-content: space-between;">
                                                <span>${sensorName}</span>
                                                <span>${total}</span>
                                            </li>
                                        `);

                        ul.append(li);
                    });

                    container.append(ul);

                } else if (isHour && isTotal) {
                    const ul = $('<ul style="text-align: right"></ul>').addClass('list-unstyled mb-0');

                    data.forEach(item => {
                        const sensorName = item.sensor || 'Unknown';
                        const averagePerDay = item.average_per_hour ?? 0;
                        const li = $(`
                                            <li style="display: flex; justify-content: space-between;">
                                                <span>${sensorName}</span>
                                                <span>${averagePerDay}</span>
                                            </li>
                                        `);

                        ul.append(li);
                    });
                    container.append(ul);

                } else if (isHour && isAverage) {
                    const ul = $('<ul style="text-align: right"></ul>').addClass('list-unstyled mb-0');

                    data.forEach(item => {
                        const sensorName = item.sensor || 'Unknown';
                        const averagePerHour = item.average_per_minute ?? 0;
                        const li = $(`
                                            <li style="display: flex; justify-content: space-between;">
                                                <span>${sensorName}</span>
                                                <span>${averagePerHour}</span>
                                            </li>
                                        `);

                        ul.append(li);
                    });
                    container.append(ul);
                    
                } else {
                    container.text('No data to show');
                }
            },
            error: function() {
                $('#totalAttackAverage').text('Failed to load');
            }
        });
    }

    // Ensure exclusivity
    $('#showAverage').on('change', function () {
        if (this.checked) $('#showTotal').prop('checked', false);
        updateAverage();
    });

    $('#showTotal').on('change', function () {
        if (this.checked) $('#showAverage').prop('checked', false);
        updateAverage();
    });

    $('#showHour').on('change', function () {
        if (this.checked) $('#showDay').prop('checked', false);
        updateAverage();
    });

    $('#showDay').on('change', function () {
        if (this.checked) $('#showHour').prop('checked', false);
        updateAverage();
    });

    // Load default view
    updateAverage();
});

</script>

<script>
function fetchSensorAttackCount() {
    const isDay = document.querySelector('#showDay').checked;
    const isHour = document.querySelector('#showHour').checked;
    const isAverage = document.querySelector('#showAverage').checked;
    const isTotal = document.querySelector('#showTotal').checked;

    fetch('/data/guest/get-attack-sensor-count')
        .then(response => response.json())
        .then(result => {
            const tbody = document.querySelector('#attackSensor tbody');
            tbody.innerHTML = '';

            const data = result.sensor_attack?.data || [];

            data.forEach((item, index) => {
                let displayValue = '-';

                if (isDay && isAverage) {
                    displayValue = item.average_per_day ?? 0;
                } else if (isHour && isAverage) {
                    displayValue = item.average_per_hour ?? 0;
                } else if ((isDay && isTotal) || (isHour && isTotal)) {
                    displayValue = item.total ?? 0;
                }

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}.</td>
                    <td>${item.sensor || '-'}</td>
                    <td>${displayValue}</td>
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
        .catch(error => {
            console.error('Error fetching sensor attack count:', error);
        });
}

fetchSensorAttackCount();
setInterval(fetchSensorAttackCount, 60000);

document.querySelectorAll('#showDay, #showHour, #showAverage, #showTotal')
    .forEach(el => el.addEventListener('change', fetchSensorAttackCount));

</script>
@else
<script>
    function fetchSensorAttackCountTenant() {
        const isDay = document.querySelector('#showDay').checked;
        const isHour = document.querySelector('#showHour').checked;
        const isAverage = document.querySelector('#showAverage').checked;
        const isTotal = document.querySelector('#showTotal').checked;

        fetch('/data/tenant/get-attack-sensor-count')
            .then(response => response.json())
            .then(result => {
                const tbody = document.querySelector('#attackSensor tbody');
                tbody.innerHTML = '';

                const data = result.sensor_attack?.data || [];
                console.log(data);

                data.forEach((item, index) => {
                    let displayValue = '';

                    if (isDay && isAverage) {
                        displayValue = item.average_per_day;
                    } else if (isHour && isAverage) {
                        displayValue = item.average_per_hour;
                    } else if ((isDay && isTotal) || (isHour && isTotal)) {
                        displayValue = item.total;
                    } else {
                        displayValue = '-';
                    }

                    const row = `
                        <tr>
                            <td>${index + 1}.</td>
                            <td>${item.sensor || '-'}</td>
                            <td>${displayValue || 0}</td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);

                    if (index === 0) {
                        const firstRow = tbody.querySelector('tr');
                        if (firstRow) {
                            firstRow.classList.add('highlight-row');
                            setTimeout(() => {
                                firstRow.classList.remove('highlight-row');
                            }, 10000);
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching top 10 data:', error);
            });
    }

    fetchSensorAttackCountTenant();
    setInterval(fetchSensorAttackCountTenant, 60000);

    document.querySelectorAll('#showDay, #showHour, #showAverage, #showTotal')
        .forEach(el => el.addEventListener('change', fetchSensorAttackCountTenant));
</script>



<script>
    //  function fetchTableDataSourceIp() {
    //     fetch('/data/tenant/top-10')
    //         .then(response => response.json())
    //         .then(result => {
    //             const tbody = document.querySelector('#attackSourceIP tbody');
    //             tbody.innerHTML = '';

    //             const data = result.total_attack?.data || [];

    //             data.forEach((item, index) => {
    //                 const row = document.createElement('tr');
    //                 row.innerHTML = `
    //                         <td>${index + 1}.</td>
    //                         <td>${item.source_address || '<span style="opacity:0.5">-</span>'}</td>
    //                         <td>${item.total_attack || '<span style="opacity:0.5">-</span>'}</td>
    //                 `;
    //                 tbody.appendChild(row);

    //                 if (index === 0) {
    //                     row.classList.add('highlight-row');
    //                     setTimeout(() => {
    //                         row.classList.remove('highlight-row');
    //                     }, 10000);
    //                 }
    //             });
    //         })
    //         .catch(error => console.error('Error fetching table data:', error));
    // }

    // fetchTableDataSourceIp();

    // setInterval(fetchTableDataSourceIp, 60000);

    function fetchTableDataSourceIp() {
        const isDay = document.querySelector('#showDay').checked;
        const isHour = document.querySelector('#showHour').checked;
        const isAverage = document.querySelector('#showAverage').checked;
        const isTotal = document.querySelector('#showTotal').checked;

        fetch('/data/tenant/top-10')
            .then(response => response.json())
            .then(result => {
                const tbody = document.querySelector('#attackSourceIP tbody');
                tbody.innerHTML = '';

                const data = result.total_attack?.data || [];
                console.log(data);

                data.forEach((item, index) => {
                    let displayValue = '';

                    if (isDay && isAverage) {
                        displayValue = item.average_per_day;
                    } else if (isHour && isAverage) {
                        displayValue = item.average_per_hour;
                    } else if ((isDay && isTotal) || (isHour && isTotal)) {
                        displayValue = item.total_attack;
                    } else {
                        displayValue = '-';
                    }

                    const row = `
                            <td>${index + 1}.</td>
                            <td>${item.source_address || '<span style="opacity:0.5">-</span>'}</td>
                            <td>${displayValue || '<span style="opacity:0.5">-</span>'}</td>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);

                    if (index === 0) {
                        const firstRow = tbody.querySelector('tr');
                        if (firstRow) {
                            firstRow.classList.add('highlight-row');
                            setTimeout(() => {
                                firstRow.classList.remove('highlight-row');
                            }, 10000);
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching top 10 data:', error);
            });
    }

    fetchTableDataSourceIp();
    setInterval(fetchTableDataSourceIp, 60000);

    document.querySelectorAll('#showDay, #showHour, #showAverage, #showTotal')
        .forEach(el => el.addEventListener('change', fetchTableDataSourceIp));



    function fetchTableData() {
        fetch('/data/tenant/top-10')
            .then(response => response.json())
            .then(result => {
                const tbody = document.querySelector('#top10IpAttacker tbody');
                tbody.innerHTML = '';

                const data = result.total_attack?.data || [];

                data.forEach((item, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                            <td>${index + 1}.</td>
                            <td>${item.eventid || '<span style="opacity:0.5">-</span>'}</td>
                            <td>${item.target_port || '<span style="opacity:0.5">-</span>'}</td>
                            <td>${item.total_attack || '<span style="opacity:0.5">-</span>'}</td>
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

    fetchTableData();

    setInterval(fetchTableData, 60000);
</script>

<script>
 $(document).ready(function() {
    function updateDisplay() {
        const isDay = $('#showDay').is(':checked');
        const isHour = $('#showHour').is(':checked');
        const isAverage = $('#showAverage').is(':checked');
        const isTotal = $('#showTotal').is(':checked');

        $.ajax({
            url: '/data/tenant/total-attack',
            method: 'GET',
            success: function(response) {
                const container = $('#totalAttack');
                container.empty();

                let value = null;

                if (isDay && isAverage) {
                    value = response.average_total_attack_per_day;
                    container.text(`${value}`);
                } else if (isDay && isTotal) {
                    value = response.total_attack;
                    container.text(`${value}`);
                } else if (isHour && isTotal) {
                    value = response.average_total_attack_per_day;
                    container.text(`${value}`);
                } else if (isHour && isAverage) {
                    value = response.average_total_attack_per_minute;
                    container.text(`${value}`);
                } else {
                    container.text('No data to show');
                }
            },
            error: function() {
                $('#totalAttack').text('Failed to load');
            }
        });
    }

    // Ensure exclusivity
    $('#showAverage').on('change', function () {
        if (this.checked) $('#showTotal').prop('checked', false);
        updateDisplay();
    });

    $('#showTotal').on('change', function () {
        if (this.checked) $('#showAverage').prop('checked', false);
        updateDisplay();
    });

    $('#showHour').on('change', function () {
        if (this.checked) $('#showDay').prop('checked', false);
        updateDisplay();
    });

    $('#showDay').on('change', function () {
        if (this.checked) $('#showHour').prop('checked', false);
        updateDisplay();
    });

    // Load default view
    updateDisplay();
});

$(document).ready(function() {
    function updateAverage() {
        const isDay = $('#showDay').is(':checked');
        const isHour = $('#showHour').is(':checked');
        const isAverage = $('#showAverage').is(':checked');
        const isTotal = $('#showTotal').is(':checked');

        $.ajax({
            url: '/data/tenant/average',
            method: 'GET',
            success: function(response) {
                const container = $('#totalAttackAverage');
                container.empty();

                const data = response.sensor_attack?.data || [];

                if (isDay && isAverage) {
                    const ul = $('<ul style="text-align: right"></ul>').addClass('list-unstyled mb-0');

                    data.forEach(item => {
                        const sensorName = item.sensor || 'Unknown';
                        const averagePerHour = item.average_per_hour ?? 0;
                        const li = $(`
                                            <li style="display: flex; justify-content: space-between;">
                                                <span>${sensorName}</span>
                                                <span>${averagePerHour}</span>
                                            </li>
                                        `);

                        ul.append(li);
                    });
                    container.append(ul);

                } else if (isDay && isTotal) {

                    const ul = $('<ul style="text-align: right"></ul>').addClass('list-unstyled mb-0');

                    data.forEach(item => {
                        const sensorName = item.sensor || 'Unknown';
                        const total = item.total_per_day ?? 0;
                        const li = $(`
                                            <li style="display: flex; justify-content: space-between;">
                                                <span>${sensorName}</span>
                                                <span>${total}</span>
                                            </li>
                                        `);

                        ul.append(li);
                    });

                    container.append(ul);

                } else if (isHour && isTotal) {
                    const ul = $('<ul style="text-align: right"></ul>').addClass('list-unstyled mb-0');

                    data.forEach(item => {
                        const sensorName = item.sensor || 'Unknown';
                        const averagePerDay = item.average_per_hour ?? 0;
                        const li = $(`
                                            <li style="display: flex; justify-content: space-between;">
                                                <span>${sensorName}</span>
                                                <span>${averagePerDay}</span>
                                            </li>
                                        `);

                        ul.append(li);
                    });
                    container.append(ul);

                } else if (isHour && isAverage) {
                    const ul = $('<ul style="text-align: right"></ul>').addClass('list-unstyled mb-0');

                    data.forEach(item => {
                        const sensorName = item.sensor || 'Unknown';
                        const averagePerHour = item.average_per_minute ?? 0;
                        const li = $(`
                                            <li style="display: flex; justify-content: space-between;">
                                                <span>${sensorName}</span>
                                                <span>${averagePerHour}</span>
                                            </li>
                                        `);

                        ul.append(li);
                    });
                    container.append(ul);
                    
                } else {
                    container.text('No data to show');
                }
            },
            error: function() {
                $('#totalAttackAverage').text('Failed to load');
            }
        });
    }

    // Ensure exclusivity
    $('#showAverage').on('change', function () {
        if (this.checked) $('#showTotal').prop('checked', false);
        updateAverage();
    });

    $('#showTotal').on('change', function () {
        if (this.checked) $('#showAverage').prop('checked', false);
        updateAverage();
    });

    $('#showHour').on('change', function () {
        if (this.checked) $('#showDay').prop('checked', false);
        updateAverage();
    });

    $('#showDay').on('change', function () {
        if (this.checked) $('#showHour').prop('checked', false);
        updateAverage();
    });

    updateAverage();
});
</script>
@endauth
@endif

@guest
<script>
$(document).ready(function() {
    function updateDisplay() {
        const isDay = $('#showDay').is(':checked');
        const isHour = $('#showHour').is(':checked');
        const isAverage = $('#showAverage').is(':checked');
        const isTotal = $('#showTotal').is(':checked');

        $.ajax({
            url: 'data/guest/get-total-attack-sensor-average',
            method: 'GET',
            success: function(response) {
                const container = $('#totalAttack');
                container.empty();

                let value = null;

                if (isDay && isAverage) {
                    value = response.average_total_attack_per_day;
                    container.text(`${value}`);
                } else if (isDay && isTotal) {
                    value = response.total_attack;
                    container.text(`${value}`);
                } else if (isHour && isTotal) {
                    value = response.average_total_attack_per_day;
                    container.text(`${value}`);
                } else if (isHour && isAverage) {
                    value = response.average_total_attack_per_minute;
                    container.text(`${value}`);
                } else {
                    container.text('No data to show');
                }
            },
            error: function() {
                $('#totalAttack').text('Failed to load');
            }
        });
    }

    // Ensure exclusivity
    $('#showAverage').on('change', function () {
        if (this.checked) $('#showTotal').prop('checked', false);
        updateDisplay();
    });

    $('#showTotal').on('change', function () {
        if (this.checked) $('#showAverage').prop('checked', false);
        updateDisplay();
    });

    $('#showHour').on('change', function () {
        if (this.checked) $('#showDay').prop('checked', false);
        updateDisplay();
    });

    $('#showDay').on('change', function () {
        if (this.checked) $('#showHour').prop('checked', false);
        updateDisplay();
    });

    // Load default view
    updateDisplay();
});


   function fetchTableData() {
    const isDay = document.querySelector('#showDay').checked;
    const isHour = document.querySelector('#showHour').checked;
    const isAverage = document.querySelector('#showAverage').checked;
    const isTotal = document.querySelector('#showTotal').checked;

    fetch('/data/guest/top-10')
        .then(response => response.json())
        .then(result => {
            const tbody = document.querySelector('#top10IpAttacker tbody');
            tbody.innerHTML = '';

            const data = result.total_attack?.data || [];

            data.forEach((item, index) => {
                let displayValue = '';

                if (isDay && isAverage) {
                    displayValue = item.average_day;
                } else if (isHour && isAverage) {
                    displayValue = item.average_hour;
                } else if (isDay && isTotal || isHour && isTotal) {
                    displayValue = item.total_attack;
                } else {
                    displayValue = '-';
                }

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}.</td>
                    <td>${item.eventid || '<span style="opacity:0.5">-</span>'}</td>
                    <td>${item.target_port || '<span style="opacity:0.5">-</span>'}</td>
                    <td>${displayValue}</td>
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
        .catch(error => {
            console.error('Error fetching top 10 data:', error);
            document.getElementById('top10Summary').textContent = 'Failed to load data.';
        });
    }

    fetchTableData();
    setInterval(fetchTableData, 60000);

    document.querySelectorAll('#showDay, #showHour, #showAverage, #showTotal')
    .forEach(el => el.addEventListener('change', fetchTableData));



function fetchTableDataSourceIp() {
    const isDay = document.querySelector('#showDay').checked;
    const isHour = document.querySelector('#showHour').checked;
    const isAverage = document.querySelector('#showAverage').checked;
    const isTotal = document.querySelector('#showTotal').checked;

    fetch('/data/guest/top-10')
        .then(response => response.json())
        .then(result => {
            const tbody = document.querySelector('#attackSourceIP tbody');
            tbody.innerHTML = '';

            const data = result.total_attack?.data || [];

            data.forEach((item, index) => {
                let displayValue = '';

                if (isDay && isAverage) {
                    displayValue = item.average_day;
                } else if (isHour && isAverage) {
                    displayValue = item.average_hour;
                } else if (isDay && isTotal || isHour && isTotal) {
                    displayValue = item.total_attack;
                } else {
                    displayValue = '-';
                }

                 const row = document.createElement('tr');
                    row.innerHTML = `
                            <td>${index + 1}.</td>
                            <td>${item.source_address || '<span style="opacity:0.5">-</span>'}</td>
                            <td>${displayValue || '<span style="opacity:0.5">-</span>'}</td>
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
        .catch(error => {
            console.error('Error fetching top 10 data:', error);
            document.getElementById('top10Summary').textContent = 'Failed to load data.';
        });
    }

    fetchTableDataSourceIp();
    setInterval(fetchTableDataSourceIp, 60000);

    document.querySelectorAll('#showDay, #showHour, #showAverage, #showTotal')
    .forEach(el => el.addEventListener('change', fetchTableDataSourceIp));
</script>

<script>

$(document).ready(function() {
    function updateAverage() {
        const isDay = $('#showDay').is(':checked');
        const isHour = $('#showHour').is(':checked');
        const isAverage = $('#showAverage').is(':checked');
        const isTotal = $('#showTotal').is(':checked');

        $.ajax({
            url: '/data/guest/get-attack-sensor-average',
            method: 'GET',
            success: function(response) {
                const container = $('#totalAttackAverage');
                container.empty();

                const data = response.sensor_attack?.data || [];

                if (isDay && isAverage) {
                    const ul = $('<ul style="text-align: right"></ul>').addClass('list-unstyled mb-0');

                    data.forEach(item => {
                        const sensorName = item.sensor || 'Unknown';
                        const averagePerHour = item.average_per_hour ?? 0;
                        const li = $(`
                                            <li style="display: flex; justify-content: space-between;">
                                                <span>${sensorName}</span>
                                                <span>${averagePerHour}</span>
                                            </li>
                                        `);

                        ul.append(li);
                    });
                    container.append(ul);

                } else if (isDay && isTotal) {

                    const ul = $('<ul style="text-align: right"></ul>').addClass('list-unstyled mb-0');

                    data.forEach(item => {
                        const sensorName = item.sensor || 'Unknown';
                        const total = item.total_per_day ?? 0;
                        const li = $(`
                                            <li style="display: flex; justify-content: space-between;">
                                                <span>${sensorName}</span>
                                                <span>${total}</span>
                                            </li>
                                        `);

                        ul.append(li);
                    });

                    container.append(ul);

                } else if (isHour && isTotal) {
                    const ul = $('<ul style="text-align: right"></ul>').addClass('list-unstyled mb-0');

                    data.forEach(item => {
                        const sensorName = item.sensor || 'Unknown';
                        const averagePerDay = item.average_per_hour ?? 0;
                        const li = $(`
                                            <li style="display: flex; justify-content: space-between;">
                                                <span>${sensorName}</span>
                                                <span>${averagePerDay}</span>
                                            </li>
                                        `);

                        ul.append(li);
                    });
                    container.append(ul);

                } else if (isHour && isAverage) {
                    const ul = $('<ul style="text-align: right"></ul>').addClass('list-unstyled mb-0');

                    data.forEach(item => {
                        const sensorName = item.sensor || 'Unknown';
                        const averagePerHour = item.average_per_minute ?? 0;
                        const li = $(`
                                            <li style="display: flex; justify-content: space-between;">
                                                <span>${sensorName}</span>
                                                <span>${averagePerHour}</span>
                                            </li>
                                        `);

                        ul.append(li);
                    });
                    container.append(ul);
                    
                } else {
                    container.text('No data to show');
                }
            },
            error: function() {
                $('#totalAttackAverage').text('Failed to load');
            }
        });
    }

    // Ensure exclusivity
    $('#showAverage').on('change', function () {
        if (this.checked) $('#showTotal').prop('checked', false);
        updateAverage();
    });

    $('#showTotal').on('change', function () {
        if (this.checked) $('#showAverage').prop('checked', false);
        updateAverage();
    });

    $('#showHour').on('change', function () {
        if (this.checked) $('#showDay').prop('checked', false);
        updateAverage();
    });

    $('#showDay').on('change', function () {
        if (this.checked) $('#showHour').prop('checked', false);
        updateAverage();
    });

    // Load default view
    updateAverage();
});

</script>

<script>
function fetchSensorAttackCount() {
    const isDay = document.querySelector('#showDay').checked;
    const isHour = document.querySelector('#showHour').checked;
    const isAverage = document.querySelector('#showAverage').checked;
    const isTotal = document.querySelector('#showTotal').checked;

    fetch('/data/guest/get-attack-sensor-count')
        .then(response => response.json())
        .then(result => {
            const tbody = document.querySelector('#attackSensor tbody');
            tbody.innerHTML = '';

            const data = result.sensor_attack?.data || [];

            data.forEach((item, index) => {
                let displayValue = '-';

                if (isDay && isAverage) {
                    displayValue = item.average_per_day ?? 0;
                } else if (isHour && isAverage) {
                    displayValue = item.average_per_hour ?? 0;
                } else if ((isDay && isTotal) || (isHour && isTotal)) {
                    displayValue = item.total ?? 0;
                }

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}.</td>
                    <td>${item.sensor || '-'}</td>
                    <td>${displayValue}</td>
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
        .catch(error => {
            console.error('Error fetching sensor attack count:', error);
        });
}

fetchSensorAttackCount();
setInterval(fetchSensorAttackCount, 60000);

document.querySelectorAll('#showDay, #showHour, #showAverage, #showTotal')
    .forEach(el => el.addEventListener('change', fetchSensorAttackCount));

</script>
@endguest
@endpush