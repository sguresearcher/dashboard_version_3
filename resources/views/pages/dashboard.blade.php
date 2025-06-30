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
                   <h2 id="websocketTotalAttack">Loading....</h2>
                </div>
                <div class="mb-3">
                    <div class="mb-3">
                        <div id="websocketsensorAttackList"></div>
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
                <table id="websocketattackSensor" class="table w-100">
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
                <table id="websocketattackSourceIP" class="table w-100">
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
                        <tr>
                            <th>No</th>
                            <th>Protocol</th>
                            <th>Port</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="col-md-12 mb-4">
    <div class="card border-0">
        <div class="card-body table-scroll">
            <h5>Live Attacks from WebSocket</h5>
            <table id="liveAttackTable" class="table table-bordered table-hover">
                <thead>
    <tr>
        <th>Sensor</th>
        <th>Source IP</th>
        <th>Country</th>
        <th>City</th>
        <th>Latitude</th>
        <th>Longitude</th>
        <th>Timestamp</th>
    </tr>
</thead>

                <tbody></tbody>
            </table>
        </div>
    </div>
</div>


</div>
@endsection
@push('js')
<script>
const socket = io("http://10.20.100.172:3330");

$(document).ready(function () {
    // Ambil data historis 24 jam terakhir untuk Top Protocol:Port
    loadTopProtocolPort();
    loadTopSourceIp();
    loadTopSensorTable();
    loadAttackInformation();
    // Refresh ulang ketika checkbox Average atau Total diubah
    $('#showAverage, #showTotal').change(loadAttackInformation);


    // WebSocket listener untuk log baru
    socket.on("new_log", (msg) => {
        updateLiveAttackTable(msg);  // hanya untuk tabel live
    });

    // Fungsi ambil dan tampilkan top 10 protocol:port dari backend
    function loadTopProtocolPort() {
        $.get("http://10.20.100.172:3330/top-protocol-port", function (res) {
            const data = res.data || [];
            const tbody = $('#top10IpAttacker tbody');
            tbody.empty();

            let no = 1;
            data.slice(0, 10).forEach(([key, count]) => {
                const [protocol, port] = key.split(':');
                const row = `
                    <tr>
                        <td>${no++}</td>
                        <td>${protocol}</td>
                        <td>${port}</td>
                        <td>${count}</td>
                    </tr>
                `;
                tbody.append(row);
            });
        });
    }

    function loadTopSourceIp() {
    $.get("http://10.20.100.172:3330/top-source-ip", function (res) {
        const data = res.data || [];
        const tbody = $('#websocketattackSourceIP tbody');
        tbody.empty();

        let no = 1;
        data.slice(0, 10).forEach(([ip, count]) => {
            const row = `
                <tr>
                    <td>${no++}</td>
                    <td>${ip}</td>
                    <td>${count}</td>
                </tr>
            `;
            tbody.append(row);
        });
    });
}

function loadTopSensorTable() {
    $.get("http://10.20.100.172:3330/top-sensor-count", function (res) {
        const data = res.data || [];
        const tbody = $('#websocketattackSensor tbody');
        tbody.empty();

        let no = 1;
        data.slice(0, 10).forEach(([sensor, count]) => {
            const row = `
                <tr>
                    <td>${no++}</td>
                    <td>${sensor}</td>
                    <td>${Number(count).toLocaleString()}</td>
                </tr>
            `;
            tbody.append(row);
        });
    });
}

function loadAttackInformation() {
    $.get("http://10.20.100.172:3330/top-sensor-count", function (res) {
        const data = res.data || [];
        const showAverage = $('#showAverage').is(':checked');
        const showTotal = $('#showTotal').is(':checked');

        let total = 0;
        let html = "";

        data.forEach(([sensor, count]) => {
            total += Number(count);
            html += `
                <div class="d-flex justify-content-between">
                    <span>${sensor}</span>
                    <span>${Number(count).toLocaleString()}</span>
                </div>`;
        });

        $('#websocketsensorAttackList').html(html);

        // Hanya tampilkan total jika dicentang
        if (showTotal) {
            $('#websocketTotalAttack').text(total.toLocaleString());
        } else {
            $('#websocketTotalAttack').text("Hidden");
        }

        // Hanya tampilkan average jika dicentang
        if (showAverage && data.length > 0) {
            const avg = Math.round(total / data.length);
            $('#websockettotalAttackAverage').text(`Avg: ${avg.toLocaleString()}`);
        } else {
            $('#websockettotalAttackAverage').text("");
        }
    });
}

    // Fungsi update tabel Live Attack berdasarkan log baru
    function updateLiveAttackTable(msg) {
        const sensor = msg.sensor || 'unknown';
        const data = msg.data || {};
        const geo = msg.geo || {};

        const tsStr = data.timestamp;
        if (!tsStr) return;

        const logTime = new Date(tsStr);
        const now = new Date();
        const diffMinutes = (now - logTime) / (1000 * 60);
        if (diffMinutes > 60 * 24) return;
        if (sensor === "all") return;

        const ip = data.src_ip || data.remote_ip || 'N/A';
        const country = geo.country || 'Unknown';
        const encodedCountry = encodeURIComponent(country);
        const countryFlag = country
            ? `<img src="/assets/img/flags/${encodedCountry}.svg" alt="${country}" title="${country}" width="24" height="18" />`
            : country;
        const city = geo.city || '-';
        const lat = geo.latitude ?? '-';
        const lon = geo.longitude ?? '-';

        const newRow = `
            <tr>
                <td>${sensor}</td>
                <td>${ip}</td>
                <td>${countryFlag} ${country}</td>
                <td>${city}</td>
                <td>${lat}</td>
                <td>${lon}</td>
                <td>${tsStr}</td>
            </tr>
        `;

        const tbodyLive = $('#liveAttackTable tbody');
        tbodyLive.prepend(newRow);
        if (tbodyLive.children().length > 10) {
            tbodyLive.children().last().remove();
        }
    }

    // Optional: refresh otomatis Top Protocol-Port setiap 5 menit
    setInterval(loadTopProtocolPort, 1 * 60 * 1000);
    setInterval(loadTopSourceIp, 1 * 60 * 1000);
    setInterval(loadTopSensorTable, 1 * 60 * 1000);
    setInterval(loadAttackInformation, 1 * 60 * 1000);
});
</script>






