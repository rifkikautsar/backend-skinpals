<?php

namespace App\Http\Controllers;

use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Str;

class ArticleController extends Controller
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
            if(!empty($payload['user_id'])){
                $user = $payload['user_id'];
            } else {
                $user = 1;
            }
            $title = $payload['title'];
            $desc = $payload['description'];
            $file = $request->file('image');
            $fileName = rand() . '.' . $file->getClientOriginalExtension();
            $dt = new \DateTime("now", new \DateTimeZone('Asia/Jakarta'));
            $timestamps = $dt->format('Y-m-d H:i:s');
            $insert = DB::table('articles')->insert([
                "user_id" => $user,
                "title" => $title,
                "image" => $fileName,
                "description" => $desc,
                "created_at" => $timestamps,
                "updated_at" => $timestamps
            ]);
            $destinationPath = public_path().'/images/articles';
            $file->move($destinationPath,$fileName);
            $response['code'] = 200;
            $response['message'] = "Success";
            $response['data'] = null;
            return new Response(200, $headers, json_encode($response));
        } else {
            $response['code'] = 400;
            $response['message'] = "Image Not Found";
            $response['data'] = null;
            return new Response(200, $headers, json_encode($response));
        }
    }
    public function all(Request $request): ResponseInterface
    {
        $headers = ['Content-Type' =>  'application/json'];
        $data = DB::table('articles')->join('users', 'articles.user_id', '=', 'users.user_id')->select('id','nama','title','description','image','articles.created_at','articles.updated_at')->get();
        
        if (!empty($data)){
            for($i=0; $i<count($data);$i++){
                $data[$i]->title = Str::limit($data[$i]->title, 20);
                $data[$i]->urlImage = "https://api.skinpals.id/images/articles/".$data[$i]->image;
                $data[$i]->ArticleById = "https://api.skinpals.id/article/".$data[$i]->id;
            }
            $response['code'] = 200;
            $response['message'] = "Success";
            $response['data'] = $data;
            return new Response(200, $headers, json_encode($response));
        }  else {
            $response['code'] = 404;
            $response['message'] = "Data Not Found";
            $response['data'] = null;
            return new Response(404, $headers, json_encode($response));
        }
    }
    public function getArticleById($key): ResponseInterface{
        $id = $key;
        $headers = ['Content-Type' =>  'application/json'];
        $data = DB::table('articles')->join('users', 'articles.user_id', '=', 'articles.user_id')->select('id','users.user_id','nama','title','description','image','articles.created_at','articles.updated_at')->where('id',$id)->first();
        if (!empty($data)){
            $data->urlImage = "https://api.skinpals.id/images/articles/".$data->image;
            $response['code'] = 200;
            $response['message'] = "Success";
            $response['data'] = $data;
            return new Response(200, $headers, json_encode($response));
        } else {
            $response['code'] = 400;
            $response['message'] = "No article data";
            $response['data'] = null;
            return new Response(200, $headers, json_encode($response));
        }
    }
}