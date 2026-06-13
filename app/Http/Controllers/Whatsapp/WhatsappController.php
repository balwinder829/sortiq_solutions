<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManualData;
use App\Traits\Utility;
use App\Models\Enquiry;
use App\Models\Student;
class WhatsappController extends Controller
{
    use Utility;
    /**
     * Summary of setupWhatsapp
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    function setupWhatsapp(Request $request)
    {
        if(session('whatsapp_token')){
            $username = session('whatsapp_username');
            return redirect()->route('admin.whatsapp.qr')->with('success', 'Already logged in with username: ' . $username);
        }
        
        return view('whatsapp.index');
    }

    /**
     * Summary of whatsapp_setup_login
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function whatsapp_setup_login(Request $request)
    {
       
        $username = $request->input('email');
        $password = $request->input('password');

        session(['whatsapp_username' => $username]);
        session(['whatsapp_password' => $password]);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://whatsbroadcast-h212.onrender.com/api/auth/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "email": "'. $username . '",
                "password": "'. $password . '"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            )
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        
        if ($response === false) {
            return back()->withErrors(['error' => 'Failed to login. Please try again.']);
        } else {
            $responseData = json_decode($response, true);
            if (isset($responseData['token'])) {
                // Store the token in the session or database as needed
                session(['whatsapp_token' => $responseData['token']]);
                return redirect()->route('admin.whatsapp.qr')->with('success', 'Logged in successfully with username: ' . $username);
            } else {
                return back()->withErrors(['error' => 'Invalid login credentials.']);
            }
        }
    }
    
    /**
     * Summary of whatsapp_qr
     * @return void
     */
    function whatsapp_qr()  {
        $qrResponse = $this->getWhatsappQR();
        $connectionStatus = $this->getConnectionStatus();

        if ($qrResponse === false) {
            return view('whatsapp.whatsapp_qr', ['qrCode' => null, 'connectionStatus' => $connectionStatus, 'message' => 'No active WhatsApp session found. Please login first.']);
            //return response()->json(['error' => 'No active WhatsApp session found. Please login first.'], 400);
        } else {
            $responseData = json_decode($qrResponse, true);
            if (isset($responseData['qrCode'])) {
                return view('whatsapp.whatsapp_qr', ['qrCode' => $responseData['qrCode'], 'connectionStatus' => $connectionStatus, 'message' => 'QR Code generated successfully.']);
            } else {
                return view('whatsapp.whatsapp_qr', ['qrCode' => null, 'connectionStatus' => $connectionStatus, 'message' => 'Failed to retrieve QR code. Please try again.']);
            }
        }
    }

    /** 
     * Summary of sendWhatsappMessage
     * @param Request $request
     */
    function sendWhatsappMessage(Request $request)
    {
        $ids = $request->input('ids'); // record entry
        $model = !empty($request->input('model')) ? $request->input('model') : ''; // table name or model name
        $message = !empty($request->input('message')) ? $request->input('message') : ''; // message content may be HTML formatted
        $message_type = $request->input('message_type'); // single or bulk or with_name
        if($message_type == '') {
            $message_type = 'single';
        }

        $filePath = null;
        $mediaUrl = null;
        if ($request->hasFile('whatsappFile')) {
            $file = $request->file('whatsappFile');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('whatsapp-files', $fileName, 'public');
        } elseif ($request->filled('existing_file_path')) {
            $filePath = $request->existing_file_path;
        }

        if ($filePath) {
            $fileName = basename($filePath);
            $mediaUrl = route('download.file', ['file' => $fileName]);
        }
        
        if(!$ids) {
            return response()->json(['error' => 'IDs are required and should be an array.'], 400);
        }

        if (is_array($ids)) {

            $mobile = [];
            $dataForLogs = [];
            $sql = ManualData::query();
            if(!empty($model) && $model =="Enquiry") {
                $sql = Enquiry::query();
            }

            $sql->whereIn('id', $ids)
                ->get()
                ->each(function($item) use ($model, $message, $message_type, $mediaUrl, &$mobile, &$dataForLogs) {

                if($model == "Enquiry") {
                    $student_name = !empty($item->name) ? $item->name : '';
                    $mobile_number = $item->mobile;
                } else {    
                    $student_name = !empty($item->student_name) ? $item->student_name : '';
                    $mobile_number = $item->student_mobile;
                }
    
                $mobile_number = preg_replace('/[\s\-]+/', '', $mobile_number);
                $mobile_number = substr($mobile_number, -10);
                $mobile_number = '91' . $mobile_number;

                $dataForLogs[] = [
                    'model' => $model,
                    'model_id' => $item->id,
                    'mobile_number' => $mobile_number,
                    'message' => $message,
                    'media_url' => $mediaUrl,
                    'name' => $student_name,
                    'status' => 'sent'
                ];

                if($message_type == 'same_message') {
                    $mobile[] = $mobile_number;
                } else if($message_type == 'with_name') {
                    $message = "Hello <strong>$student_name</strong>,<br>".$message;
                    // dd([$message, $mobile_number]);
                    $response = $this->proceedSendWhatsappMessage([$mobile_number], $message, $mediaUrl, $dataForLogs);
                    // dd($response);
                    return response()->json($response);
                } else {
                    // dd([$message, $mobile, $mediaUrl]);
                    $response = $this->proceedSendWhatsappMessage([$mobile_number], $message, $mediaUrl, $dataForLogs);
                    return response()->json($response);
                }
            });
        
            if($message_type == 'same_message' && !empty($mobile)) {
                // dd([$message, $mobile, $mediaUrl]);
                $response = $this->proceedSendWhatsappMessage($mobile, $message, $mediaUrl, $dataForLogs);
                return response()->json($response);
            }
        } else {
            return response()->json(['error' => 'IDs should be an array.'], 400);
        }
    }

    /**
     * Summary of whatsapp_reset
     * @return \Illuminate\Http\RedirectResponse
     */
    function whatsapp_reset() {
        session()->forget('whatsapp_token');
        return redirect()->route('admin.message.setup_whatsapp')->with('success', 'WhatsApp session has been reset successfully.');
    }
}
