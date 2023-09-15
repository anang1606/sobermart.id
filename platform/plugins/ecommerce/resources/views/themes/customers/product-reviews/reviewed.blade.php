<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('Image') }}</th>
                <th>{{ __('Product Name') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Star') }}</th>
                <th width="200">{{ __('Comment') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @if ($reviews->total() > 0)
                @foreach ($reviews as $item)
                    <tr>
                        <th scope="row">
                            <img src="{{ RvMedia::getImageUrl($item->product->image, 'thumb', false, RvMedia::getDefaultImage()) }}"
                                alt="{{ $item->product->name }}" class="img-thumb" style="max-width: 70px">
                        </th>
                        <th scope="row">
                            <a href="{{ $item->product->url }}">{{ $item->product->name }}</a>
                        </th>
                        <td>{{ $item->created_at->translatedFormat('M d, Y h:m') }}</td>
                        <td>
                            <span>{{ $item->star }}</span>
                            <span class="ecommerce-icon text-primary">
                                <svg>
                                    <use href="#ecommerce-icon-star-o" xlink:href="#ecommerce-icon-star-o"></use>
                                </svg>
                            </span>
                        </td>
                        <td>{{ Str::limit($item->comment, 120) }}</td>
                        <td>
                            {!! Form::open([
                                'url' => route('public.reviews.destroy', $item->id),
                                'onSubmit' => 'return confirm("' . __('Do you really want to delete the review?') . '")',
                            ]) !!}
                            <input type="hidden" name="_method" value="DELETE">
                            <button class="btn btn-danger btn-sm">{{ __('Delete') }}</button>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                    @foreach ($item->parent as $parent)
                        <tr>
                            <td colspan="6">
                                <article class="css-1fb72zd">
                                    <div class="css-pexmea-unf-heading e1qvo2ff8">
                                        <div class="css-8cwkuh-unf-link e1u528jj0">
                                            <span class="seller-name">{{ $parent->vendor->name }}</span>
                                            <div class="css-1tahbs6-unf-label e15jnsqh0">
                                                <p class="css-4du2dp-unf-heading e1qvo2ff8"
                                                    style="text-transform: capitalize;">Penjual</p>
                                            </div>
                                        </div>
                                        <div class="post-time">{{ $parent->created_at->diffForHumans() }}</div>
                                    </div>
                                    <p class="css-t78k5l-unf-heading e1qvo2ff8">
                                        <span>
                                            {{ $parent->comment }}
                                        </span>
                                    </p>
                                </article>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            @else
                <tr>
                    <td colspan="6" class="text-center">{{ __('No reviews!') }}</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<div class="pagination">
    {!! $reviews->links() !!}
</div>
