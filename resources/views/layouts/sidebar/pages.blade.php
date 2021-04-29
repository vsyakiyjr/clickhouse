@php
	$pages = App\Models\Pages::where('visible', 1)->get();
@endphp
<div class="sidebar">
	@foreach($pages as $page)
		<a class="sidebar-item {{ $page->alias == Request::path() ? 'active' : '' }}" href="/{{$page->alias}}">
			<span class="sidebar-item__text">{{$page->title}}</span>
		</a>
	@endforeach
	<a class="sidebar-item {{ 'contacts' == Request::path() ? 'active' : '' }}" href="/contacts">
		<span class="sidebar-item__text">Контакты</span>
	</a>

{{--	<a class="sidebarItem sidebarItem__info login {{ 'contacts' == Request::path() ? 'active' : '' }}"
	   href="{{Auth::user() ? '/account' : '/login'}}">
        <span class="font-xs text-center sidebarItem__text">
            {{Auth::user() ? 'Профиль' : 'Войти'}}
        </span>
	</a>--}}


</div>