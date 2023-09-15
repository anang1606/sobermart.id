<?php

namespace Theme\Farmart\Http\Controllers;
use Botble\Theme\Http\Controllers\PublicController;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\BankList;
use Botble\Marketplace\Models\Store;
use Botble\Base\Supports\Avatar;
use RvMedia;
use EcommerceHelper;

class ApiController extends PublicController
{
    public function productDetails($id){
        $condition = [
            'ec_products.id' => $id,
        ];

        $get_product = get_products(array_merge([
            'condition' => $condition,
            'take' => 1,
            'with' => [
                'slugable',
                'tags',
                'tags.slugable',
                'categories',
                'categories.slugable',
                'options',
                'options.values',
            ],
        ], EcommerceHelper::withReviewsParams()));
        // $get_product = Product::where('id',$id)->first();
        $get_product = $get_product->original_product;
        $get_product->url = $get_product->url;
        $get_product->reviews_avg = $get_product->reviews_avg;
        $get_product->reviews_count = $get_product->reviews_count;
        $get_product->image = RvMedia::url($get_product->image);
        return response()->json($get_product);
    }

    public function getVendorById($id){
        $get_product = Store::where('id',$id)->first();

        // $get_product->image = RvMedia::url($get_product->image);
        if ($get_product->logo) {
            $get_product->logo = RvMedia::getImageUrl($get_product->logo, 'thumb');
        }

        try {
            $get_product->logo = (new Avatar())->create($get_product->name)->toBase64();
        } catch (Exception) {
            $get_product->logo = RvMedia::getDefaultImage();
        }
        return response()->json($get_product);
    }

    public function getVendor($id){
        $get_product = Store::where('customer_id',$id)->first();

        // $get_product->image = RvMedia::url($get_product->image);
        if ($get_product->logo) {
            $get_product->logo = RvMedia::getImageUrl($get_product->logo, 'thumb');
        }

        try {
            $get_product->logo = (new Avatar())->create($get_product->name)->toBase64();
        } catch (Exception) {
            $get_product->logo = RvMedia::getDefaultImage();
        }
        return response()->json($get_product);
    }

    public function getImage($image){
        return RvMedia::url($image);
    }

    public function manualPaymentBank(){
        $get_bank = BankList::get();

        $banks = [];
        $trueCount = 0;
        $totalBanks = count($get_bank);
        foreach ($get_bank as $key => $bank) {
            $bank_['bank_code'] = $bank->bank_code;
            $bank_['bank_name'] = $bank->bank_name;
            $bank_['fee'] = $bank->fee;
            $bank_['icons'] = RvMedia::url($bank->icons);
            $bank_['type'] = 'bank_transfer';

            // Cek apakah nilai $trueCount sudah mencapai 3
            if ($trueCount < 3 && $totalBanks > 1) {
                $bank_['isDefault'] = rand(0, 1) === 1; // Menghasilkan nilai acak true atau false

                // Jika $bank_['isDefault'] adalah true, tambahkan 1 ke $trueCount
                if ($bank_['isDefault']) {
                    $trueCount++;
                }
            } else {
                $bank_['isDefault'] = true; // Jika $trueCount sudah mencapai 3 atau hanya ada satu bank, set isDefault sebagai true
            }

            $bank_['information'] = [
                'Masukkan info terkait di atas sesuai pada buku tabungan.',
                'Untuk pembayaran lewat teller, isi *"No. Rekening"* dengan *0000*. Lalu, isi *"Nama Pemilik Rekening"*  dengan *nama Anda*.',
                'Total tagihan sudah termasuk biaya transaksi Rp3.000.'
            ];

            $banks[] = $bank_;
        }
        return $banks;
    }
}
