@extends('layouts.app')

@section('content')
    <div id="home" class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h2 style="text-align: center;">{{$exception->getStatusCode()}} | {{ $exception->getMessage() }}</h2>
            </div>
        </div>
    </div>

@endsection
