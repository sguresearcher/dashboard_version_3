@extends('master')
@section('content')

<h5 class="text-center mt-4">Monitoring Sensor</h5>
<hr>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="text-center">Nama Sensor</h5>
            </div>
            <div class="card-body bg-success">
                <h5 class="text-center">Status</h5>
                <h4 class="text-center">ACTIVE</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="text-center">Nama Sensor</h5>
            </div>
            <div class="card-body bg-danger">
                <h5 class="text-center">Status</h5>
                <h4 class="text-center">UNACTIVE</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="text-center">Nama Sensor</h5>
            </div>
            <div class="card-body">
                <h5 class="text-center">Status</h5>
                <h4 class="text-center">ACTIVE</h4>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')

@endpush
