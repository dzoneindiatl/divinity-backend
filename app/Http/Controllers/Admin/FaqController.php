<?php

namespace App\Http\Controllers\Admin;

use App\Config;
use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Category;
use App\Models\User;
use App\Models\CouponAssign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Redirect,DB,Response,Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class FaqController extends Controller
{
    public $model = 'faqs';
    public function __construct(Request $request)
    {
        $this->middleware('permission:view_faq|create_faq|edit_faq|delete_faq', ['only' => ['index','store','show', 'changeStatus']]);
        $this->middleware('permission:create_faq', ['only' => ['create','store']]);
        $this->middleware('permission:edit_faq', ['only' => ['edit','update', 'changeStatus']]);
        $this->middleware('permission:delete_faq', ['only' => ['destroy']]);

        $this->listRouteName = 'admin-faqs.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;

    }

    public function index(Request $request)
    {
        $DB = Faq::query();
        $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'faqs.created_at';
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
                $DB->whereBetween('faqs.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('faqs.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('faqs.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "question") {
                        $DB->where("faqs.question", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("faqs.is_active", $fieldValue);
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
        return view("admin.$this->model.add");
    }

    public function edit(Request $request, $enuserid = null)
    {
        $user_id = '';
        if (!empty($enuserid)) {

            $user_id = base64_decode($enuserid);
            $userDetails = Faq::where('faqs.id', $user_id)->first();

            return View("admin.$this->model.edit",['userDetails' => $userDetails]);
        }
    }
    public function save(Request $request)
    {

        $formData = $request->all();
        if (!empty($formData)) {
            $validator = Validator::make(
                $request->all(),
                array(
                    'question' => 'required',
                    'answer' => 'required',
                ),
                array(
                    "question.required" => trans("The question field is required."),
                    "answer.required" => trans("The answer field is required."),
                )
            );
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {
                DB::beginTransaction();
                $obj                                = new Faq;
                $obj->question                          = $request->input('question');
                $obj->answer                          = $request->input('answer');
                $obj->save();
                $lastId = $obj->id;
                if(!empty($lastId)){
                    DB::commit();
                }else{
                    DB::rollback();
                    Session()->flash('flash_notice', 'Something Went Wrong');
                    return Redirect::route('admin-faqs.index');
                }
                Session()->flash('flash_notice', trans("Faq saved successfully."));
                return Redirect::route('admin-faqs.index');
            }
        }

    }
    public function update(Request $request, $enuserid = null)
    {

        $model = Faq::find($enuserid);
        if (empty($model)) {
            return View("admin.$this->model.edit");
        } else {
            $formData = $request->all();
            if (!empty($formData)) {
                $validator = Validator::make(
                    $request->all(),
                    array(
                        'question' => 'required',
                        'answer' => 'required',
                    ),
                    array(
                        "question.required" => trans("The name field is required."),
                        "answer.required" => trans("The answer field is required."),
                    )
                );
                if ($validator->fails()) {
                    return Redirect::back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();

                    $obj                      = $model;
                    $obj->question                          = $request->input('question');
                    $obj->answer                          = $request->input('answer');
                    $obj->save();
                    $lastId = $obj->id;
                    if(!empty($lastId)){
                        DB::commit();
                    }else{
                        DB::rollback();
                        Session()->flash('flash_notice', 'Something Went Wrong');
                        return Redirect::route('admin-faqs.index');
                    }
                    Session()->flash('flash_notice', trans("Faq updated successfully."));
                    return Redirect::route('admin-faqs.index');
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
        $userDetails = Faq::find($user_id);
        if (empty($userDetails)) {
            return Redirect::route('admin-faqs.index');
        }
        if ($user_id) {
            Faq::where('id', $user_id)->delete();

            Session()->flash('flash_notice', trans("Faq has been removed successfully."));
        }
        return back();
    }


    public function changeStatus($modelId = 0, $status = 0)
    {
        if ($status == 1) {
            $statusMessage = trans("Faq has been actvated successfully");
        } else {
            $statusMessage = trans("Faq has been deactivated successfully");
        }
        $user = Faq::find($modelId);
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




    public function show(Request $request, $enuserid = null)
    {
        $user_id = '';
        if (!empty($enuserid)) {

            $user_id = base64_decode($enuserid);
            $userDetails = Faq::where('users.id',$user_id)->first();

            $data = compact('user_id', 'userDetails');

            return View("admin.$this->model.view", $data);
        }
    }


}
