<?php

namespace App\Http\Controllers\studio;

use App\Models\Jasa;
use App\Models\Studio;
use App\Models\Category;
use App\Models\JasaView;
use App\Models\OrderJasa;
use App\Models\StudioLogo;
use App\Models\StudioRank;
use App\Models\JasaPicture;
use App\Models\OrderCancel;
use App\Models\StudioLover;
use App\Models\StudioPoint;
use App\Models\JasaRevision;
use App\Models\OrderSuccess;
use Illuminate\Http\Request;
use App\Models\StudioAddress;
use App\Models\StudioVisitor;
use App\Models\JasaAdditional;
use App\Http\Controllers\Controller;

class StudioController extends Controller
{
    public function __construct() {
        return $this->middleware(['auth', 'studiocomplete'])->except('studios');
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

    public function love(Request $request)
    {
        StudioLover::firstOrCreate([
            'studio_id' => $request->id,
            'customer_id' => auth()->user()->id,
        ]);
    }

    public function unlove(Request $request)
    {
        StudioLover::where([
            ['studio_id', $request->id],
            ['customer_id', auth()->user()->id],
            ])->forceDelete();
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
            'studio_id' => $studio->id,
            'jasa_name' => $request->title,
            'jasa_deskripsi' => $request->description,
        ]);
        JasaAdditional::create([
            'jasa_id' => $jasa->jasa_id,
            'title' => 'Revisi',
            'price' => 0,
            'type' => 'Revision',
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
        $points = StudioPoint::where('studio_id', studio()->id)->sum('value');
        $seller = Studio::with(
            'portfolio.subcategory.parent',
            'owner', 'logo',
            'address.province', 'address.district',
            'portfolio.cover', 'portfolio.views')
        ->where([
            ['user_id', auth()->user()->id],
            ])
        ->first();
        // $sells = Jasa::where('studio_id', auth()->user()->studio->id)->sum('jasa_sold');
        $orders = OrderJasa::with(['success', 'products' => function($q) {
            $q->where('studio_id', studio()->id);
        }])->get();
        $success = OrderSuccess::with(['orders.products' => function($q) {
            $q->where('studio_id', studio()->id);
        }])->get();
        $category = Category::all();
        foreach($seller->portfolio->sortby('jasa_sold')->take(3) as $p) {
            $jasaChart[] = $p->jasa_name;
            $jasaJual[] = $p->jasa_sold;
            $jasaTampil[] = $p->views()->count();
        }
        $jasaTampilCount = 0;
        foreach($seller->portfolio as $p) {
            $jasaTampilCount += $p->views()->count();
        }
        $cancel = OrderCancel::where([
            ['studio_id', studio()->id],
            ])->get();
        $growthJasa = new Jasa;
        $growthJasa->studio_id = studio()->id;

        $growthView = new JasaView;

        $growthSells = new OrderSuccess;
        $growthSells->studio_id = studio()->id;

        $growthCancel = new OrderCancel;
        $growthCancel->studio_id = studio()->id;

        $visitors = new StudioVisitor;
        $visitors->studio_id = studio()->id;

        $revenue = new OrderSuccess;
        $revenue->studio_id = studio()->id;

        $visitors = new StudioVisitor;
        $visitors->studio_id = studio()->id;

        $ranks = StudioRank::get();

        switch($id) {
            case "dashboard":
                return view('seller.dashboard', ['seller' => $seller, 'orders' => $orders]);
                // return response()->json(['seller' => $seller, 'sells' => $sells, 'orders' => $orders]);
                break;
            case "produk":
                return view('seller.products', ['seller' => $seller, 'orders' => $orders, 'jasa_name' => $jasaChart,
                'jasa_jual' => $jasaJual, 'jasa_tampil' => $jasaTampil, 'jasa_count' => $jasaTampilCount, 
                'cancel' => $cancel, 'growthJasa' => $growthJasa->setGrowth(), 'growthView' => $growthView->setGrowth(),
                'growthSells' => $growthSells->setGrowth(), 'growthCancel' => $growthCancel->setGrowth()]);
                // return response()->json(['seller' => $seller, 'orders' => $orders, 'jasa_name' => $jasaChart]);
                break;
            case "statistik":
                return view('seller.statistics', ['seller' => $seller, 'orders' => $orders, 'success' => $success, 
                'visitors' => $visitors, 'revenue' => $revenue, 'visitors' => $visitors, 'points' => $points, 'ranks' => $ranks]);
                // return response()->json(['seller' => $seller, 'orders' => $orders, 'success' => $success]);
                break;
            case "upload":
                return view('seller.uploads.title', ['seller' => $seller, 'category' => $category]);
                // return response()->json(['seller' => $seller]);
                break;
            case "orders":
                return view('seller.orders', ['seller' => $seller]);
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
            ['studio_id', $studio->id],
            ])
        ->first();
        if($data) {
            return view('seller.uploads.index', ['products' => $data, 'category' => $category, 'seller' => $studio]);
            // return response()->json($data);
        } else {
            return back();
        }

    }

