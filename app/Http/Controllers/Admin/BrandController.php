<?php

namespace App\Http\Controllers\Admin;

use App\Config;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Redirect,DB,Response;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class BrandController extends Controller
{
    public $model = 'brand';
    public function __construct(Request $request)
    {
        $this->middleware('permission:view_brand|create_brand|edit_brand|delete_brand', ['only' => ['index','store']]);
        $this->middleware('permission:create_brand', ['only' => ['create','store']]);
        $this->middleware('permission:edit_brand', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_brand', ['only' => ['destroy']]);
        
        $this->listRouteName = 'admin-brand.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;
        
    }

    public function index(Request $request)
    {
        $DB = Brand::query();
        $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'brands.created_at';
        $order = $request->input('order') ? $request->input('order') : 'desc';
        $offset = !empty($request->input('offset')) ? $request->input('offset') : 0 ;
        $limit =  !empty($request->input('limit')) ? $request->input('limit') : Config("Reading.records_per_page"); 

        if ($request->all()) {
            $searchData            =    $request->all();
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
                $DB->whereBetween('brands.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('brands.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('brands.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("brands.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("brands.is_active", $fieldValue);
                    }
                }
            }
        }

        $DB->where("brands.is_deleted", 0);
       
        $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();
        $totalResults = $DB->count();

        if($request->ajax()){

            return  View("admin.$this->model.load_more_data", compact('results','totalResults'));
        }else{
            
            return  View("admin.$this->model.index", compact('results','totalResults'));
        }
    }

    public function create(Request $request)
    {
        return View("admin.$this->model.create");
    }
    
    public function edit(Request $request, $enuserid = null)
    {
        $user_id = '';
        if (!empty($enuserid)) {

            $user_id = base64_decode($enuserid);
            $userDetails = Brand::where('brands.id',$user_id)->first();

            return View("admin.$this->model.edit", compact( 'userDetails'));
        }
    }
    public function store(Request $request)
    {

        $formData = $request->all();
        
        if (!empty($formData)) {
            $validator = Validator::make(
                $request->all(),
                array(

                    'name' => 'required|unique:brands,name',
                ),
                array(
                    "name.required" => trans("The name field is required."),
                )
            );
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {
                DB::beginTransaction();
                $obj                                = new Brand;
                $obj->name                          = $request->input('name');
                $obj->save();
                $lastId = $obj->id;
                if(!empty($lastId)){
                    DB::commit();
                }else{
                    DB::rollback();
                    Session()->flash('flash_notice', 'Something Went Wrong');
                    return Redirect::route('admin-brand.index');
                }
                Session()->flash('flash_notice', trans("Brand saved successfully."));
                return Redirect::route('admin-brand.index');
            }
        }

    }
    public function update(Request $request, $enuserid = null)
    {
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        }
        $model = Brand::find($user_id);
        if (empty($model)) {
            return View("admin.$this->model.edit");
        } else {
            $formData = $request->all();
            if (!empty($formData)) {
                $validator = Validator::make(
                    $request->all(),
                    array(
                        'name' => 'required|unique:brands,name',
                    ),
                    array(
                        "name.required" => trans("The name field is required."),
                    )
                );
                if ($validator->fails()) {
                    return Redirect::back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();
                    $obj                                = $model;
                    $obj->name                          = $request->input('name');
                    $obj->save();
                    $lastId = $obj->id;
                    if(!empty($lastId)){
                        DB::commit();
                    }else{
                        DB::rollback();
                        Session()->flash('flash_notice', 'Something Went Wrong');
                        return Redirect::route('admin-brand.index');
                    }
                    Session()->flash('flash_notice', trans("Brand updated successfully."));
                    return Redirect::route('admin-brand.index');
                }
            }
        }
    }

    public function destroy($enuserid)
    {
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        }
        $userDetails = Brand::find($user_id);
        if (empty($userDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        if ($user_id) {
            Brand::where('id', $user_id)->update(array(
                'is_deleted' => 1,
            ));

            Session()->flash('flash_notice', trans("Brand has been removed successfully."));
        }
        return back();
    }

    public function changeStatus($modelId = 0, $status = 0)
    {
        if ($status == 1) {
            $statusMessage = trans("Brand has been actvated successfully");
        } else {
            $statusMessage = trans("Brand has been deactivated successfully");
        }
        $user = Brand::find($modelId);
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
    
     public function changePrimaryStatus($modelId = 0, $status = 0)
    {
        if ($status == 1) 
        {
            $statusMessage = trans("Brand has been set primary successfully");
        } 
        else 
        {
            $statusMessage = trans("Brand has been remove from primary brand");
        }
        $user = Brand::find($modelId);
        if ($user) 
        {
            $currentStatus = $user->is_primary;
            if (isset($currentStatus) && $currentStatus == 0) 
            {
                $NewStatus = 1;
            } 
            else 
            {
                $NewStatus = 0;
            }
            $user->is_primary = $NewStatus;
            $ResponseStatus = $user->save();
        }
        Session()->flash('flash_notice', $statusMessage);
        return back();
    }

}
