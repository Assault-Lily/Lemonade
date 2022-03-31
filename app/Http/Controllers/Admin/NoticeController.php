<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Log;

class NoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $notice = Notice::all();
        return view('admin.notice.index', compact('notice'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.notice.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'bail|required|unique:App\Models\Notice,slug',
            'title' => 'required',
            'importance' => 'required|between:1,100',
            'body' => 'required'
        ]);

        $notice = new Notice();

        $notice->slug = $request->slug;
        $notice->title = $request->title;
        $notice->category = $request->category;
        $notice->importance = $request->importance;
        $notice->body = base64_encode($request->body);

        $notice->save();

        Log::channel('adminlog')->info('Notice added. id:'.$notice->id.' slug:'.$notice->slug);

        return redirect(route('admin.notice.edit', ['notice' => $notice->id]))->with('message', 'お知らせを追加しました');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function show($id)
    {
        return redirect(route('admin.notice.edit', ['notice' => $id]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $notice = Notice::findOrFail($id);
        }catch (ModelNotFoundException $e){
            abort(404, '指定されたお知らせは存在しません');
        }

        return view('admin.notice.edit', compact('notice'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        try {
            $notice = Notice::findOrFail($id);
        }catch (ModelNotFoundException $e){
            abort(404, '指定されたお知らせは存在しません');
        }

        $request->validate([
            'slug' => ['bail', 'required', Rule::unique('notices')->ignore($notice->slug, 'slug')],
            'title' => 'required',
            'importance' => 'required|between:1,100',
            'body' => 'required',
        ]);

        if((int)$request->id !== $notice->id) abort(400, 'お知らせのIDが異なります');

        $notice->slug = $request->slug;
        $notice->title = $request->title;
        $notice->category = $request->category;
        $notice->importance = $request->importance;
        $notice->body = base64_encode($request->body);

        $notice->update();

        Log::channel('adminlog')->info('Notice edited. id:'.$notice->id.' slug:'.$notice->slug);

        return redirect(route('admin.notice.edit', ['notice' => $notice->id]))->with('message', 'お知らせを更新しました');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        try {
            Notice::findOrFail($id)->delete();
        }catch (ModelNotFoundException $e){
            abort(404 ,'指定されたお知らせは存在しません');
        }

        return redirect(route('admin.notice.index'))->with('message', 'お知らせを削除しました');
    }
}
