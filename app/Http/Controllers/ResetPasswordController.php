<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function index(ServerRequestInterface $request): ResponseInterface {
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
            //local time
            $dt = new \DateTime("now", new \DateTimeZone('Asia/Jakarta'));
            $timestamps = $dt->format('Y-m-d H:i:s');
            $obj = json_decode($request->getBody()->getContents());
            // $token=hash('sha256', date('Y-m-d h:i:s'));
            $token=Str::random(8);
            $user = DB::table('users')->where('email', $obj->email)->get()->first();
            if (empty($user)) {
                $response['code'] = 200;
                $response['message'] = "Email tidak terdaftar. Silakan daftar terlebih dahulu";
                $response['data'] = null;
                return new Response(200, $headers, json_encode($response));
                $stmt = null;
            } else {
                $reset = DB::table('password_resets')->where('email', $obj->email)->get()->first();
                if(!empty($reset)){
                    $deleted = DB::delete('delete from password_resets where email = ?',[$obj->email]);
                }
                try {
                    $insert = DB::table('password_resets')->insert([
                        'email' => $obj->email,
                        'token' => $token,
                        'created_at' => $timestamps
                    ]);
                    $mail = MailController::reset($token,$obj->email);
                    $response['code'] = 200;
                    $response['message'] = "Token telah dikirim. Silakan cek Email";
                    $response['data'] = null;
                    return new Response(200, $headers, json_encode($response));
                } catch(\Exception $e){
                    $response['code'] = 400;
                    $response['message'] = "Fail ". $e->getMessage();
                    $response['data'] = null;
                    return new Response(400, $headers, json_encode($response));
                }
            }
        }
    }
    public function verify(ServerRequestInterface $request): ResponseInterface {
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
            $dt = new \DateTime("now", new \DateTimeZone('Asia/Jakarta'));
            $timestamps = $dt->format('Y-m-d H:i:s');
            $obj = json_decode($request->getBody()->getContents());
            $data = DB::table('password_resets')->select('email','token','created_at')->where('email',$obj->email)->get()->first();
            if (!empty($data)){
                $created = strtotime($data->created_at);
                $now = strtotime($timestamps);
                $duration = ($now - $created);
                if($data->token === $obj->token){
                    if($duration > 3600) {
                        $response['code'] = 400;
                        $response['message'] = "Token EXPIRED. Silakan verifikasi ulang";
                        $response['data'] = null;
                        return new Response(400, $headers, json_encode($response));                        
                    } else {
                        $response['code'] = 200;
                        $response['message'] = "Verified";
                        $response['data'] = null;
                        return new Response(200, $headers, json_encode($response));
                    }
                } else {
                    $response['code'] = 401;
                    $response['message'] = "Token tidak sesuai. Silakan cek kembali";
                    $response['data'] = null;
                    return new Response(401, $headers, json_encode($response));
                }
            } else {
                $response['code'] = 404;
                $response['message'] = "Data not found";
                $response['data'] = null;
                return new Response(404, $headers, json_encode($response));
            }
        }
    }
    public function new_password(ServerRequestInterface $request): ResponseInterface {
        $headers = ['Content-Type' =>  'application/json'];
        $obj = json_decode($request->getBody()->getContents());
        //local time
        $dt = new \DateTime("now", new \DateTimeZone('Asia/Jakarta'));
        $timestamps = $dt->format('Y-m-d H:i:s');
        //end local time 
        $hashPasswd = password_hash($obj->pass, PASSWORD_DEFAULT);       
        try {
            $data = DB::table('users')->where('email',$obj->email)->update([
                'pass' => $hashPasswd,
                'updated_at' => $timestamps
            ]);
            $response['code'] = 200;
            $response['message'] = "Password berhasil diupdate";
            $response['data'] = null;
            return new Response(200, $headers, json_encode($response));
        } catch (\Exception $e){
            $response['code'] = 400;
            $response['message'] = "Password Gagal diupdate";
            $response['data'] = null;
            return new Response(400, $headers, json_encode($response));
        }
    }
}