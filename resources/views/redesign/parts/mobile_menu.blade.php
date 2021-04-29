<?php
	$settings = App\Models\Setting::where('key', 'contacts')->first();
	$data = $settings ? json_decode($settings->value, true) : [];
	$firstPhone = $data['phones'][0] ?? '';
	$instagram = $data['social']['instagram']['link'] ?? 'https://www.instagram.com/scandimania.by';
?>

<div class="mobile-menu not-visible">

	<a class="mobile-menu-item catalog"
	   href="/catalog">
		Каталог
	</a>

	<a class="mobile-menu-item info"
	   href="/info">
		Инфо
	</a>
	<a class="mobile-menu-item blog"
	   href="/blog">
		Идеи интерьера
	</a>

	<span class="mobile-menu-item catalog-button" style="display: none">
		<i class="fa fa-th menu" aria-hidden="true"></i>
		<span class="close">×</span>
	</span>
</div>

<div class="catalog-mobile">
	@include('redesign.parts.catalog')
</div>
