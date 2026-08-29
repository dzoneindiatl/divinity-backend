<?php

namespace App\Http\Controllers\Admin;

use App\Config;
use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Response,Str,Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

class SliderController extends Controller {
    public $model = 'sliders';
    public $listRouteName;
    public $request;
    public function __construct(Request $request) {
        $this->middleware('permission:view_slider|create_slider|edit_slider|delete_slider', ['only' => ['index','store']]);
        $this->middleware('permission:create_slider', ['only' => ['create','store']]);
        $this->middleware('permission:edit_slider', ['only' => ['edit','update','changeStatus']]);
        $this->middleware('permission:delete_slider', ['only' => ['destroy']]);

        $this->listRouteName = 'admin-sliders.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;
    }

    public function index(Request $request) {
        $DB = Slider::query();
        $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'sliders.created_at';
        $order = $request->input('order') ? $request->input('order') : 'desc';
        $offset = !empty($request->input('offset')) ? $request->input('offset') : 0 ;
        $limit =  !empty($request->input('limit')) ? $request->input('limit') : Config("Reading.records_per_page");

        if ($request->all()) {
            $searchData = $request->all();
            unset($searchData['display']);
            unset($searchData['_token']);
            if (isset($searchData['order'])) {
                unset($searchData['order']);
            }
            if (isset($searchData['sortBy'])) {
                unset($searchData['sortBy']);
            }
            if (isset($searchData['offset'])) {
                unset($searchData['offset']);
            }
            if (isset($searchData['limit'])) {
                unset($searchData['limit']);
            }
            if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
                $dateS = $searchData['date_from'];
                $dateE = $searchData['date_to'];
                $DB->whereBetween('orders.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('orders.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('orders.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "title") {
                        $DB->where("sliders.title", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("sliders.is_active", $fieldValue);
                    }
                }
            }
        }

        $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();
        $totalResults = $DB->count();
        if($request->ajax()){
            return  View("admin.$this->model.load_more_data", compact('results','totalResults'));
        }else{
            return  View("admin.$this->model.index", compact('results','totalResults'));
        }
    }

    public function view($id){
        $id = base64_decode($id);
        $order = Slider::find($id);
        return View("admin.$this->model.view",['order' => $order]);
    }

