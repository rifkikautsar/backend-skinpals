<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
class MailController extends Controller
{
    public function index($token, $to){
        $details = [
        'token' => $token 
        ];
        \Mail::to($to)->send(new \App\Mail\MyMail($details));
    }
    public function reset($token, $to){
        $details = [
        'token' => $token 
        ];
        \Mail::to($to)->send(new \App\Mail\ResetMail($details));
    }
}