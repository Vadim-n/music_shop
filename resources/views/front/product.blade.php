@extends('front.layout')

@section('content')
    <div id="home">
        <product-page category-alias="{{$productAlias}}"></product-page>
    </div>
@endsection
