@extends(MarketplaceHelper::viewPath('dashboard.layouts.master'))

@section('content')
<div class="ps-card__content">
    {!! Form::open(['route' => 'marketplace.vendor.settings', 'class' => 'ps-form--account-setting', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
        <div class="ps-form__content">
            <ul class="nav nav-tabs ">
                <li class="nav-item">
                    <a href="#tab_information" class="nav-link active" data-bs-toggle="tab">{{ __('General Information') }}</a>
                </li>
                @include('plugins/marketplace::customers.tax-info-tab')
                @include('plugins/marketplace::customers.payout-info-tab')
                {!! apply_filters('marketplace_vendor_settings_register_content_tabs', null, $store) !!}
            </ul>
            
            <div class="tab-content">
                <div class="tab-pane active" id="tab_information">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="shop-name" class="required">{{ __('Shop Name') }}</label>
                                <input class="form-control" name="name" id="shop-name" type="text" value="{{ old('name', $store->name) }}" placeholder="{{ __('Shop Name') }}">
                                @if ($errors->has('name'))
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="shop-company" class="required">{{ __('Company Name') }}</label>
                                <label for="shop-company" class="required">{{ __($store->company) }}</label>
                                <input class="form-control" name="company" id="shop-company" type="text" value="{{ old('company', $store->company) }}" placeholder="{{ __('Company Name') }}">
                                @if ($errors->has('company'))
                                    <span class="text-danger">{{ $errors->first('company') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="shop-phone" class="required">{{ __('Phone Number') }}</label>
                                <input class="form-control" name="phone" id="shop-phone" type="text" value="{{ old('phone', $store->phone) }}" placeholder="{{ __('Shop phone') }}">
                                @if ($errors->has('phone'))
                                    <span class="text-danger">{{ $errors->first('phone') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="shop-email" class="required">{{ __('Email Toko') }}</label>
                                <input class="form-control" name="email" id="shop-email" type="email" value="{{ old('email', $store->email ?: $store->customer->email) }}" placeholder="{{ __('Shop Email') }}">
                                @if ($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <input type="hidden" name="reference_id" value="{{ $store->id }}">
                            <div class="form-group shop-url-wrapper">
                                <label for="shop-url" class="required float-start">{{ __('Link Toko') }}</label>
                                <span class="d-inline-block float-end shop-url-status"></span>
                                <input class="form-control" name="slug" id="shop-url" type="text" value="{{ old('slug', $store->slug) }}" placeholder="{{ __('Shop URL') }}" data-url="{{ route('public.ajax.check-store-url') }}">
                                @if ($errors->has('slug'))
                                    <span class="text-danger">{{ $errors->first('slug') }}</span>
                                @endif
                                <span class="d-inline-block"><small data-base-url="{{ route('public.store', old('slug', '')) }}">{{ route('public.store', old('slug', $store->slug)) }}</small></span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" class="form-control" name="country" id="country" type="text" value="ID" >
                    <div class="row">
                    	<div class="col-sm-6">
                            <div class="form-group">
                            	<label for="address" class="required">{{ __('Alamat') }}</label>                                
                                <input id="address" type="text" class="form-control" name="address" value="{{ old('address', $store->address) }}">
                                {!! Form::error('address', $errors) !!}                                
                            </div>
                        </div>
                        <div class="col-sm-6">
                        	<div class="form-group">
                                    <label for="zip_code" class="required">{{ __('Zip code') }}</label>
                                    <div class="input-group mb-3">
                                    	<input type="text" id="zip_code" name="zip_code" class="form-control @error('zip_code') is-invalid @enderror" maxlength="5"
                                                     value="{{ old('zip_code') ? old('zip_code') : $store->zip_code }}"
                                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');" required>
                                      
                                      <button class="btn btn-outline-secondary" type="button" id="btnkodepos">Cari</button>
                                    </div>                                    
                                    {!! Form::error('zip_code', $errors) !!}
                                </div>
                        </div>                        
                    </div>
                    
                    <div class="row">
                    	<div class="col-sm-6">
                        	<div class="form-group">
                                    <label for="kelurahan" class="required">{{ __('Kelurahan') }}</label>
                                    <select name="kelurahan" id="kelurahan" class="form-control @error('kelurahan') is-invalid @enderror" required>
                                                        <option value="{{ old('kelurahan') ? old('kelurahan') : $store->kelurahan }}" selected>
                                                            {{ old('kelurahan') ? old('kelurahan') : $store->kelurahan }}
                                                        </option>
                                                    </select>
                                    {!! Form::error('kelurahan', $errors) !!}
                                </div>
                        </div>
                        <div class="col-sm-6">
                        	<div class="form-group" >
                                    <label for="kecamatan" class="required">{{ __('Kecamatan') }}</label>
                                    <input id="kecamatan" type="text" class="form-control" name="kecamatan" value="{{ old('kecamatan', $store->kecamatan) }}" >
                                    {!! Form::error('kecamatan', $errors) !!}
                                </div>
                        </div>
                        
                    </div>
                    
                    <div class="row">
                    	<div class="col-sm-6">
                        	<div class="form-group">
                                    <label for="city">{{ __('Kota \ Kabupaten') }}</label>
                                    <input id="city" type="text" class="form-control" name="city" value="{{ old('city', $store->city) }}" >
                                    {!! Form::error('city', $errors) !!}
                                </div>
                        </div>
                        <div class="col-sm-6">
                        	<div class="form-group">
                                    <label for="state">{{ __('Provinsi') }}</label>                                    
                                    <input id="state" type="text" class="form-control" name="state" value="{{ old('state', $store->state) }}">
                                    {!! Form::error('state', $errors) !!}
                                </div>
                        </div>                        
                    </div>
                    
                    <div class="row">
                    	<div class="col-sm-6">
                        	<div class="form-group">
                                    <label for="city">{{ __('Nomor KTP') }}</label>
                                    <input id="idktp" type="text" class="form-control" name="idktp" value="{{ old('idktp', $store->idktp) }}" >
                                    {!! Form::error('idktp', $errors) !!}
                                </div>
                        </div>                                           
                    </div>
                    
                    <div class="row">
                        <div class="col-sm">
                            <div class="form-group">
                                <label for="logo">{{ __('Foto KTP') }}</label>
                                {!! Form::customImage('ktp', old('ktp', $store->ktp)) !!}
                                {!! Form::error('ktp', $errors) !!}
                            </div>                            
                        </div>
                        <div class="col-sm">
                            <div class="form-group">
                                <label for="logo">{{ __('Logo') }}</label>
                                {!! Form::customImage('logo', old('logo', $store->logo)) !!}
                                {!! Form::error('logo', $errors) !!}
                            </div>                         
                        </div>
                        <div class="col-sm">
                        	<div class="form-group">
                                <label for="logo">{{ __('Cover Image') }}</label>
                                {!! Form::customImage('covers', old('covers', $store->covers)) !!}
                                {!! Form::error('covers', $errors) !!}
                            </div>   	
                                                    
                        </div>
                    </div>                    

                    <div class="form-group">
                        <label for="description">{{ __('Description') }}</label>
                        <textarea id="description" class="form-control" name="description" rows="3">{{ old('description', $store->description) }}</textarea>
                        {!! Form::error('description', $errors) !!}
                    </div>

                    <div class="form-group">
                        <label for="content">{{ __('Content') }}</label>
                        {!! Form::customEditor('content', old('content', $store->content)) !!}
                        {!! Form::error('content', $errors) !!}
                    </div>
                </div>
                @include('plugins/marketplace::customers.tax-form', ['model' => $store->customer])
                @include('plugins/marketplace::customers.payout-form', ['model' => $store->customer])
                {!! apply_filters('marketplace_vendor_settings_register_content_tab_inside', null, $store) !!}
            </div>


            <div class="form-group text-center">
                <div class="form-group submit">
                    <div class="ps-form__submit text-center">
                        <button class="ps-btn success">{{ __('Save settings') }}</button>
                    </div>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
</div>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $('document').ready(function() {
        $('textarea').each(function() {
            $(this).val($(this).val().trim());
        });
		
		document.getElementById("btnkodepos").onclick = function () {
			var a = document.getElementById("zip_code").value; 
			$.ajax({
				method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    _xhks: a
                },
                url: '{{ route('marketplace.vendor.wilayah') }}',
				success: function(response){
					var myData = JSON.parse(response);
					var kelurahanlist = myData.listkelurahan;
					let jmlkelurahan =  kelurahanlist.length;
					if (myData.status == 'OK') {
						document.getElementById("kecamatan").value = myData.kecamatan;
						document.getElementById("city").value = myData.kota;
						document.getElementById("state").value = myData.provinsi;
						document.getElementById("kelurahan").innerHTML = "";
						if (jmlkelurahan > 1)
						{
							document.getElementById('kelurahan').innerHTML =
                        	`<option value='' selected disabled>--Silahkan Pilih--</option>`
						}
						jQuery.each(kelurahanlist, function(index, value){
							document.getElementById('kelurahan').innerHTML +=
                        	"<option data-id="+ value.kelurahan +" value="+ value.kelurahan +">"+ value.kelurahan +"</option>"
						})
					} else {
						alert(myData.message)
					}										
				}
			})
		}
    });

</script>

<script>
	var input = document.getElementById("kodepos");
	
	if (input > 0) 
	{
		input.addEventListener("keypress", function(event) {
  			if (event.key === "Enter") 
			{
    			event.preventDefault();
    			document.getElementById("myButton").click();
  			}	
		})
		
	}
</script>
@stop