@auth
@if (auth()->user()->role == 'superadmin')
<script>
// Inisialisasi koneksi WebSocket dan struktur data
const socket = io("http://10.20.100.172:3330");

let liveLogs = [];
let topSensors = {};
let topSourceIPs = {};
let topProtocolPorts = {};

$(document).ready(function () {
    // Listener WebSocket: terima data real-time
    socket.on("new_log", updateFromWebSocket);

    // Fungsi utama untuk memproses setiap log baru
    function updateFromWebSocket(msg) {
        const sensor = msg.sensor || 'unknown';
        const data = msg.data || {};
        const geo = msg.geo || {};
        const tsStr = data.timestamp;
        if (!tsStr) return;

        const logTime = new Date(tsStr);
        const now = new Date();
        const diffMinutes = (now - logTime) / (1000 * 60);
        if (diffMinutes > 60 * 24) return;

        const ip = data.src_ip || data.remote_ip || 'N/A';
        const protocol = data.protocol || data.eventid || 'unknown';
        const port = data.port || data.target_port || '-';
        const country = geo.country || 'Unknown';
        const city = geo.city || '-';
        const lat = geo.latitude ?? '-';
        const lon = geo.longitude ?? '-';

        // Update tabel live attack
        liveLogs.push({ sensor, ip, country, city, lat, lon, timestamp: tsStr, timeObj: logTime });
        liveLogs.sort((a, b) => b.timeObj - a.timeObj);
        if (liveLogs.length > 10) liveLogs = liveLogs.slice(0, 10);
        renderLiveAttackTable();

        // Update sensor count
        topSensors[sensor] = (topSensors[sensor] || 0) + 1;
        renderTopSensorTable();

        // Update source IP count
        topSourceIPs[ip] = (topSourceIPs[ip] || 0) + 1;
        renderTopSourceIpTable();

        // Update protocol-port count
        const key = `${protocol}:${port}`;
        topProtocolPorts[key] = (topProtocolPorts[key] || 0) + 1;
        renderTopProtocolPortTable();

        // Update total attack value
        renderTotalAttack();
    }

    function renderLiveAttackTable() {
        const tbody = $('#liveAttackTable tbody');
        tbody.empty();
        liveLogs.forEach(log => {
            const newRow = `
                <tr>
                    <td>${log.sensor}</td>
                    <td>${log.ip}</td>
                    <td>${log.country}</td>
                    <td>${log.city}</td>
                    <td>${log.lat}</td>
                    <td>${log.lon}</td>
                    <td>${log.timestamp}</td>
                </tr>`;
            tbody.append(newRow);
        });
    }

    function renderTopSensorTable() {
        const tbody = $('#attackSensor tbody');
        const sorted = Object.entries(topSensors).sort((a, b) => b[1] - a[1]).slice(0, 10);
        tbody.empty();
        sorted.forEach(([sensor, count], i) => {
            tbody.append(`<tr><td>${i+1}</td><td>${sensor}</td><td>${count}</td></tr>`);
        });

        // Rata-rata juga ditampilkan
        renderSensorAverageTable();
    }

    function renderTopSourceIpTable() {
        const tbody = $('#attackSourceIP tbody');
        const sorted = Object.entries(topSourceIPs).sort((a, b) => b[1] - a[1]).slice(0, 10);
        tbody.empty();
        sorted.forEach(([ip, count], i) => {
            tbody.append(`<tr><td>${i+1}</td><td>${ip}</td><td>${count}</td></tr>`);
        });
    }

    function renderTopProtocolPortTable() {
        const tbody = $('#top10IpAttacker tbody');
        const sorted = Object.entries(topProtocolPorts).sort((a, b) => b[1] - a[1]).slice(0, 10);
        tbody.empty();
        sorted.forEach(([key, count], i) => {
            const [protocol, port] = key.split(':');
            tbody.append(`<tr><td>${i+1}</td><td>${protocol}</td><td>${port}</td><td>${count}</td></tr>`);
        });
    }

    function renderTotalAttack() {
        const total = Object.values(topSensors).reduce((acc, val) => acc + val, 0);
        $('#totalAttack').text(total.toLocaleString());
    }

    function renderSensorAverageTable() {
        const avgContainer = $('#totalAttackAverage');
        const total = Object.values(topSensors).reduce((a, b) => a + b, 0);
        const sensors = Object.keys(topSensors).length;

        avgContainer.empty();
        if (sensors === 0) {
            avgContainer.text('No data to show');
            return;
        }

        const ul = $('<ul class="list-unstyled mb-0" style="text-align: right"></ul>');
        Object.entries(topSensors).forEach(([sensor, count]) => {
            const avg = Math.round(count); // Jika ingin tampil rata-rata per sensor
            const li = `
                <li style="display: flex; justify-content: space-between;">
                    <span>${sensor}</span>
                    <span>${avg}</span>
                </li>`;
            ul.append(li);
        });

        avgContainer.append(ul);
    }

    // Checkbox behavior (Average vs Total, Hour vs Day)
    $('#showAverage').on('change', function () {
        if (this.checked) $('#showTotal').prop('checked', false);
    });
    $('#showTotal').on('change', function () {
        if (this.checked) $('#showAverage').prop('checked', false);
    });
    $('#showHour').on('change', function () {
        if (this.checked) $('#showDay').prop('checked', false);
    });
    $('#showDay').on('change', function () {
        if (this.checked) $('#showHour').prop('checked', false);
    });
});
</script>
@endif
@endauth


