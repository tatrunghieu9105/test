<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class PaymentGatewayController extends Controller
{
    // Redirect to Momo payment gateway
    public function momo(Request $request)
    {
        // TODO: Lấy thông tin merchant từ config
    // Momo sandbox test account (lấy trên https://developers.momo.vn)
    $endpoint = 'https://test-payment.momo.vn/v2/gateway/api/create';
    $partnerCode = 'MOMO'; // Thay bằng partnerCode test của bạn
    $accessKey = 'F8BBA842ECF85'; // AccessKey test mặc định
    $secretKey = 'K951B6PE1waDMi640xX08PD3vg6EkVlz'; // SecretKey test mặc định
        $orderId = uniqid('momo_');
        $amount = $request->input('amount');
        $orderInfo = 'Thanh toan ve xem phim';
        $redirectUrl = route('payments.momo.callback');
        $ipnUrl = route('payments.momo.callback');
        $extraData = '';

        // Tạo signature
        $rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$orderId&requestType=captureWallet";
        $signature = hash_hmac('sha256', $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'accessKey' => $accessKey,
            'requestId' => $orderId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'extraData' => $extraData,
            'requestType' => 'captureWallet',
            'signature' => $signature,
            'lang' => 'vi',
        ];

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = curl_exec($ch);
        curl_close($ch);
        $jsonResult = json_decode($result, true);

        if (isset($jsonResult['payUrl'])) {
            return redirect($jsonResult['payUrl']);
        }
        return back()->with('error', 'Không thể kết nối Momo');
    }

    // Momo callback (redirectUrl & ipnUrl)
    public function momoCallback(Request $request)
    {
        // TODO: Xác thực signature, cập nhật trạng thái vé
        // $request->all() chứa thông tin giao dịch trả về
        // Ví dụ: $request->input('resultCode') == 0 là thành công
        // Cập nhật trạng thái vé sang paid_online
        // Giả sử ticket_ids được truyền qua session hoặc request (cần truyền ticket_ids khi redirect về)
        $ticketIds = session('ticket_ids', []);
        if ($request->has('ticket_ids')) {
            $ticketIds = explode(',', $request->input('ticket_ids'));
        }
        if (!empty($ticketIds)) {
            $tickets = \App\Models\Ticket::whereIn('id', $ticketIds)->get();
            foreach ($tickets as $t) {
                if ($t->status === 'pending_online') {
                    $t->update(['status' => 'paid_online']);
                    \App\Services\LoyaltyService::awardForTicket($t);
                }
            }
        }
        return redirect()->route('me.orders')->with('success', 'Thanh toán Momo thành công!');
    }

    // Redirect to VNPay payment gateway
    public function vnpay(Request $request)
    {
        // TODO: Lấy thông tin merchant từ config
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('payments.vnpay.callback');
        // VNPay sandbox test account (lấy trên https://sandbox.vnpayment.vn)
        $vnp_TmnCode = "2QXUI4J4"; // TmnCode test mặc định
        $vnp_HashSecret = "SECRETKEYTESTVNPAY"; // HashSecret test mặc định
        $vnp_TxnRef = uniqid('vnpay_');
        $vnp_OrderInfo = "Thanh toan ve xem phim";
        $amount = $request->input('amount');
        $vnp_Amount = intval(round($amount)) * 100; // Đảm bảo là số nguyên, nhân 100
        $vnp_Locale = "vn";
        $vnp_IpAddr = $request->ip();
        $vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes'));

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $vnp_ExpireDate,
        ];
        // Loại bỏ các trường rỗng
        $inputData = array_filter($inputData, function($v) { return $v !== null && $v !== ''; });
        ksort($inputData);
        $query = [];
        foreach ($inputData as $key => $value) {
            $query[] = urlencode($key) . "=" . urlencode($value);
        }
        $hashdata = urldecode(implode('&', $query));
        $vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= "?" . implode('&', $query) . '&vnp_SecureHash=' . $vnp_SecureHash;
        return redirect($vnp_Url);
    }

    // VNPay callback
    public function vnpayCallback(Request $request)
    {
        // TODO: Xác thực checksum, cập nhật trạng thái vé
        // $request->all() chứa thông tin giao dịch trả về
        // Ví dụ: $request->input('vnp_ResponseCode') == '00' là thành công
        $ticketIds = session('ticket_ids', []);
        if ($request->has('ticket_ids')) {
            $ticketIds = explode(',', $request->input('ticket_ids'));
        }
        if (!empty($ticketIds)) {
            $tickets = \App\Models\Ticket::whereIn('id', $ticketIds)->get();
            foreach ($tickets as $t) {
                if ($t->status === 'pending_online') {
                    $t->update(['status' => 'paid_online']);
                    \App\Services\LoyaltyService::awardForTicket($t);
                }
            }
        }
        return redirect()->route('me.orders')->with('success', 'Thanh toán VNPay thành công!');
    }
}
