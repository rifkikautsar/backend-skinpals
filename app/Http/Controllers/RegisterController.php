<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use Illuminate\Support\Facades\DB;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class RegisterController extends Controller
{
    public function index(ServerRequestInterface $request): ResponseInterface 
    {
        // header('Content-Type: application/json; charset=utf-8');
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
                $response['data']['message'] = 'Method Not Allowed: expected POST, found ' . $request->getMethod();
                return new Response(405, $headers, json_encode($response));
            }
            $contentType = $request->getHeader('Content-Type')[0];
            if (strpos($contentType, 'application/json') !== 0) {
                $response['code'] = 400;
                $response['message'] = 'Bad Request: Invalid Content Type';
                return new Response(400, $headers, json_encode($response));
            }
            //local time
            $dt = new \DateTime("now", new \DateTimeZone('Asia/Jakarta'));
            $timestamps = $dt->format('Y-m-d H:i:s');
            $obj = json_decode($request->getBody()->getContents());
            $user = DB::table('users')->where('email', $obj->email)->get()->first();
            if (!empty($user)) {
                $response['code'] = 200;
                $response['message'] = "Email telah terdaftar. Silakan Login";
                $response['data'] = null;
                return new Response(200, $headers, json_encode($response));
                $stmt = null;
            } else {
            DB::beginTransaction();
              try {
                $newDate = date('Y-m-d',strtotime($obj->tanggalLahir));
                $hashPasswd = password_hash($obj->pass, PASSWORD_DEFAULT);
                $token=hash('sha256', md5(date('Y-m-d h:i:s').$obj->nama));
                $hashToken = password_hash($token, PASSWORD_DEFAULT);
                $mail = MailController::index($token,$obj->email);
                $insertId = DB::table('users')->insertGetId([
                    'nama' => $obj->nama,
                    'email' => $obj->email,
                    'jenisKelamin' => $obj->jenisKelamin,
                    'tanggalLahir' => $newDate,
                    'pass' => $hashPasswd,
                    'remember_token' => $token,
                    'aktif' => '0',
                    'status' => '0',
                    'created_at' => $timestamps,
                    'updated_at' => $timestamps
                ]);
                $informasiKulit = DB::table('informasi_kulit')->insert([
                    'user_id' => $insertId,
                    'jenisKulit' => $obj->jenisKulit,
                    'keluhan' => $obj->keluhan,
                    'created_at' => $timestamps,
                    'updated_at' => $timestamps
                ]);
                DB::commit();
                $response['code'] = 200;
                $response['message'] = "Registrasi Berhasil";
                $response['data'] = null;
                return new Response(200, $headers, json_encode($response));
              } catch(\Exception $e){
                DB::rollback();
                $response['code'] = 400;
                $response['message'] = "Registrasi Gagal " .$e->getMessage();
                $response['data'] = null;
                return new Response(400, $headers, json_encode($response));
              }
            }
        }
    } public function activation($key) {
        $token = $key;
        $data = DB::table('users')->select('user_id','aktif')->where('remember_token',$token)->get()->first();
        if(!empty($data)){
            $user_id = $data->user_id;
            $aktivasi = $data->aktif;
            if($aktivasi == 1){
                return view('konfirmasi', ['title' => 'Aktivasi Gagal karena Email telah Aktif. Silakan Login']);
            } else {
                $update = DB::table('users')->where('user_id', $user_id)->update(['aktif' => 1]);
                return view('konfirmasi', ['title' => 'Aktivasi Berhasil. Silakan Login']);
            }
        } else {
            return view('konfirmasi', ['title' => 'Link Expired']);
        }
    }
}