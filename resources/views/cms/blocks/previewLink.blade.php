<div class="cms-previewLink">
	<?php /** @var \App\Models\Cms\PagePreview $page */ ?>

	@if($page->image_path)
		<a href="{{$page->getPath()}}" class="cms-thumbnail">
			<img src="{{$page->image_path}}" alt="{{$page->title}}"/>
			<div class="cms-view-count">
				<span>
					<img src="/images/redesign/icons/eye.svg" alt="eye">
					{{$page->view_count}}
				</span>
				<span>
					<img src="/images/redesign/icons/hearth.svg" alt="hearth">
					231
				</span>
			</div>
		</a>
	@endif
	<div class="blog-page__inner">
		<div class="h2">
			<a href="{{$page->getPath()}}">
				<span class="underlined">{{$page->getTitle()}}</span>
			</a>
		</div>
	
		@php($descriptionLen = empty($descriptionLen) ? 125 : $descriptionLen)
		<div class="description">
			{{ cutText(strip_tags($page->firstTextBlock()), $descriptionLen) }}
		</div>
	</div>
	
</div>
