<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ImageDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $images = Image::all();
        return view('admin.image.index', compact('images'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {
        $default = [
            'for' => $request->input('for'),
            'type' => $request->input('type'),
            'author' => $request->input('author'),
            'image_url' => $request->input('image_url'),
            'author_info' => $request->input('author_info'),
        ];

        return view('admin.image.create', compact('default'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'for' => ['required'],
            'type' => ['required'],
            'author' => ['required'],
            'image_url' => ['required', 'active_url']
        ]);

        $image = new Image();
        $image->for = $request->for;
        $image->type = $request->type;
        $image->author = $request->author;
        $image->image_url = $request->image_url;
        $image->author_info = $request->author_info ?? null;

        $image->save();

        return redirect(route('admin.image.edit', ['image' => $image->id]))->with('message','画像レコードを追加しました');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function show($id)
    {
        return redirect(route('admin.image.edit',['image' => $id]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        try {
            $image = Image::findOrFail($id);
        }catch (ModelNotFoundException $e){
            abort(404, '画像レコードが存在しません');
        }

        return view('admin.image.edit', compact('image'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        try {
            $image = Image::findOrFail($id);
        }catch (ModelNotFoundException $e){
            abort(404, 'レコードが存在しません');
        }

        if ($request->id != $id){
            abort(400);
        }

        $request->validate([
            'for' => ['required'],
            'type' => ['required'],
            'author' => ['required'],
            'image_url' => ['required', 'active_url']
        ]);

        $image->for = $request->for;
        $image->type = $request->type;
        $image->author = $request->author;
        $image->image_url = $request->image_url;
        $image->author_info = $request->author_info ?? null;

        $image->save();

        return redirect(route('admin.image.edit',['image' => $id]))->with('message', 'レコードを更新しました');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        try {
            Image::findOrFail($id)->delete();
        }catch (ModelNotFoundException $e){
            abort(400, '指定された画像レコードは存在しません');
        }

        return redirect(route('admin.image.index'))->with('message', 'レコードを削除しました');
    }
}
