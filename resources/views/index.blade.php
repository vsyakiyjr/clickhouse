@extends('layouts.column-catalog')
@section('content')

    @component('catalog',['catalog' => $catalog,])
    @endcomponent

@endsection