<div class="breadcrumbs">
	<a class="item" href="/catalog">Каталог</a>
	@if($category)
		<span class="breadcrumbs-arrow">
			<svg width="4" height="6" viewBox="0 0 4 6" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path fill-rule="evenodd" clip-rule="evenodd"
					d="M2.00037 5.00049L2.99982 4.00024L4 3L2.99982 1.99976L2.00037 1.00024L1.00018 -1.31126e-07L1.94697e-07 1.00024L1.00018 1.99976L2.00037 3L1.00018 4.00024L1.98403e-08 5.00049L1.00018 6L2.00037 5.00049Z"
					fill="#828282" />
			</svg>
		</span>
		<a class="item" href="/catalog/{{ $category->alias }}">{{ $category->name }}</a>
		@if(isset($subCategory))
			<span class="breadcrumbs-arrow">
				<svg width="4" height="6" viewBox="0 0 4 6" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd"
						d="M2.00037 5.00049L2.99982 4.00024L4 3L2.99982 1.99976L2.00037 1.00024L1.00018 -1.31126e-07L1.94697e-07 1.00024L1.00018 1.99976L2.00037 3L1.00018 4.00024L1.98403e-08 5.00049L1.00018 6L2.00037 5.00049Z"
						fill="#828282" />
				</svg>
			</span>
			<a class="item"
				href="/catalog/{{ $category->alias }}/{{ $subCategory->alias }}">{{ $subCategory->name }}</a>
		@endif
	@endif
</div>