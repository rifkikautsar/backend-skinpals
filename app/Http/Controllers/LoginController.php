<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;

class LoginController extends Controller
{
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $headers = ['Access-Control-Allow-Origin' => '*'];

        if ($request->getMethod() === 'OPTIONS') {
            // Send response to OPTIONS requests
            $headers = array_merge($headers, [
                'Access-Control-Allow-Methods' => 'POST',
                'Access-Control-Allow-Headers' => 'Content-Type',
                'Access-Control-Max-Age' => '3600'
            ]);
            return new Response(204, $headers, '');
        } else {
            $headers = ['Content-Type' =>  'application/json'];
            if ($request->getMethod() != 'POST') {
                $response['code'] = 405;
                $response['message'] = 'Method Not Allowed: expected POST, found ' . $request->getMethod();
                $response['data'] = null;
                return new Response(405, $headers, json_encode($response));
            }
            $contentType = $request->getHeader('Content-Type')[0];
            if (strpos($contentType, 'application/json') !== 0) {
                $response['code'] = 400;
                $response['message'] = 'Bad Request: Invalid Content-Type';
                $response['data'] = null;
                return new Response(400, $headers, json_encode($response));
            }
            $obj = json_decode($request->getBody()->getContents());
            $user = DB::table('users')->where('email', $obj->email)->first();
            if (!empty($user)){
                if($user->aktif == 0){
                    $response['code'] = 200;
                    $response['message'] = "Konfirmasi Email terlebih dahulu";
                    $response['data'] = null;
                    return new Response(200, $headers, json_encode($response));
                } else {
                        if (password_verify($obj->pass,$user->pass)) {
                            $dataUser = DB::table('users')
                            ->join('informasi_kulit', 'users.user_id', '=', 'informasi_kulit.user_id')
                            ->select('users.*', 'informasi_kulit.*')
                            ->where('email', $obj->email)->first();
                            $array['user_id'] = $dataUser->user_id;
                            $array['nama'] = $dataUser->nama;
                            $array['email'] = $dataUser->email;
                            $array['jenisKelamin'] = $dataUser->jenisKelamin;
                            $array['jenisKulit'] = $dataUser->jenisKulit;
                            $array['tanggalLahir'] = $dataUser->tanggalLahir;
                            $array['keluhan'] = $dataUser->keluhan;
                        $response['code'] = 200;
                        $response['message'] = "Login berhasil";
                        $response['data'] = $array;
                        return new Response(200, $headers, json_encode($response));
                    } else {
                        $response['code'] = 400;
                        $response['message'] = "Login Gagal. Password Salah";
                        $response['data'] = null;
                        return new Response(400, $headers, json_encode($response));
                    }
                }
            } else {
                    $response['code'] = 400;
                    $response['message'] = "Data tidak ditemukan";
                    $response['data'] = null;
                    return new Response(400, $headers, json_encode($response));
            }
        }
    }
}