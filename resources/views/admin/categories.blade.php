@extends('admin.layouts.app')

@section('content')


    <a href="#" class="_run_categories">Парсить категории</a>

    <table>
        <tr>
            <td>Id</td>
            <td>Название</td>
            <td>Псевдоним</td>
            <td>Иконка меню</td>
            <td>Видимость</td>
            <td></td>
            <td></td>
        </tr>
        @foreach($categories as $row)
            <tr>
                <td>{{$row->id}}</td>
                <td>{{$row->name}}</td>
                <td>{{$row->alias}}</td>
                <td>{{$row->img}}</td>
                <td>{{$row->visible}}</td>
                <td>
                    <a href="{{route('parser', $row->id)}}">Парсить</a>
                </td>
                <td>
                    Наличие
                </td>
            </tr>

        @endforeach
    </table>

    <div class="mt-5">
        <a href="/admin/parse-categories" class="btn btn-primary">Найти еще категории и субкатегории</a>
    </div>
@endsection


@section('js')
    <script src="https://www.ikea.com/ms/ru_RU/js/plex_header_final/menu.js"></script>

    <script>
        $(document).ready(function () {
            alert('dssd');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var menuItems = menu.top;

            $('._run_categories').on('click touch', function () {
                menuItems.forEach(function (item, i, menuItems) {
                    //   alert(item.cols);
                    //   alert(item.title);

                    $.ajax({
                        type: 'POST',
                        url: '/run-parse-categories',
                        data: {
                            category: item,
                        },
                        success: function (data) {

                        },
                        error: function (data) {

                        }
                    });
                });
            });
        });
        var jProductData = {
            "comparisonPriceExists": false,
            "hasTemporaryFamilyOffer": true,
            "hasFamilyPrice": false,
            "usesUnitPriceMeasure": false,
            "isUnitPricePrimary": false,
            "normal": {
                "priceNormal": {
                    "priceExclVat": "1 101.\u2013", "value": "1 299.\u2013", "rawPrice": 1299
                },
                "priceNormalDual": {},
                "priceNormalPerUnit": {
                    "unit": ""}
                    },
            "familyNormal": {
                "priceNormalDual": {},
                "priceNormal": {
                    "familyofferstartdate": "10 май, 2018", "priceExclVat": "847.\u2013", "value": "999.\u2013", "familyofferenddate": "24 июн, 2018"},
                "priceNormalPerUnit": {}
                },
            "hasEcoFee": false, "enablenlpinterval": 0, "hasPrfCharge": false
        };
    </script>
@endsection
