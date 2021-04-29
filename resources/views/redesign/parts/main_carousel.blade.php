<?php

use App\Models\MainPageWarning;

/** @var MainPageWarning $mainPageWarning */
$mainPageWarning = MainPageWarning::find(1);
?>

@if($mainPageWarning && $mainPageWarning->visible)
	<div class="header-alert">
		<h2>{{ $mainPageWarning->title }}</h2>
		<p>{{ $mainPageWarning->description }}</p>
	</div>
@endif
	
<section class="my-slider">
	<div class="container">
		<div id="mainSlider">
			@foreach($slidesHorizontal as $index => $slide)
				<div class="slider__item" data-image="{{ $slide }}"></div>
			@endforeach
		</div>
		<div class="slider-content__wrap">
			<div class="slider-content">
				<h1>Доставим <br> любой <br> товар <span>IKEA</span></h1>
				<ul>
					<li>Бесплатно по Минску от 250 <span>BYN</span></li>
					<li>Работаем с тремя магазинами <span>IKEA</span> <img src="images/redesign/icons/info-icon.svg" alt="info" class="sl-info"><div id="openImportant" class="why">Почему <br> это важно?</div></li>
					<li>Доставляем по всей Беларуси <img src="images/redesign/icons/info-icon.svg" alt="info" class="sl-info"><div id="openDeliviry" class="why">Как мы <br> доставляем?</div></li>
					<li>Профессионально соберем и установим</li>
				</ul>
				<a class="button-link" href="/catalog">Перейти в каталог</a>
				<a id="orderToggle" class="button-link button-relative" href="#">
					Оформить заказ<p>У меня есть <br/>список товаров</p>
				</a>
			</div>
		</div>
	</div>
</section>

<div style="display: none;" id="modalImportant" class="modal modal-why">
	<div class="modal-content">
		<div class="modal-close jqmodal-close">
			<img src="images/redesign/icons/modal-close.svg" alt="close">
		</div>
		<div class="modal-title">Почему это важно?</div>
		<p>Наше главное преимущество – работа сразу с тремя магазинами IKEA. </p>
		<p>Это дает большую вероятность того, что нужный вам товар будет в наличии.</p>
		<p><b>Например</b>, вы хотите заказать полный комплект кухонного гаритура. В одном магазине может не оказаться шкафа для посуды. Мы найдем его во втором.</p>
		<a href="#" class="modal-more">Узнать больше</a>
	</div>
</div>
<div style="display: none;" id="modalDeliviry" class="modal modal-why">
	<div class="modal-content">
		<div class="modal-close jqmodal-close">
			<img src="images/redesign/icons/modal-close.svg" alt="close">
		</div>
		<div class="modal-title">Как мы работаем?</div>
		<p>Наше преимущество - фиксированная <br> стоимость доставки по Беларуси.</p>
		<p>Такой приятной цены вы не найдете у транспортных компаний.</p>
		<a href="#" class="modal-more">Узнать больше</a>
	</div>
</div>
