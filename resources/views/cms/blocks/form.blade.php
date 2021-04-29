<?php
$blockClass = 'cms-form ' .( empty($from) || empty($to) ? ' plain-page' : ' popular-directions');

if (!empty($from) && !empty($to) && empty($formData)) {
	$formData = [
		'from' 			=> $from->iata,
		'to' 			=> $to->iata,
		'from_object'   => $from,
		'to_object'     => $to,
		'type'          => $type ?? 'RT',
		'departureDate' => $departureDate ?? null,
		'returnDate'    => $returnDate ?? null,
	];
}

?>
<div class="{{$blockClass}}">
	@if(empty($from) || empty($to))
		{{--форма для обычных страниц--}}
		<frm-search-form form-data-readonly="{{json_encode($contentBlock->config)}}"></frm-search-form>
	@else
		{{--форма для популярных направлений--}}
		<frm-search-form form-data-readonly="{{json_encode($formData)}}"></frm-search-form>
	@endif
</div>