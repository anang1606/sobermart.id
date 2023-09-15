{{--  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>  --}}
<div class="row price-group">
    <input type="hidden" value="{{ old('sale_type', $product ? $product->sale_type : 0) }}" class="detect-schedule hidden"
        name="sale_type">


    <div class="col-md-12" style="display: {{ $isVariation ? 'none' : '' }}">
        <div class="form-group mb-3">
            <label class="text-title-field">Kategori</label>
            <div class="next-input--stylized">
                <select name="kategori1" id="kategori1" class="form-control @error('kategori1') is-invalid @enderror"
                    required>
                    <option value="{{ old('kategori1') ? old('kategori1') : $product->kategori1_id ?? null }}" selected>
                        {{ old('kategori1') ? old('kategori1') : $product->kategori1_name ?? null }}
                    </option>
                </select>
                {!! Form::error('kategori1', $errors) !!}

                <select name="kategori2" id="kategori2" class="form-control @error('kategori2') is-invalid @enderror"
                    required>
                    <option value="{{ old('kategori2') ? old('kategori2') : $product->kategori2_id ?? null }}" selected>
                        {{ old('kategori2') ? old('kategori2') : $product->kategori2_name ?? null }}
                    </option>
                </select>
                {!! Form::error('kategori2', $errors) !!}

                <select name="kategori3" id="kategori3" class="form-control @error('kategori3') is-invalid @enderror"
                    required>
                    <option value="{{ old('kategori3') ? old('kategori3') : $product->kategori3_id ?? null }}" selected>
                        {{ old('kategori3') ? old('kategori3') : $product->kategori3_name ?? null }}
                    </option>
                </select>
                {!! Form::error('kategori3', $errors) !!}

                {{--  <input name="kategoris" id="kategoris" readonly class="next-input"
                    value="{{ old('kategori1', $product ? $product->kategori1 : $originalProduct->kategori1 ?? null) }} -> {{ old('kategori2', $product ? $product->kategori2 : $originalProduct->kategori2 ?? null) }} -> {{ old('kategori3', $product ? $product->kategori3 : $originalProduct->kategori3 ?? null) }}"
                    type="text">  --}}

            </div>
        </div>
    </div>

    <div class="col-md-12" style="display: {{ $isVariation ? 'none' : '' }}">
        <div class="form-group mb-3">
            <label class="text-title-field">Etalase</label>
            <input list="listetalase" name="etalase" id="etalase"
                value="{{ old('etalase', $product ? $product->etalase : $originalProduct->etalase ?? null) }}"
                class="form-control @error('etalase') is-invalid @enderror">
            <datalist id="listetalase">
                <?php
			$records = DB::table('ec_products')
                 ->select('etalase')
				 ->where('store_id',auth('customer')->user()->store->id)
                 ->groupBy('etalase')
                 ->get();

			foreach ($records as $row) {
				if (strlen($row->etalase) > 0)?>
                <option value="{{ $row->etalase }}">
                    <?php }	?>
            </datalist>

        </div>
    </div>



    <div class="col-md-12" style="display: {{ $isVariation ? 'none' : '' }}">
        <div class="form-group mb-3">
            <label for="content">{{ __('Specification') }}</label>
            {!! Form::customEditor('description', old('description', $product ? $product->description : null)) !!}
            {!! Form::error('description', $errors) !!}
        </div>
    </div>

    <div class="col-md-12" style="display: {{ $isVariation ? 'none' : '' }}">
        <div class="form-group mb-3">
            <label for="content">{{ __('Description') }}</label>
            {!! Form::customEditor('content', old('content', $product ? $product->content : null)) !!}
            {!! Form::error('content', $errors) !!}
        </div>
    </div>


    <div class="col-md-12">
        <div class="form-group mb-3 @if ($errors->has('sku')) has-error @endif">
            <label class="text-title-field">{{ trans('plugins/ecommerce::products.sku') }}</label>
            {!! Form::text('sku', old('sku', $product ? $product->sku : null), [
                'class' => 'next-input',
                'id' => 'sku',
                'data-counter' => 30,
            ]) !!}
        </div>
        @if (($isVariation && !$product) || ($product && $product->is_variation && !$product->sku))
            <div class="form-group mb-3">
                <label class="text-title-field">
                    <input type="hidden" name="auto_generate_sku" value="0">
                    <input type="checkbox" name="auto_generate_sku" value="1">
                    &nbsp;{{ trans('plugins/ecommerce::products.form.auto_generate_sku') }}
                </label>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <label class="text-title-field">Harga</label>
            <div class="next-input--stylized">
                <span
                    class="next-input-add-on next-input__add-on--before">{{ get_application_currency()->symbol }}</span>
                <input name="hpp" id="hpp" onkeyup="findTotal()"
                    class="next-input input-mask-number regular-price next-input--invisible harga-input"
                    data-thousands-separator="{{ EcommerceHelper::getThousandSeparatorForInputMask() }}"
                    data-decimal-separator="{{ EcommerceHelper::getDecimalSeparatorForInputMask() }}" step="any"
                    value="{{ old('hpp', $product ? $product->hpp : $originalProduct->hpp ?? 0) }}" type="text"
                    style="color:#999999" placeholder="0">
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group mb-3">
            <label class="text-title-field">Fee Aplikasi</label>
            <div class="next-input--stylized">
                <span
                    class="next-input-add-on next-input__add-on--before">{{ get_application_currency()->symbol }}</span>
                <input name="fee" id="fee" readonly
                    class="next-input input-mask-number regular-price next-input--invisible"
                    data-thousands-separator="{{ EcommerceHelper::getThousandSeparatorForInputMask() }}"
                    data-decimal-separator="{{ EcommerceHelper::getDecimalSeparatorForInputMask() }}" step="any"
                    value="{{ old('fee', $product ? $product->fee : $originalProduct->fee ?? 0) }}" type="text"
                    style="color:#999999">
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group mb-3">
            <label class="text-title-field">Harga yang ditampilkan</label>
            <div class="next-input--stylized">
                <span
                    class="next-input-add-on next-input__add-on--before">{{ get_application_currency()->symbol }}</span>
                <input name="price" id="price" readonly
                    class="next-input input-mask-number regular-price next-input--invisible"
                    data-thousands-separator="{{ EcommerceHelper::getThousandSeparatorForInputMask() }}"
                    data-decimal-separator="{{ EcommerceHelper::getDecimalSeparatorForInputMask() }}" step="any"
                    value="{{ old('price', $product ? $product->price : $originalProduct->price ?? 0) }}"
                    type="text" style="color:#999999">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label class="text-title-field">
                    <span>{{ trans('plugins/ecommerce::products.form.price_sale') }}</span>
                    <a href="javascript:;"
                        class="turn-on-schedule @if (old('sale_type', $product ? $product->sale_type : $originalProduct->sale_type ?? 0) == 1) hidden @endif">{{ trans('plugins/ecommerce::products.form.choose_discount_period') }}</a>
                    <a href="javascript:;"
                        class="turn-off-schedule @if (old('sale_type', $product ? $product->sale_type : $originalProduct->sale_type ?? 0) == 0) hidden @endif">{{ trans('plugins/ecommerce::products.form.cancel') }}</a>
                </label>
                <div class="next-input--stylized">
                    <span
                        class="next-input-add-on next-input__add-on--before">{{ get_application_currency()->symbol }}</span>
                    <input name="sale_price"
                        class="next-input input-mask-number sale-price next-input--invisible harga-input"
                        data-thousands-separator="{{ EcommerceHelper::getThousandSeparatorForInputMask() }}"
                        data-decimal-separator="{{ EcommerceHelper::getDecimalSeparatorForInputMask() }}"
                        value="{{ old('sale_price', $product ? $product->sale_price : $originalProduct->sale_price ?? 0) }}"
                        type="text" style="color:#999999" placeholder="0">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label class="text-title-field">{{ trans('plugins/ecommerce::products.form.cost_per_item') }}</label>
                <div class="next-input--stylized">
                    <span
                        class="next-input-add-on next-input__add-on--before">{{ get_application_currency()->symbol }}</span>
                    <input name="cost_per_item"
                        class="next-input input-mask-number regular-price next-input--invisible" step="any"
                        value="{{ old('cost_per_item', $product ? $product->cost_per_item : $originalProduct->cost_per_item ?? null) }}"
                        type="text"
                        placeholder="{{ trans('plugins/ecommerce::products.form.cost_per_item_placeholder') }}">
                </div>
                {!! Form::helper(trans('plugins/ecommerce::products.form.cost_per_item_helper')) !!}
            </div>
        </div>
        <input type="hidden" value="{{ $product->id ?? null }}" name="product_id">
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label class="text-title-field">{{ trans('plugins/ecommerce::products.form.barcode') }}</label>
                <div class="next-input--stylized">
                    <input name="barcode" class="next-input next-input--invisible" step="any"
                        value="{{ old('barcode', $product ? $product->barcode : $originalProduct->barcode ?? null) }}"
                        type="text"
                        placeholder="{{ trans('plugins/ecommerce::products.form.barcode_placeholder') }}">
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 scheduled-time @if (old('sale_type', $product ? $product->sale_type : $originalProduct->sale_type ?? 0) == 0) hidden @endif">
        <div class="form-group mb-3">
            <label class="text-title-field">{{ trans('plugins/ecommerce::products.form.date.start') }}</label>
            <input name="start_date" class="next-input form-date-time"
                value="{{ old('start_date', $product ? $product->start_date : $originalProduct->start_date ?? null) }}"
                type="text">
        </div>
    </div>
    <div class="col-md-6 scheduled-time @if (old('sale_type', $product ? $product->sale_type : $originalProduct->sale_type ?? 0) == 0) hidden @endif">
        <div class="form-group mb-3">
            <label class="text-title-field">{{ trans('plugins/ecommerce::products.form.date.end') }}</label>
            <input name="end_date" class="next-input form-date-time"
                value="{{ old('end_date', $product ? $product->end_date : $originalProduct->end_date ?? null) }}"
                type="text">
        </div>
    </div>
