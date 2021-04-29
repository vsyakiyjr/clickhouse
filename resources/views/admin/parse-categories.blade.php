@extends('admin.layouts.app')
@section('content')
    <a href="#" class="_run_categories">Парсить категории</a>

@endsection
@section('js')
    <script src="https://www.ikea.com/ms/ru_RU/js/plex_header_final/menu.js"></script>

    <script>
        $(document).ready(function () {
            /*$.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });*/
            var men =  menu.top;
            var menuItems = men.concat(menu.bottom);


            $('._run_categories').on('click touch', function () {
                menuItems.forEach(function (item, i, menuItems) {
                    $.ajax({
                        type: 'POST',
                        url: '/admin/run-parse-categories',
                        data: {
                            category: item,
                        },
                        beforeSend: function(){
                            // $('#spanimg').html('<img id="imgcode" src="ajaxform/loadinfo.gif">');
                        },

                        success: function (data) {

                        },
                        error: function (data) {

                        }
                    });
                });
            });
        });
    </script>
@endsection