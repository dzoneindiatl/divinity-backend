<?php
namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\NewSizeChart;
use App\Models\NewSizeChartContent;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\SizeChartImages;
use Illuminate\Support\Facades\File;
use Redirect,DB,Response;

class NewSizeChartController extends Controller
{
    public $model = 'new-size-charts';
    public function __construct(Request $request)
    {
        $this->listRouteName = 'admin-new-size-charts.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;

    }

    public function index(Request $request)
    {
        try {
            $DB = NewSizeChart::query();
            $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'new_size_charts.created_at';
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
                    $DB->whereBetween('new_size_charts.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
                } elseif (!empty($searchData['date_from'])) {
                    $dateS = $searchData['date_from'];
                    $DB->where('new_size_charts.created_at', '>=', [$dateS . " 00:00:00"]);
                } elseif (!empty($searchData['date_to'])) {
                    $dateE = $searchData['date_to'];
                    $DB->where('new_size_charts.created_at', '<=', [$dateE . " 00:00:00"]);
                }
                foreach ($searchData as $fieldName => $fieldValue) {
                    if ($fieldValue != "") {
                        if ($fieldName == "name") {
                            $DB->where("new_size_charts.name", 'like', '%' . $fieldValue . '%');
                        }
                        if ($fieldName == "is_active") {
                            $DB->where("new_size_charts.is_active", $fieldValue);
                        }
                    }
                }
            }

            $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();

            #echo "<pre>";print_r($results);exit;
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
            $bottom_data = NewSizeChartContent::first();
            return view('admin.new-size-charts.create',['bottom_data' => $bottom_data]);
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
            $request->validate([
                'category' => 'required|string',
                'size' => 'required|array',
            ]);

            // Start the database transaction
            DB::beginTransaction();
            
            try {                             
                    #echo "<pre>";print_r($formData);exit;
                    $sizes = $request->input('size');
                    $chests = $request->input('chest');
                    $waists = $request->input('waist');
                    $hips = $request->input('hip');
                    $shoulders = $request->input('shoulder');
                    $armholes = $request->input('armhole');
                    $sleeveLengths = $request->input('sleeve_length');
                    $lengths = $request->input('length');

                    foreach ($sizes as $index => $sizeType) {
                        NewSizeChart::create([
                            'category' => $request->category,
                            'size' => $sizes[$index] ?? null,
                            'chest' => $chests[$index] ?? null,
                            'waist' => $waists[$index] ?? null,
                            'hip' => $hips[$index] ?? null,
                            'shoulder' => $shoulders[$index] ?? null,
                            'armhole' => $armholes[$index] ?? null,
                            'sleeve_length' => $sleeveLengths[$index] ?? null,
                            'length' => $lengths[$index] ?? null,
                        ]);
                    }

                    $bottom_data = NewSizeChartContent::first();
                    if ($bottom_data) {
                        $bottom_data->description = $request->description;
                        $saved = $bottom_data->save();
                    } else {
                        NewSizeChartContent::create([
                            'description' => $request->description
                        ]);
                    }

            
                // Commit the transaction after successful save
                DB::commit();

                return redirect()->route('admin-new-size-charts.index')->with('success', 'Size Chart created successfully');
                
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

                $id = base64_decode($token);

                $size_charts = NewSizeChart::find($id);
                $bottom_data = NewSizeChartContent::first();


                return View("admin.$this->model.edit",['size_charts' => $size_charts, 'bottom_data' => $bottom_data]);
            }
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $token)
    {
        try {          

            $id = '';
            if (!empty($token)) {
                $id = base64_decode($token);
            } else {
                return redirect()->route('admin-'.$this->model . ".index");
            }

            $size_charts = NewSizeChart::find($id);
            if (empty($size_charts)) {
                return View("admin.$this->model.edit");
            } else {
                $formData = $request->all();
                #echo "<pre>";print_r($formData);exit;
                if (!empty($formData)) {
                    
                    $validator = Validator::make(
                        $request->all(),
                        array(
                            'category' => 'required|string',
                            'size' => 'required|array',
                        ),
                        array(
                            "category.required" => trans("The category field is required."),
                            "size.required" => trans("The size field is required."),
                        )
                    );
                    if ($validator->fails()) {
                        return Redirect::back()->withErrors($validator)->withInput();
                    } else {
                        DB::beginTransaction();
                        $obj        = $size_charts;
                        $obj->category = $formData['category'];// Upper,Bottom
                        $obj->size = $formData['size'][0];//S, M, L
                        $obj->chest = $formData['chest'][0];
                        $obj->waist = $formData['waist'][0];
                        $obj->hip = $formData['hip'][0];
                        $obj->shoulder = $formData['shoulder'][0];
                        $obj->armhole = $formData['armhole'][0];
                        $obj->sleeve_length = $formData['sleeve_length'][0];
                        $obj->length = $formData['length'][0];                        
                        $obj->save();

                        $bottom_data = NewSizeChartContent::first();
                        if ($bottom_data) {
                            $bottom_data->description = $request->description;
                            $saved = $bottom_data->save();
                        } else {
                            NewSizeChartContent::create([
                                'description' => $request->description
                            ]);
                        }

                        DB::commit();
                        Session()->flash('flash_notice', trans("Size Chart updated successfully."));
                        return Redirect::route('admin-new-size-charts.index');
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
            $category = NewSizeChart::find($categoryId);
            if (empty($category)) {
                return Redirect()->route('admin-'.$this->model . '.index');
            }
            if ($category) {
                NewSizeChart::where('id', $categoryId)->delete();

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
    
        $category = NewSizeChart::find($modelId);
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
