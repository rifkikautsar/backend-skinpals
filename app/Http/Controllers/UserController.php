<?php

namespace App\Http\Controllers;
use PDO;
use Illuminate\Http\Request;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;

class UserController extends Controller
{
    //
    public function activation(Request $request): ResponseInterface
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
            $key = $request->key;
            try{
                $username = getenv('DB_USERNAME');
                $password = getenv('DB_PASSWORD');
                $dbName = getenv('DB_DATABASE');
                $dbHost = getenv('DB_HOST');
                $conn = new PDO("mysql:host=".$dbHost.";dbname=".$dbName, $username, $password);
                $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            } catch(PDOException $e) {
                // tampilkan pesan kesalahan jika koneksi gagal
                return new Response(200, $headers, json_encode("Gagal Koneksi ke Database ", $e->getMessage()));
                die();
            }
            $stmt = $conn->prepare("SELECT id from users where remember_token = :remember_token");
            $stmt->bindParam(":remember_token", $key);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if($stmt->rowCount() == 1){
                $aktif = 1;
                $stmt = $conn->prepare("UPDATE users SET aktif = :aktif WHERE id = :id");
                $stmt->bindParam(":aktif", $aktif);
                $stmt->bindParam(":id", $data['id']);
                $stmt->execute();
                $stmt = null;
                $response['code'] = 200;
                $response['data']['message'] = "Email berhasil diverifikasi. Silakan login ke aplikasi";
                return new Response(200, $headers, json_encode($response));
            }
        }
    }
}