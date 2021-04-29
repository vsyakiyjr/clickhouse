<article class="blog-article">
	<div class="container">
	<?php /** @var \App\Models\Cms\Page $page */ ?>

	<?php
	$date = $page->news_date ?? $page->created_at;
	?>
	<div class="blog-article__wrapper">
		@component('cms.pageBreadcrumbs', ['breadCrumbs' => $page->getNestingChain()])@endcomponent
		<span class="blog-article__date">{{ App\Helpers\Calendar::convertDate($date->format('d.m.Y'), true) }}</span>
	</div>

	<h1 class="blog-article__title">{!! $page->getTitle() !!}</h1>
	<div class="blog-page-info__wrap">
	<div style="opacity: 0;" class="blog-page__stats">
		<span>
			<img src="/images/redesign/icons/blog-page/eye.svg" alt="eye"> 
			23
		</span>
		<span>
			<img src="/images/redesign/icons/blog-page/hearth.svg" alt="hearth"> 
			2
		</span>
		<span>
			<img src="/images/redesign/icons/blog-page/fb.svg" alt="fb"> 
			1
		</span>
		<span>
			<img src="/images/redesign/icons/blog-page/vk.svg" alt="vk"> 
			2
		</span>
	</div>
	<div class="blog-page-info__wrap-second">
	<?php $totalTextBlocks = 0 ?>
	@foreach($page->getVisibleBlocks() as $contentBlock)
		@if($contentBlock['title'])
			<h2 class="cms-textblock-title">{!! $contentBlock->title !!}</h2>
		@endif
		@include("cms.blocks.{$contentBlock->type}")
	@endforeach
	@if($page->parent->show_siblings)
		<?php
			$contentBlock = new App\Cms\Structures\SiblingsLinksCmsBlock([
				'visible' => 1,
				'config' => [
					'page' => $page
				]
			])
		?>
		@include('cms.blocks.siblingLinks')
	@endif
	</div>
	</div>
		@include('cms.blocks.latestPages')
	</div>
</article>
<aside>
	@if($page->parent->show_siblings)
		@component('cms.pagesListBlock', [
			'pages' => $page->siblings('<', 50),
			'parent' => $page->parent,
			'withDate' => ($page->isInNews())
		])
			@slot('title')
				{{ $page->parent->title ?? $page->parent->getDescription() }}
			@endslot
		@endcomponent
	@endif
</aside>