</div>

<hr />

<div class="form-group mb-3">
    <div class="storehouse-management">
        <div class="mt5">
            <input type="hidden" name="with_storehouse_management" value="0">
            <label><input type="checkbox" class="storehouse-management-status" value="1"
                    name="with_storehouse_management" @if (old(
                            'with_storehouse_management',
                            $product ? $product->with_storehouse_management : $originalProduct->with_storehouse_management ?? 0) == 1) checked @endif>
                {{ trans('plugins/ecommerce::products.form.storehouse.storehouse') }}</label>
        </div>
    </div>
</div>

<div class="storehouse-info @if (old(
        'with_storehouse_management',
        $product ? $product->with_storehouse_management : $originalProduct->with_storehouse_management ?? 0) == 0) hidden @endif">
    <div class="form-group mb-3">
        <label class="text-title-field">{{ trans('plugins/ecommerce::products.form.storehouse.quantity') }}</label>
        <input type="text" class="next-input input-mask-number input-medium"
            value="{{ old('quantity', $product ? $product->quantity : $originalProduct->quantity ?? 0) }}"
            name="quantity">
    </div>
    <div class="form-group mb-3">
        <label class="text-title-field">
            <input type="hidden" name="allow_checkout_when_out_of_stock" value="0">
            <input type="checkbox" name="allow_checkout_when_out_of_stock" value="1"
                @if (old(
                        'allow_checkout_when_out_of_stock',
                        $product ? $product->allow_checkout_when_out_of_stock : $originalProduct->allow_checkout_when_out_of_stock ?? 0) == 1) checked @endif>
            &nbsp;{{ trans('plugins/ecommerce::products.form.stock.allow_order_when_out') }}
        </label>
    </div>
