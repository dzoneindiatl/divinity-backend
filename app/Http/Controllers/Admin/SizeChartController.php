<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\SizeChart;
use App\Models\SizeChartDetail;
use App\Models\SizeChartDetailValue;
use App\Models\SizeChartAssign;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\SizeChartImages;
use Illuminate\Support\Facades\File;
use Redirect,DB,Response;

class SizeChartController extends Controller
{
    public $model = 'size-charts';
    public function __construct(Request $request)
    {

        $this->middleware('permission:view_size_chart_tebular|view_sizechart|edit_sizechart|delete_sizechart', ['only' => ['index', 'show']]);
        $this->middleware('permission:create_sizechart', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_sizechart', ['only' => ['edit', 'update', 'changeStatus']]);
        $this->middleware('permission:delete_sizechart', ['only' => ['destroy']]);


        $this->listRouteName = 'admin-size-charts.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;

    }

    public function index(Request $request)
    {
        try {
            $DB = SizeChart::query();
            $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'size_charts.created_at';
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
                    $DB->whereBetween('size_charts.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
                } elseif (!empty($searchData['date_from'])) {
                    $dateS = $searchData['date_from'];
                    $DB->where('size_charts.created_at', '>=', [$dateS . " 00:00:00"]);
                } elseif (!empty($searchData['date_to'])) {
                    $dateE = $searchData['date_to'];
                    $DB->where('size_charts.created_at', '<=', [$dateE . " 00:00:00"]);
                }
                foreach ($searchData as $fieldName => $fieldValue) {
                    if ($fieldValue != "") {
                        if ($fieldName == "name") {
                            $DB->where("size_charts.name", 'like', '%' . $fieldValue . '%');
                        }
                        if ($fieldName == "is_active") {
                            $DB->where("size_charts.is_active", $fieldValue);
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
            $categories = Category::whereNull('parent_id')->where('is_active', 1)->where('is_deleted', 0)->get();

            $products = DB::table('products')->where('is_active', 1)->where('is_deleted', 0)->select('id','name')->get()->toArray();
            $countries = Country::where('is_active', 1)->select('id','name')->get()->toArray();
            return view('admin.size-charts.create',['categories' => $categories, 'products' => $products,'countries'=>$countries]);
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
{
    try {
        $formData = $request->all();
        
        // Validate input data
        $validator = Validator::make(
            $formData,
            [
                'name' => 'required',
            ],
            [
                "name.required" => trans("The name field is required."),
            ]
        );

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        // Start the database transaction
        DB::beginTransaction();
        
        try {
            $obj = new SizeChart;
            $obj->name = $request->input('name');
            $obj->description = $request->input('description', '');
            $obj->country_id = $request->input('country_id', '');
            $obj->category_id = $request->input('category_id', '');
            $obj->sub_category_id = $request->input('sub_category_id', '');
            $obj->child_category_id = $request->input('child_category_id', '');
            $obj->centimeter_details = $request->input('centimeter_details', '');
            $obj->inch_details = $request->input('inch_details', '');

            $obj->save(); 
            

            if ($request->hasFile('chart_image')) {
                foreach ($request->file('chart_image') as $key => $file) {
                    // Generate a unique filename
                    $filename = time() . '_' . $key . '.' . $file->getClientOriginalExtension();
                    
                    // Define the folder name based on the current month and year
                    $folderName = strtoupper(date('M') . date('Y')) . "/";
                    $folderPath = Config('constant.SIZECHART_IMAGE_ROOT_PATH') . $folderName;
                    
                    // Create the directory if it doesn't exist
                    if (!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, 0777, true);
                    }
            
                    // Move the uploaded file to the specified path
                    if ($file->move($folderPath, $filename)) {
                        // Create a new SizeChartImages instance and save it
                        $image = new SizeChartImages();
                        $image->size_chart_id = $obj->id; // Assuming you have a foreign key in the SizeChartImages table
                        $image->image = $folderName . $filename; // Store relative path
                        $image->heading = $request->input('image_heading')[$key];
                        $image->description = $request->input('image_description')[$key];
                        
                        // Save the image record
                        $image->save();
                    }
                }
            }
            




           
            // Commit the transaction after successful save
            DB::commit();

            return redirect()->route('admin-size-charts.index')->with('success', 'Size Chart created successfully');
            
        } catch (Exception $e) {
            // Rollback the transaction on error
            DB::rollback();
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something went wrong', 'error_msg' => $e->getMessage()]);
        }
    } catch (Exception $e) {
        Log::error($e);
        return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
    }
}


    public function edit(Request $request, $token = null)
    {
        try {
            $categoryId = '';
            if (!empty($token)) {

                $categoryId = base64_decode($token);

                $size_charts = SizeChart::find($categoryId);

                $categories = Category::whereNull('parent_id')
                        ->where('is_active', 1)
                        ->where('is_deleted', 0)
                        ->get();

                $products = DB::table('products')->where('is_active', 1)
                            ->where('is_deleted', 0)
                            ->select('id','name')->get()->toArray();
                $category_assigned = SizeChartAssign::where('size_chart_id', $categoryId)->pluck('category_id')->toArray();
                $product_assigned = SizeChartAssign::where('size_chart_id', $categoryId)->pluck('product_id')->toArray();
                return View("admin.$this->model.edit",['size_charts' => $size_charts,'categories' => $categories, 'products' => $products, 'category_assigned' => $category_assigned, 'product_assigned' => $product_assigned]);
            }
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $token)
    {//echo "<pre>"; print_r($request->all()); die;
        try {

            $categoryId = '';
            if (!empty($token)) {
                $categoryId = base64_decode($token);
            } else {
                return redirect()->route('admin-'.$this->model . ".index");
            }

            $size_charts = SizeChart::find($categoryId);
            if (empty($size_charts)) {
                return View("admin.$this->model.edit");
            } else {
                $formData = $request->all();
                if (!empty($formData)) {
                    $validator = Validator::make(
                        $request->all(),
                        array(
                            'name' => 'required',
                            'file' => 'nullable|mimes:jpg,jpeg,png,pdf',
                        ),
                        array(
                            "name.required" => trans("The name field is required."),
                        )
                    );
                    if ($validator->fails()) {
                        return Redirect::back()->withErrors($validator)->withInput();
                    } else {
                        DB::beginTransaction();
                        $obj                                = $size_charts;
                        $obj->name                          = $request->input('name');
                        $obj->description                   = !empty($request->input('description')) ? $request->input('description') : "";

                        if ($request->hasFile('file')) {
                            $extension = $request->file('file')->getClientOriginalExtension();
                            $originalName = $request->file('file')->getClientOriginalName();
                            $fileName = time() . '-file.' . $extension;

                            $folderName = strtoupper(date('M') . date('Y')) . "/";
                            $folderPath = Config('constant.SIZECHART_IMAGE_ROOT_PATH') . $folderName;
                            if (!File::exists($folderPath)) {
                                File::makeDirectory($folderPath, $mode = 0777, true);
                            }
                            if ($request->file('file')->move($folderPath, $fileName)) {
                                $obj->file = $folderName . $fileName;
                            }
                        }
                        $obj->save();
                        $lastId = $obj->id;
                        if(!empty($lastId)){
                            if(!empty($request->categoryData)) {
                                SizeChartAssign::where('size_chart_id', $lastId)->where('category_id', '!=', 0)->delete();
                                foreach($request->categoryData as $cat_data) {
                                    $obj1 = new SizeChartAssign;
                                    $obj1->size_chart_id = $lastId;
                                    $obj1->category_id = $cat_data;
                                    $obj1->product_id = 0;
                                    $obj1->save();
                                }
                            }

                            if(!empty($request->productData)) {
                                SizeChartAssign::where('size_chart_id', $lastId)->where('product_id', '!=', 0)->delete();
                                foreach($request->productData as $pro_data) {
                                    $obj1 = new SizeChartAssign;
                                    $obj1->size_chart_id = $lastId;
                                    $obj1->product_id = $pro_data;
                                    $obj1->category_id = 0;
                                    $obj1->save();
                                }
                            }
                            DB::commit();
                        }else{
                            DB::rollback();
                            Session()->flash('flash_notice', 'Something Went Wrong');
                            return Redirect::route('admin-size-charts.index');
                        }
                        Session()->flash('flash_notice', trans("Size Chart updated successfully."));
                        return Redirect::route('admin-size-charts.index');
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
            $category = SizeChart::find($categoryId);
            if (empty($category)) {
                return Redirect()->route($this->model . '.index');
            }
            if ($category) {
                SizeChart::where('id', $categoryId)->delete();
                SizeChartDetail::where('size_chart_id',$categoryId)->delete();

                Session()->flash('flash_notice', trans("Size chart has been removed successfully."));
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
                $statusMessage = trans("Size chart has been activated successfully");
            } else {
                $statusMessage = trans("Size chart has been deactivated successfully");
            }
        
            $category = SizeChart::find($modelId);
            if ($category) {
                $currentStatus = $category->is_active;
                $newStatus = $status;
        
                // Update the selected model's status
                $category->is_active = $newStatus;
                $responseStatus = $category->save();
        
                if ($newStatus == 1) {
                    // Deactivate all other models
                    SizeChart::where('id', '!=', $modelId)->update(['is_active' => 0]);
                }
        
                Session()->flash('flash_notice', $statusMessage);
            } else {
                Session()->flash('flash_notice', trans("Size chart not found"));
            }
        
            return back();
        }

}
