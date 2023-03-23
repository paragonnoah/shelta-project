<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mpesa;

class MpesaController extends Controller
{
    //
    public function StkSimulation(){

        $mpesa= new \Safaricom\Mpesa\Mpesa();
        $stkPushSimulation=$mpesa->STKPushSimulation(
            $BusinessShortCode, 
            $LipaNaMpesaPasskey, 
            $TransactionType, 
            $Amount, 
            $PartyA, 
            $PartyB, 
            $PhoneNumber, 
            $CallBackURL, 
            $AccountReference, 
            $TransactionDesc, 
            $Remarks);

    }
}
