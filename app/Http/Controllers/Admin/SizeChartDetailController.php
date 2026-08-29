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
use App\Models\SizeChart;
use App\Models\SizeChartDetail;
use App\Models\SizeChartDetailValue;
use App\Models\City;

class SizeChartDetailController extends Controller
{
    public $model =    'size-chart-details';
    public function __construct(Request $request)
    {
        $this->listRouteName = 'admin-size-chart-details.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;
    }

    public function index(Request $request, $endesid = null)
    {
        if (!empty($endesid)) {
            $dep_id = base64_decode($endesid);
        }
        $SizeChart  =  SizeChart::where('size_charts.id', $dep_id)->first();
        if (empty($dep_id) && empty($SizeChart) ) {
            return Redirect()->back();
        }
        $DB                    =    SizeChartDetail::where('size_chart_id', $dep_id);
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
                $DB->whereBetween('size_chart_details.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('size_chart_details.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('size_chart_details.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("size_chart_details.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("size_chart_details.is_active", $fieldValue);
                    }
                }
                $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }
        $DB->select("size_chart_details.*");
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
        $SizeChart = SizeChart::where('size_charts.id', $dep_id)->first();
        $listQuery['country'] = DB::table('countries')->where('is_active', 1)
        ->select('id', 'name')->get()->toArray();

        $listQuery['currency'] = DB::table('currencies')->where('is_active', 1)
        ->select('id', 'name','symbol', 'currency_code')->get()->toArray();

        if ($request->isMethod('POST')) {
            if (!empty($formData)) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'name' => 'required',
                    ],
                    [
                        "name.required" => trans("The name field is required."),
                       
                    ]
                );

                if ($validator->fails()) {
                    return Redirect::back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();

                    $sizeChartDetail = new SizeChartDetail();
                    $sizeChartDetail->size_chart_id = $dep_id;
                    $sizeChartDetail->name = $request->input('name');
                    $sizeChartDetail->country_ids  = json_encode($request->input('countryData'));
                    $sizeChartDetail->currency_ids = json_encode($request->input('currencyData'));
                    $sizeChartDetail->units = $request->input('unitData');
                    $sizeChartDetail->save();

                    $lastId = $sizeChartDetail->id;
                    $tableHeaders = json_decode($request->input('tableHeadersInput'), true);
                    // echo'<pre>';
                    // print_r($tableHeaders);
                    // die();
                    $tableData = $request->input('cell');

                    if (!empty($lastId)) {
                        $sizeChartDetailValue = new SizeChartDetailValue();
                        $sizeChartDetailValue->size_chart_detail_id = $lastId;
                        $sizeChartDetailValue->categoryIds  = $request->input('categoryData');
                        $sizeChartDetailValue->productIds  = $request->input('productData');
                        $sizeChartDetailValue->table_headers = $tableHeaders;
                        $sizeChartDetailValue->table_data = $tableData;
                        $sizeChartDetailValue->save();

                        DB::commit();
                    } else {
                        DB::rollback();
                        Session()->flash('flash_notice', 'Something Went Wrong');
                        return Redirect::route('admin-size-chart-details.index', $endesid);
                    }

                    return redirect()->route('admin-size-chart-details.index', $endesid)
                        ->with('success', 'Size chart detail created successfully');
                }
            }
        }
      
        try {
            $categories = Category::whereNull('parent_id')
                ->where('is_active', 1)
                ->where('is_deleted', 0)
                ->get();

            $products = DB::table('products')->where('is_active', 1)
                ->where('is_deleted', 0)
                ->select('id', 'name')->get()->toArray();

            return view("admin.$this->model.create", compact('dep_id', 'categories', 'products','listQuery'));
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }
    
    
    
    
    public function update(Request $request, $endesid = null)
    {
     $des_id = '';
     if (!empty($endesid)) {
         $des_id = base64_decode($endesid);
     }
     $SizeChartDetail  =  SizeChartDetail::where('size_chart_details.id', $des_id)->first(); //dd($SizeChartDetail);
     if (empty($SizeChartDetail)) {
         return Redirect()->back();
     }
     if ($request->isMethod('POST')) {
 
         $formData = $request->all();
         if (!empty($formData)) {
             $validator = Validator::make(
                 $request->all(),
                 array(
                     'name' => 'required',
                 ),
                 array(
                     "name.required" => trans("The name field is required."),
                 )
             );
             if ($validator->fails()) {
                 return Redirect::back()->withErrors($validator)->withInput();
             } else {
                
                 DB::beginTransaction();
                 $obj                                = SizeChartDetail::find($des_id);
                 $obj->name                          = $request->input('name');
                 $obj->save();
                 $lastId = $obj->id;
               
                 
                 if(!empty($lastId)){
                     SizeChartDetailValue::where('size_chart_detail_id', $lastId)->delete();
                      
                     $tableHeadersArray = $request->input('headers');
                     $tableHeaders = [];
                     foreach ($tableHeadersArray as $index => $header) {
                         $tableHeaders["header" . ($index + 1)] = $header;
                     }
                     $tableData = $request->input('cell');
                     $sizeChartDetailValue = new SizeChartDetailValue();
                     $sizeChartDetailValue->size_chart_detail_id = $lastId;
                     $sizeChartDetailValue->categoryIds  = $request->input('categoryData');
                     $sizeChartDetailValue->productIds  = $request->input('productData');
                     $sizeChartDetailValue->table_headers = $tableHeaders;
                     $sizeChartDetailValue->table_data = $tableData;
                     $sizeChartDetailValue->save();
                     DB::commit();
                 }else{
                     DB::rollback();
                     Session()->flash('flash_notice', 'Something Went Wrong');
                     return Redirect::route('admin-size-chart-details.index', $endesid);
                 }
                 Session()->flash('flash_notice', trans("Size chart detail updated successfully."));
                 return Redirect::route('admin-size-chart-details.index', $endesid);
             }
         }
     }
         $categories = Category::whereNull('parent_id')
         ->where('is_active', 1)
         ->where('is_deleted', 0)
         ->get();
         $products = DB::table('products')->where('is_active', 1)
         ->where('is_deleted', 0)
         ->select('id', 'name')->get()->toArray();
         $sizeChartDetailValue = SizeChartDetailValue::where('size_chart_detail_id',$des_id)->get()->toArray();
         $listQuery['country'] = DB::table('countries')->where('is_active', 1)
            ->select('id', 'name')->get()->toArray();
        $listQuery['currency'] = DB::table('currencies')->where('is_active', 1)
            ->select('id', 'name','symbol', 'currency_code')->get()->toArray();
         return  View("admin.$this->model.edit", compact('SizeChartDetail', 'categories', 'products', 'sizeChartDetailValue','des_id','listQuery'));
     }

    public function delete($token)
    {
        try {
            $categoryId = '';
            if (!empty($token)) {
                $categoryId = base64_decode($token);
            }
            $category = SizeChartDetail::find($categoryId);
            if (empty($category)) {
                return Redirect()->route($this->model . '.index');
            }
            if ($category) {
                SizeChartDetail::where('id', $categoryId)->delete();
                SizeChartDetailValue::where('size_chart_detail_id',$categoryId)->delete();

                Session()->flash('flash_notice', trans("Size chart detail has been removed successfully."));
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
            $statusMessage = trans("Size chart detail has been actvated successfully");
        } else {
            $statusMessage = trans("Size chart detail has been deactivated successfully");
        }
        $category = SizeChartDetail::find($modelId);
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
