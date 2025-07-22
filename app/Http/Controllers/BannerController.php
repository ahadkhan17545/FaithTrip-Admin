<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Yajra\DataTables\DataTables;

class BannerController extends Controller
{
    public function viewAllBanners(Request $request){
        if ($request->ajax()) {

            $query = Banner::orderBy('id', 'desc');

            return Datatables::of($query)
                    ->editColumn('status', function ($data) {
                        if ($data->status == 1) {
                            return '<span class="btn btn-sm btn-success rounded" style="padding: 0.1rem .5rem;">Active</span>';
                        } else {
                            return '<span class="btn btn-sm btn-warning rounded" style="padding: 0.1rem .5rem;">Inactive</span>';
                        }
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" data-original-title="Delete" class="btn-sm btn-danger rounded d-inline-block deleteBtn mb-1"><i class="fa fa-trash"></i></a>';
                        $btn .= ' <a href="'.url('edit/banner')."/".$data->slug.'" class="btn-sm btn-warning rounded d-inline-block mb-1"><i class="fa fa-edit"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'image', 'status'])
                    ->make(true);
        }
        return view('banners.view');
    }

    public function addNewBanner(){
        return view('banners.create');
    }

    public function saveBanner(Request $request){

        $image = null;
        if ($request->hasFile('image')){
            $file = $request->file('image');
            $file_name = str::random(5) . time() . '.' . $file->getClientOriginalExtension();
            $file_location = public_path('bannerImages/');
            $file->move($file_location, $file_name);
            $image = "bannerImages/" . $file_name;
        }

        Banner::insert([
            'image' => $image,
            'url' => $request->url,
            'status' => $request->status,
            'slug' => str::random(3) . "-" . time(),
            'created_at' => Carbon::now()
        ]);

        Toastr::success('New Banner Added');
        return redirect('view/all/banners');
    }

    public function editBanner($slug){
        $data = Banner::where('slug', $slug)->first();
        return view('banners.update', compact('data'));
    }

    public function updateBanner(Request $request){
        $bannerInfo = Banner::where('id', $request->banner_id)->first();

        $image = $bannerInfo->image;
        if ($request->hasFile('image')){
            if (file_exists(public_path($bannerInfo->image))){
                unlink(public_path($bannerInfo->image));
            }
            $file = $request->file('image');
            $file_name = str::random(5) . time() . '.' . $file->getClientOriginalExtension();
            $file_location = public_path('bannerImages/');
            $file->move($file_location, $file_name);
            $image = "bannerImages/" . $file_name;
        }

        Banner::where('id', $request->banner_id)->update([
            'image' => $image,
            'url' => $request->url,
            'status' => $request->status,
            'updated_at' => Carbon::now()
        ]);

        Toastr::success('Banner Info Updated');
        return redirect('view/all/banners');
    }

    public function deleteBanner($slug){
        $banner = Banner::where('slug', $slug)->first();
        if (file_exists(public_path($banner->image))){
            unlink(public_path($banner->image));
        }
        $banner->delete();
        return response()->json(['success' => 'Deleted Successfully']);
    }
}
