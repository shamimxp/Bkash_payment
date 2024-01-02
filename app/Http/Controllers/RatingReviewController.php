<?php

namespace App\Http\Controllers;

use App\Models\RatingReview;
use App\Models\Setting;
use Illuminate\Http\Request;

class RatingReviewController extends Controller
{
    public function index(){
        $review_data = RatingReview::with('product')->paginate(10);
        return view('admin.rating.index',compact('review_data'));
    }
    public function store(Request $request){
        $request->validate([
            'name'=>'required',
            'product_id'=>'required',
            'rating'=>'required',
            'review_title'=>'required',
            'review'=>'required',
        ]);
        $data =new RatingReview();
        $data->product_id = $request->product_id;
        $data->name = $request->name;
        $data->rating = $request->rating;
        $data->review_title = $request->review_title;
        $data->review = $request->review;
        $data->save();
        $alert = ['success','Comment insert Successfully!'];
        return back()->withAlert($alert);
    }
    public function delete($id){
        $data = RatingReview::findOrfail($id);
        $data->delete();
        $alert = ['success','Review Delete Successfully!'];
        return back()->withAlert($alert);
    }
}