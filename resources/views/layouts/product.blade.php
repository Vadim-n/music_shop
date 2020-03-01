@extends('layouts.app')

@section('content')
    <div id="home" class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Товары @if(isset($category)) категории {{$category->name}} @endif</div>

                    @yield('product_content')

                </div>
            </div>
        </div>
    </div>
@endsection
