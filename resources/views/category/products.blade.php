@extends('layouts.product')

@section('product_content')
    <product-index
        data-url="{{$dataUrl}}"
        category-id="{{$categoryId}}"
        message="{{$successMessage}}">
    </product-index>
@endsection
