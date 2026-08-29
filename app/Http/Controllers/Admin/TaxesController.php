<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Tax;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Redirect,DB,Response;

class TaxesController extends Controller
{
    public $model = 'taxes';
    public function __construct(Request $request)
    {
        $this->middleware('permission:view_taxmanagement|create_taxmanagement|edit_taxmanagement|delete_taxmanagement', ['only' => ['index','store']]);
        $this->middleware('permission:create_taxmanagement', ['only' => ['create','store']]);
        $this->middleware('permission:edit_taxmanagement', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_taxmanagement', ['only' => ['destroy']]);

        $this->listRouteName = 'admin-taxes.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;
        
    }

    public function index(Request $request)
    {
        try {
            $DB = Tax::query();
            $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'taxes.created_at';
            $order = $request->input('order') ? $request->input('order') : 'Desc';
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
                    $DB->whereBetween('taxes.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
                } elseif (!empty($searchData['date_from'])) {
                    $dateS = $searchData['date_from'];
                    $DB->where('taxes.created_at', '>=', [$dateS . " 00:00:00"]);
                } elseif (!empty($searchData['date_to'])) {
                    $dateE = $searchData['date_to'];
                    $DB->where('taxes.created_at', '<=', [$dateE . " 00:00:00"]);
                }
                foreach ($searchData as $fieldName => $fieldValue) {
                    if ($fieldValue != "") {
                        if ($fieldName == "tax_type") {
                            $DB->where("taxes.tax_type", 'like', '%' . $fieldValue . '%');
                        }
                        if ($fieldName == "is_active") {
                            $DB->where("taxes.is_active", $fieldValue);
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
            return view('admin.taxes.create');
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
                if ($request->input('tax_type') == "flat") {
                    $validator = Validator::make($formData,
                        [
                            'tax_option' => 'required|string|in:inclusive,exclusive',
                            'tax_type' => 'required|string|in:flat,percentage',
                            'tax_rate_flat' => 'required|array|min:1',
                            'tax_rate_flat.*' => 'required',
                        ],
                        [
                           'tax_rate_flat.*.tax_rate_flat.required' => 'The tax rate field is required.',
                        ]
                    );
                } else if ($request->input('tax_type') == "floating") {
                    $validator = Validator::make($formData,
                        [
                            'tax_option' => 'required|string|in:inclusive,exclusive',
                            'tax_type' => 'required|string|in:flat,floating',

                            'tax_from' => 'required|array|min:1',
                            'tax_to' => 'required|array|min:1',
                            'tax_rate_floating' => 'required|array|min:1',

                            // Each item numeric
                            'tax_from.*' => 'required',
                            'tax_to.*' => 'required',
                            'tax_rate_floating.*' => 'required',
                        ],
                        [
                           'tax_from.*.tax_from.required' => 'The tax from field is required.',
                           'tax_to.*.tax_to.required' => 'The tax to field is required.',
                           'tax_rate_floating.*.tax_rate_floating.required' => 'The tax rate field is required.',
                        ]
                    );
                }
               
                if ($validator->fails()) {
                    return Redirect::back()->withErrors($validator)->withInput();
                } else {
                    $tax_option = $request->input('tax_option');
                    $tax_type = $request->input('tax_type');
                    $tax_from = $request->input('tax_from');
                    $tax_to = $request->input('tax_to');
                    $tax_rate_flat = $request->input('tax_rate_flat');
                    $tax_rate_floating = $request->input('tax_rate_floating');
                    
                    DB::beginTransaction();
                    try {
                        if($tax_type == "flat"){
                            foreach ($tax_rate_flat as $index => $flat) {
                                $tax = new Tax();
                                $tax->tax_option = $tax_option;
                                $tax->tax_type = $tax_type;
                                $tax->tax_rate = $flat;
                                if (!$tax->save()) {
                                    throw new \Exception("Failed to save tax record at index $index");
                                }
                            }
                        } else if ($tax_type == "floating") {
                            foreach ($tax_from as $index => $from) {
                                $tax = new Tax();
                                $tax->tax_option = $tax_option;
                                $tax->tax_type = $tax_type;
                                $tax->tax_from = $from;
                                $tax->tax_to = $tax_to[$index];
                                $tax->tax_rate = $tax_rate_floating[$index];
                                if (!$tax->save()) {
                                    // If saving fails, throw exception to rollback
                                    throw new \Exception("Failed to save tax record at index $index");
                                }
                            }
                        }
                        // All records saved successfully
                        DB::commit();
                        return Redirect()->route('admin-'.$this->model . ".index")->with('success', 'All tax entries saved successfully!');
                    } catch (\Exception $e) {
                        DB::rollback();
                        Session()->flash('flash_notice', 'Something went wrong: ' . $e->getMessage());
                        return redirect()->route('admin-'.$this->model . ".index");
                    }
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function edit(Request $request, $token = null)
    {
        try {
            $taxId = '';
            if (!empty($token)) {
                $taxId = base64_decode($token);
                $tax = Tax::find($taxId);
                return View("admin.$this->model.edit", compact('tax'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $token)
    { 
        try {
            $taxId = '';
            if (!empty($token)) {
                $taxId = base64_decode($token);
            } else {
                return redirect()->route('admin-'.$this->model . ".index");
            }
            $taxData = Tax::find($taxId);
            if (empty($taxData)) {
                return View("admin.$this->model.edit");
            } else {
                $taxData->delete();
                $formData = $request->all();
                if (!empty($formData)) {
                    if ($request->input('tax_type') == "flat") {
                        $validator = Validator::make($formData,
                            [
                                'tax_option' => 'required|string|in:inclusive,exclusive',
                                'tax_type' => 'required|string|in:flat,percentage',
                                'tax_rate_flat' => 'required|array|min:1',
                                'tax_rate_flat.*' => 'required',
                            ],
                            [
                            'tax_rate_flat.*.tax_rate_flat.required' => 'The tax rate field is required.',
                            ]
                        );
                    } else if ($request->input('tax_type') == "floating") {
                        $validator = Validator::make($formData,
                            [
                                'tax_option' => 'required|string|in:inclusive,exclusive',
                                'tax_type' => 'required|string|in:flat,floating',

                                'tax_from' => 'required|array|min:1',
                                'tax_to' => 'required|array|min:1',
                                'tax_rate_floating' => 'required|array|min:1',

                                // Each item numeric
                                'tax_from.*' => 'required',
                                'tax_to.*' => 'required',
                                'tax_rate_floating.*' => 'required',
                            ],
                            [
                            'tax_from.*.tax_from.required' => 'The tax from field is required.',
                            'tax_to.*.tax_to.required' => 'The tax to field is required.',
                            'tax_rate_floating.*.tax_rate_floating.required' => 'The tax rate field is required.',
                            ]
                        );
                    }
                    if ($validator->fails()) {
                        return Redirect::back()->withErrors($validator)->withInput();
                    } else {
                        $tax_option = $request->input('tax_option');
                        $tax_type = $request->input('tax_type');
                        $tax_from = $request->input('tax_from');
                        $tax_to = $request->input('tax_to');
                        $tax_rate_flat = $request->input('tax_rate_flat');
                        $tax_rate_floating = $request->input('tax_rate_floating');
                        
                        DB::beginTransaction();
                        try {
                            if($tax_type == "flat"){
                                foreach ($tax_rate_flat as $index => $flat) {
                                    $tax = new Tax();
                                    $tax->tax_option = $tax_option;
                                    $tax->tax_type = $tax_type;
                                    $tax->tax_rate = $flat;
                                    if (!$tax->save()) {
                                        throw new \Exception("Failed to save tax record at index $index");
                                    }
                                }
                            } else if ($tax_type == "floating") {
                                foreach ($tax_from as $index => $from) {
                                    $tax = new Tax();
                                    $tax->tax_option = $tax_option;
                                    $tax->tax_type = $tax_type;
                                    $tax->tax_from = $from;
                                    $tax->tax_to = $tax_to[$index];
                                    $tax->tax_rate = $tax_rate_floating[$index];
                                    if (!$tax->save()) {
                                        // If saving fails, throw exception to rollback
                                        throw new \Exception("Failed to save tax record at index $index");
                                    }
                                }
                            }
                            // All records saved successfully
                            DB::commit();
                            return Redirect()->route('admin-'.$this->model . ".index")->with('success', 'All tax entries saved successfully!');
                        } catch (\Exception $e) {
                            DB::rollback();
                            Session()->flash('flash_notice', 'Something went wrong: ' . $e->getMessage());
                            return redirect()->route('admin-'.$this->model . ".index");
                        }
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
            $taxId = '';
            if (!empty($token)) {
                $taxId = base64_decode($token);
            }
            $tax = Tax::find($taxId);
            if (empty($tax)) {
                return Redirect()->route($this->model . '.index');
            }
            if ($tax) {
                Tax::where('id', $taxId)->delete();
                Session()->flash('flash_notice', trans("Tax has been removed successfully."));
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
            $statusMessage = trans("Tax has been actvated successfully");
        } else {
            $statusMessage = trans("Tax has been deactivated successfully");
        }
        $tax = Tax::find($modelId);
        if ($tax) {
            $currentStatus = $tax->is_active;
            if (isset($currentStatus) && $currentStatus == 0) {
                $NewStatus = 1;
            } else {
                $NewStatus = 0;
            }
            $tax->is_active = $NewStatus;
            $ResponseStatus = $tax->save();
        }
        Session()->flash('flash_notice', $statusMessage);
        return back();
    }

    
}
