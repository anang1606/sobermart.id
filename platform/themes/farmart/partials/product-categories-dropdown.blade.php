<div class="css-1jjxysq e1429ojz2 css-hidden">
    <div class="css-1nri6k7-unf-tab e1429ojz3">
        <div class="css-dj3o3l">
            <div class="css-1xeubms e1429ojz4">
                @foreach ($categories as $category)
                    <a href="{{ $category->url }}" class="css-me46ht" data-testid="{{ base64_encode($category->id) }}">
                        {!! BaseHelper::clean($category->name) !!}
                    </a>
                @endforeach
            </div>
            <div class="css-1iefgdn e1429ojz5">
                <div class="css-113hzvq">
                    <div class="css-11p7ov6">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="css-3ordv edxse4c3 css-hidden"></div>
