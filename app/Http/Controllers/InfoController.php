<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notice;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InfoController extends Controller
{
    public function index(Request $request)
    {
        $categories = match ($request->get('list', 'default')) {
            'fixed' => ['fixed'],
            'terms' => ['terms'],
            'default' => array_filter(array_keys(config('noticeCategories')), function ($c) {
                return !in_array($c, [
                    'fixed',
                    'terms',
                ], true);
            }),
            default => abort(400)
        };

        $page_info = [];
        $order_column = 'updated_at';
        $order_direction = 'desc';
        $order_info_ja = '更新が新しい順';

        switch ($request->get('list')) {
            case 'terms':
                $page_info['type'] = '規約・方針';
                $order_column = 'title';
                $order_direction = 'asc';
                $order_info_ja = '辞書順';
                break;
            default:
                $page_info['type'] = 'お知らせ一覧';
                break;
        }

        $notices = Notice::select(['*'])
            ->whereIn('category', $categories)
            ->orderBy($order_column, $order_direction)
            ->get();

        return view('info.index', compact('notices', 'page_info', 'order_info_ja'));
    }

    public function show(string $slug, Request $request)
    {
        try {
            $notice = Notice::whereSlug($slug)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            abort(404, '該当するお知らせは存在しないか、削除されました。');
        }

        $blade = match ($notice->category) {
            'terms' => 'info.showTerms',
            default => 'info.show',
        };

        return view($blade ?: 'info.show', compact('notice'));
    }
}
