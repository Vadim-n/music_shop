@extends('front.layout')

@section('title')
    <title>Музыкальный магазин Живой звук | Воронеж</title>
    <meta name="description" content="Музыкальные инструменты и оборудование"/>
@endsection

@section('content')
    <div id="home">
        <div class="container">
            <h1>Контакты</h1>
            <h3>Магазин «Живой звук»</h3>
            <p><strong>Адрес:</strong> 394006, г. Воронеж, ул. Куцыгина, 35/1</p>
            <p><strong>Телефон:</strong> +7 906 671 16 75</p>
            <p><strong>Почта:</strong> sound36ru@yandex.ru</p>
            <p><strong>График работы:</strong></p>
            <ul style="margin-left: 30px;">
                <li>10-18 (будние дни)</li>
                <li>10-17 (суббота)</li>
                <li>10-15 (воскресенье)</li>
            </ul>
        </div>
    </div>
@endsection

@section('map')
    <script type="text/javascript" charset="utf-8" async
            src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3Aa919f58fa97b119967035567534a54a03b69509648e852d7a24c34c206fb48f3&amp;width=100%25&amp;height=500&amp;lang=ru_RU&amp;scroll=false"></script>
@endsection
