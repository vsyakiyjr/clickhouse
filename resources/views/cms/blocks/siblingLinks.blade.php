<div class="cms-previewLinks siblings">
	@if($contentBlock->previousPages->count() > 0)
		<div class="column left">
			<a class="clearfix" href="{{$contentBlock->previousPages->first()->getPath()}}">
				Предыдущие {{ mb_convert_case($contentBlock->page->parent->description, MB_CASE_LOWER) }}
			@foreach($contentBlock->previousPages as $page)
				@component('cms.blocks.previewLink')
					@slot('page', $page)
				@endcomponent
			@endforeach
		</div>
	@endif
	@if($contentBlock->nextPages->count() > 0)
		<div class="column right">
		<a class="clearfix" href="{{$contentBlock->nextPages->first()->getPath()}}">
			Следующие {{ mb_convert_case($contentBlock->page->parent->description, MB_CASE_LOWER) }}
		</a>
		@foreach($contentBlock->nextPages as $page)
			@component('cms.blocks.previewLink')
				@slot('page', $page)
			@endcomponent
		@endforeach
	</div>
	@endif
</div>