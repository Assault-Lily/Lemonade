<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lily;
use App\Rules\HexColorCode;
use App\Rules\Hiragana;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class LilyDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $data = Lily::all();
        return view('admin.lily.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.lily.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'slug'   => ['required', 'alpha'],
            'name'   => 'required',
            'name_y' => ['required', new Hiragana('・ ')],
            'name_a' => ['required'],
            'color'  => [new HexColorCode],
        ]);

        $lily = new Lily;
        $lily->slug = strtolower($request->slug);
        $lily->name = $request->name;
        $lily->name_y = $request->name_y;
        $lily->name_a = $request->name_a;
        $lily->color = $request->color;

        $lily->save();

        return redirect(route('admin.lily.index'))->with('message', "レコードを追加しました");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $lily = Lily::findOrFail($id);
        }catch (ModelNotFoundException $e){
            abort(404, 'レコードが存在しません');
        }


        return view('admin.lily.show', compact('lily'));
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
            $lily = Lily::findOrFail($id);
        }catch (ModelNotFoundException $e){
            abort(404, 'レコードが存在しません');
        }

        return view('admin.lily.edit', compact('lily'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        try {
            $lily = Lily::findOrFail($id);
        }catch (ModelNotFoundException $e){
            abort(404, 'レコードが存在しません');
        }

        if ($request->id != $id){
            abort(400);
        }

        $request->validate([
            'slug'   => ['required', 'alpha'],
            'name'   => 'required',
            'name_y' => ['required', new Hiragana('・ ')],
            'name_a' => ['required'],
            'color'  => [new HexColorCode],
        ]);

        $lily->slug = strtolower($request->slug);
        $lily->name = $request->name;
        $lily->name_y = $request->name_y;
        $lily->name_a = $request->name_a;
        $lily->color = $request->color;

        $lily->save();

        return redirect(route('admin.lily.index'))->with('message', "レコードを更新しました");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort(400);
    }
}
