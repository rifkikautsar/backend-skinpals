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

class UploadController extends Controller
{
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $url = "https://predicts-gnkxwtwa6q-et.a.run.app";
        // $url = "http://127.0.0.1:5000/api";
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
            if (strpos($contentType, 'multipart/form-data') !== 0) {
                $response['code'] = 400;
                $response['message'] = 'Bad Request: Invalid Content-Type';
                $response['data'] = null;
                return new Response(400, $headers, json_encode($response));
            }
            if($request->getUploadedFiles()['uploads']){
                if($request->getUploadedFiles()['uploads']->getSize()==0) {
                    $response['code'] = 400;
                    $response['message'] = 'Bad Request: No Photo Uploaded. (Size : ' . $request->getUploadedFiles()['uploads']->getSize(). ")";
                    $response['data'] = null;
                    return new Response(400, $headers, json_encode($response));
                }
                $type = explode("/", $request->getUploadedFiles()['uploads']->getClientMediaType())[1];
                $name = uniqid("img-", true) . "." . $type;
                $size = $request->getUploadedFiles()['uploads']->getSize();
                $tmpFile = $request->getUploadedFiles()['uploads']->getStream()->getMetadata('uri');
                //Array type of image allow
                $ext = [
                    'image/jpg',
                    'image/jpeg',
                ];
                //Validation
                //Maximum Size 10Mb
                if($size > 1000*10000)
                {
                    $response['code'] = 400;
                    $response['message'] = 'Bad Request: Maksimal Size 10 Mb!. (Size : ' . $request->getUploadedFiles()['uploads']->getSize(). ")";
                    $response['data'] = null;
                    return new Response(400, $headers, json_encode($response));
                }
                if(!in_array(mime_content_type($tmpFile), $ext))
                {
                    $response['code'] = 400;
                    $response['message'] = 'Bad Request: Hanya format gambar (jpg) yang diterima!';
                    $response['data'] = null;
                    return new Response(400, $headers, json_encode($response));
                }
                $payload = $request->getParsedBody();
                // $user_id = $payload['user_id'];
                $user_id = 1;
                $data = file_get_contents($tmpFile);
                $path = storage_path() . "/bustling-bot-350614-5dab7679f2d4.json";
                $storage = new StorageClient([
                    'projectId' => 'bustling-bot-350614',
                    'keyFile' => json_decode(File::get($path),true)
                ]);
                $bucketName = 'kulitku-incubation';
                $cloudPath = 'images/' . $name;
                $bucket = $storage->bucket($bucketName);
                $object = $bucket->upload($data, [
                    'name' => $cloudPath
                ]);
                $object->update(['acl' => []], ['predefinedAcl' => 'PUBLICREAD']);
                $urlImage = "https://storage.googleapis.com/kulitku-incubation/images/". $name;
                $fields = [
                    'image' => $name,
                ];
                $payload = json_encode($fields);
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_URL, $url);
                curl_setopt($ch,CURLOPT_POST, true);
                curl_setopt($ch,CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);
                if(empty($result)){
                    $response['code'] = 400;
                    $response['message'] = 'Prediction Fail';
                    $response['data'] = null;
                    return new Response(400, $headers, json_encode($response));
                }
                $res = json_decode($result);
                $class = $res->class;

                DB::beginTransaction();
                try{
                    $data=DB::table('diseases')
                    ->join('ingredients', 'diseases.disease_id', '=', 'ingredients.disease_id')
                    ->select('diseases.namaPenyakit','diseases.disease_id', 'diseases.rekomendasi','diseases.larangan','ingredients.kandungan')->where('diseases.namaPenyakit',$class)
                    ->get()->first();
                    $timestamps = date('Y-m-d H:i:s', time());
                    if(!empty($data)){
                        $result = DB::table('results')->insert([
                            "user_id" => $user_id,
                            "disease_id" => $data->disease_id,
                            "urlImage" => $urlImage,
                            "created_at" => $timestamps,
                            "updated_at" => $timestamps
                        ]);
                        DB::commit();
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
                        $larangan = explode(", ", $data->larangan);
                        $banyakLarangan = count($larangan);
                        for ($i=0; $i<$banyakLarangan; $i++){
                            $arrayLarangan[$i] = array("name" => $larangan[$i]);
                        }
                        $response['code'] = 200;
                        $response['message'] = 'Success';
                        $response['data']['result'] = $res;
                        $response['data']['kandungan'] = $arrayKandungan;
                        $response['data']['saran'] = $arraySaran;
                        $response['data']['larangan'] = $arrayLarangan;
                        return new Response(200, $headers, json_encode($response));
                    } else {
                        $response['code'] = 404;
                        $response['message'] = 'Data not found';
                        $response['data'] = null;
                        return new Response(404, $headers, json_encode($response));
                    }
                } catch(\Exception $e){
                    DB::rollback();
                    $response['code'] = 401;
                    $response['message'] = "Gagal query ke Database ". $e->getMessage();
                    $response['data'] = null;
                    return new Response(401, $headers, json_encode($response));
                }
            } else {
                $response['code'] = 400;
                $response['message'] = 'Bad Request: Field Request';
                $response['data'] = null;
                return new Response(400, $headers, json_encode($response));
            }  
        }
    }
}