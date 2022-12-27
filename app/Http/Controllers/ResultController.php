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

        $data = DB::table('results')->join('diseases', 'results.disease_id', '=', 'diseases.disease_id')->join('ingredients','diseases.disease_id','=','ingredients.disease_id')->select('results.*','diseases.disease_id', 'diseases.namaPenyakit', 'diseases.rekomendasi','diseases.larangan','ingredients.kandungan')->where("user_id",$key)->get();
        if(!empty($data)){
            for ($i=0; $i<count($data);$i++){
                $saran = explode(", ", $data[$i]->rekomendasi);
                $banyakSaran = count($saran);
                for ($j=0; $j<$banyakSaran; $j++){
                    $arraySaran[$j] = array("name" => $saran[$j]);
                }                        
                $kandungan = explode(", ", $data[$i]->kandungan);
                $banyakKandungan = count($kandungan);
                for ($j=0; $j<$banyakKandungan; $j++){
                    $arrayKandungan[$j] = array("name" => $kandungan[$j]);
                }
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