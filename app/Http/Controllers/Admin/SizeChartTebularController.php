<?php
namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\SizeChartTebular;
use App\Models\SizeChart;
use App\Models\SizeChartTebularContent;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\SizeChartImages;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Response;

class SizeChartTebularController extends Controller
{
    public $model = 'size-chart-tebulars';
    public $listRouteName;
    public $request;

    public function __construct(Request $request)
    {
        $this->middleware('permission:view_size_chart_tebular|create_size_chart_tebular|edit_size_chart_tebular|delete_size_chart_tebular', ['only' => ['index', 'show']]);
        $this->middleware('permission:create_size_chart_tebular', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_size_chart_tebular', ['only' => ['edit', 'update', 'changeStatus']]);
        $this->middleware('permission:delete_size_chart_tebular', ['only' => ['destroy']]);


        $this->listRouteName = 'admin-size-chart-tebulars.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;

    }

    public function index(Request $request)
    {
        try {
            $DB = SizeChartTebular::query();
            $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'size_chart_tebulars.created_at';
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
                    $DB->whereBetween('size_chart_tebulars.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
                } elseif (!empty($searchData['date_from'])) {
                    $dateS = $searchData['date_from'];
                    $DB->where('size_chart_tebulars.created_at', '>=', [$dateS . " 00:00:00"]);
                } elseif (!empty($searchData['date_to'])) {
                    $dateE = $searchData['date_to'];
                    $DB->where('size_chart_tebulars.created_at', '<=', [$dateE . " 00:00:00"]);
                }
                foreach ($searchData as $fieldName => $fieldValue) {
                    if ($fieldValue != "") {
                        if ($fieldName == "name") {
                            $DB->where("size_chart_tebulars.name", 'like', '%' . $fieldValue . '%');
                        }
                        if ($fieldName == "is_active") {
                            $DB->where("size_chart_tebulars.is_active", $fieldValue);
                        }
                    }
                }
            }
            $DB->where("size_chart_tebulars.type_show", 'default');

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
            $chart_content = SizeChartTebularContent::first();
            return view('admin.size-chart-tebulars.create',['chart_content' => $chart_content]);
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
            // $request->validate([
            //     'category' => 'required|string',
            //     'size' => 'required|array',
            // ]);

            // Start the database transaction
            DB::beginTransaction();
            
            try {                             
                    /* Size Chart Code start */
                    $size_array['upper'] = array();
                    $size_array['bottom'] = array();
                    if($request->mesurement_type == 'inch')
                    {
                        if(!empty($request->upper_type) && is_array($request->upper_type))
                        {
                            foreach ($request->upper_type as $key => $u_type) {
                                $size_array['upper'][$u_type]['xs'] = $request->top_size_xs[$key] ?? 0;
                                $size_array['upper'][$u_type]['s']  = $request->top_size_s[$key] ?? 0;
                                $size_array['upper'][$u_type]['m']  = $request->top_size_m[$key] ?? 0;
                                $size_array['upper'][$u_type]['l']  = $request->top_size_l[$key] ?? 0;
                                $size_array['upper'][$u_type]['xl'] = $request->top_size_xl[$key] ?? 0;
                                $size_array['upper'][$u_type]['2xl'] = $request->top_size_2xl[$key] ?? 0;
                                $size_array['upper'][$u_type]['is_active'] = 1;
                            }
                        }              

                        if(!empty($request->bottom_type) && is_array($request->bottom_type))
                        {
                            foreach($request->bottom_type as $key => $u_type)
                            {
                                $size_array['bottom'][$u_type]['xs'] = $request->bottom_size_xs[$key] ?? 0;
                                $size_array['bottom'][$u_type]['s'] = $request->bottom_size_s[$key] ?? 0;
                                $size_array['bottom'][$u_type]['m'] = $request->bottom_size_m[$key] ?? 0;
                                $size_array['bottom'][$u_type]['l'] = $request->bottom_size_l[$key] ?? 0;
                                $size_array['bottom'][$u_type]['xl'] = $request->bottom_size_xl[$key] ?? 0;
                                $size_array['bottom'][$u_type]['2xl'] = $request->bottom_size_2xl[$key] ?? 0;
                                $size_array['bottom'][$u_type]['is_active'] = 1;
                            }
                        }
                    }

                    if($request->mesurement_type == 'cm')
                    {
                        if(!empty($request->upper_type_cm) && is_array($request->upper_type_cm))
                        {
                            foreach ($request->upper_type_cm as $key => $u_type) {
                                $size_array['upper'][$u_type]['xs'] = $request->top_size_cm_xs[$key] ?? 0;
                                $size_array['upper'][$u_type]['s']  = $request->top_size_cm_s[$key] ?? 0;
                                $size_array['upper'][$u_type]['m']  = $request->top_size_cm_m[$key] ?? 0;
                                $size_array['upper'][$u_type]['l']  = $request->top_size_cm_l[$key] ?? 0;
                                $size_array['upper'][$u_type]['xl'] = $request->top_size_cm_xl[$key] ?? 0;
                                $size_array['upper'][$u_type]['2xl'] = $request->top_size_cm_2xl[$key] ?? 0;
                                $size_array['upper'][$u_type]['is_active'] = 1;
                            }
                        }              

                        if(!empty($request->bottom_type_cm) && is_array($request->bottom_type_cm))
                        {
                            foreach($request->bottom_type_cm as $key => $u_type)
                            {
                                $size_array['bottom'][$u_type]['xs'] = $request->bottom_size_cm_xs[$key] ?? 0;
                                $size_array['bottom'][$u_type]['s'] = $request->bottom_size_cm_s[$key] ?? 0;
                                $size_array['bottom'][$u_type]['m'] = $request->bottom_size_cm_m[$key] ?? 0;
                                $size_array['bottom'][$u_type]['l'] = $request->bottom_size_cm_l[$key] ?? 0;
                                $size_array['bottom'][$u_type]['xl'] = $request->bottom_size_cm_xl[$key] ?? 0;
                                $size_array['bottom'][$u_type]['2xl'] = $request->bottom_size_cm_2xl[$key] ?? 0;
                                $size_array['bottom'][$u_type]['is_active'] = 1;
                            }
                        }
                    }

                    //echo "<pre>";print_r($size_array);exit;

                    $chart_content = SizeChartTebularContent::first();
                    if ($chart_content) {
                        $chart_content->title = $request->chart_title;
                        $chart_content->description = $request->chart_description;
                        $chart_content->save();
                    } else {
                        SizeChartTebularContent::create([
                            'title' => $request->chart_title,
                            'description' => $request->chart_description,
                        ]);
                    }

                    //$chart_measurement = SizeChartTebular::where('category_id', $lastId)->first();
                    $chart_measurement = SizeChartTebular::where('category_id', 0)->where('type_show', 'default')->first();
                    if ($chart_measurement) {
                        $chart_measurement->category_id = 0; // for default no need to category id.
                        if($request->mesurement_type == 'inch')
                        {
                            $chart_measurement->measurement_inch = json_encode($size_array);
                            $chart_measurement->type_show = 'default';
                        }

                        if($request->mesurement_type == 'cm')
                        {
                            $chart_measurement->measurement_cm = json_encode($size_array);
                            $chart_measurement->type_show = 'default';
                        }           
                        
                        $chart_measurement->save();
                    } else {
                        if($request->mesurement_type == 'inch')
                        {
                            SizeChartTebular::create([
                                'category_id' => 0,
                                'type_show' => 'default',
                                'measurement_inch' => json_encode($size_array)
                            ]);
                        }

                        if($request->mesurement_type == 'cm')
                        {
                            SizeChartTebular::create([
                                'category_id' => 0,
                                'type_show' => 'default',
                                'measurement_cm' => json_encode($size_array)
                            ]);
                        }
                    }

                    /* Size Chart Code End */

            
                // Commit the transaction after successful save
                DB::commit();

                return redirect()->route('admin-size-chart-tebulars.index')->with('success', 'Size Chart created successfully');
                
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

                //$size_charts = SizeChartTebular::find($id);
                $chart_measurement = SizeChartTebular::where('id', $id)
                ->where('type_show', 'default') // Example additional condition
                ->first();
                $chart_content = SizeChartTebularContent::first();

                return View("admin.$this->model.edit",['chart_measurement' => $chart_measurement, 'chart_content' => $chart_content]);
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

            $size_charts = SizeChartTebular::find($id);
            if (empty($size_charts)) {
                return View("admin.$this->model.edit");
            } else {
                $formData = $request->all();
                
                if (!empty($formData)) {
                    
                    // $validator = Validator::make(
                    //     $request->all(),
                    //     array(
                    //         'category' => 'required|string',
                    //         'size' => 'required|array',
                    //     ),
                    //     array(
                    //         "category.required" => trans("The category field is required."),
                    //         "size.required" => trans("The size field is required."),
                    //     )
                    // );
                    // if ($validator->fails()) {
                    //     return Redirect::back()->withErrors($validator)->withInput();
                    // } else {
                    // }

                    
                        DB::beginTransaction();

                        /* Size Chart Code Start */
                            $size_array['upper'] = array();
                            $size_array['bottom'] = array();
                            if($request->mesurement_type == 'inch')
                            {
                                if(!empty($request->upper_type) && is_array($request->upper_type))
                                {
                                    foreach ($request->upper_type as $key => $u_type) {
                                        $size_array['upper'][$u_type]['xs'] = $request->top_size_xs[$key] ?? 0;
                                        $size_array['upper'][$u_type]['s']  = $request->top_size_s[$key] ?? 0;
                                        $size_array['upper'][$u_type]['m']  = $request->top_size_m[$key] ?? 0;
                                        $size_array['upper'][$u_type]['l']  = $request->top_size_l[$key] ?? 0;
                                        $size_array['upper'][$u_type]['xl'] = $request->top_size_xl[$key] ?? 0;
                                        $size_array['upper'][$u_type]['2xl'] = $request->top_size_2xl[$key] ?? 0;
                                        $size_array['upper'][$u_type]['is_active'] = 1;
                                    }
                                }              

                                if(!empty($request->bottom_type) && is_array($request->bottom_type))
                                {
                                    foreach($request->bottom_type as $key => $u_type)
                                    {
                                        $size_array['bottom'][$u_type]['xs'] = $request->bottom_size_xs[$key] ?? 0;
                                        $size_array['bottom'][$u_type]['s'] = $request->bottom_size_s[$key] ?? 0;
                                        $size_array['bottom'][$u_type]['m'] = $request->bottom_size_m[$key] ?? 0;
                                        $size_array['bottom'][$u_type]['l'] = $request->bottom_size_l[$key] ?? 0;
                                        $size_array['bottom'][$u_type]['xl'] = $request->bottom_size_xl[$key] ?? 0;
                                        $size_array['bottom'][$u_type]['2xl'] = $request->bottom_size_2xl[$key] ?? 0;
                                        $size_array['bottom'][$u_type]['is_active'] = 1;
                                    }
                                }
                            }

                            if($request->mesurement_type == 'cm')
                            {
                                if(!empty($request->upper_type_cm) && is_array($request->upper_type_cm))
                                {
                                    foreach ($request->upper_type_cm as $key => $u_type) {
                                        $size_array['upper'][$u_type]['xs'] = $request->top_size_cm_xs[$key] ?? 0;
                                        $size_array['upper'][$u_type]['s']  = $request->top_size_cm_s[$key] ?? 0;
                                        $size_array['upper'][$u_type]['m']  = $request->top_size_cm_m[$key] ?? 0;
                                        $size_array['upper'][$u_type]['l']  = $request->top_size_cm_l[$key] ?? 0;
                                        $size_array['upper'][$u_type]['xl'] = $request->top_size_cm_xl[$key] ?? 0;
                                        $size_array['upper'][$u_type]['2xl'] = $request->top_size_cm_2xl[$key] ?? 0;
                                        $size_array['upper'][$u_type]['is_active'] = 1;
                                    }
                                }              

                                if(!empty($request->bottom_type_cm) && is_array($request->bottom_type_cm))
                                {
                                    foreach($request->bottom_type_cm as $key => $u_type)
                                    {
                                        $size_array['bottom'][$u_type]['xs'] = $request->bottom_size_cm_xs[$key] ?? 0;
                                        $size_array['bottom'][$u_type]['s'] = $request->bottom_size_cm_s[$key] ?? 0;
                                        $size_array['bottom'][$u_type]['m'] = $request->bottom_size_cm_m[$key] ?? 0;
                                        $size_array['bottom'][$u_type]['l'] = $request->bottom_size_cm_l[$key] ?? 0;
                                        $size_array['bottom'][$u_type]['xl'] = $request->bottom_size_cm_xl[$key] ?? 0;
                                        $size_array['bottom'][$u_type]['2xl'] = $request->bottom_size_cm_2xl[$key] ?? 0;
                                        $size_array['bottom'][$u_type]['is_active'] = 1;
                                    }
                                }
                            }

                            #echo "<pre>";print_r($size_array);exit;

                            $chart_content = SizeChartTebularContent::first();
                            if ($chart_content) {
                                $chart_content->title = $request->chart_title;
                                $chart_content->description = $request->chart_description;
                                $chart_content->save();
                            } else {
                                SizeChartTebularContent::create([
                                    'title' => $request->chart_title,
                                    'description' => $request->chart_description,
                                ]);
                            }

                            $chart_measurement = SizeChartTebular::where('category_id', 0)->where('type_show', 'default')->first();
                            if ($chart_measurement) {
                                $chart_measurement->category_id = 0;
                                $chart_measurement->type_show = 'default';
                                if($request->mesurement_type == 'inch')
                                {
                                    $chart_measurement->measurement_inch = json_encode($size_array);
                                }

                                if($request->mesurement_type == 'cm')
                                {
                                    $chart_measurement->measurement_cm = json_encode($size_array);
                                }           
                                
                                $chart_measurement->save();
                            } else {
                                if($request->mesurement_type == 'inch')
                                {
                                    SizeChartTebular::create([
                                        'category_id' => 0,
                                        'type_show' => 'default',
                                        'measurement_inch' => json_encode($size_array)
                                    ]);
                                }

                                if($request->mesurement_type == 'cm')
                                {
                                    SizeChartTebular::create([
                                        'category_id' => 0,
                                        'type_show' => 'default',
                                        'measurement_cm' => json_encode($size_array)
                                    ]);
                                }
                            }

                            /* Size Chart Code End */

                        DB::commit();
                        Session()->flash('flash_notice', trans("Size Chart updated successfully."));
                        return Redirect::route('admin-size-chart-tebulars.index');
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
            $category = SizeChartTebular::find($categoryId);
            if (empty($category)) {
                return Redirect()->route('admin-'.$this->model . '.index');
            }
            if ($category) {
                SizeChartTebular::where('id', $categoryId)->delete();

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
    
        $category = SizeChartTebular::find($modelId);
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

    public function showSizeChart(Request $request, $id)
    {
        $size_chart = SizeChartTebular::where(['id' => $id])->where(['category_id' => 0])->where(['type_show' => 'default'])        
        ->first();//type_show = 'default';

        $size_chart_content = SizeChartTebularContent::first();        

        
        //echo "<pre>";print_r($size_chart);
        //echo "<pre>";print_r($size_chart_content);
        
        // exit;

        $sizeChart = view('admin.size-chart-tebulars.size-chart-tebular-popup', compact('size_chart','size_chart_content'))->render();
        $res['success'] = true;
        $res['partialsSize'] = $sizeChart;
        $res['message'] = 'Dynamic data created successfully.';
       // Log::info('Response: ', $res);
        return response()->json($res);
    }

}
