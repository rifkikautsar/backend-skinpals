<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;

class SurveyController extends Controller
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
            $contentType = $request->getHeader('Content-Type')[0];
            if (strpos($contentType, 'application/json') !== 0) {
                $response['code'] = 400;
                $response['message'] = 'Bad Request: Invalid Content Type';
                $response['data'] = null;
                return new Response(400, $headers, json_encode($response));
            }
            
            //local time
            $dt = new \DateTime("now", new \DateTimeZone('Asia/Jakarta'));
            $timestamps = $dt->format('Y-m-d H:i:s');
            $obj = json_decode($request->getBody()->getContents());
            if(isset($obj->user_id)){
                if(empty($obj->user_id)){
                    $user_id = 1;
                } else {
                    $user_id = $obj->user_id;
                }
            } else {
                $user_id = 1;
            }
            try{
                $data = DB::table("surveys")->insert([
                    "user_id" => $user_id,
                    "porsi_minum" => $obj->porsi_minum,
                    "jam_tidur" => $obj->jam_tidur,
                    "olahraga" => $obj->olahraga,
                    "sinar_matahari" => $obj->sinar_matahari,
                    "kondisi_kulit1" => $obj->kondisi1,
                    "kondisi_kulit2" => $obj->kondisi2,
                    "kondisi_kulit3" => $obj->kondisi3,
                    "created_at" => $timestamps,
                    "updated_at" => $timestamps
                ]);
                $response['code'] = 200;
                $response['message'] = "Success";
                $response['data'] = null;
                return new Response(200, $headers, json_encode($response));
            } catch (\Exception $e){
                $response['code'] = 400;
                $response['message'] = "Data gagal diinput, ". $e->getMessage();
                $response['data'] = null;
                return new Response(200, $headers, json_encode($response));
            }
        }
    }
}