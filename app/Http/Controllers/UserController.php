<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;

class UserController extends Controller
{
    public function edit(Request $request): ResponseInterface {
        $headers = ['Content-Type' =>  'application/json'];
        if(!empty($request->all())){
            $payload = $request->all();
            $data = DB::table('users')->where('user_id',$payload['user_id'])->update(['nama' => $payload['nama'], 'tanggalLahir' => $payload['tanggalLahir'], 'jenisKulit' => $payload['jenisKulit'], 'Keluhan' => $payload['keluhan']]);
            
            $response['code'] = 200;
            $response['message'] = "Success";
            $response['data'] = null;
            return new Response(200, $headers, json_encode($response));
        } else {
            $response['code'] = 400;
            $response['message'] = "Body Null!";
            $response['data'] = null;
            return new Response(200, $headers, json_encode($response));
        }
        
    }
}