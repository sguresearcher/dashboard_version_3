@extends('master')
@section('content')
<div class="row">
        <div id="formControls" class="mb-5">
            <h4 class="mb-3">{{ Str::upper(auth()->user()->name) }} Setting</h4>
            <div class="card p-3">
                <div class="card-body pb-2">
                    <form>
                        <div class="row">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="exampleFormControlInput1">Name</label>
                                    <input type="text" class="form-control"  value="{{ auth()->user()->name }}" disabled>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label" for="exampleFormControlInput1">Email</label>
                                    <input type="email" class="form-control"  value="{{ auth()->user()->email }}" disabled>
                                </div>
                                <hr>
                                <h5>Change Password</h5>
                                <div class="form-group mb-3">
                                    <label class="form-label" for="exampleFormControlTextarea1">Current Password</label>
                                    <input type="password" name="current" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label" for="exampleFormControlTextarea1">New Password</label>
                                    <input type="password" name="new" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label" for="exampleFormControlTextarea1">Re Type Password</label>
                                    <input type="password" name="retype" class="form-control">
                                </div>
                        </div>

                        <div class="d-flex gap-3">
                            <button class="btn btn-outline-success">Change Password</button>
                            <a href="/" class="btn btn-outline-warning">Back</a>
                        </div>
                    </form>
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
                <div class="hljs-container">
                    <pre><code class="xml" data-url="assets/data/form-elements/code-1.json"></code></pre>
                </div>
            </div>
        </div>
</div>
@endsection