<?php /** @var \App\Models\Cms\PagePreview $page */ ?>

@if($pages->count() > 0)
	<div class="pages-list-block">
		<div class="h4">
			<a href="{{$parent->fullpath}}">{{ $title ?? $parent->title }}</a></div>
		<ul>
			@foreach($pages as $page)
			<li>
				<a href="{{$parent->fullpath}}/{{$page->alias}}">
					@if($withDate)
						<span class="date">{{$page->news_date ? $page->news_date->format('d.m.y') : $page->created_at->format('d.m.y')}}</span>
					@endif
					{{ $page->getTitle() }}
				</a>

				@if($page->popularDirection)
					<span class="text-primary pull-right">
						от {{$page->price}} грн
					</span>
				@endif
			</li>
			@endforeach
		</ul>
	</div>
@endif