<?php
$categories = $categories ??  App\Models\Category::with('subcategories')->where(['visible' => 1])->get();
?>

<ul class="catalog-list">
@foreach($categories as $item)
	<li class="sidebarItem {{ 'catalog/'. $item->alias == Request::path() ? 'active' : '' }}" >
		<a class="catalog-list__item" href="/catalog/{{$item->alias}}" class="sidebarItem__text mobile-menu-item">{{$item->name}}</a>
		<ul class="catalog-list__sublist">
			<li>
				<a href="/catalog/{{$item->alias}}" class="mobile-menu-item all-items">Все</a>
			</li>
			@foreach($item->subcategories as $sub)
				<li>
					<a class="mobile-menu-item" href="/catalog/{{$sub->category->alias}}/{{$sub->alias}}">{{strip_tags($sub->name)}}</a>
				</li>
			@endforeach
		</ul>
	</li>
@endforeach
</ul>