<ul style="list-style: none;padding:0">
    <li class="{{ $isActive === 'all-product' ? 'css-3m0mp1' : 'css-17zyde9' }}">
        <a href="{{ $urlCurrent }}">All Products</a>
    </li>
    @foreach ($etalase as $etls)
        <li class="{{ $isActive === strtolower(str_replace(' ', '-', $etls)) ? 'css-3m0mp1' : 'css-17zyde9' }}">
            <a href="{{ $urlCurrent . '/' . strtolower(str_replace(' ', '-', $etls)) }}">{{ $etls }}</a>
        </li>
    @endforeach
</ul>
