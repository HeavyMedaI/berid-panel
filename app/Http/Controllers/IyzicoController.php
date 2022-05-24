<?php
/*
 * File name: IyzicoController.php
 * Last modified: 2021.05.07 at 19:12:31
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Http\Controllers;

use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
#use Stripe\Exception\ApiErrorException;
#use Stripe\PaymentIntent;

class IyzicoController extends ParentPaymentController
{

    private $iyzicoPaymentMethod;
    private $options;

    public function __init()
    {
        $this->iyzicoPaymentMethod = $this->paymentMethodRepository->findByField('route', "/Iyzico")->first();
        $this->paymentMethodId = $this->iyzicoPaymentMethod->id;

        $this->options = new \Iyzipay\Options();
        $this->options->setApiKey(setting('iyzico_api_token'));
        $this->options->setSecretKey(setting('iyzico_secret_key'));
        $this->options->setBaseUrl(setting('iyzico_api_url'));
    }

    public function index()
    {
        return view('home');
    }

    public function checkout(Request $request)
    {
        $this->advert = $this->advertRepository->findWithoutFail($request->get('advert_id'));
        if (empty($this->advert)) {
            Flash::error("Error processing Iyzico payment for your advert");
            return redirect(route('payments.failed'));
        }
        if ($this->advert->payment == null) {
            $this->startTransaction();
        }
        if ($this->advert->payment->payment_method_id != $this->paymentMethodId) {
            $this->advert->payment->payment_method_id = $this->iyzicoPaymentMethod->id;
            $this->paymentRepository->update($this->advert->payment, $this->paymentMethodId);
        }

        $user = $this->advert->user;

        $hashText = "{$this->advert->payment->id}-{$this->advert->payment->user_id}-{$this->advert->payment->amount}-{$this->advert->payment->created_at}";

        # create request class
        $paymentRequest = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
        $paymentRequest->setLocale(\Iyzipay\Model\Locale::TR);
        $paymentRequest->setConversationId(Hash::make($hashText));
        $paymentRequest->setPrice($this->advert->price);
        $paymentRequest->setPaidPrice($this->advert->payment->amount);
        $paymentRequest->setCurrency(\Iyzipay\Model\Currency::TL);
        $paymentRequest->setBasketId($this->advert->payment->id);
        $paymentRequest->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $paymentRequest->setCallbackUrl(str_replace("/public", null, url('payments/iyzico/success', ["advertId" => $this->advert->id, "paymentId" => $this->advert->payment->id]))); #"https://www.merchant.com/callback"
        #$request->setEnabledInstallments(array(2, 3, 6, 9));

        $userName = explode(" ", $user["name"]);

        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId($user["id"]);
        $buyer->setName($userName[0]);
        $buyer->setSurname($userName[1]);
        $buyer->setGsmNumber($user["phone_number"]);
        $buyer->setEmail($user["email"]);
        $buyer->setIdentityNumber("74300864791");
        #$buyer->setLastLoginDate("2015-10-05 12:43:35");
        $buyer->setRegistrationDate("2013-04-21 15:12:09");
        $buyer->setRegistrationAddress($user->custom_fields["address"]["value"]);
        $buyer->setIp($request->ip);
        $buyer->setCity($user->custom_fields["city"]["value"]);
        $buyer->setCountry("Turkey");
        $buyer->setZipCode($user->custom_fields["zip_code"]["value"]);
        $paymentRequest->setBuyer($buyer);

        $cityDropOff = json_decode($this->advert->city_drop_off);
        $zipCodeDropOff = get_postal_code($cityDropOff->address_components);
        /*foreach ($cityDropOff->address_components as $address_component) {
            if ($address_component->types[0] == "postal_code") {
                $zipCodeDropOff = $address_component->long_name;
            }
        }*/

        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName($this->advert->receiver_full_name);
        $shippingAddress->setCity("Istanbul");
        $shippingAddress->setCountry("Turkey");
        $shippingAddress->setAddress($this->advert->place_drop_off);
        $shippingAddress->setZipCode($zipCodeDropOff);
        $paymentRequest->setShippingAddress($shippingAddress);

        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName($user["name"]);
        $billingAddress->setCity($user->custom_fields["city"]["value"]);
        $billingAddress->setCountry("Turkey");
        $billingAddress->setAddress($user->custom_fields["address"]["value"]);
        $billingAddress->setZipCode($user->custom_fields["zip_code"]["value"]);
        $paymentRequest->setBillingAddress($billingAddress);

        $advertSizes = ["", "Zarf", "Koltuğa Sığar", "Bagaja Sığar"];

        $basketItems = array();
        $firstBasketItem = new \Iyzipay\Model\BasketItem();
        $firstBasketItem->setId($this->advert->id);
        $firstBasketItem->setName($this->advert->title);
        $firstBasketItem->setCategory1("Kurye");
        $firstBasketItem->setCategory2($advertSizes[$this->advert->size]);
        $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
        $firstBasketItem->setPrice($this->advert->price);
        $basketItems[0] = $firstBasketItem;
        $paymentRequest->setBasketItems($basketItems);

        $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($paymentRequest, $this->options);

        #print_r($checkoutFormInitialize->getErrorCode());
        print_r($checkoutFormInitialize->getErrorMessage());
        #print_r($checkoutFormInitialize->getStatus());
        exit('<html><body><div style="width: 100% !important;" id="iyzipay-checkout-form" class="responsive">'.$checkoutFormInitialize->getCheckoutFormContent().'</div></body></html>');

        #return view('payment_methods.iyzico_charge', ['advert' => $this->advert]);
    }

    public function paySuccess(Request $request, int $advertId, int $paymentId)
    {
        echo "<center><button style='font-size: 75px !important;text-align: center;margin: auto;' onclick='javascript:location.reload();'>Test</button></center><br>";

        if ($request->get("message") == "success" || strtolower($request->method()) != "post") {
            exit;
        }
        $this->advert = $this->advert->find($advertId);
        $payment = $this->paymentRepository->findWithoutFail($paymentId);
        $this->paymentMethodId = $payment->payment_method_id;

        if (empty($this->advert)) {
            Flash::error("Error processing Iyzico payment for your advert");
            return redirect(route('payments.failed'));
        } else {
            try {
                $iyzicoCart = $this->getTransactionData();

                $hashText = "{$payment->id}-{$payment->user_id}-{$payment->amount}-{$payment->created_at}";
                #var_dump($hashText);
                #var_dump(Hash::make($hashText));
                #exit;

                $formRequest = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
                $formRequest->setLocale(\Iyzipay\Model\Locale::TR);
                $formRequest->setConversationId(Hash::make($hashText));
                $formRequest->setToken($request->post("token"));

                $checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($formRequest, $this->options);

                var_dump($checkoutForm->getStatus());
                #var_dump($this->advertStatusRepository->findByField("order", 20)->first());

                if ($checkoutForm->getStatus() == 'success') {
                    $payment->payment_status_id = 2;
                    $payment->save();
                    $this->advert->status = $this->advertStatusRepository->findByField("order", 20)->first();
                    $this->advert->save();

                    header("Location: ?message=success");
                }else{
                    exit("<b style='color: red;'>HATA:</b><br><p>".$checkoutForm->getErrorMessage()."</p>");
                }
                exit;

                $intent = PaymentIntent::create($iyzicoCart);
                $intent = PaymentIntent::retrieve($intent->id);
                $intent = $intent->confirm();
                Log::info($intent->status);
                if ($intent->status == 'succeeded') {
                    $this->paymentMethodId = 7; // Stripe method
                    $this->startTransaction();
                }
                return $this->sendResponse($intent, __('lang.saved_successfully'));
            } catch (ApiErrorException $e) {
                return $this->sendError($e->getMessage());
            }
        }
    }

    /**
     * Set cart data for processing payment on Stripe.
     */
    private function getTransactionData(): array
    {
        $data = [];
        $amount = $this->advert->getTotal();
        $data['amount'] = (int)($amount * 100);
        $data['payment_method'] = $this->iyzicoPaymentMethod;
        $data['currency'] = setting('default_currency_code');

        return $data;
    }
}