@guest
<script>
    // const socket = io("http://your-domain.com");
    // const ipAttackMap = {};

$(document).ready(function () {
  function getCheckboxStatus() {
    return {
      isDay: $('#showDay').is(':checked'),
      isHour: $('#showHour').is(':checked'),
      isAverage: $('#showAverage').is(':checked'),
      isTotal: $('#showTotal').is(':checked'),
    };
  }

  function setupCheckboxExclusivity() {
    $('#showAverage').change(function () {
      if (this.checked) $('#showTotal').prop('checked', false);
      triggerAllUpdates();
    });

    $('#showTotal').change(function () {
      if (this.checked) $('#showAverage').prop('checked', false);
      triggerAllUpdates();
    });

    $('#showHour').change(function () {
      if (this.checked) $('#showDay').prop('checked', false);
      triggerAllUpdates();
    });

    $('#showDay').change(function () {
      if (this.checked) $('#showHour').prop('checked', false);
      triggerAllUpdates();
    });
  }

  function renderSensorList(tbodySelector, data, columns, displayValueFn) {
    const tbody = $(tbodySelector);
    tbody.empty();

    data.forEach((item, index) => {
      const row = $('<tr>');

      row.append(`<td>${index + 1}.</td>`);
      
      columns.forEach(col => {
        if (typeof col === 'string') {
          const val = item[col] || '<span style="opacity:0.5">-</span>';
          row.append(`<td>${val}</td>`);
        } else if (typeof col === 'function') {
          row.append(`<td>${col(item)}</td>`);
        }
      });

      if (displayValueFn) {
        row.append(`<td>${displayValueFn(item)}</td>`);
      }

      tbody.append(row);

      if (index === 0) {
        row.addClass('highlight-row');
        setTimeout(() => row.removeClass('highlight-row'), 10000);
      }
    });
  }

  // Fungsi update display total attack (top number)
  function updateDisplay() {
    const { isDay, isHour, isAverage, isTotal } = getCheckboxStatus();

    $.ajax({
      url: 'data/guest/get-total-attack-sensor-average',
      method: 'GET',
      success: function (response) {
        const container = $('#totalAttack');
        container.empty();

        let value = null;

        if (isDay && isAverage) {
          value = response.average_total_attack_per_day;
        } else if (isDay && isTotal) {
          value = response.total_attack;
        } else if (isHour && isTotal) {
          value = response.average_total_attack_per_day;
        } else if (isHour && isAverage) {
          value = response.average_total_attack_per_minute;
        }

        if (value !== null) {
          container.text(value);
        } else {
          container.text('No data to show');
        }
      },
      error: function () {
        $('#totalAttack').text('Failed to load');
      },
    });
  }

  // Fungsi reusable untuk render tabel (dipakai di 3 tabel berbeda)
  function renderTable(tbodySelector, data, getDisplayValueFunc, getSecondColFunc = null) {
    const tbody = $(tbodySelector + ' tbody');
    tbody.empty();

    data.forEach((item, index) => {
      let displayValue = getDisplayValueFunc(item);

      const secondCol = getSecondColFunc ? getSecondColFunc(item) : '';

      const row = $('<tr>');
      row.append(`<td>${index + 1}.</td>`);
      if (secondCol) {
        row.append(`<td>${secondCol}</td>`);
      } else {
        row.append(`<td>${item.eventid || item.source_address || item.sensor || '<span style="opacity:0.5">-</span>'}</td>`);
      }
      row.append(`<td>${displayValue}</td>`);
      tbody.append(row);

      if (index === 0) {
        row.addClass('highlight-row');
        setTimeout(() => row.removeClass('highlight-row'), 10000);
      }
    });
  }

  function fetchTableData() {
//   const { isDay, isHour, isAverage, isTotal } = getCheckboxStatus();

//   const sortedData = Object.entries(ipAttackMap)
//     .map(([ip, data]) => ({ ip, ...data }))
//     .sort((a, b) => b.total_attack - a.total_attack)
//     .slice(0, 10);

//   const displayValueFn = item => {
//     if (isDay && isAverage) return item.average_day || '-';
//     if (isHour && isAverage) return item.average_hour || '-';
//     if ((isDay && isTotal) || (isHour && isTotal)) return item.total_attack || '-';
//     return '-';
//   };

//   renderSensorList('#top10IpAttacker tbody', sortedData, ['eventid', 'target_port'], displayValueFn);
}

// Tambahkan variabel array liveLogs di awal script di dalam $(document).ready()
let liveLogs = [];

socket.on("new_log", (msg) => {
    const sensor = msg.sensor || 'unknown';
    const data = msg.data || {};
    const geo = msg.geo || {};

    const tsStr = data.timestamp;
    if (!tsStr) return; // abaikan jika tidak ada timestamp

    const logTime = new Date(tsStr);
    const now = new Date();

    // Batasi data yang lebih dari 60 menit (1 jam) lalu
    const diffMinutes = (now - logTime) / (1000 * 60);
    if (diffMinutes > 60) return;

    const ip = data.src_ip || data.remote_ip || 'N/A';
    const country = geo.country || 'Unknown';
    const city = geo.city || '-';
    const lat = geo.latitude ?? '-';
    const lon = geo.longitude ?? '-';

    // Simpan data ke array liveLogs
    liveLogs.push({
        sensor,
        ip,
        country,
        city,
        lat,
        lon,
        timestamp: tsStr,
        timeObj: logTime
    });

    // Urutkan liveLogs berdasar waktu terbaru
    liveLogs.sort((a, b) => b.timeObj - a.timeObj);

    // Batasi hanya 10 data terbaru
    if (liveLogs.length > 10) {
        liveLogs = liveLogs.slice(0, 10);
    }

    // Render ulang tabel
    const tbody = $('#liveAttackTable tbody');
    tbody.empty();

    liveLogs.forEach(log => {
        const newRow = `
            <tr>
                <td>${log.sensor}</td>
                <td>${log.ip}</td>
                <td>${log.country}</td>
                <td>${log.city}</td>
                <td>${log.lat}</td>
                <td>${log.lon}</td>
                <td>${log.timestamp}</td>
            </tr>
        `;
        tbody.append(newRow);
    });
});


  function fetchTableDataSourceIp() {
    const { isDay, isHour, isAverage, isTotal } = getCheckboxStatus();

    $.getJSON('/data/guest/top-10', function(result) {
      const data = result.total_attack?.data || [];

      const displayValueFn = item => {
        if (isDay && isAverage) return item.average_day || '-';
        if (isHour && isAverage) return item.average_hour || '-';
        if ((isDay && isTotal) || (isHour && isTotal)) return item.total_attack || '-';
        return '-';
      };

      renderSensorList('#attackSourceIP tbody', data, ['source_address'], displayValueFn);
    }).fail(() => {
      $('#top10Summary').text('Failed to load data.');
    });
  }

  // Updated updateAverage function to display data as a list similar to your example
  function updateAverage() {
    const { isDay, isHour, isAverage, isTotal } = getCheckboxStatus();

    $.ajax({
      url: '/data/guest/get-attack-sensor-average',
      method: 'GET',
      success: function (response) {
        const data = response.sensor_attack?.data || [];
        const container = $('#totalAttackAverage');
        container.empty();
        
        if (!data.length) {
          container.html('<p>No data to show</p>');
          return;
        }

        // Create an unstyled list with right alignment
        const ul = $('<ul class="list-unstyled mb-0" style="text-align: right"></ul>');
        
        data.forEach((item, index) => {
          const label = item.sensor || 'Unknown';
          let value = '-';
          
          if (isDay && isAverage) {
            value = item.average_per_hour ?? 0;
          } else if (isDay && isTotal) {
            value = item.total_per_day ?? 0;
          } else if (isHour && isTotal) {
            value = item.average_per_hour ?? 0;
          } else if (isHour && isAverage) {
            value = item.average_per_minute ?? 0;
          }
          
          const li = $(`<li style="display: flex; justify-content: space-between;"><span>${label}</span><span>${value}</span></li>`);
          ul.append(li);
          

        });
        
        container.append(ul);
      },
      error: function () {
        $('#totalAttackAverage').text('Failed to load');
      },
    });
  }

  // Fetch sensor attack count
  function fetchSensorAttackCount() {
    const { isDay, isHour, isAverage, isTotal } = getCheckboxStatus();

    $.getJSON('/data/guest/get-attack-sensor-count', function (result) {
      const data = result.sensor_attack?.data || [];

      renderTable(
        '#attackSensor',
        data,
        item => {
          if (isDay && isAverage) return item.average_per_day ?? 0;
          if (isHour && isAverage) return item.average_per_hour ?? 0;
          if ((isDay && isTotal) || (isHour && isTotal)) return item.total ?? 0;
          return '-';
        },
        item => item.sensor || '-'
      );
    }).fail((error) => {
      console.error('Error fetching sensor attack count:', error);
    });
  }
  
  function triggerAllUpdates() {
    updateDisplay();
    fetchTableData();
    fetchTableDataSourceIp();
    updateAverage();
    fetchSensorAttackCount();
  }

  setupCheckboxExclusivity();

  $('#showDay, #showHour, #showAverage, #showTotal').change(triggerAllUpdates);

  triggerAllUpdates();

  setInterval(triggerAllUpdates, 60000);
});

