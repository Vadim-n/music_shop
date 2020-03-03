@extends('front.layout')

@section('content')
    <div id="home">
        <category-page category-alias="{{$categoryAlias}}"></category-page>
    </div>
@endsection
