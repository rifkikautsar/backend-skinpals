<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Mail\MyMail;
use App\Mail\ResetMail;

class MailController
{
    public function index($token, $to){
        $details = [
        'token' => $token 
        ];
        Mail::to($to)->send(new MyMail($details));
    }
    public function reset($token, $to){
        $details = [
        'token' => $token 
        ];
        Mail::to($to)->send(new ResetMail($details));
    }
}