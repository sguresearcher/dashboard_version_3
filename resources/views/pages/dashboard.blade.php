@extends('master')
@section('content')
<div class="row">
    <!-- BEGIN col-6 -->
    <div class="col-xl-4">
        <!-- BEGIN card -->
        <div class="card mb-3">
            <!-- BEGIN card-body -->
            <div class="card-body">
                <!-- BEGIN title -->
                <div class="d-flex fw-bold small mb-3">
                    <span class="flex-grow-1">Total Attack</span>
                </div>
                <div class="mb-3">
                   <h2>2,000,000</h2>
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
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <table id="datatableDefault" class="table text-nowrap w-100">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Sensor</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1.</td>
                            <td>Honeytrap</td>
                            <td>6000</td>
                        </tr>
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
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <table id="datatableDefault" class="table text-nowrap w-100">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Country</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1.</td>
                            <td>Germany</td>
                            <td>8000</td>
                        </tr>
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
                <table id="datatableDefault" class="table text-nowrap w-100">
                    <thead>
                            <th>No</th>
                            <th>Source Ip</th>
                            <th>Destination Ip</th>
                            <th>Category</th>
                            <th>Port</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1.</td>
                            <td>103.170.100.111</td>
                            <td>103.168.181.177</td>
                            <td>Honeytrap</td>
                            <td>8000</td>
                        </tr>
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