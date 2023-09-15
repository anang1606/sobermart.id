<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Atribut :atribut harus diterima.',
     'accepted_if' => ' :Attribute harus diterima bila :other adalah :value.',
     'active_url' => ' :attribute bukan URL yang valid.',
     'after' => ' :attribute harus berupa tanggal setelah :date.',
     'after_or_equal' => ' :Attribute harus berupa tanggal setelah atau sama dengan :date.',
     'alpha' => ' :Attribute hanya boleh berisi huruf.',
     'alpha_dash' => ' :Attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
     'alpha_num' => ' :Attribute hanya boleh berisi huruf dan angka.',
     'array' => ' :attribute harus berupa array.',
     'ascii' => ' :Attribute hanya boleh berisi karakter dan simbol alfanumerik byte tunggal.',
     'before' => ' :attribute harus berupa tanggal sebelum :date.',
     'before_or_equal' => ' :Attribute harus berupa tanggal sebelum atau sama dengan :date.',
     'antara' => [
         'array' => ' :attribute harus berada di antara item :min dan :max.',
         'file' => ' :Attribute harus antara :min dan :max kilobyte.',
         'numeric' => 'Atribut :atribut harus berada di antara :min dan :max.',
         'string' => ':Attribute harus berada di antara karakter :min dan :max.',
     ],
     'boolean' => 'Bidang :attribute harus benar atau salah.',
     'confirmed' => 'Konfirmasi :attribute tidak cocok.',
     'current_password' => 'Kata sandi salah.',
     'date' => 'Atribut :atribut bukan tanggal yang valid.',
     'date_equals' => ' :attribute harus berupa tanggal yang sama dengan :date.',
     'date_format' => ' :Atribut tidak cocok dengan format :format.',
     'decimal' => ' :Attribute harus mempunyai :desimal desimal.',
     'declined' => ' :Attribute harus ditolak.',
     'declined_if' => ' :Attribute harus ditolak jika :other adalah :value.',
     'different' => ' :attribute dan :other harus berbeda.',
     'digits' => ' :Attribute harus berupa :digits digit.',
     'digits_between' => 'Atribut :atribut harus berada di antara angka :min dan :max.',
     'dimensions' => ' :Attribute memiliki dimensi gambar yang tidak valid.',
     'distinct' => 'Bidang :attribute mempunyai nilai duplikat.',
     'doesnt_end_with' => ' :Attribute tidak boleh diakhiri dengan salah satu dari berikut ini: :values.',
     'doesnt_start_with' => ' :Attribute tidak boleh dimulai dengan salah satu dari berikut ini: :values.',
     'email' => ':attribute harus berupa alamat email yang valid.',
     'ends_with' => ' :Attribute harus diakhiri dengan salah satu dari yang berikut: :values.',
     'enum' => ' :Atribut yang dipilih tidak valid.',
     'exists' => ' :Atribut yang dipilih tidak valid.',
     'file' => ' :attribute harus berupa file.',
     'filled' => 'Kolom :attribute harus mempunyai nilai.',
     'gt' => [
         'array' => 'Item :attribute harus lebih dari :value.',
         'file' => ' :Attribute harus lebih besar dari :value kilobyte.',
         'numeric' => ' :Attribute harus lebih besar dari :value.',
         'string' => 'Karakter :attribute harus lebih besar dari :value.',
     ],
     'gte' => [
         'array' => ' :attribute harus mempunyai item :value atau lebih.',
         'file' => ' :Attribute harus lebih besar atau sama dengan :value kilobyte.',
         'numeric' => 'Atribut :atribut harus lebih besar atau sama dengan :nilai.',
         'string' => 'Karakter :attribute harus lebih besar atau sama dengan :value.',
     ],
     'image' => ' :Attribute harus berupa gambar.',
     'in' => 'Atribut yang dipilih tidak valid.',
     'in_array' => 'Kolom :attribute tidak ada di :other.',
     'integer' => 'Isi :attribute harus berupa integer.',
     'ip' => ' :attribute harus berupa alamat IP yang valid.',
     'ipv4' => ' :Attribute harus berupa alamat IPv4 yang valid.',
     'ipv6' => ':atribut harus berupa alamat IPv6 yang valid.',
     'json' => ' :attribute harus berupa string JSON yang valid.',
     'huruf kecil' => 'Atribut :atribut harus huruf kecil.',
     'lt' => [
         'array' => 'Item :attribute harus kurang dari :value.',
         'file' => ' :Attribute harus kurang dari :value kilobyte.',
         'numeric' => ' :Attribute harus lebih kecil dari :value.',
         'string' => 'Karakter :attribute harus kurang dari :value.',
     ],
     'lte' => [
         'array' => 'Item :attribute tidak boleh lebih dari :value.',
         'file' => ' :Attribute harus kurang dari atau sama dengan :value kilobyte.',
         'numeric' => 'Atribut :atribut harus lebih kecil atau sama dengan :nilai.',
         'string' => 'Karakter :attribute harus kurang dari atau sama dengan :value.',
     ],
     'mac_address' => ' :attribute harus berupa alamat MAC yang valid.',
     'maks' => [
         'array' => 'Item :attribute tidak boleh lebih dari :max.',
         'file' => ' :Attribute tidak boleh lebih besar dari :max kilobyte.',
         'numeric' => ' :Attribute tidak boleh lebih besar dari :max.',
         'string' => ' :Attribute tidak boleh lebih besar dari :max karakter.',
     ],
     'max_digits' => ' :Attribute tidak boleh lebih dari :max digit.',
     'mimes' => 'Atribut :atribut harus ada',
	 'mimetypes' => ' :attribute harus berupa file dengan tipe: :values.',
     'menit' => [
         'array' => ' :attribute harus memiliki setidaknya :min item.',
         'file' => ' :Attribute minimal harus :min kilobyte.',
         'numeric' => ':Attribute minimal harus :min.',
         'string' => ' :Attribute minimal harus terdiri dari :min karakter.',
     ],
     'min_digits' => ' :Attribute harus memiliki setidaknya :min digit.',
     'missing' => 'Kolom :attribute harus hilang.',
     'missing_if' => 'Kolom :attribute harus hilang jika :other adalah :value.',
     'missing_unless' => 'Kolom :attribute harus hilang kecuali :other adalah :value.',
     'missing_with' => 'Kolom :attribute harus hilang jika :values ada.',
     'missing_with_all' => 'Bidang :attribute harus hilang jika :values ada.',
     'multiple_of' => ' :Atribut harus kelipatan :nilai.',
     'not_in' => 'Atribut yang dipilih tidak valid.',
     'not_regex' => 'Format :attribute tidak valid.',
     'numeric' => 'Isi :attribute harus berupa angka.',
     'kata sandi' => [
         'letters' => ' :attribute harus mengandung setidaknya satu huruf.',
         'mixed' => ' :attribute harus mengandung setidaknya satu huruf besar dan satu huruf kecil.',
         'numbers' => ' :attribute harus berisi minimal satu angka.',
         'symbols' => ' :attribute harus mengandung setidaknya satu simbol.',
         'uncompromised' => ' :Attribute yang diberikan telah muncul dalam kebocoran data. Silakan pilih :atribut yang lain.',
     ],
     'present' => 'Kolom :attribute harus ada.',
     'prohibited' => 'Bidang :attribute dilarang.',
     'prohibited_if' => 'Bidang :attribute dilarang jika :other adalah :value.',
     'prohibited_unless' => 'Kolom :attribute dilarang kecuali :other ada di :values.',
     'prohibits' => 'Bidang :attribute melarang :other untuk hadir.',
     'regex' => 'Format :attribute tidak valid.',
     'required' => 'Kolom :attribute wajib diisi.',
     'required_array_keys' => 'Kolom :attribute harus berisi entri untuk: :values.',
     'required_if' => 'Kolom :attribute wajib diisi jika :other adalah :value.',
     'required_if_accepted' => 'Kolom :attribute wajib diisi jika :other diterima.',
     'required_unless' => 'Kolom :attribute wajib diisi kecuali :other ada di :values.',
     'required_with' => 'Kolom :attribute wajib diisi bila :values ada.',
     'required_with_all' => 'Bidang :attribute diperlukan jika :values ada.',
     'required_without' => 'Bidang :attribute diperlukan bila :values tidak ada.',
     'required_without_all' => 'Kolom :attribute wajib diisi jika :values tidak ada.',
     'same' => ' :attribute dan :other harus cocok.',
     'ukuran' => [
         'array' => ' :attribute harus berisi item :size.',
         'file' => ' :Attribute harus :ukuran kilobyte.',
         'numeric' => ' :Attribute harus :size.',
         'string' => ' :Attribute harus berupa karakter :size.',
     ],
     'starts_with' => ' :Attribute harus diawali dengan salah satu dari berikut ini: :values.',
     'string' => ':atribut harus berupa string.',
     'timezone' => ' :attribute harus berupa zona waktu yang valid.',
     'unique' => ' :attribute sudah dipakai.',
     'uploaded' => ' :Attribute gagal diunggah.',
     'uppercase' => ' :Attribute harus huruf besar.',
     'url' => ' :attribute harus berupa URL yang valid.',
     'ulid' => ' :attribute harus berupa ULID yang valid.',
     'uuid' => ' :Attribute harus berupa UUID yang valid.',
	
	

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
