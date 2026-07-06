<?php

namespace App\Traits;
use App\Models\Whatsapp\WhatsappLogs;
use App\Models\ManualData;
use App\Models\Enquiry;

trait Utility
{
    protected $whatsappApiBaseUrl = 'https://whatsbroadcast-h212.onrender.com/api';

     /**
      * Summary of whatsapp_qr
      * @param Request $request
      * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
      */
     function whatsapp_qr()  {
        $qrResponse = $this->getWhatsappQR();
        if ($qrResponse === false) {
            return view('whatsapp.whatsapp_qr', ['qrCode' => null, 'message' => 'No active WhatsApp session found. Please login first.']);
        } else {
            return view('whatsapp.whatsapp_qr', ['qrCode' => $qrResponse, 'message' => null]);
        }
    }
    /**`
     * Summary of sendWhatsappMessage
     * @return void
     */
    function proceedSendWhatsappMessage($mobile, $message, $mediaUrl = null, $dataForLogs = null)
    {
        $mobile_json = json_encode($mobile);
        $payload = [
            "html" => $message,
            "recipients" => $mobile_json
        ];

        if($mediaUrl) {
            $payload['mediaUrl'] = $mediaUrl; //"https://as1.ftcdn.net/v2/jpg/19/13/99/88/1000_F_1913998859_HpcAUXeGF4buoTuFsKlagjvHKskPccf3.jpg";
        }
        // echo $token = session('whatsapp_token');
        // print_r(json_encode($payload));

        if(session('whatsapp_token')) {
            $token = session('whatsapp_token');
            // $token = "8ccebf3674ebc9d46aaa1e09345666e6805b93d2676248b935f667eec943d812";
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $this->whatsappApiBaseUrl.'/broadcast',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer '.$token
            ),
            ));

            $response = curl_exec($curl);
            // dd($response);
            curl_close($curl);
            if(strpos($response, 'error') !== false) {
                $response_decoded = json_decode($response, true);
                if(isset($response_decoded['error']) && $response_decoded['error'] === 'Invalid or expired session token') {
                    session()->forget('whatsapp_token');
                    return ['status' => false, 'message' => 'WhatsApp session expired. Please login again.', 'original_response' => $response];
                } else {
                    $mesg = !empty($response_decoded['message']) ? $response_decoded['message'] : 'WhatsApp session expired. Please login again.';
                    return ['status' => false, 'message' => $mesg, 'original_response' => $response]; 
                }
            } else {

                $res = json_decode($response, true);
                if(!empty($res['note'])){
                    $mesg = $res['note'];
                } else if(!empty($res['message'])){
                    $mesg = $res['message'];
                } else {
                    $mesg = 'WhatsApp message sent successfully.';
                }

                $status = !empty($res['success']) ? $res['success'] : false;
                $recipientCount = !empty($res['recipientCount']) ? $res['recipientCount'] : 0;
                if($status === true){

                    $dataToBeInserted = [];
                    foreach($dataForLogs as $item){
                        $message = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $item['message']);
                        $item['message'] = $message;
                        $dataToBeInserted[] = $item;
                    }
                    WhatsappLogs::insert($dataToBeInserted);
                    foreach($dataToBeInserted as $item){
                        $model = $item['model'];
                        $id = $item['model_id'];
                        $sql = ManualData::query();
                        if(!empty($model) && $model =="Enquiry") {
                            $sql = Enquiry::query();
                        }
                        $sql->where('id', $id)->increment('whatsapp_message_count',1, ['last_whatsapp_message_at' => now()]);
                    }
                }
                return ['status' => $status, 'message' => $mesg, "recipientCount" => $recipientCount, 'original_response' => $response];
            }
        } else {
            return ['status' => false, 'message' => 'No active WhatsApp session found. Please login first.', 'original_response' => null];
        }

    }
    
    /**
     * Summary of Whatsapp Login status
     * @return void
     */
    function getConnectionStatus(){
        if(session('whatsapp_token')) {
            $token = session('whatsapp_token');
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->whatsappApiBaseUrl.'/status/qr',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer '.$token
                )
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        }else {
            return null;
        }
    }

    /**
     * Summary of getWhatsappQR
     * @return bool|string
     */
    function getWhatsappQR(){
        
        if(session('whatsapp_token')) {
            $token = session('whatsapp_token');
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $this->whatsappApiBaseUrl.'/status',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
            ));
            $response = curl_exec($curl);

            curl_close($curl);
            return $response;

        } else {
            return false;
        }
    }
}

