<?php

namespace App\Library;

class FpaymentCustom
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function add_invoice($merchant_id, $api_key, $name, $description, $amount, $request_id, $callback_url, $success_url, $cancel_url)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://app.fpayment.net/api/AddInvoice",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'merchant_id'     => $merchant_id,                          // Không được để lộ thông tin này
                'api_key'         => $api_key,                 // Không được để lộ thông tin này
                'name'            => $name,                         // Tên hóa đơn ví dụ như: Nạp tiền vào website abc.xyz
                'description'     => $description,                  // Mô tả hóa đơn ví dụ như: Username hoặc Email của user tạo hóa đơn
                'amount'          => $amount,                                   // Số tiền user muốn nạp vào hệ thống bạn (nhập USDT)
                'request_id'      => $request_id,                // Mã giao dịch bí mật của hệ thống bạn dùng để so sánh giao dịch của user nào
                'callback_url'    => $callback_url,     // Liên kết dùng nhận kết quả giao dịch
                'success_url'     => $success_url,      // Liên kết khi người dùng nhấn nút Return to Website khi hóa đơn xử lý thành công
                'cancel_url'      => $cancel_url        // Liên kết khi người dùng nhấn nút Return to Website khi hóa đơn hết hạn
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response, true);
        return $result;
        // Xử lý kết quả cURL sau đó lưu vào Database để đem ra xử lý khi có callback
        // if ($result['status'] == 'success') {
        //     // Tạo hóa đơn thành công, lưu thông tin hóa đơn vào Database
        //     $trans_id       = $result['data']['trans_id'];      // Mã giao dịch FPAYMENT trả về
        //     $amount         = $result['data']['amount'];        // Số tiền mà user cần phải chuyển đúng chính xác
        //     $status         = $result['data']['status'];        // Trạng thái hóa đơn sau khi tạo là waiting
        //     $url_payment    = $result['data']['url_payment'];   // Liên kết trang thanh toán, chuyển hướng đến link này để cho user tiến hành thanh toán

        //     // Đoạn code lưu thông tin hóa đơn vào database

        // } else {
        //     // Trả thông báo lỗi cho user
        // }
    }
}
