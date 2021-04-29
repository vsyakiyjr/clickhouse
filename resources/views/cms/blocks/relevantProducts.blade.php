<h3 class="more-articles__title">Товары по теме</h3>
<div class="blog-page__wrap blog-page__wrapper">
    @foreach($page->products as $product)
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
							{{$product->name}}
						</span>
                    </a>
                </div>
                <div class="description">
                    {{$product->description}}
                </div>
            </div>

        </div>
    @endforeach
</div>