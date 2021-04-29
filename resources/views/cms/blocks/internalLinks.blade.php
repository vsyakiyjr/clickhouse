<?php
if ($contentBlock['config']['columns'] == 1){
	$class = '';
} else {
	$class = 'two-cols';
}

?>
<div class="cms-previewLinks {{$class}}">
	@foreach($contentBlock->pages as $page)
		@if ($loop->iteration > $contentBlock['config']['pages'])
			@break
		@endif

		@component('cms.blocks.previewLink')
			@slot('page', $page)
			@if($contentBlock['config']['columns'] == 1)
				@slot('descriptionLen', $contentBlock['config']['text_cut_1_col'])
			@else
				@slot('descriptionLen', $contentBlock['config']['text_cut_2_col'])
			@endif
		@endcomponent

	@endforeach
</div>