<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Redirect,DB,Response;
use App\Models\Variant;
use App\Models\CategoryVariant;
use App\Models\CategorySpecification;
use App\Models\Specification;
use App\Models\ShippingAreaCity;
use App\Models\ShippingArea;
use App\Models\ShippingCost;
use App\Models\ShippingCompany;
use App\Models\City;

class ShippingCostsController extends Controller
{
    public $model =    'shipping-costs';
    public function __construct(Request $request)
    {
        $this->listRouteName = 'admin-shipping-costs.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;
    }

    public function index(Request $request, $endesid = null)
    {
        if (!empty($endesid)) {
            $dep_id = base64_decode($endesid);
        }
        $ShippingAreaDetails  =  ShippingArea::where('shipping_areas.id', $dep_id)->first();

        if (empty($dep_id) && empty($ShippingAreaDetails) ) {
            return Redirect()->back();
        }
        $shipping_company_id = $ShippingAreaDetails->shipping_company_id;
        $DB                    =    ShippingCost::where('shipping_area_id', $dep_id);
        $searchVariable        =    array();
        $inputGet            =    $request->all();
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
            if (isset($searchData['page'])) {
                unset($searchData['page']);
            }
            if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
                $dateS = $searchData['date_from'];
                $dateE = $searchData['date_to'];
                $DB->whereBetween('shipping_costs.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('shipping_costs.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('shipping_costs.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "weight") {
                        $DB->where("shipping_costs.weight", 'like', '%' . $fieldValue . '%');
                    }
                }
                $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }
        $DB->select("shipping_costs.*");
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
        $order  = ($request->input('order')) ? $request->input('order')   : 'Desc';
        $offset = !empty($request->input('offset')) ? $request->input('offset') : 0 ;
        $limit =  !empty($request->input('limit')) ? $request->input('limit') : Config("Reading.records_per_page");

        $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();
        $totalResults = $DB->count();

        if($request->ajax()){

            return  View("admin.$this->model.load_more_data", compact('results','totalResults','dep_id','shipping_company_id'));
        }else{
            return  View("admin.$this->model.index", compact('results','totalResults','dep_id','shipping_company_id'));
        }
    }

    public function add(Request $request, $endesid = null)
    {
        if (!empty($endesid)) {
            $dep_id = base64_decode($endesid);
        }
        if (empty($endesid)) {
            return Redirect()->back();
        }
        $formData =    $request->all();
        $ShippingCompanyDetails  =  ShippingArea::where('shipping_areas.id', $dep_id)->first();
        if ($request->isMethod('POST')) {
            if (!empty($formData)) {
                $validator = Validator::make(
                    $request->all(),
                    array(
                        'weight' => 'required|numeric',
                        'amount' => 'required|numeric',
                    ),
                    array(
                        "weight.required" => trans("The weight field is required."),
                        "amount.required" => trans("The amount field is required."),
                    )
                );
                if ($validator->fails()) {
                    return Redirect::back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();
                    $obj                                = new ShippingCost;
                    $obj->shipping_company_id           = $ShippingCompanyDetails->shipping_company_id;
                    $obj->shipping_area_id              = $dep_id;
                    $obj->weight                          = $request->input('weight');
                    $obj->amount                          = $request->input('amount');
                    $obj->save();
                    $lastId = $obj->id;

                    if(!empty($lastId)){
                        DB::commit();
                    }else{
                        DB::rollback();
                        Session()->flash('flash_notice', 'Something Went Wrong');
                        return Redirect::route('admin-shipping-costs.index', $endesid);
                    }
                    return redirect()->route('admin-shipping-costs.index', $endesid)
                    ->with('success', 'Shipping cost created successfully');
                }
            }
        }
        return  View("admin.$this->model.create", compact('dep_id', 'ShippingCompanyDetails'));
    }

    public function update(Request $request, $endesid = null)
    {
        $des_id = '';
        if (!empty($endesid)) {
            $des_id = base64_decode($endesid);
        }
        $ShippingCostDetails  =  ShippingCost::where('shipping_costs.id', $des_id)->first();
        if (empty($ShippingCostDetails)) {
            return Redirect()->back();
        }
        if ($request->isMethod('POST')) {
            $formData = $request->all();
            if (!empty($formData)) {
                $validator = Validator::make(
                    $request->all(),
                    array(
                        'weight' => 'required|numeric',
                        'amount' => 'required|numeric',
                    ),
                    array(
                        "weight.required" => trans("The weight field is required."),
                        "amount.required" => trans("The amount field is required."),
                    )
                );
                if ($validator->fails()) {
                    return Redirect::back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();
                    $obj                                = ShippingCost::find($des_id);
                    $obj->shipping_company_id           = $ShippingCostDetails->shipping_company_id;
                    $obj->shipping_area_id              = $ShippingCostDetails->shipping_area_id;
                    $obj->weight                          = $request->input('weight');
                    $obj->amount                          = $request->input('amount');
                    $obj->save();
                    $lastId = $obj->id;
                    if(!empty($lastId)){
                        DB::commit();
                    }else{
                        DB::rollback();
                        Session()->flash('flash_notice', 'Something Went Wrong');
                        return Redirect::route('admin-shipping-costs.index', $endesid);
                    }
                    Session()->flash('flash_notice', trans("Shipping cost updated successfully."));
                    return Redirect::route('admin-shipping-costs.index', $endesid);
                }
            }
        }

        return  View("admin.$this->model.edit", compact('ShippingCostDetails'));
    }


    public function delete($token)
    {
        try {
            $categoryId = '';
            if (!empty($token)) {
                $categoryId = base64_decode($token);
            }
            $category = ShippingCost::find($categoryId);
            if (empty($category)) {
                return Redirect()->route($this->model . '.index');
            }
            if ($category) {
                ShippingCost::where('id', $categoryId)->delete();
                Session()->flash('flash_notice', trans("Shipping cost has been removed successfully."));
            }
            return back();
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function changeStatus($modelId = 0, $status = 0)
    {
        if ($status == 1) {
            $statusMessage = trans("Shipping cost has been actvated successfully");
        } else {
            $statusMessage = trans("Shipping cost has been deactivated successfully");
        }
        $category = ShippingCost::find($modelId);
        if ($category) {
            $currentStatus = $category->is_active;
            if (isset($currentStatus) && $currentStatus == 0) {
                $NewStatus = 1;
            } else {
                $NewStatus = 0;
            }
            $category->is_active = $NewStatus;
            $ResponseStatus = $category->save();
        }
        Session()->flash('flash_notice', $statusMessage);
        return back();
    }

}
