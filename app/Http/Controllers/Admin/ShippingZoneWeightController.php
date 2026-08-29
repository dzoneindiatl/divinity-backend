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
use App\Models\ShippingZone;
use App\Models\ShippingWeight;
use App\Models\ShippingCompany;
use App\Models\Country;
use App\Models\State;
use App\Models\City; 

class ShippingZoneWeightController extends Controller
{
    public $model =    'shipping-zones-weights';
    public function __construct(Request $request)
    {
        $this->listRouteName = 'admin-shipping-zones-weights.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;
    }

    public function index(Request $request, $endesid = null)
    {
        if (!empty($endesid)) {
            $dep_id = base64_decode($endesid);
        }
        $ShippingZoneDetails  =  ShippingZone::where('shipping_zone.id', $dep_id)->first();
        if (empty($dep_id) && empty($ShippingZoneDetails) ) {
            return Redirect()->back();
        }
        $DB                    =    ShippingWeight::where('shipping_zone_id', $dep_id);
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
                $DB->whereBetween('shipping_weight.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('shipping_weight.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('shipping_weight.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("shipping_weight.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("shipping_weight.is_active", $fieldValue);
                    }
                }
                $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }
        $DB->select("shipping_weight.*");
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
        $order  = ($request->input('order')) ? $request->input('order')   : 'Desc';
        $offset = !empty($request->input('offset')) ? $request->input('offset') : 0 ;
        $limit =  !empty($request->input('limit')) ? $request->input('limit') : Config("Reading.records_per_page");

        $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();
        $totalResults = $DB->count();

        if($request->ajax()){

            return  View("admin.$this->model.load_more_data", compact('results','totalResults','dep_id'));
        }else{
            return  View("admin.$this->model.index", compact('results','totalResults','dep_id'));
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
        
        $formData = $request->all();
        $ShippingZoneDetails = ShippingZone::where('shipping_zone.id', $dep_id)->first();
    
        if ($request->isMethod('POST')) {
            if (!empty($formData)) {
                // Input Validation
                $validator = Validator::make(
                    $request->all(),
                    array(
                        'weight_min' => 'required|numeric|min:0',
                        'weight_max' => 'required|numeric|min:0|gt:weight_min',
                        'amount' => 'required|numeric|min:0',
                        'weight_type' => 'required',  
                    ),
                    array(
                        "weight_min.required" => trans("The weight_min field is required."),
                        "weight_min.numeric" => trans("The weight_min field must be a number."),
                        "weight_min.min" => trans("The weight_min must be at least 0."),
                        "weight_max.required" => trans("The weight_max field is required."),
                        "weight_max.numeric" => trans("The weight_max field must be a number."),
                        "weight_max.min" => trans("The weight_max must be at least 0."),
                        "weight_max.gt" => trans("The weight_max must be greater than weight_min."),
                        "amount.required" => trans("The amount field is required."),
                        "amount.numeric" => trans("The amount field must be a number."),
                        "amount.min" => trans("The amount must be at least 0."),
                        "weight_type.required" => trans("The weight_type field is required."),
                    )
                );
                $alreadyexisting = ShippingWeight::where('shipping_zone_id', $dep_id)
                ->where('weight_min', $request->input('weight_min'))
                ->where('weight_max', $request->input('weight_max'))
                ->where('weight_type', $request->input('weight_type'))
                ->first();

                // If the weight range already exists, show an error message
                //dd($request->input('weight_min'));
                if ($alreadyexisting) {
                    $validator->after(function ($validator) {
                        $validator->errors()->add('weight_min', 'This weight range already exists for the selected shipping zone.');
                    });
                }

                if ($alreadyexisting) {
                    $validator->after(function ($validator) {
                        $validator->errors()->add('weight_max', 'This weight range already exists for the selected shipping zone.');
                    });
                }

                if ($validator->fails()) {
                    return Redirect::back()->withErrors($validator)->withInput();
                } else 
                {
                    DB::beginTransaction();
                    try
                     {
                        $obj = new ShippingWeight;
                        $obj->shipping_zone_id = $dep_id;
                        $obj->weight_min = $request->input('weight_min');
                        $obj->weight_max = $request->input('weight_max');
                        $obj->weight_type = $request->input('weight_type');
                        $obj->amount = $request->input('amount');
                        $obj->save();
    
                        DB::commit();
                        return redirect()->route('admin-shipping-zones-weights.index', $endesid)
                            ->with('success', 'Shipping weights created successfully');
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return Redirect::back()->withErrors(["error" => "Something went wrong."])->withInput();
                    }
                }
            }
        }
    
        return View("admin.$this->model.create", compact('dep_id'));
    }
    

