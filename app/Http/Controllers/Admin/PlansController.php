<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Plan;
use App\Models\PlanDetail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Redirect,DB,Response;

class PlansController extends Controller
{
    public $model = 'plans';
    public function __construct(Request $request)
    {
        $this->middleware('permission:view_partner_plan|create_partner_plan|edit_partner_plan|delete_partner_plan', ['only' => ['index','store']]);
        $this->middleware('permission:create_partner_plan', ['only' => ['create','store']]);
        $this->middleware('permission:edit_partner_plan', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_partner_plan', ['only' => ['destroy']]);

        $this->listRouteName = 'admin-plans.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;

    }

    public function index(Request $request)
    {
        try {
            $DB = Plan::query();
            $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'plans.created_at';
            $order = $request->input('order') ? $request->input('order') : 'DESC';
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
                    $DB->whereBetween('plans.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
                } elseif (!empty($searchData['date_from'])) {
                    $dateS = $searchData['date_from'];
                    $DB->where('plans.created_at', '>=', [$dateS . " 00:00:00"]);
                } elseif (!empty($searchData['date_to'])) {
                    $dateE = $searchData['date_to'];
                    $DB->where('plans.created_at', '<=', [$dateE . " 00:00:00"]);
                }
                foreach ($searchData as $fieldName => $fieldValue) {
                    if ($fieldValue != "") {
                        if ($fieldName == "name") {
                            $DB->where("plans.name", 'like', '%' . $fieldValue . '%');
                        }
                        if ($fieldName == "is_active") {
                            $DB->where("plans.is_active", $fieldValue);
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

        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }

    }

    public function create()
    {
        try {
            return view('admin.plans.create');
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $formData = $request->all();
            if (!empty($formData)) {
                $validator = Validator::make(
                    $request->all(),
                    array(
                        'name' => 'required',
                        'payout_period' => 'required|gt:0',
                    ),
                    array(
                        "name.required" => trans("The name field is required."),
                    )
                );
                if ($validator->fails()) {
                    return Redirect::back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();
                    $obj                                = new Plan;
                    $obj->name                          = $request->input('name');
                    $obj->payout_period                 = !empty($request->input('payout_period')) ? $request->input('payout_period') : 0;
                    $obj->description                 = !empty($request->input('description')) ? $request->input('description') : "";
                    $obj->term_conditions                 = !empty($request->input('term_conditions')) ? $request->input('term_conditions') : "";
                    $obj->save();
                    $lastId = $obj->id;

                    if(!empty($lastId)){
                        if(!empty($request->planDetailsArr)){
                            foreach($request->planDetailsArr as $planKey => $planVal){
                                if(!empty($planVal['sales_from']) && !empty($planVal['sales_to']) && !empty($planVal['type']) && !empty($planVal['amount'])){
                                    $planObj = new PlanDetail;
                                    $planObj->plan_id = $lastId;
                                    $planObj->sales_from = $planVal['sales_from'] ?? 0;
                                    $planObj->sales_to = $planVal['sales_to'] ?? 0;
                                    $planObj->type = $planVal['type'] ?? NULL;
                                    $planObj->amount = $planVal['amount'] ?? 0;
                                    $planObj->save();
                                    if(empty($planObj->id)){
                                        DB::rollback();
                                        Session()->flash('flash_notice', 'Something Went Wrong');
                                        return Redirect::route('admin-plans.index');
                                    }
                                }
                            }
                        }
    
                        DB::commit();
                    }else{
                        DB::rollback();
                        Session()->flash('flash_notice', 'Something Went Wrong');
                        return Redirect::route('admin-plans.index');
                    }

                    return redirect()->route('admin-plans.index')->with('success', 'Plan created successfully');
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' =>$e->getMessage(), 'error_msg' => $e->getMessage()]);
        }
    }

    public function edit(Request $request, $token = null)
    {
        try {
            $categoryId = '';
            if (!empty($token)) {

                $categoryId = base64_decode($token);

                $plans = Plan::find($categoryId);
                $planDetailsData = PlanDetail::where('plan_id',$categoryId)->get()->toArray();
                return View("admin.$this->model.edit", compact('plans','planDetailsData'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $token)
    {
        try {

            $categoryId = '';
            if (!empty($token)) {
                $categoryId = base64_decode($token);
            } else {
                return redirect()->route('admin-'.$this->model . ".index");
            }

            $plans = Plan::find($categoryId);
            if (empty($plans)) {
                return View("admin.$this->model.edit");
            } else {
                $formData = $request->all();
                if (!empty($formData)) {
                    $validator = Validator::make(
                        $request->all(),
                        array(
                            'name' => 'required',
                            'payout_period' => 'required|gt:0',
                        ),
                        array(
                            "name.required" => trans("The name field is required."),
                        )
                    );
                    if ($validator->fails()) {
                        return Redirect::back()->withErrors($validator)->withInput();
                    } else {
                        DB::beginTransaction();
                        $obj                                = $plans;
                        $obj->name                          = $request->input('name');
                        $obj->payout_period                 = !empty($request->input('payout_period')) ? $request->input('payout_period') : 0;
                        $obj->description                 = !empty($request->input('description')) ? $request->input('description') : "";
                        $obj->term_conditions                 = !empty($request->input('term_conditions')) ? $request->input('term_conditions') : "";
                        $obj->save();
                        $lastId = $obj->id;
                        if(!empty($lastId)){
                            PlanDetail::where('plan_id',$lastId)->delete();
                            if(!empty($request->planDetailsArr)){
                                foreach($request->planDetailsArr as $planKey => $planVal){
                                    if(!empty($planVal['sales_from']) && !empty($planVal['sales_to']) && !empty($planVal['type']) && !empty($planVal['amount'])){
                                        $planObj = new PlanDetail;
                                        $planObj->plan_id = $lastId;
                                        $planObj->sales_from = $planVal['sales_from'] ?? 0;
                                        $planObj->sales_to = $planVal['sales_to'] ?? 0;
                                        $planObj->type = $planVal['type'] ?? NULL;
                                        $planObj->amount = $planVal['amount'] ?? 0;
                                        $planObj->save();
                                        if(empty($planObj->id)){
                                            DB::rollback();
                                            Session()->flash('flash_notice', 'Something Went Wrong');
                                            return Redirect::route('admin-plans.index');
                                        }
                                    }
                                }
                            }
        
                            DB::commit();
                        }else{
                            DB::rollback();
                            Session()->flash('flash_notice', 'Something Went Wrong');
                            return Redirect::route('admin-plans.index');
                        }
                        Session()->flash('flash_notice', trans("Plan updated successfully."));
                        return Redirect::route('admin-plans.index');
                    }
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function destroy($token)
    {
        try {
            $categoryId = '';
            if (!empty($token)) {
                $categoryId = base64_decode($token);
            }
            $category = Plan::find($categoryId);
            if (empty($category)) {
                return Redirect()->route($this->model . '.index');
            }
            if ($category) {
                Plan::where('id', $categoryId)->delete();
                PlanDetail::where('plan_id',$categoryId)->delete();

                Session()->flash('flash_notice', trans("Plan has been removed successfully."));
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
            $statusMessage = trans("Plan has been actvated successfully");
        } else {
            $statusMessage = trans("Plan has been deactivated successfully");
        }
        $category = Plan::find($modelId);
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
