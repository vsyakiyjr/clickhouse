@extends('admin.layouts.app')
@section('content')

    <h1 class="font-xl text-gray">Планирование</h1>
    <div class="row">
        <div class="col-md-4">
            <div class="admin-calendar pr-2 pl-2 substrate-shadow">
                <div style="overflow:hidden;">
                    <div class="font-sm text-center text-gray font-weight-bold">Ближайшие планы</div>
                    <div class="">
                        <div id="showcase-picker"></div>
                    </div>
                    <button class="btn btn-danger btn-sm font-micro w-100 font-weight-bold">Идет прием заказов<br>на 30 марта</button>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="substrate-shadow p-4">
                <div class="font-lg font-weight-bold mt-2">{{$nearest}}</div>
                <div class="font-weight-bold mt-4 font-sm">Клиентов
                    <span class="text-gray underline">{{$orders_count}}</span>
                </div>
                <div class="font-weight-bold mt-2 font-sm">Заказано товаров на сумму
                    <span class="text-gray underline">{{$orders_sum}}</span>
                </div>
                <div class="text-right mt-6">
                    <span class="text-gray font-weight-bold">Перенести</span>
                </div>
                <div class="text-right mt-2">
                    <span class="text-gray font-weight-bold">Отменить</span>
                </div>
            </div>
            <div class="text-right mt-5 ">
                <button class="btn btn-gray mr-3">Отмена</button>
                <button class="btn btn-danger">Сохранить</button>
            </div>
        </div>
    </div>
@endsection