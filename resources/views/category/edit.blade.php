@extends('layouts.category')

@section('category_content')
    <category-add :category-id="{{$categoryId}}"></category-add>
@endsection
