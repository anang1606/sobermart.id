<?php
$category_expl = explode(',', Arr::get($attributes, 'category_id'));
?>
<div class="form-group">
    <label class="control-label">{{ __('Select category') }}</label>
    <div class="ui-select-wrapper form-group">
        <select name="category_id" id="category_id" class="ui-select">
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @if ($category->id == $category_expl[0]) selected @endif>
                    {!! BaseHelper::clean($category->indent_text) !!} {!! BaseHelper::clean($category->name) !!}</option>
            @endforeach
        </select>
        <svg class="svg-next-icon svg-next-icon-size-16">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
        </svg>
    </div>
</div>
<div class="form-group" id="form_category_child_1" style="display: none">
    <label class="control-label">{{ __('Select category') }}</label>
    <div class="ui-select-wrapper form-group">
        <select name="category_id" id="category_child_1" class="ui-select">

        </select>
        <svg class="svg-next-icon svg-next-icon-size-16">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
        </svg>
    </div>
</div>
<div class="form-group" id="form_category_child_2" style="display: none">
    <label class="control-label">{{ __('Select category') }}</label>
    <div class="ui-select-wrapper form-group">
        <select name="category_id" id="category_child_2" class="ui-select">

        </select>
        <svg class="svg-next-icon svg-next-icon-size-16">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
        </svg>
    </div>
</div>

<div class="form-group" style="display: none">
    <label class="control-label">{{ __('Limit number of categories') }}</label>
    <input type="number" name="number_of_categories" value="{{ Arr::get($attributes, 'number_of_categories', 3) }}"
        class="form-control" placeholder="{{ __('Default: 3') }}">
</div>

<div class="form-group">
    <label class="control-label">{{ __('Limit number of products') }}</label>
    <input type="number" min="1" name="limit" value="{{ Arr::get($attributes, 'limit') }}" class="form-control"
        placeholder="{{ __('Unlimited by default') }}">
</div>

{!! Theme::partial('shortcodes.includes.autoplay-settings', compact('attributes')) !!}

@if (count($category_expl) > 1)
    <script>
        let selected_category_2 = {{ $category_expl[1] }}
        $.ajax({
            method: 'POST',
            url: "{{ route('short-codes.get-category-2') }}",
            dataType: 'json',
            data: {
                category: '{{ $category_expl[0] }}'
            },
            success: function(response) {
                var myData = response;
                if (myData.data.length > 0) {
                    document.getElementById('category_child_1').innerHTML =
                        `<option value='' selected disabled>--Silahkan Pilih--</option>`
                    jQuery.each(myData.data, function(index, value) {
                        if (selected_category_2 === value.id) {
                            document.getElementById('category_child_1').innerHTML +=
                                "<option data-id=" + value.id + " selected value=" + value.id + ">" +
                                value.name + "</option>"
                        } else {
                            document.getElementById('category_child_1').innerHTML +=
                                "<option data-id=" + value.id + " value=" + value.id + ">" +
                                value.name + "</option>"
                        }
                    })
                    $("#form_category_child_1").show();
                    $("#form_category_child_2").hide();
                } else {
                    $("#form_category_child_1").hide();
                    $("#form_category_child_2").hide();
                }
            },
            error: function(err) {
                console.log(err)
            }
        });
    </script>
@endif

@if (count($category_expl) > 2)
    <script>
        let selected_category_3 = {{ $category_expl[2] }}
        $.ajax({
            method: 'POST',
            url: "{{ route('short-codes.get-category-3') }}",
            dataType: 'json',
            data: {
                category: '{{ $category_expl[1] }}'
            },
            success: function(response) {
                var myData = response;
                if (myData.data.length > 0) {
                    document.getElementById('category_child_2').innerHTML =
                        `<option value='' selected disabled>--Silahkan Pilih--</option>`
                    jQuery.each(myData.data, function(index, value) {
                        if (selected_category_3 === value.id) {
                            document.getElementById('category_child_2').innerHTML +=
                                "<option data-id=" + value.id + " selected value=" + value.id + ">" +
                                value.name + "</option>"
                        } else {
                            document.getElementById('category_child_2').innerHTML +=
                                "<option data-id=" + value.id + " value=" + value.id + ">" +
                                value.name + "</option>"
                        }
                    })
                    $("#form_category_child_2").show();
                } else {
                    $("#form_category_child_2").hide();
                }
            },
            error: function(err) {
                console.log(err)
            }
        });
    </script>
@endif

<script>
    document.getElementById('category_id').addEventListener('change', function(e) {
        const category_id = e.target.value
        $.ajax({
            method: 'POST',
            url: "{{ route('short-codes.get-category-2') }}",
            dataType: 'json',
            data: {
                category: category_id
            },
            success: function(response) {
                var myData = response;
                if (myData.data.length > 0) {
                    document.getElementById('category_child_1').innerHTML =
                        `<option value='' selected disabled>--Silahkan Pilih--</option>`
                    jQuery.each(myData.data, function(index, value) {
                        document.getElementById('category_child_1').innerHTML +=
                            "<option data-id=" + value.id + " value=" + value.id + ">" +
                            value.name + "</option>"
                    })
                    $("#form_category_child_1").show();
                    $("#form_category_child_2").hide();
                } else {
                    $("#form_category_child_1").hide();
                    $("#form_category_child_2").hide();
                }
            },
            error: function(err) {
                console.log(err)
            }
        });
    })
    document.getElementById('form_category_child_1').addEventListener('change', function(e) {
        const category_id = e.target.value
        $.ajax({
            method: 'POST',
            url: "{{ route('short-codes.get-category-3') }}",
            dataType: 'json',
            data: {
                category: category_id
            },
            success: function(response) {
                var myData = response;
                if (myData.data.length > 0) {
                    document.getElementById('category_child_2').innerHTML =
                        `<option value='' selected disabled>--Silahkan Pilih--</option>`
                    jQuery.each(myData.data, function(index, value) {
                        document.getElementById('category_child_2').innerHTML +=
                            "<option data-id=" + value.id + " value=" + value.id + ">" +
                            value.name + "</option>"
                    })
                    $("#form_category_child_2").show();
                } else {
                    $("#form_category_child_2").hide();
                }
            },
            error: function(err) {
                console.log(err)
            }
        });
    })
</script>
