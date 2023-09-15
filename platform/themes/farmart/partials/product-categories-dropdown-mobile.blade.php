<div class="css-ob1k6i css-ph07rh">
    @foreach ($categories as $category)
        <a href="{{ $category->url }}" class="css-15junsn">
            <div class="css-zklpkv">
                <div class="fixed css-1ybjgrp">
                    <img src="{{ RvMedia::getImageUrl($category->image) }}" alt="{{ $category->name }}"
                        class="css-1026e2w" />
                </div>
            </div>
            <div class="css-1fhlj2u">
                {!! BaseHelper::clean($category->name) !!}
            </div>
        </a>
    @endforeach
</div>
