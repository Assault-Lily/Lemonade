<?php

namespace App\Http\Controllers;

use App\Http\Request;
use App\Models\Notice;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InfoController extends Controller
{
    public function index(Request $request)
    {
        $notices = Notice::select(['*'])
            ->whereNotIn('category', ['fixed'])
            ->orWhereNull('category')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('info.index', compact('notices'));
    }

    public function show(string $slug, Request $request)
    {
        try {
            $notice = Notice::whereSlug($slug)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            abort(404, '該当するお知らせは存在しないか、削除されました。');
        }

        return view('info.show', compact('notice'));
    }
}
