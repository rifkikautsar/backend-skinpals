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
    public function index($key): ResponseInterface {
        $headers = ['Content-Type' =>  'application/json'];
        $user_id = $key;
        $data = DB::table('users')
        ->join('informasi_kulit', 'users.user_id', '=', 'informasi_kulit.user_id')
        ->select('users.nama','users.email','users.jenisKelamin','users.tanggalLahir', 'informasi_kulit.*')
        ->where('users.user_id', $key)->first();
        $response['code'] = 200;
        $response['message'] = "Success";
        $response['data'] = $data;
        return new Response(200, $headers, json_encode($response));
    }
    public function edit(Request $request): ResponseInterface {
        $headers = ['Content-Type' =>  'application/json'];
        if(!empty($request->all())){
            $dt = new \DateTime("now", new \DateTimeZone('Asia/Jakarta'));
            $timestamps = $dt->format('Y-m-d H:i:s');
            $payload = $request->all();
            if(isset($payload['user_id'])){
                $user_id = $payload['user_id'];
            } else {
                $response['code'] = 400;
                $response['message'] = "ID Not Found";
                $response['data'] = null;
                return new Response(400, $headers, json_encode($response));
            }
            DB::beginTransaction();
            try {
                $data = DB::table('users')->where('user_id',$payload['user_id'])->update(['nama' => $payload['nama'], 'tanggalLahir' => $payload['tanggalLahir'], 'jenisKelamin' => $payload['jenisKelamin'], 'updated_at' => $timestamps]);
                if(isset($payload['keluhan'])){
                    $data = DB::table('informasi_kulit')->where('user_id',$payload['user_id'])->update([
                        'jenisKulit' => $payload['jenisKulit'],
                        'keluhan' => $payload['keluhan'],
                        'updated_at' => $timestamps
                    ]);
                } else {
                    $data = DB::table('informasi_kulit')->where('user_id',$payload['user_id'])->update([
                        'jenisKulit' => $payload['jenisKulit'],
                        'updated_at' => $timestamps
                    ]);
                }

                DB::commit();
                $response['code'] = 200;
                $response['message'] = "Edit Profile Success";
                $response['data'] = null;
                return new Response(200, $headers, json_encode($response));
            } catch (\Exception $e){
                DB::rollback();
                $response['code'] = 400;
                $response['message'] = "Edit Profile Gagal ". $e;
                $response['data'] = null;
                return new Response(400, $headers, json_encode($response));
            }
        } else {
            $response['code'] = 400;
            $response['message'] = "Body Null!";
            $response['data'] = null;
            return new Response(400, $headers, json_encode($response));
        }
    }
}