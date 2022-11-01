<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDO;
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
            try{       
                $username = getenv('DB_USERNAME');
                $password = getenv('DB_PASSWORD');
                $dbName = getenv('DB_DATABASE');
                $dbHost = getenv('DB_HOST');
                $conn = new PDO("mysql:host=".$dbHost.";dbname=".$dbName, $username, $password);
                $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            } catch (PDOException $e) {
                // tampilkan pesan kesalahan jika koneksi gagal
                return new Response(401, $headers, json_encode("Gagal Koneksi ke Database ", $e->getMessage()));
                die();
            }
            //local time
            $dt = new \DateTime("now", new \DateTimeZone('Asia/Jakarta'));
            $timestamps = $dt->format('Y-m-d H:i:s');
            $obj = json_decode($request->getBody()->getContents());
            $stmt = $conn->prepare("SELECT id, email FROM users WHERE email = :email");
            $stmt->bindParam(":email", $obj->email);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $response['code'] = 200;
                $response['data']['message'] = "Email telah terdaftar. Silakan Login";
                return new Response(200, $headers, json_encode($response));
                $stmt = null;
            } else {
                $hashPasswd = password_hash($obj->pass, PASSWORD_DEFAULT);
                $token=hash('sha256', md5(date('Y-m-d h:i:s').$obj->nama));
                $hashToken = password_hash($token, PASSWORD_DEFAULT);
                $apiKey = implode('', str_split(substr(hash('sha256',microtime().rand(1000, 9999)), 0, 45), 6));
                $aktif = 0;
                $stmt = $conn->prepare("INSERT INTO users(nama,email,jenisKelamin,jenisKulit,tanggalLahir,pass,apiKey,remember_token,aktif,created_at,updated_at) values(:nama,:email,:jenisKelamin,:jenisKulit,:tanggalLahir,:pass,:apiKey,:remember_token,:aktif,:created_at,:updated_at)");
                $stmt->bindParam(":nama", $obj->nama);
                $stmt->bindParam(":email", $obj->email);
                $stmt->bindParam(":jenisKelamin", $obj->jenisKelamin);
                $stmt->bindParam(":jenisKulit", $obj->jenisKulit);
                $stmt->bindParam(":tanggalLahir", $obj->tanggalLahir);
                $stmt->bindParam(":pass", $hashPasswd);
                $stmt->bindParam(":apiKey", $apiKey);
                $stmt->bindParam(":remember_token", $token);
                $stmt->bindParam(":aktif", $aktif);
                $stmt->bindParam("created_at", $timestamps);
                $stmt->bindParam("updated_at", $timestamps);
                $stmt->execute();
                $stmt = null;
                require base_path("vendor/autoload.php");
                $URL = "http://127.0.0.1:8000";
                $mail = new PHPMailer(true);     // Passing `true` enables exceptions
                $body =
                '<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
                
                <head>
                  <!--[if gte mso 9]>
                <xml>
                  <o:OfficeDocumentSettings>
                    <o:AllowPNG/>
                    <o:PixelsPerInch>96</o:PixelsPerInch>
                  </o:OfficeDocumentSettings>
                </xml>
                <![endif]-->
                  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                  <meta name="viewport" content="width=device-width, initial-scale=1.0">
                  <meta name="x-apple-disable-message-reformatting">
                  <!--[if !mso]><!-->
                  <meta http-equiv="X-UA-Compatible" content="IE=edge">
                  <!--<![endif]-->
                  <title></title>
                
                  <style type="text/css">
                    @media only screen and (min-width: 620px) {
                      .u-row {
                        width: 600px !important;
                      }
                      .u-row .u-col {
                        vertical-align: top;
                      }
                      .u-row .u-col-100 {
                        width: 600px !important;
                      }
                    }
                    
                    @media (max-width: 620px) {
                      .u-row-container {
                        max-width: 100% !important;
                        padding-left: 0px !important;
                        padding-right: 0px !important;
                      }
                      .u-row .u-col {
                        min-width: 320px !important;
                        max-width: 100% !important;
                        display: block !important;
                      }
                      .u-row {
                        width: calc(100% - 40px) !important;
                      }
                      .u-col {
                        width: 100% !important;
                      }
                      .u-col>div {
                        margin: 0 auto;
                      }
                    }
                    
                    body {
                      margin: 0;
                      padding: 0;
                    }
                    
                    table,
                    tr,
                    td {
                      vertical-align: top;
                      border-collapse: collapse;
                    }
                    
                    p {
                      margin: 0;
                    }
                    
                    .ie-container table,
                    .mso-container table {
                      table-layout: fixed;
                    }
                    
                    * {
                      line-height: inherit;
                    }
                    
                    a[x-apple-data-detectors='.'true'.'] {
                      color: inherit !important;
                      text-decoration: none !important;
                    }
                    
                    table,
                    td {
                      color: #000000;
                    }
                  </style>
                
                
                
                  <!--[if !mso]><!-->
                  <link href="https://fonts.googleapis.com/css?family=Cabin:400,700" rel="stylesheet" type="text/css">
                  <!--<![endif]-->
                
                </head>
                
                <body class="clean-body u_body" style="margin: 0;padding: 0;-webkit-text-size-adjust: 100%;background-color: #f9f9f9;color: #000000">
                  <!--[if IE]><div class="ie-container"><![endif]-->
                  <!--[if mso]><div class="mso-container"><![endif]-->
                  <table style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;min-width: 320px;Margin: 0 auto;background-color: #f9f9f9;width:100%" cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr style="vertical-align: top">
                        <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                          <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="center" style="background-color: #f9f9f9;"><![endif]-->
                
                
                          <div class="u-row-container" style="padding: 0px;background-color: transparent">
                            <div class="u-row" style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #f8dfc9;">
                              <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                                <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: #f8dfc9;"><![endif]-->
                
                                <!--[if (mso)|(IE)]><td align="center" width="600" style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;" valign="top"><![endif]-->
                                <div class="u-col u-col-100" style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                  <div style="height: 100%;width: 100% !important;">
                                    <!--[if (!mso)&(!IE)]><!-->
                                    <div style="height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;">
                                      <!--<![endif]-->
                
                                      <table style="font-family:'.'Cabin'.',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                                        <tbody>
                                          <tr>
                                            <td style="overflow-wrap:break-word;word-break:break-word;padding:40px 10px 10px;font-family:'.'Cabin'.',sans-serif;" align="left">
                
                                              <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                <tr>
                                                  <td style="padding-right: 0px;padding-left: 0px;" align="center">
                
                                                    <img align="center" border="0" src="https://cdn.templates.unlayer.com/assets/1597218650916-xxxxc.png" alt="Image" title="Image" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 26%;max-width: 150.8px;"
                                                      width="150.8" />
                
                                                  </td>
                                                </tr>
                                              </table>
                
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                
                                      <table style="font-family:'.'Cabin'.',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                                        <tbody>
                                          <tr>
                                            <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'.'Cabin'.',sans-serif;" align="left">
                
                                              <div style="color: #000000; line-height: 140%; text-align: center; word-wrap: break-word;">
                                                <p style="font-size: 14px; line-height: 140%;"><strong>Terimakasih telah mendaftar !</strong></p>
                                              </div>
                
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                
                                      <table style="font-family:'.'Cabin'.',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                                        <tbody>
                                          <tr>
                                            <td style="overflow-wrap:break-word;word-break:break-word;padding:0px 10px 31px;font-family:'.'Cabin'.',sans-serif;" align="left">
                
                                              <div style="color: #000000; line-height: 140%; text-align: center; word-wrap: break-word;">
                                                <p style="font-size: 14px; line-height: 140%;"><span style="font-size: 28px; line-height: 39.2px;"><strong><span style="line-height: 39.2px; font-size: 28px;">Verifikasi Alamat Email</span></strong>
                                                  </span>
                                                </p>
                                              </div>
                
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                
                                      <!--[if (!mso)&(!IE)]><!-->
                                    </div>
                                    <!--<![endif]-->
                                  </div>
                                </div>
                                <!--[if (mso)|(IE)]></td><![endif]-->
                                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                              </div>
                            </div>
                          </div>
                
                
                
                          <div class="u-row-container" style="padding: 0px;background-color: transparent">
                            <div class="u-row" style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;">
                              <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                                <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: #ffffff;"><![endif]-->
                
                                <!--[if (mso)|(IE)]><td align="center" width="600" style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;" valign="top"><![endif]-->
                                <div class="u-col u-col-100" style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                  <div style="height: 100%;width: 100% !important;">
                                    <!--[if (!mso)&(!IE)]><!-->
                                    <div style="height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;">
                                      <!--<![endif]-->
                
                                      <table style="font-family:'.'Cabin'.',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                                        <tbody>
                                          <tr>
                                            <td style="overflow-wrap:break-word;word-break:break-word;padding:33px 55px;font-family:'.'Cabin'.',sans-serif;" align="left">
                
                                              <div style="line-height: 160%; text-align: center; word-wrap: break-word;">
                                                <p style="font-size: 14px; line-height: 160%;"><span style="font-size: 22px; line-height: 35.2px;">Hi, '.$obj->nama.'</span></p>
                                                <p style="font-size: 14px; line-height: 160%;"><span style="font-size: 18px; line-height: 28.8px;">Sebelum login ke aplikasi SkinPals, silakan anda melakukan verifikasi email dengan klik tombol di bawah.</span></p>
                                              </div>
                
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                
                                      <table style="font-family:'.'Cabin'.',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                                        <tbody>
                                          <tr>
                                            <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'.'Cabin'.',sans-serif;" align="left">
                
                                              <div style="color: #f8dfc9; line-height: 260%; text-align: center; word-wrap: break-word;">
                                                <p style="font-size: 14px; line-height: 260%;"><strong><span style="font-size: 20px; line-height: 52px;"><a href = '.$URL.'/activation?'.$token.'>VERIFIKASI <span style="line-height: 52px; font-size: 20px;">DISINI</span></span></a></strong></p>
                                              </div>
                
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                
                                      <table style="font-family:'.'Cabin'.',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                                        <tbody>
                                          <tr>
                                            <td style="overflow-wrap:break-word;word-break:break-word;padding:33px 55px 60px;font-family:'.'Cabin'.',sans-serif;" align="left">
                
                                              <div style="line-height: 160%; text-align: center; word-wrap: break-word;">
                                                <p style="line-height: 160%; font-size: 14px;"><span style="font-size: 18px; line-height: 28.8px;">Terimakasih,</span></p>
                                                <p style="line-height: 160%; font-size: 14px;"><span style="font-size: 18px; line-height: 28.8px;">Tim SkinPals</span></p>
                                              </div>
                
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                
                                      <!--[if (!mso)&(!IE)]><!-->
                                    </div>
                                    <!--<![endif]-->
                                  </div>
                                </div>
                                <!--[if (mso)|(IE)]></td><![endif]-->
                                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                              </div>
                            </div>
                          </div>
                
                
                
                          <div class="u-row-container" style="padding: 0px;background-color: transparent">
                            <div class="u-row" style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #f8dfc9;">
                              <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                                <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: #f8dfc9;"><![endif]-->
                
                                <!--[if (mso)|(IE)]><td align="center" width="600" style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;" valign="top"><![endif]-->
                                <div class="u-col u-col-100" style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                  <div style="height: 100%;width: 100% !important;">
                                    <!--[if (!mso)&(!IE)]><!-->
                                    <div style="height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;">
                                      <!--<![endif]-->
                
                                      <table style="font-family:'.'Cabin'.',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                                        <tbody>
                                          <tr>
                                            <td style="overflow-wrap:break-word;word-break:break-word;padding:41px 55px 18px;font-family:'.'Cabin'.',sans-serif;" align="left">
                
                                              <div style="color: #000000; line-height: 160%; text-align: center; word-wrap: break-word;">
                                                <p style="font-size: 14px; line-height: 160%;"><span style="font-size: 20px; line-height: 32px;"><strong>Support Service</strong></span></p>
                                                <p style="font-size: 14px; line-height: 160%;"><span style="font-size: 16px; line-height: 25.6px; color: #000000;">support@skinpals.id</span></p>
                                              </div>
                
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                
                                      <!--[if (!mso)&(!IE)]><!-->
                                    </div>
                                    <!--<![endif]-->
                                  </div>
                                </div>
                                <!--[if (mso)|(IE)]></td><![endif]-->
                                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                              </div>
                            </div>
                          </div>
                
                
                
                          <div class="u-row-container" style="padding: 0px;background-color: transparent">
                            <div class="u-row" style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #f8dfc9;">
                              <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                                <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: #f8dfc9;"><![endif]-->
                
                                <!--[if (mso)|(IE)]><td align="center" width="600" style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;" valign="top"><![endif]-->
                                <div class="u-col u-col-100" style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                  <div style="height: 100%;width: 100% !important;">
                                    <!--[if (!mso)&(!IE)]><!-->
                                    <div style="height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;">
                                      <!--<![endif]-->
                
                                      <table style="font-family:'.'Cabin'.',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                                        <tbody>
                                          <tr>
                                            <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'.'Cabin'.',sans-serif;" align="left">
                
                                              <div style="color: #000000; line-height: 180%; text-align: center; word-wrap: break-word;">
                                                <p style="font-size: 14px; line-height: 180%;"><span style="font-size: 16px; line-height: 28.8px;">Copyrights &copy SkinPals All Rights Reserved</span></p>
                                              </div>
                
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                
                                      <!--[if (!mso)&(!IE)]><!-->
                                    </div>
                                    <!--<![endif]-->
                                  </div>
                                </div>
                                <!--[if (mso)|(IE)]></td><![endif]-->
                                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                              </div>
                            </div>
                          </div>
                
                
                          <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <!--[if mso]></div><![endif]-->
                  <!--[if IE]></div><![endif]-->
                </body>
                </html>';
                try {
                    // Email server settings
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP();
                    $mail->Host = 'mail.skinpals.id';             //  smtp host
                    $mail->SMTPAuth = true;
                    $mail->Username = 'noreply@skinpals.id';   //  sender username
                    $mail->Password = getenv("PASS_EMAIL");       // sender password
                    $mail->SMTPSecure = 'ssl';                  // encryption - ssl/tls
                    $mail->Port = 465;                          // port - 587/465
                    $mail->setFrom('noreply@skinpals.id', 'noreply@skinpals.id');
                    $mail->addAddress($obj->email);    
                    $mail->addReplyTo('support@skinpals.id', '');
                    $mail->isHTML(true);                // Set email content format to HTML
                    $mail->Subject = "Verifikasi Akun Anda";
                    $mail->MsgHTML($body);
                    if( !$mail->send() ) {
                        $response['code'] = 400;
                        $response['data']['message'] = "Registrasi Gagal. Email tidak terkirim. {$mail->ErrorInfo}";
                        return new Response (400, $headers, json_encode($response));
                    }
                    else {
                        $response['code'] = 200;
                        $response['data']['message'] = "Registrasi Berhasil. Silakan Cek Email untuk verifikasi";
                        return new Response(200, $headers, json_encode($response));
                    }
                } catch (Exception $e) {
                    $response['code'] = 400;
                    $response['data']['message'] = "Registrasi Gagal. Email tidak terkirim. {$mail->ErrorInfo}";
                    return new Response (400, $headers, json_encode($response));
                }
            }
        }
    }
}