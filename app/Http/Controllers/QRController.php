<?php

namespace App\Http\Controllers;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;

class QRController extends Controller
{
    public function generate(Request $request){

        if (!str_starts_with($_SERVER['HTTP_REFERER'] ?? '', config('app.url'))) abort(403);

        $data = $request->get('data');

        if(empty($data)) abort(400);

        $writer = new PngWriter();

        $qrCode = QrCode::create($data)->setEncoding(new Encoding('UTF-8'))
            ->setSize(230)->setMargin(10);

        $result = $writer->write($qrCode);

        return response($result->getString())->header('Content-Type', $result->getMimeType());
    }
}
