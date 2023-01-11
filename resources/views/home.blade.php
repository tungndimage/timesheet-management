@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div>January 11th, 2023</div>
                    <div>1:13 PM</div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-primary">IN</button>
                        <button type="button" class="btn btn-secondary" onclick="window.location = '{{ url('/timesheet') }}'">Timesheet</button>
                        <button type="button" class="btn btn-success">OUT</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection