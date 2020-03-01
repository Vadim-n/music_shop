@extends('layouts.product')

@section('product_content')
    <product-add :product-id="{{$productId}}"></product-add>
@endsection