// $(document).ready(function() {
//     function updateDisplay() {
//         const isDay = $('#showDay').is(':checked');
//         const isHour = $('#showHour').is(':checked');
//         const isAverage = $('#showAverage').is(':checked');
//         const isTotal = $('#showTotal').is(':checked');

//         $.ajax({
//             url: 'data/guest/get-total-attack-sensor-average',
//             method: 'GET',
//             success: function(response) {
//                 const container = $('#totalAttack');
//                 container.empty();

//                 let value = null;

//                 if (isDay && isAverage) {
//                     value = response.average_total_attack_per_day;
//                     container.text(`${value}`);
//                 } else if (isDay && isTotal) {
//                     value = response.total_attack;
//                     container.text(`${value}`);
//                 } else if (isHour && isTotal) {
//                     value = response.average_total_attack_per_day;
//                     container.text(`${value}`);
//                 } else if (isHour && isAverage) {
//                     value = response.average_total_attack_per_minute;
//                     container.text(`${value}`);
//                 } else {
//                     container.text('No data to show');
//                 }
//             },
//             error: function() {
//                 $('#totalAttack').text('Failed to load');
//             }
//         });
//     }

//     // Ensure exclusivity
//     $('#showAverage').on('change', function () {
//         if (this.checked) $('#showTotal').prop('checked', false);
//         updateDisplay();
//     });

