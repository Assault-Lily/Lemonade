<?php

namespace App\Http\Controllers;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Endroid\QrCode\Label\Label;

class QRController extends Controller
{
    public function generate(Request $request){

        if (!str_starts_with($_SERVER['HTTP_REFERER'] ?? '', config('app.url'))) abort(403);

        $data = $request->get('data');

        if(empty($data)) abort(400);

        $writer = new PngWriter();

        $qrCode = QrCode::create($data)->setEncoding(new Encoding('UTF-8'))
            ->setSize($request->get('size', 200) - 20)->setMargin(10);

        if(!empty($request->get('label'))){
            $label = Label::create($request->get('label'))->setFont(new NotoSans(10));
        }else{
            $label = null;
        }

        $result = $writer->write($qrCode,null , $label);

        return response($result->getString())->header('Content-Type', $result->getMimeType());
    }
}
