<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use BaseHelper;
use Botble\ACL\Traits\RegistersUsers;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Carbon\Carbon;
use EcommerceHelper;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use SeoHelper;
use Theme;
use Botble\Ecommerce\Models\Customer;
use Botble\Payment\Models\Payment;
use Botble\Ecommerce\Models\PaketMaster;
use Botble\Ecommerce\Models\MemberPaket;
use Illuminate\Validation\Rule;
use URL, DB;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected string $redirectTo = '/';

    protected CustomerInterface $customerRepository;

    public function __construct(CustomerInterface $customerRepository)
    {
        $this->middleware('customer.guest');
        $this->customerRepository = $customerRepository;
    }

    public function showRegistrationForm()
    {
        SeoHelper::setTitle(__('Register'));

        Theme::breadcrumb()->add(__('Home'), route('public.index'))->add(__('Register'), route('customer.register'));

        if (!session()->has('url.intended')) {
            if (!in_array(url()->previous(), [route('customer.login'), route('customer.register')])) {
                session(['url.intended' => url()->previous()]);
            }
        }

        $memberPaket = PaketMaster::get();

        return Theme::scope(
            'ecommerce.customers.register',
            compact('memberPaket'),
            'plugins/ecommerce::themes.customers.register'
        )
            ->render();
    }

    public function checkRefferal(Request $request, BaseHttpResponse $response)
    {
        $data = $request->input();
        $check_code = MemberPaket::where('code', $data['code'])->get();

        if (count($check_code) > 0) {
            $html = '';
            foreach ($check_code as $code) {
                $paket = PaketMaster::where('id', $code->id_paket)->first();

                if ($paket) {
                    $nominal = format_price($paket->nominal);
                    $html .= '<div class="col-md-6 mb-3">';
                    $html .= '<div class="DjhkItMLcf" style="height: 100%">';
                    $html .= "<label for='small-$paket->id' class='DQXPDCmiQw' style='height: 100%'>";
                    $html .= '<div class="pAEwcwpIAP">';
                    $html .= '<div class="LFNFvicbva">';
                    $html .= '<div class="vhrStrXljR"></div>';
                    $html .= '<div class="jppTUaEgYy">';
                    $html .= "<h5 class='zqYgwSlvmX'>$paket->name</h5>";
                    $html .= "<span class='dICQuslKpk d-flex flex-column'><span>$nominal</span></span>";
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= "<input id='small-$paket->id' type='radio' name='paket' value='$paket->id' class='haHukqnIXN'>";
                    $html .= '</div>';
                    // $html .= '<div class="line-hr"></div>';
                    // $html .= '<div class="relqpLAcxs oUYXsUylHm">';
                    //     $html .= '<ul class="URAXAoOcDM">';
                    //         foreach($paket->details as $detail){
                    //             $html .= '<li class="AGZeUBWgnP">';
                    //                 $html .= '<div class="dec-icons" style="width: 15px;height: 15px;"></div>';
                    //                 $html .= "<div class='dec-details'>$detail->content</div>";
                    //             $html .= '</li>';
                    //         }
                    //     $html .= '</ul>';
                    // $html .= '</div>';
                    $html .= '</label>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
            }
            return $response->setData($html);
        } else {
            $html = '';
            $memberPaket = PaketMaster::with('details')->get();
            foreach ($memberPaket as $paket) {
                $nominal = format_price($paket->nominal);
                $html .= '<div class="col-md-6 mb-3">';
                $html .= '<div class="DjhkItMLcf" style="height: 100%">';
                $html .= "<label for='small-$paket->id' class='DQXPDCmiQw' style='height: 100%'>";
                $html .= '<div class="pAEwcwpIAP">';
                $html .= '<div class="LFNFvicbva">';
                $html .= '<div class="vhrStrXljR"></div>';
                $html .= '<div class="jppTUaEgYy">';
                $html .= "<h5 class='zqYgwSlvmX'>$paket->name</h5>";
                $html .= "<span
                                            class='dICQuslKpk d-flex flex-column'><span>$nominal</span></span>";
                $html .= '</div>';
                $html .= '</div>';
                $html .= "<input id='small-$paket->id' type='radio' name='paket' value='$paket->id'
                                    class='haHukqnIXN'>";
                $html .= '</div>';
                // $html .= '<div class="line-hr"></div>';
                // $html .= '<div class="relqpLAcxs oUYXsUylHm">';
                // $html .= '<ul class="URAXAoOcDM">';
                //     foreach($paket->details as $detail){
                //         $html .= '<li class="AGZeUBWgnP">';
                //         $html .= '<div class="dec-icons" style="width: 15px;height: 15px;"></div>';
                //         $html .= "<div class='dec-details'>$detail->content</div>";
                //         $html .= '</li>';
                //     }
                //     $html .= '</ul>';
                // $html .= '</div>';
                $html .= '</label>';
                $html .= '</div>';
                $html .= '</div>';
            }
            return $response->setError()->setData($html);
        }
    }

    private function member_code()
    {
        $data = MemberPaket::max('code');
        // $data = Member::max('code');
        if ($data) {
            $urutan = (int)substr($data, 11, 5);
            $urutan++;
        } else {
            $urutan = "00001";
        }
        $letter = date('Ymd');
        $code = $letter . sprintf("%05s", $urutan);
        return $code;
    }

    private function member_unique()
    {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; // Huruf kapital yang mungkin
        $randomLetter = $letters[rand(0, strlen($letters) - 1)]; // Pilih huruf kapital secara acak
        $randomDigits = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT); // Angka acak enam digit

        $data = MemberPaket::max('code');
        if ($data) {
            $urutan = (int)substr($data, -5); // Ambil 5 digit terakhir dari kode yang sudah ada
            $urutan++;
        } else {
            $urutan = 1;
        }

        $code = $randomLetter . $randomDigits . str_pad($urutan, 5, $letters, STR_PAD_LEFT);

        return $code;
    }

    public function register(Request $request, BaseHttpResponse $response)
    {
        $this->validator($request->input())->validate();

        do_action('customer_register_validation', $request);

        if ((int)$request->is_vendor === 0 && $request->referral_code !== '') {
            $validator = Validator::make($request->all(), [
                'referral_code' => [
                    'nullable',
                    Rule::exists('ec_customer_pakets', 'code')
                        ->where(function ($query) {
                            $query->whereNull('ec_customer_pakets.deleted_at');
                        }),
                    function ($attribute, $value, $fail) {
                        $check_code = MemberPaket::where('code', $value)->first();
                        if ($check_code) {
                            $parent_total = Customer::where('id', $check_code->user_id)->count();
                            if ($parent_total > 5) {
                                $fail('Leader has reached the max limit!!');
                            } else if ($check_code) {
                                $parent = Customer::where('id', $check_code->user_id)->first();
                                if ($parent->level > 8) {
                                    $fail('Leader has reached the max limit!!');
                                }
                            }
                        } else {
                            $fail('Leader Code not found!!');
                        }
                    },
                ],
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }
        DB::beginTransaction();
        try {
            $data = $request->input();

            $paket_member = MemberPaket::where('code', $request->referral_code)->first();
            if ($paket_member)
                $parent = Customer::where('id', $paket_member->user_id)->first();

            $data['parent'] = null;
            $data['level'] = ($request->referral_code) ? ((int)$parent->level + 1) : 0;
            $paket = $request->paket;

            // $data['password'] = \Hash::make($data['password']);
            $customer = $this->create($data);
            if ($customer) {
                if ((int)$request->is_vendor === 0) {
                    if ($request->paket) {
                        $code = $this->member_code();
                        $member_paket = new MemberPaket();
                        $member_paket->user_id = $customer->id;
                        $member_paket->code = $code;
                        $member_paket->id_paket = $paket;
                        $member_paket->is_active = 1;
                        $member_paket->parent = ($request->referral_code) ? $parent->id : null;
                        $member_paket->uuid = $this->member_unique();
                        if ($member_paket->save()) {
                            $get_paket = PaketMaster::where('id', $paket)->first();
                            $token = bin2hex(random_bytes(75 / 2));

                            $create_payment = new Payment;
                            $create_payment->user_id = 0;
                            $create_payment->charge_id = $token;
                            $create_payment->bank = 'bca';
                            $create_payment->va_number = 1;
                            $create_payment->payment_channel = 'bank_transfer';
                            $create_payment->status = 'pending';
                            $create_payment->currency = 'IDR';
                            $create_payment->customer_type = 'Botble\Ecommerce\Models\Customer';
                            $create_payment->payment_type = 'confirm';
                            $create_payment->customer_id = $customer->id;
                            $create_payment->amount = $get_paket->nominal;
                            $create_payment->order_id = $member_paket->id;
                            $create_payment->type_status = 'paket';
                            $create_payment->expiry_time = Carbon::now()->addDay(1);
                            $create_payment->save();
                        }
                    }
                }

                event(new Registered($customer));

                if (EcommerceHelper::isEnableEmailVerification()) {
                    DB::commit();
                    return $this->registered($request, $customer)
                        ?: $response
                        ->setNextUrl(route('customer.login'))
                        ->setMessage(__('We have sent you an email to verify your email. Please check and confirm your email address!'));
                }

                $customer->confirmed_at = Carbon::now();
                $this->customerRepository->createOrUpdate($customer);
                DB::commit();
                $this->guard()->login($customer);
                // return $response->setNextUrl($this->redirectPath())->setMessage(__('Registered successfully!'));
                return $response->setNextUrl(((int)$request->is_vendor === 0 && (int)$paket !== 0) ? route('customer.payments') : $this->redirectPath())->setMessage(__('Registered successfully!'));
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput();
        }
    }

    private function createPayment($payloads)
    {
        $ch = curl_init();
        $secret_key = "JDJ5JDEzJFZJOEUzREhPRDBEUDB1aXo4MS5EVC4zMUJGS0Z4T1g1a1liWHB5MXlZTmNKYUJ3VFZlSmZx";

        curl_setopt($ch, CURLOPT_URL, "https://bigflip.id/big_sandbox_api/v2/pwf/bill");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_POST, TRUE);

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payloads));

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/x-www-form-urlencoded"
        ));

        curl_setopt($ch, CURLOPT_USERPWD, $secret_key . ":");

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }

    protected function requestMidtrans(?object $data, $customer)
    {
        $time = gettimeofday();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.sandbox.midtrans.com/v2/charge',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "payment_type": "bank_transfer",
                "transaction_details": {
                    "gross_amount": ' . (int)$data->paket . ',
                    "order_id": "order-101c-' . $time["usec"] . '"
                },
                "customer_details": {
                    "email": "' . $customer->email . '",
                    "first_name": "' . $customer->name . '",
                    "last_name": "",
                    "phone": ""
                },
                "item_details": [
                    {
                        "id": "' . base64_encode((int)$data->paket) . '",
                        "price": ' . (int)$data->paket . ',
                        "quantity": 1,
                        "name": "Packages join with us"
                    }
                ],
                "bank_transfer": {
                    "bank": "bca"
                }
            }',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Basic U0ItTWlkLXNlcnZlci1Db1cweDZQZTlLY1o1MGs3QWhvelBRYU06'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return (object)json_decode($response, true);
    }

    protected function validator(array $data)
    {
        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:ec_customers',
            'password' => 'required|min:6|confirmed',
        ];

        if (is_plugin_active('captcha') && setting('enable_captcha') && get_ecommerce_setting(
            'enable_recaptcha_in_register_page',
            0
        )) {
            $rules += ['g-recaptcha-response' => 'required|captcha'];
        }

        if (request()->has('agree_terms_and_policy')) {
            $rules['agree_terms_and_policy'] = 'accepted:1';
        }

        $attributes = [
            'name' => __('Name'),
            'email' => __('Email'),
            'password' => __('Password'),
            'g-recaptcha-response' => __('Captcha'),
            'agree_terms_and_policy' => __('Term and Policy'),
        ];

        return Validator::make($data, apply_filters('ecommerce_customer_registration_form_validation_rules', $rules), [
            'g-recaptcha-response.required' => __('Captcha Verification Failed!'),
            'g-recaptcha-response.captcha' => __('Captcha Verification Failed!'),
        ], $attributes);
    }

    protected function create(array $data)
    {
        return $this->customerRepository->create([
            'name' => BaseHelper::clean($data['name']),
            'email' => BaseHelper::clean($data['email']),
            'password' => Hash::make($data['password']),
            'parent' => $data['parent'],
            'level' => $data['level'],
            'is_vendor' => $data['is_vendor'],
        ]);
    }

    protected function guard()
    {
        return auth('customer');
    }

    public function confirm(int $id, Request $request, BaseHttpResponse $response, CustomerInterface $customerRepository)
    {
        if (!URL::hasValidSignature($request)) {
            abort(404);
        }

        $customer = $customerRepository->findOrFail($id);

        $customer->confirmed_at = Carbon::now();
        $this->customerRepository->createOrUpdate($customer);

        $this->guard()->login($customer);

        return $response
            ->setNextUrl(route('customer.overview'))
            ->setMessage(__('You successfully confirmed your email address.'));
    }

    public function resendConfirmation(
        Request $request,
        CustomerInterface $customerRepository,
        BaseHttpResponse $response
    ) {
        $customer = $customerRepository->getFirstBy(['email' => $request->input('email')]);

        if (!$customer) {
            return $response
                ->setError()
                ->setMessage(__('Cannot find this customer!'));
        }

        $customer->sendEmailVerificationNotification();

        return $response
            ->setMessage(__('We sent you another confirmation email. You should receive it shortly.'));
    }

    public function getVerify()
    {
        return view('plugins/ecommerce::themes.customers.verify');
    }
}