    public function update(Request $request, $endesid = null)
    {
        
        $des_id = '';
        if (!empty($endesid)) {
            $des_id = base64_decode($endesid);
        }
    //dd($des_id);
        // Fetch existing shipping weight details
        $ShippingWeightDetails = ShippingWeight::findOrFail($des_id);
    
        // Validation for the update form
        if ($request->isMethod('POST')) {
            $formData = $request->all();
            
            if (!empty($formData)) {
                // Input validation
                $validator = Validator::make(
                    $request->all(),
                    array( 
                        'weight_min' => 'required|numeric|min:0',
                        'weight_max' => 'required|numeric|min:0|gt:weight_min',
                        'amount' => 'required|numeric|min:0',
                    ),
                    array(  
                        "weight_min.required" => trans("The weight_min field is required."),
                        "weight_min.numeric" => trans("The weight_min field must be a number."),
                        "weight_min.min" => trans("The weight_min must be at least 0."),
                        "weight_max.required" => trans("The weight_max field is required."),
                        "weight_max.numeric" => trans("The weight_max field must be a number."),
                        "weight_max.min" => trans("The weight_max must be at least 0."),
                        "weight_max.gt" => trans("The weight_max must be greater than weight_min."),
                        "amount.required" => trans("The amount field is required."),
                        "amount.numeric" => trans("The amount field must be a number."),
                        "amount.min" => trans("The amount must be at least 0."),
                    )
                );
    
                if ($validator->fails()) {
                    return Redirect::back()->withErrors($validator)->withInput();
                } else {
                    try {
                        DB::beginTransaction();
    
                        // Find and update the shipping weight
                        $obj = ShippingWeight::find($des_id); 
                      $obj->weight_min = $request->input('weight_min');
                      $obj->weight_max = $request->input('weight_max');
                      $obj->weight_type = $request->input('weight_type');
                        $obj->amount = $request->input('amount');
                        $obj->save();
    
                        DB::commit();
    
                        Session()->flash('flash_notice', trans("Shipping Weights updated successfully."));
                        return Redirect::route('admin-shipping-zones-weights.index', base64_encode( $ShippingWeightDetails->shipping_zone_id));
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return Redirect::back()->withErrors(["error" => "Something went wrong."])->withInput();
                    }
                }
            }
        }
    
        // Render the edit view with shipping weight details
        return View("admin.$this->model.edit", compact('ShippingWeightDetails'));
    }
    


    public function delete($token)
    {
        try {
            $categoryId = '';
            if (!empty($token)) {
                $categoryId = base64_decode($token);
            }
            $category = ShippingWeight::find($categoryId);
            if (empty($category)) {
                return Redirect()->route($this->model . '.index');
            }
            if ($category) {
                ShippingWeight::where('id', $categoryId)->delete();
               // ShippingAreaCity::where('shipping_area_id',$categoryId)->delete();

                Session()->flash('flash_notice', trans("Shipping Weight has been removed successfully."));
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
            $statusMessage = trans("Shipping Weight has been actvated successfully");
        } else {
            $statusMessage = trans("Shipping Weight has been deactivated successfully");
        }
        $category = ShippingWeight::find($modelId);
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