//     $('#showTotal').on('change', function () {
//         if (this.checked) $('#showAverage').prop('checked', false);
//         updateDisplay();
//     });

//     $('#showHour').on('change', function () {
//         if (this.checked) $('#showDay').prop('checked', false);
//         updateDisplay();
//     });

//     $('#showDay').on('change', function () {
//         if (this.checked) $('#showHour').prop('checked', false);
//         updateDisplay();
//     });

//     // Load default view
//     updateDisplay();
// });


//    function fetchTableData() {
//     const isDay = document.querySelector('#showDay').checked;
//     const isHour = document.querySelector('#showHour').checked;
//     const isAverage = document.querySelector('#showAverage').checked;
//     const isTotal = document.querySelector('#showTotal').checked;

//     fetch('/data/guest/top-10')
//         .then(response => response.json())
//         .then(result => {
//             const tbody = document.querySelector('#top10IpAttacker tbody');
//             tbody.innerHTML = '';

//             const data = result.total_attack?.data || [];

//             data.forEach((item, index) => {
//                 let displayValue = '';

//                 if (isDay && isAverage) {
//                     displayValue = item.average_day;
//                 } else if (isHour && isAverage) {
//                     displayValue = item.average_hour;
//                 } else if (isDay && isTotal || isHour && isTotal) {
//                     displayValue = item.total_attack;
//                 } else {
//                     displayValue = '-';
//                 }

