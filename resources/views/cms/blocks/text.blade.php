<?php $totalTextBlocks++ ?>
@if($page->image_path && $totalTextBlocks == 1)
	<div class="blog-article__inner">
		<img class="blog-article__img"
			 src="{{$page->image_path}}"
			 alt="{{$page->title}}"
		/>
	</div>

@endif
<div class="blog-article__inner">
	{!! $contentBlock->content !!}
</div>