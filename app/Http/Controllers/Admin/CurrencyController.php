<?php

namespace App\Http\Controllers\Admin;

use App\Config;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Category;
use App\Models\User;
use App\Models\Product;
use App\Models\CouponAssign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Redirect,DB,Response,Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class CurrencyController extends Controller
{
    public $model = 'currencies';
    public function __construct(Request $request)
    {
        $this->middleware('permission:view_currency|create_currency|edit_currency|delete_currency', ['only' => ['index','store']]);
        $this->middleware('permission:create_currency', ['only' => ['create','store']]);
        $this->middleware('permission:edit_currency', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_currency', ['only' => ['destroy']]);

        $this->listRouteName = 'admin-currencies.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;

    }

    public function index(Request $request)
    {
        $DB = Currency::query();
        $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'currencies.created_at';
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
                $DB->whereBetween('currencies.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('currencies.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('currencies.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("currencies.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "currency_code") {
                        $DB->where("currencies.currency_code", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("currencies.is_active", $fieldValue);
                    }
                }
            }
        }

        $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();
        $totalResults = $DB->count();
        $defaultCurrency = Currency::where('is_active', 1)->where('is_default', 1)->value('currency_code');
        if($request->ajax()){

            return  View("admin.$this->model.load_more_data", compact('results','totalResults','defaultCurrency'));
        }else{

            return  View("admin.$this->model.index", compact('results','totalResults','defaultCurrency'));
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
            $userDetails = Currency::where('currencies.id', $user_id)->first();

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
                    'name' => 'required',
                    'symbol' => 'required',
                    'currency_code' => 'required',
                    'amount' => 'required|numeric',
                ),
                array(
                    "name.required" => trans("The name field is required."),
                    "symbol.required" => trans("The symbol field is required."),
                    "amount.numeric" => trans("The amount should be numeric."),
                    "amount.required" => trans("The amount field is required."),
                    "currency_code.required" => trans("The currency_code field is required."),
                )
            );
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {
                DB::beginTransaction();
                $obj                                = new Currency;
                $obj->name                          = $request->input('name');
                $obj->symbol                        = !empty($request->input('symbol')) ? $request->input('symbol') : "";
                $obj->currency_code                 = !empty($request->input('currency_code')) ? $request->input('currency_code') : "";
                $obj->amount                        = !empty($request->input('amount')) ? $request->input('amount') : 0;
                $obj->save();
                $lastId = $obj->id;
                if(!empty($lastId)){
                    DB::commit();
                }else{
                    DB::rollback();
                    Session()->flash('flash_notice', 'Something Went Wrong');
                    return Redirect::route('admin-currencies.index');
                }
                Session()->flash('flash_notice', trans("Currency saved successfully."));
                return Redirect::route('admin-currencies.index');
            }
        }

    }
    public function update(Request $request, $enuserid = null)
    {

        $model = Currency::find($enuserid);
        if (empty($model)) {
            return View("admin.$this->model.edit");
        } else {
            $formData = $request->all();
            if (!empty($formData)) {
                $validator = Validator::make(
                    $request->all(),
                    array(
                        'name' => 'required',
                        'symbol' => 'required',
                        'currency_code' => 'required',
                        'amount' => 'required|numeric',
                    ),
                    array(
                        "name.required" => trans("The name field is required."),
                        "symbol.required" => trans("The symbol field is required."),
                        "amount.numeric" => trans("The amount should be numeric."),
                        "amount.required" => trans("The amount field is required."),
                        "currency_code.required" => trans("The currency_code field is required."),
                    )
                );
                if ($validator->fails()) {
                    return Redirect::back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();

                    $obj                      = $model;
                    $obj->name                = $request->input('name');
                    $obj->symbol              = !empty($request->input('symbol')) ? $request->input('symbol') : "";
                    $obj->currency_code       = !empty($request->input('currency_code')) ? $request->input('currency_code') : "";
                    $obj->amount              = !empty($request->input('amount')) ? $request->input('amount') : 0;
                    $obj->save();
                    $lastId = $obj->id;
                    if(!empty($lastId)){
                        DB::commit();
                    }else{
                        DB::rollback();
                        Session()->flash('flash_notice', 'Something Went Wrong');
                        return Redirect::route('admin-currencies.index');
                    }
                    Session()->flash('flash_notice', trans("Currency updated successfully."));
                    return Redirect::route('admin-currencies.index');
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
        $userDetails = Currency::find($user_id);
        if (empty($userDetails)) {
            return Redirect::route('admin-currencies.index');
        }
        if ($user_id) {
            Currency::where('id', $user_id)->delete();

            Session()->flash('flash_notice', trans("Currency has been removed successfully."));
        }
        return back();
    }

    public function makeDefault($enuserid)
    {
        $userDetails = Currency::find($enuserid);
        if (empty($userDetails)) {
            return Redirect::route('admin-currencies.index');
        }
        if ($enuserid) {
            Currency::where('id', $enuserid)->update(['is_default' => 1]);
            Currency::where('id', '!=' ,$enuserid)->update(['is_default' => 0]);

            Session()->flash('flash_notice', trans("Currency has been marked default successfully."));
        }
        return back();
    }

    public function changeStatus($modelId = 0, $status = 0)
    {
        if ($status == 1) {
            $statusMessage = trans("Currency has been actvated successfully");
        } else {
            $statusMessage = trans("Currency has been deactivated successfully");
        }
        $user = Currency::find($modelId);
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
            $userDetails = Currency::where('users.id',$user_id)->first();

            $data = compact('user_id', 'userDetails');

            return View("admin.$this->model.view", $data);
        }
    }


}