</div>

<div class="form-group stock-status-wrapper @if (old(
        'with_storehouse_management',
        $product ? $product->with_storehouse_management : $originalProduct->with_storehouse_management ?? 0) == 1) hidden @endif">
    <label class="text-title-field">{{ trans('plugins/ecommerce::products.form.stock_status') }}</label>
    {!! Form::customSelect(
        'stock_status',
        \Botble\Ecommerce\Enums\StockStatusEnum::labels(),
        $product ? $product->stock_status : null,
    ) !!}
</div>

<hr />

@if (
    !EcommerceHelper::isEnabledSupportDigitalProducts() ||
        (!$product &&
            !$originalProduct &&
            request()->input('product_type') != Botble\Ecommerce\Enums\ProductTypeEnum::DIGITAL) ||
        ($originalProduct && $originalProduct->isTypePhysical()) ||
        ($product && $product->isTypePhysical()))
    <div class="shipping-management ">
        <label class="text-title-field">{{ trans('plugins/ecommerce::products.form.shipping.title') }}</label>
        <div class="row">
            <div class="col-md-3 col-md-6">
                <div class="form-group mb-3">
                    <label>{{ trans('plugins/ecommerce::products.form.shipping.weight') }}
                        ({{ ecommerce_weight_unit() }})</label>
                    <div class="next-input--stylized">
                        <span
                            class="next-input-add-on next-input__add-on--before">{{ ecommerce_weight_unit() }}</span>
                        <input type="text" class="next-input input-mask-number next-input--invisible harga-input"
                            name="weight"
                            value="{{ old('weight', $product ? $product->weight : $originalProduct->weight ?? 0) }}"
                            style="color:#999999">
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-md-6">
                <div class="form-group mb-3">
                    <label>{{ trans('plugins/ecommerce::products.form.shipping.length') }}
                        ({{ ecommerce_width_height_unit() }})</label>
                    <div class="next-input--stylized">
                        <span
                            class="next-input-add-on next-input__add-on--before">{{ ecommerce_width_height_unit() }}</span>
                        <input type="text" class="next-input input-mask-number next-input--invisible harga-input"
                            name="length"
                            value="{{ old('length', $product ? $product->length : $originalProduct->length ?? 0) }}"
                            style="color:#999999">
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-md-6">
                <div class="form-group mb-3">
                    <label>{{ trans('plugins/ecommerce::products.form.shipping.wide') }}
                        ({{ ecommerce_width_height_unit() }})</label>
                    <div class="next-input--stylized">
                        <span
                            class="next-input-add-on next-input__add-on--before">{{ ecommerce_width_height_unit() }}</span>
                        <input type="text" class="next-input input-mask-number next-input--invisible harga-input"
                            name="wide"
                            value="{{ old('wide', $product ? $product->wide : $originalProduct->wide ?? 0) }}"
                            style="color:#999999">
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-md-6">
                <div class="form-group mb-3">
                    <label>{{ trans('plugins/ecommerce::products.form.shipping.height') }}
                        ({{ ecommerce_width_height_unit() }})</label>
                    <div class="next-input--stylized">
                        <span
                            class="next-input-add-on next-input__add-on--before">{{ ecommerce_width_height_unit() }}</span>
                        <input type="text" class="next-input input-mask-number next-input--invisible harga-input"
                            name="height"
                            value="{{ old('height', $product ? $product->height : $originalProduct->height ?? 0) }}"
                            style="color:#999999">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if (EcommerceHelper::isEnabledSupportDigitalProducts() &&
        ((!$product &&
            !$originalProduct &&
            request()->input('product_type') == Botble\Ecommerce\Enums\ProductTypeEnum::DIGITAL) ||
            ($originalProduct && $originalProduct->isTypeDigital()) ||
            ($product && $product->isTypeDigital())))
    <div class="mb-3 product-type-digital-management">
        <label for="product_file">{{ trans('plugins/ecommerce::products.digital_attachments.title') }}</label>
        <table class="table border">
            <thead>
                <tr>
                    <th width="40"></th>
                    <th>{{ trans('plugins/ecommerce::products.digital_attachments.file_name') }}</th>
                    <th width="100">{{ trans('plugins/ecommerce::products.digital_attachments.file_size') }}</th>
                    <th width="100">{{ trans('core/base::tables.created_at') }}</th>
                    <th class="text-end" width="100"></th>
                </tr>
            </thead>
            <tbody>
                @if ($product)
                    @foreach ($product->productFiles as $file)
                        <tr>
                            <td>
                                {!! Form::checkbox('product_files[' . $file->id . ']', 0, true, ['class' => 'd-none']) !!}
                                {!! Form::checkbox('product_files[' . $file->id . ']', $file->id, true, [
                                    'class' => 'digital-attachment-checkbox',
                                ]) !!}
                            </td>
                            <td>
                                <div>
                                    <i class="fas fa-paperclip"></i>
                                    <span>{{ $file->basename }}</span>
                                </div>
                            </td>
                            <td>{{ BaseHelper::humanFileSize($file->file_size) }}</td>
                            <td>{{ BaseHelper::formatDate($file->created_at) }}</td>
                            <td class="text-end"></td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <div class="digital_attachments_input">
            <input type="file" name="product_files_input[]" data-id="{{ Str::random(10) }}">
        </div>
        <div class="mt-2">
            <a href=""
                class="digital_attachments_btn">{{ trans('plugins/ecommerce::products.digital_attachments.add') }}</a>
        </div>
    </div>


    <script type="text/x-custom-template" id="digital_attachment_template">
        <tr data-id="__id__">
            <td>
                <a class="text-danger remove-attachment-input"><i class="fas fa-minus-circle"></i></a>
            </td>
            <td>
                <i class="fas fa-paperclip"></i>
				<span>__file_name__</span>
            </td>
            <td>__file_size__</td>
            <td>-</td>
            <td class="text-end">
                <span class="text-warning">{{ trans('plugins/ecommerce::products.digital_attachments.unsaved') }}</span>
            </td>
        </tr>
    </script>