    public function create(Request $request) {
        if($request->isMethod('post')){
            try{
                $rules = [
                    'media_type' => 'required',
                    'media' => 'required|mimes:jpg,jpeg,png|max:2048',
                    'position' => 'required',
                    'redirection_url' => 'nullable|url'
                ];
                if($request->media_type == 'others'){
                    $rules = [
                        'media_url' => 'required'
                    ];
                }
                if($request->media_type == 'video'){
                    $rules = [
                        'media' => 'required|mimes:mp4,wmv|max:2048'
                    ];
                }
                if($request->media_type == 'banner'){
                    $rules = [
                        'media' => 'required|mimes:mp4,wmv,jpg,jpeg,png|max:2048'
                    ];
                }
                //echo "<pre>"; print_r($request->all()); die;
                
                $validator = Validator::make($request->all(),$rules);
                if($validator->fails()) {
                    return Redirect::back()->withErrors($validator);
                }
                DB::beginTransaction();
                
                //echo "<pre>"; print_r($request->all()); die;
                $slider = new Slider();
                $slider->media_type         = $request->media_type;
                $slider->position           = $request->position;
                $slider->title              = $request->title;
                $slider->redirection_url    = $request->redirection_url;
                $slider->is_active_shop_btn = $request->is_active_shop_btn;
                $slider->is_active          = $request->is_active;
                $slider->description        = $request->description;
                $slider->height             = $request->height;
                $slider->width              = $request->width;

                if ($request->hasFile('media')) {
                    $extension = $request->file('media')->getClientOriginalExtension();
                    $originalName = $request->file('media')->getClientOriginalName();
                    $fileName = time() . '-'.$request->media_type.'.' . $extension;

                    $folderName = "slider/".strtoupper(date('M') . date('Y')) . "/";
                    $folderPath = Config('constant.BANNER_IMAGE_ROOT_PATH') . $folderName;
                    if (!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, $mode = 0777, true);
                    }
                    if ($request->file('media')->move($folderPath, $fileName)) {
                        $slider->media_url = $folderName . $fileName;
                    }
                } else {
                    $slider->media_url = $request->media_url;
                }

                $slider->save();
                
                DB::commit();
                return redirect()->route("admin-$this->model.index")->with(['success'=>"Slider Added Successfully."]);
            } catch(Exception $e) {
                DB::rollback();
                Log::error($e);
                return redirect()->back()->with(['error' => $e->getMessage()]);
            }
        } else {
            $pageHeading = "create";
            return View("admin.$this->model.create",compact('pageHeading'));
        }
    }
    
    public function edit(Request $request, $id = null) {
        $id = base64_decode($id);
        if($request->isMethod('post')){
            try{
                $rules = [
                    'media_type' => 'required',
                    'media' => 'nullable|mimes:jpg,jpeg,png|max:2048',
                    'position' => 'required',
                    'redirection_url' => 'nullable|url'
                ];
                if($request->media_type == 'others'){
                    $rules = [
                        'media_url' => 'required'
                    ];
                }
                if($request->media_type == 'video'){
                    $rules = [
                        'media' => 'nullable|mimes:mp4,wmv|max:2048'
                    ];
                }
                if($request->media_type == 'banner'){
                    $rules = [
                        'media' => 'required|mimes:mp4,wmv,jpg,jpeg,png|max:2048'
                    ];
                }
                //echo "<pre>"; print_r($request->all()); die;
                
                $validator = Validator::make($request->all(),$rules);
                if($validator->fails()) {
                    return Redirect::back()->withErrors($validator);
                }
                DB::beginTransaction();
                
                //echo "<pre>"; print_r($request->all()); die;
                $slider = Slider::find($id);
                $slider->media_type         = $request->media_type;
                $slider->position           = $request->position;
                $slider->title              = $request->title;
                $slider->redirection_url    = $request->redirection_url;
                $slider->is_active_shop_btn = $request->is_active_shop_btn;
                $slider->is_active          = $request->is_active;
                $slider->description        = $request->description;
                $slider->height             = $request->height;
                $slider->width              = $request->width;

                if ($request->hasFile('media')) {
                    $extension = $request->file('media')->getClientOriginalExtension();
                    $originalName = $request->file('media')->getClientOriginalName();
                    $fileName = time() . '-'.$request->media_type.'.' . $extension;

                    $folderName = "slider/".strtoupper(date('M') . date('Y')) . "/";
                    $folderPath = Config('constant.BANNER_IMAGE_ROOT_PATH') . $folderName;
                    if (!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, $mode = 0777, true);
                    }
                    if ($request->file('media')->move($folderPath, $fileName)) {
                        $slider->media_url = $folderName . $fileName;
                    }
                } 
                if(!empty($request->media_url)) {
                    $slider->media_url = $request->media_url;
                }

                $slider->save();

                DB::commit();
                return redirect()->route("admin-$this->model.index")->with(['success'=>"Slider Updated Successfully."]);
            } catch(Exception $e) {
                DB::rollback();
                Log::error($e);
                return redirect()->back()->with(['error' => $e->getMessage()]);
            }
        } else {
            $slider = Slider::find($id);
            $pageHeading = "edit";
            return View("admin.$this->model.create",['details' => $slider,'pageHeading'=>$pageHeading]);
        }
    }

    public function changeStatus($modelId = 0, $status = 0) {
        if ($status == 1) {
            $statusMessage = trans("Slider has been actvated successfully");
        } else {
            $statusMessage = trans("Slider has been deactivated successfully");
        }
        $user = Slider::find($modelId);
        if ($user) {
            $currentStatus = $user->is_active;
            if (isset($currentStatus) && $currentStatus == 0) {
                $NewStatus = 1;
            } else {
                $NewStatus = 0;
            }
            $user->is_active = $NewStatus;
            $ResponseStatus = $user->save();
        }
        Session()->flash('flash_notice', $statusMessage);
        return back();
    }

    public function destroy($enuserid) {
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        }
        $userDetails = Slider::find($user_id);
        if (empty($userDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        if ($user_id) {
            Slider::where('id', $user_id)->delete();
            Session()->flash('flash_notice', trans("Slider has been removed successfully."));
        }
        return back();
    }
}