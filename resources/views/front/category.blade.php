@extends('front.layout')

@section('title')
    <title>{{$category->meta_title ? $category->meta_title : $category->name}}</title>
    <meta name="description" content="{{$category->meta_description ? $category->meta_description : $category->name}}"/>
@endsection

@section('content')
    <div id="home">
        <category-page category-alias="{{$category->alias}}"></category-page>
    </div>
@endsection