    public function share($id)
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
            return view('seller.uploads.finish', ['products' => $data]);
            // return response()->json($data);
        } else {
            return back();
        }

    }

    public function studios($slug) {
        $slugs = str_replace('-', ' ', $slug);
        $seller = Studio::with(
            'portfolio.subcategory.parent',
            'owner', 'logo',
            'address.province', 'address.district',
            'portfolio.cover', 'portfolio')
        ->where([
            ['name', $slugs],
            ['is_complete', 1],
            ])
        ->first();
        if(! empty($seller)) {
            if($seller->user_id == auth()->user()->id ?? 0) {
                return redirect('/mystudio/dashboard');
            } else {
                return view('seller.dashboard', ['seller' => $seller]);
            }
        } else {
            abort(404);
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function visitors(Request $request)
    {
        StudioVisitor::create([
            'user_id' => auth()->user()->id,
            'studio_id' => $request->id,
        ]);
    }

    public function edit_profil(Request $request, $id)
    {
        $data = Studio::where('user_id', auth()->user()->id)->first();
        $data->update([
            'slogan' => $request->studio_slogan,
            'description' => $request->description,
            'logo_id' => ($request->studio_logo) ? $request->studio_logo : $data->logo_id,
        ]);

        StudioAddress::updateOrCreate([
            'studio_id' => $data->id,
        ],
        [
            'studio_id' => $data->id,
            'address_name' => $request->address_name,
            'address' => $request->address,
            'state' => $request->province,
            'city' => $request->district,
            'subdistrict' => $request->subdistrict,
            'village' => $request->village,
        ]);
        return back();
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
        $jasa = Jasa::where('jasa_id', $id)->first();
        $studio = Studio::where('user_id', auth()->user()->id)->first();

        if($request->picture) {
            foreach($request->picture as $jp) {
                JasaPicture::where('id', $jp)->update([
                    'jasa_id' => $id,
                ]);
            }
        }

        Jasa::where('jasa_id', $id)->update([
            'jasa_name' => $request->info['title'],
            'jasa_deskripsi' => $request->info['description'],
            'jasa_subcategory' => $request->info['subcategory'],
            'jasa_price' =>  preg_replace(['/[Rp,.]/'],'',$request->info['price_start']),
            'jasa_thumbnail' => ($request->picture == [null]) ? $jasa->jasa_thumbnail : $request->picture[0],
            'jasa_revision' => $revision->id ?? $jasa->jasa_revision,
        ]);
        $add = array();
        if($request->data) {
            foreach($request->data as $name) {
                JasaAdditional::updateOrCreate([
                    'id' => $name['id'] ?? '',
                ],[
                    'title' => $name['add_name'],
                    'jasa_id' => $id,
                    'quantity' => intval($name['quantity']),
                    'price' => preg_replace(['/[Rp,.]/'],'', $name['add_price'] ?? 0),
                    'add_day' => $name['add_day'],
                ]);
            }
        }
        // return response()->json($request->all());
        return response()->json(['status' => 200, 'url' => url('share/' . strtolower(str_replace(' ', '-', $request->title))), 'message' => 'Berhasil Mengubah']);
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
        $jasa = Jasa::where('jasa_thumbnail', $id)->first();
        JasaPicture::where('id', $id)->delete();
        if(! empty($jasa)) {
            $pic = JasaPicture::where('jasa_id', $jasa->jasa_id)->first();
            $jasa = Jasa::where('jasa_thumbnail', $id)
            ->update([
                'jasa_thumbnail' => $pic->id ?? '',
                ]);
            }
    }
}
