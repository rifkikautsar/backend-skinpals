<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;

class DiseasesController extends Controller
{
    public function index(Request $request): ResponseInterface
    {
        $headers = ['Access-Control-Allow-Origin' => '*'];
        $headers = ['Content-Type' =>  'application/json']; 
        $request->validate([
            'image.*' => 'mimes:jpeg,png,jpg',
        ]);
        if($request->hasFile('image')) {
            $payload = $request->all();
            $namaPenyakit = $payload['nama'];
            $rekomendasi = $payload['rekomendasi'];
            $larangan = $payload['larangan'];
            $file = $request->file('image');
            $fileName = rand() . '.' . $file->getClientOriginalExtension();
            $dt = new \DateTime("now", new \DateTimeZone('Asia/Jakarta'));
            $timestamps = $dt->format('Y-m-d H:i:s');
            $insert = DB::table('diseases')->insert([
                "namaPenyakit" => $namaPenyakit,
                "image" => $fileName,
                "rekomendasi" => $rekomendasi,
                "larangan" => $larangan,
                "created_at" => $timestamps,
                "updated_at" => $timestamps
            ]);
            $destinationPath = public_path().'/images/diseases';
            $file->move($destinationPath,$fileName);
            $response['code'] = 200;
            $response['message'] = "Success";
            $response['data'] = null;
            return new Response(200, $headers, json_encode($response));
        }
    }
    public function all():ResponseInterface {
        $headers = ['Content-Type' =>  'application/json'];

        $data = DB::table('diseases')->get();
        if(!empty($data)){
            for($i=0; $i<count($data);$i++){
                $data[$i]->urlImage = "https://api.skinpals.id/images/diseases/".$data[$i]->image;
                $data[$i]->DiseaseById = "https://api.skinpals.id/disease/".$data[$i]->disease_id;
            }
            $response['code'] = 200;
            $response['message'] = "Success";
            $response['data'] = $data;
            return new Response(200, $headers, json_encode($response));
        } else {
            $response['code'] = 404;
            $response['message'] = "Data Not Found";
            $response['data'] = null;
            return new Response(404, $headers, json_encode($response));
        }
    }
}