@extends('front.layout')

@section('title')
    <title>{{$product->meta_title ? $product->meta_title : $product->name}}</title>
    <meta name="description" content="{{$product->meta_description ? $product->meta_description : $product->name}}"/>
@endsection

@section('content')
    <div id="home">
        <product-page product-alias="{{$product->alias}}"></product-page>
    </div>
@endsection
