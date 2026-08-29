<?php

namespace App\Http\Controllers\Admin;

// use App\Config;
use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\EmailAction;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Redirect,DB,Response,Str,Config;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class TestimonialController extends Controller
{
    public $model = 'testimonials';
    public $listRouteName;
    public $request;
    public function __construct(Request $request)
    {
        $this->middleware('permission:view_testimonial|create_testimonial|edit_testimonial|delete_testimonial', ['only' => ['index','store']]);
        $this->middleware('permission:create_testimonial', ['only' => ['create','store']]);
        $this->middleware('permission:edit_testimonial', ['only' => ['edit','update', 'changeStatus']]);
        $this->middleware('permission:delete_testimonial', ['only' => ['destroy']]);


        $this->listRouteName = 'admin-testimonials.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;

    }

    public function index(Request $request)
    {
        $DB = Testimonial::query();
        $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'testimonials.created_at';
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
                $DB->whereBetween('testimonials.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('testimonials.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('testimonials.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("testimonials.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("testimonials.is_active", $fieldValue);
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

    public function create(Request $request)
    {
        return View("admin.$this->model.add");
    }

    public function edit(Request $request, $enuserid = null)
    {
        $user_id = '';
        if (!empty($enuserid)) {

            $user_id = base64_decode($enuserid);
            $userDetails = Testimonial::where('id',$user_id)->first();

            return View("admin.$this->model.edit", compact( 'userDetails'));
        }
    }
    public function save(Request $request)
    {

        $formData = $request->all();

        if (!empty($formData)) {
            $validator = Validator::make(
                $request->all(),
                array(
                    'name' => 'required',
                    'image' => 'nullable|mimes:jpg,jpeg,png',
                )
            );
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {
                DB::beginTransaction();

                $obj                                = new Testimonial;
                $obj->name                          = $request->input('name');
                $obj->description                          = $request->input('description');
                $obj->rating                          = $request->input('rating');
                $obj->city                  = $request->input('city');
                if ($request->hasFile('image')) {
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $originalName = $request->file('image')->getClientOriginalName();
                    $fileName = time() . '-image.' . $extension;

                    $folderName = strtoupper(date('M') . date('Y')) . "/";
                    $folderPath = Config('constant.TESTIMONIAL_IMAGE_ROOT_PATH') . $folderName;
                    if (!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, $mode = 0777, true);
                    }
                    if ($request->file('image')->move($folderPath, $fileName)) {
                        $obj->image = $folderName . $fileName;
                    }
                }
                $obj->save();
                $lastId = $obj->id;
                if(!empty($lastId)){

                    DB::commit();
                }else{
                    DB::rollback();
                    Session()->flash('flash_notice', 'Something Went Wrong');
                    return Redirect::route('admin-testimonials.index');
                }
                Session()->flash('flash_notice', trans("Testimonial saved successfully."));
                return Redirect::route('admin-testimonials.index');
            }
        }

    }
    public function update(Request $request, $enuserid = null)
    {

        $model = Testimonial::find($enuserid);
        if (empty($model)) {
            return View("admin.$this->model.edit");
        } else {
            $formData = $request->all();
            if (!empty($formData)) {
                $validator = Validator::make(
                    $request->all(),
                    array(
                        'name' => 'required',
                        'image' => 'nullable|mimes:jpg,jpeg,png',
                    )
                );
                if ($validator->fails()) {
                    return Redirect::back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();
                    $obj                                = $model;
                    $obj->name                          = $request->input('name');
                    $obj->description                          = $request->input('description');
                    $obj->rating                          = $request->input('rating');
                    $obj->city                  = $request->input('city');
                    if ($request->hasFile('image')) {
                        $extension = $request->file('image')->getClientOriginalExtension();
                        $originalName = $request->file('image')->getClientOriginalName();
                        $fileName = time() . '-image.' . $extension;

                        $folderName = strtoupper(date('M') . date('Y')) . "/";
                        $folderPath = Config('constant.TESTIMONIAL_IMAGE_ROOT_PATH') . $folderName;
                        if (!File::exists($folderPath)) {
                            File::makeDirectory($folderPath, $mode = 0777, true);
                        }
                        if ($request->file('image')->move($folderPath, $fileName)) {
                            $obj->image = $folderName . $fileName;
                        }
                    }
                    $obj->save();
                    $lastId = $obj->id;
                    if(!empty($lastId)){
                        DB::commit();
                    }else{
                        DB::rollback();
                        Session()->flash('flash_notice', 'Something Went Wrong');
                        return Redirect::route('admin-testimonials.index');
                    }
                    Session()->flash('flash_notice', trans("Testimonial updated successfully."));
                    return Redirect::route('admin-testimonials.index');
                }
            }
        }
    }

    public function destroy($enuserid) {
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        }
        $userDetails = Testimonial::find($user_id);
        if (empty($userDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        if ($user_id) {
            Testimonial::where('id', $user_id)->delete();
            Session()->flash('flash_notice', trans("Testimonial has been removed successfully."));
        }
        return back();
    }

    public function changeStatus($modelId = 0, $status = 0)
    {
        if ($status == 1) {
            $statusMessage = trans("Testimonial has been actvated successfully");
        } else {
            $statusMessage = trans("Testimonial has been deactivated successfully");
        }
        $user = Testimonial::find($modelId);
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

}
