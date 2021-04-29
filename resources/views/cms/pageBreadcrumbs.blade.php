<div class="breadcrumbs">
	@foreach($breadCrumbs as $breadCrumb)
		@if($loop->first)
			<?php $itemProp = '';	?>
			@else
			<?php $itemProp = 'itemprop="child" ';	?>
		@endif
		<span itemscope {{$itemProp}} itemtype="http://data-vocabulary.org/Breadcrumb">
			<a @if(!$loop->last) href="{{$breadCrumb['link']}}" @endif class="pathway" itemprop="url">
				<span itemprop="title">{{$breadCrumb['name']}}</span>
			</a>
			@if(!$loop->last)
				›
			@endif
		</span>
	@endforeach
</div>