@endif

{{--  <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>  --}}


@if ($product && !$isVariation)
    @php
        $kategori2 = $product->kategori2;
        $kategori3 = $product->kategori3;
    @endphp
    <script>
        var kategori2 = {{ $kategori2 }}
        var kategori3 = {{ $kategori3 }}
        if (!kategori2) {
            $("#kategori2").hide();
            document.getElementById("kategori2").innerHTML = "";
            document.getElementById("kategori3").innerHTML = "";
        }
        if (!kategori3) {
            $("#kategori3").hide();
            document.getElementById("kategori3").innerHTML = "";
        }
        $("#kategoris").hide();
    </script>
@endif

<script>
    $('document').ready(function() {
        let elements = document.querySelectorAll('.harga-input');
        let elements2 = document.querySelectorAll('.harga-input');
        elements.forEach((item) => {
            item.addEventListener('click', event => {
                if (item.value === null) {} else {
                    if (item.value > 0) {
                        item.style.color = "#000000";
                    } else {
                        item.style.color = "#999999";
                        item.value = "";
                    }
                }
            });

        });

        elements2.forEach((item) => {
            item.addEventListener('focusout', event => {
                if (item.value === null) {
                    item.style.color = "#999999";
                    item.value = "0";
                } else {
                    if (item.value > 0) {
                        item.style.color = "#000000";
                    } else {
                        item.style.color = "#999999";
                        item.value = "";
                    }
                }
            });

        });

        if (document.title == 'New product') {
            $("#kategoris").hide();
            $("#kategori2").hide();
            $("#kategori3").hide();

        }

        $.ajax({
            method: 'GET',
            url: '{{ route('marketplace.vendor.products.kategori1') }}',
            success: function(response) {
                var myData = JSON.parse(response);
                var kategorilist = myData.listkategori;
                let jmlkategori = kategorilist.length;
                if (myData.status == 'OK') {
                    document.getElementById("kategori1").innerHTML = "";
                    if (document.title == 'New product') {
                        document.getElementById('kategori1').innerHTML =
                            `<option value='' selected disabled>--Silahkan Pilih--</option>`
                    }
                    jQuery.each(kategorilist, function(index, value) {
                        document.getElementById('kategori1').innerHTML +=
                            "<option data-id=" + value.id + " value=" + value.id + ">" +
                            value.name + "</option>"
                    })
                } else {
                    $("#kategori2").hide();
                    $("#kategori3").hide();
                    {{--  alert(myData.message)  --}}
                }

            }
        })
        document.getElementById('kategori1').addEventListener('change', (e) => {
            $.ajax({
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    _xhks: e.target.value
                },
                url: '{{ route('marketplace.vendor.products.kategori2') }}',
                success: function(response) {
                    var myData = JSON.parse(response);
                    var kategorilist = myData.listkategori;
                    if (myData.status == 'OK') {
                        document.getElementById("kategori2").innerHTML = "";
                        document.getElementById('kategori2').innerHTML =
                            `<option value='' selected disabled>--Silahkan Pilih--</option>`
                        jQuery.each(kategorilist, function(index, value) {
                            document.getElementById('kategori2').innerHTML +=
                                "<option data-id=" + value.id + " value=" + value
                                .id + ">" + value.name + "</option>"
                        })
                        $("#kategori2").show();
                        $("#kategori3").hide();
                    } else {
                        document.getElementById("kategori2").innerHTML = "";
                        document.getElementById("kategori3").innerHTML = "";
                        $("#kategori2").hide();
                        $("#kategori3").hide();
                        {{--  alert(myData.message)  --}}
                    }
                }
            })
        })
        document.getElementById('kategori2').addEventListener('change', (e) => {
            $.ajax({
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    _xhks: e.target.value
                },
                url: '{{ route('marketplace.vendor.products.kategori3') }}',
                success: function(response) {
                    var myData = JSON.parse(response);
                    var kategorilist = myData.listkategori;
                    if (myData.status == 'OK') {
                        document.getElementById("kategori3").innerHTML = "";
                        document.getElementById('kategori3').innerHTML =
                            `<option value='' selected disabled>--Silahkan Pilih--</option>`
                        jQuery.each(kategorilist, function(index, value) {
                            document.getElementById('kategori3').innerHTML +=
                                "<option data-id=" + value.id + " value=" + value
                                .id + ">" + value.name + "</option>"
                        })
                        $("#kategori3").show();
                    } else {
                        document.getElementById("kategori3").innerHTML = "";
                        $("#kategori3").hide();
                        {{--  alert(myData.message)  --}}
                    }
                }
            })
        })

    });
</script>
<script></script>

<script>
    function findTotal() {
        var hpp = document.getElementById('hpp').value;
        var feePercent = {{MarketplaceHelper::getSetting('fee_per_order', 0)}};
        var fee = parseFloat(hpp * (feePercent/100));
        var jual = parseFloat(hpp) + fee;
        document.getElementById('fee').value = fee;
        document.getElementById('price').value = jual;
        if (hpp > 0) {
            document.getElementById("hpp").style.color = "#000000";
            document.getElementById("fee").style.color = "#000000";
            document.getElementById("price").style.color = "#000000";
        } else {
            document.getElementById("hpp").style.color = "#999999";
            document.getElementById("fee").style.color = "#999999";
            document.getElementById("price").style.color = "#999999";
            document.getElementById("hpp").value = "0";
        }
    }
</script>