//                 const row = document.createElement('tr');
//                 row.innerHTML = `
//                     <td>${index + 1}.</td>
//                     <td>${item.eventid || '<span style="opacity:0.5">-</span>'}</td>
//                     <td>${item.target_port || '<span style="opacity:0.5">-</span>'}</td>
//                     <td>${displayValue}</td>
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
//         .catch(error => {
//             console.error('Error fetching top 10 data:', error);
//             document.getElementById('top10Summary').textContent = 'Failed to load data.';
//         });
//     }

//     fetchTableData();
//     setInterval(fetchTableData, 60000);

//     document.querySelectorAll('#showDay, #showHour, #showAverage, #showTotal')
//     .forEach(el => el.addEventListener('change', fetchTableData));



// function fetchTableDataSourceIp() {
//     const isDay = document.querySelector('#showDay').checked;
//     const isHour = document.querySelector('#showHour').checked;
//     const isAverage = document.querySelector('#showAverage').checked;
//     const isTotal = document.querySelector('#showTotal').checked;

//     fetch('/data/guest/top-10')
//         .then(response => response.json())
//         .then(result => {
//             const tbody = document.querySelector('#attackSourceIP tbody');
//             tbody.innerHTML = '';

//             const data = result.total_attack?.data || [];

//             data.forEach((item, index) => {
//                 let displayValue = '';

//                 if (isDay && isAverage) {
//                     displayValue = item.average_day;
//                 } else if (isHour && isAverage) {
//                     displayValue = item.average_hour;
//                 } else if (isDay && isTotal || isHour && isTotal) {
//                     displayValue = item.total_attack;
//                 } else {
//                     displayValue = '-';
//                 }

//                  const row = document.createElement('tr');
//                     row.innerHTML = `
//                             <td>${index + 1}.</td>
//                             <td>${item.source_address || '<span style="opacity:0.5">-</span>'}</td>
//                             <td>${displayValue || '<span style="opacity:0.5">-</span>'}</td>
//                     `;
//                     tbody.appendChild(row);

//                 if (index === 0) {
//                     row.classList.add('highlight-row');
//                     setTimeout(() => {
//                         row.classList.remove('highlight-row');
//                     }, 10000);
//                 }
//             });
//         })
//         .catch(error => {
//             console.error('Error fetching top 10 data:', error);
//             document.getElementById('top10Summary').textContent = 'Failed to load data.';
//         });
//     }

//     fetchTableDataSourceIp();
//     setInterval(fetchTableDataSourceIp, 60000);

//     document.querySelectorAll('#showDay, #showHour, #showAverage, #showTotal')
//     .forEach(el => el.addEventListener('change', fetchTableDataSourceIp));
// </script>
{{-- 
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

</script> --}}

{{-- <script>
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
    .forEach(el => el.addEventListener('change', fetchSensorAttackCount)); --}}
@endguest
@endpush