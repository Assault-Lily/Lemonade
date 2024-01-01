<?php

namespace App\Http\Controllers;

use Intervention\Image\ImageManager;

class OGPController extends Controller
{
    public function generate($type, $title){
        $manager = ImageManager::imagick();
        $img = $manager->create(1200, 630); //Image(1200,630, '#F4F5F9');

        // Background
        $img->fill('#F4F5F9');
        $img->drawPolygon(function ($polygon){
            $polygon->point(0,0);
            $polygon->point(700,0);
            $polygon->point(550,630);
            $polygon->point(0,630);
            $polygon->background('#E4E7EE');
        });

        // 0,0, 700,0, 550,630, 0,630

        // SubLine or SiteName
        $subLine = match ($type) {
            'lily' => 'プロフィール',
            'legion' => 'レギオン詳細',
            'book' => '書籍詳細',
            'play' => '公演詳細',
            'anime-series' => 'アニメシリーズ詳細',
            'anime-episode' => 'アニメ各話詳細',
            'other' => config('app.name'),
            default => abort(400),
        };
        $img->text($subLine, 70, 100, function ($font){
            $font->size(50);
            $font->color('#333');
            $font->filename(resource_path('fonts/SourceHanSans-Normal.otf'));
        });

        // Title
        $img->text($title, 70, 240, function ($font) use ($title){
            if(mb_strlen($title) >= 15){
                $size = 60;
            }elseif(mb_strlen($title) >= 11){
                $size = 70;
            }else{
                $size = 100;
            }
            $font->size($size);
            $font->color('#222');
            $font->filename(resource_path('fonts/SourceHanSans-Normal.otf'));
        });

        // Footer
        $footer = config('app.name', 'Lemonade').' : AssaultLily FanSite';
        $img->text($footer, 70, 450, function ($font){
            $font->size(43);
            $font->color('#444');
            $font->filename(resource_path('fonts/SourceHanSans-Normal.otf'));
        });

        $info =  $_SERVER['HTTP_HOST'].PHP_EOL;
        $info .= config('app.name', 'Lemonade').' - Ver'.config('lemonade.version');
        $img->text($info, 70, 530, function ($font){
            $font->size(30);
            $font->color('#444');
            $font->filename(resource_path('fonts/SourceHanSans-Normal.otf'));
        });

        return response($img->toJpeg())->header('Content-Type', 'image/jpeg');
    }
}
