<?php

namespace App\Http\Controllers;

use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;

class ResultController extends Controller
{
    public function index($key):ResponseInterface {
        $headers = ['Content-Type' =>  'application/json'];

        $data = DB::table('results')->join('diseases', 'results.disease_id', '=', 'diseases.disease_id')->join('ingredients','diseases.disease_id','=','ingredients.disease_id')->select('results.*','diseases.disease_id', 'diseases.namaPenyakit', 'diseases.rekomendasi','diseases.larangan','ingredients.kandungan')->where("user_id",$key)->get()->first();
        if(!empty($data)){
            $saran = explode(", ", $data->rekomendasi);
            $banyakSaran = count($saran);
            for ($i=0; $i<$banyakSaran; $i++){
                $arraySaran[$i] = array("name" => $saran[$i]);
            }                        
            $kandungan = explode(", ", $data->kandungan);
            $banyakKandungan = count($kandungan);
            for ($i=0; $i<$banyakKandungan; $i++){
                $arrayKandungan[$i] = array("name" => $kandungan[$i]);
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