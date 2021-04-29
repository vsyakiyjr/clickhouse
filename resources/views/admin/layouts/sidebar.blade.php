<?php
$menu = [
    'shedule'          => 'Планирование',
    'orders'           => 'Заказы',
    'categories'       => 'Категории',
    'products'         => 'Товары',
    'customers'        => 'Клиенты',
    'info'             => 'Информация',
    'parse-categories' => 'Парсить категории'
];
?>
<div class="sidebar  bg-secondary">
    <div class="pt-5 pb-5 pl-4 pr-4">
        <img src="/images/logo.png" alt="" class="adminLogo">
    </div>
    <div class="sidebarMenu">
        @foreach($menu as $key => $value)
            <a class="sidebarItem d-block text-center border-all-golden" href="{{ route($key) }}">
                <div class="pr-2 pl-2 pb-4 pt-5">
                    <div class="font-weight-bold text-center pr-2 pl-2">{{$value}}</div>
                </div>
            </a>
        @endforeach
    </div>
</div>