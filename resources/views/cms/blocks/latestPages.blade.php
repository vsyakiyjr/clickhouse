<h3 class="more-articles__title">Больше интересных статей</h3>
<div class="blog-page__wrap blog-page__wrapper">
    @foreach($latestPages as $latestPage)
        <div class="cms-previewLink">
            <a href="#" class="cms-thumbnail">
                <img src="/resources/images/blog/skandi.jpg" alt="article-more"/>
                <div class="cms-view-count">
						<span>
							<img src="/images/redesign/icons/eye.svg" alt="eye">
							544
						</span>
                    <span>
							<img src="/images/redesign/icons/hearth.svg" alt="hearth">
							231
						</span>
                </div>
            </a>
            <div class="blog-page__inner">
                <div class="h2">
                    <a href="#">
						<span class="underlined">
							{{$latestPage->title}}
						</span>
                    </a>
                </div>
                <div class="description">
                    {{$latestPage->description}}
                </div>
            </div>

        </div>
    @endforeach
</div>