<?php

namespace App\Http\Controllers\studio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Studio;
use App\Models\Jasa;
use App\Models\JasaRevision;
use App\Models\JasaPicture;
use App\Models\JasaAdditional;
use App\Models\Category;

class StudioController extends Controller
{
    public function __construct() {
        return $this->middleware(['auth', 'studiocomplete']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect('/mystudio/dashboard');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $studio = Studio::where('user_id', auth()->user()->id)->first();
        $jasa = Jasa::create([
            'jasa_id' => date('ymd') . rand(),
            'user_id' => $studio->id,
            'jasa_name' => $request->title,
        ]);
        return redirect('manage/' . strtolower(str_replace(' ', '-', $request->title)));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $seller = Studio::with('portfolio.subcategory.parent', 'owner', 'logo')
        ->where([
            ['user_id', auth()->user()->id],
            ])
        ->first();
        $category = Category::all();
        switch($id) {
            case "dashboard":
                return view('seller.dashboard', ['seller' => $seller]);
                // return response()->json(['seller' => $seller]);
                break;
            case "upload":
                return view('seller.uploads.title', ['seller' => $seller, 'category' => $category]);
                // return response()->json(['seller' => $seller]);
                break;
        }
    }

    public function manage($id)
    {
        $category = Category::all();
        $studio = Studio::where('user_id', auth()->user()->id)->first();
        $slugs = str_replace('-', ' ', $id);
        $data = Jasa::with('seller', 'subcategory', 'revisi', 'additional', 'pictures')
        ->where([
            ['jasa_name', $slugs],
            ['user_id', $studio->id],
            ])
        ->first();
        if($data) {
            return view('seller.uploads.index', ['products' => $data, 'category' => $category, 'seller' => $studio]);
            // return response()->json($data);
        } else {
            return back();
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $studio = Studio::where('user_id', auth()->user()->id)->first();
        $revision = JasaRevision::create([
            'count' => $request->info['revisi_count'],
            'price' => preg_replace(['/[Rp,.]/'],'',$request->info['revisi_price'] ?? 0),
            'add_day' => $request->info['revisi_waktu'],
        ]);
        if($request->picture) {
            foreach($request->picture as $jp) {
                JasaPicture::where('id', $jp)->update([
                    'jasa_id' => $id,
                ]);
            }
        }

        $jasa = Jasa::where('jasa_id', $id)->update([
            'jasa_name' => $request->info['title'],
            'jasa_deskripsi' => $request->info['description'],
            'jasa_subcategory' => $request->info['subcategory'],
            'jasa_price' =>  preg_replace(['/[Rp,.]/'],'',$request->info['price_start']),
            // 'jasa_thumbnail' => $request->info['cover'],
            'jasa_revision' => $revision->id,
            'jasa_status' => true,
        ]);
        $add = array();
        if($request->data) {
            foreach($request->data as $name) {
                if(isset($name['id'])) {
                    JasaAdditional::where('id', $name['id'])->update([
                        'title' => $name['add_name'],
                        'jasa_id' => $id,
                        'price' => preg_replace(['/[Rp,.]/'],'', $name['add_price'] ?? 0),
                        'add_day' => $name['add_day'],
                    ]);
                } else {
                    JasaAdditional::create([
                        'title' => $name['add_name'],
                        'jasa_id' => $id,
                        'price' => preg_replace(['/[Rp,.]/'],'', $name['add_price'] ?? 0),
                        'add_day' => $name['add_day'],
                    ]);
                }
            }
        }
        return response()->json($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = JasaAdditional::where('id', $id)->delete();
    }

    public function destroy_picture($id)
    {
        $data = JasaPicture::where('id', $id)->delete();
    }
}
