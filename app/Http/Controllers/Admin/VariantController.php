<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use  App\Models\Variant;
use  App\Models\VariantValue;
use DB, Redirect, Response;
use App\Models\ProductVariant;
use App\Models\ProductVariantValue;

class VariantController extends Controller
{
    public $model   =   'variants';
    public function __construct(Request $request)
    {
        $this->middleware('permission:view_variant|create_variant|edit_variant|delete_variant', ['only' => ['index', 'store']]);
        $this->middleware('permission:create_variant', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_variant', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_variant', ['only' => ['destroy']]);

        $this->listRouteName = 'admin-variants.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;
    }
    public function index(Request $request)
    {

        $DB = Variant::query();
        $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'variants.created_at';
        $order = $request->input('order') ? $request->input('order') : 'desc';
        $offset = !empty($request->input('offset')) ? $request->input('offset') : 0;
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
                $DB->whereBetween('variants.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('variants.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('variants.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("variants.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("variants.is_active", $fieldValue);
                    }
                }
            }
        }

        $DB->where("is_deleted", 0);
        $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();
        $totalResults = $DB->count();
        if ($request->ajax()) {

            return  View("admin.$this->model.load_more_data", compact('results', 'totalResults'));
        } else {

            return  View("admin.$this->model.index", compact('results', 'totalResults'));
        }
    }

    public function create()
    {
        return view("admin.$this->model.add");
    }

    
    public function store(Request $request)
    {
        $formData = $request->all();
        if (!empty($formData)) {
            $validator = Validator::make(
                $request->all(),
                array(
                    'name' =>'required|unique:variants,name',
                    'product_type' =>'required',
                ),
                array(
                    "name.required" => trans("The name field is required."),
                    'name.unique' => trans("This name already exists."),
                    "product_type.required" => trans("The Variant Design Type field is required."),
                )
            );
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {
                DB::beginTransaction();
                $obj                                = new Variant;
                $obj->name                          = $request->input('name');
                $obj->type                          = $request->input('product_type');
                $obj->save();
                $lastId = $obj->id;
                if (!empty($lastId)) {
                    if (!empty($request->dataArr)) {
                        foreach ($request->dataArr as $variantValue) {
                            if (!empty($variantValue['name'])) {
                                $obj2   =  new VariantValue;
                                $obj2->variant_id = $lastId;
                                $obj2->name = $variantValue['name'];
                                $obj2->color_code = !empty($variantValue['color_code_hidden']) ? $variantValue['color_code_hidden'] : null;
                                $obj2->save();
                                if (empty($obj2->id)) {
                                    DB::rollback();
                                }
                            }
                        }
                    }
                    DB::commit();
                    Session()->flash('flash_notice', trans("Variant saved successfully."));
                    return Redirect::route('admin-variants.index');
                } else {
                    DB::rollback();
                    Session()->flash('flash_notice', 'Something Went Wrong');
                    return Redirect::route('admin-variants.index');
                }
            }
        }
    }

    public function edit($endepid)
    {
        $record_id = '';
        if (!empty($endepid)) {
            $record_id = base64_decode($endepid);
            $recordDetails   =   Variant::find($record_id);

            $variantValuesData = VariantValue::where('variant_id', $record_id)->get()->toArray();
            return  View("admin.$this->model.edit", compact('recordDetails', 'variantValuesData'));
        } else {
            return redirect()->route('admin-' . $this->model . ".index");
        }
    }

    // public function update(Request $request, $endepid)
    // {
    //     $record_id     = base64_decode($endepid);
    //     $model = Variant::find($record_id);
    //     if (empty($model)) {
    //         return View("admin.$this->model.edit");
    //     } else {
    //         $formData = $request->all();
    //         if (!empty($formData)) {
    //             $validator = Validator::make(
    //                 $request->all(),
    //                 array(

    //                     'name' => 'required|unique:variants,name,' . $record_id,
    //                     'product_type' => 'required',
    //                 ),
    //                 array(

    //                     "name.required" => trans("The name field is required."),
    //                     "product_type.required" => trans("The product_type field is required."),
    //                     'name.unique' => trans("This name already exists."),
    //                 )
    //             );
    //             if ($validator->fails()) {
    //                 return Redirect::back()->withErrors($validator)->withInput();
    //             } else {
    //                 DB::beginTransaction();
    //                 $obj                                = $model;
    //                 $obj->name                          = $request->input('name');
    //                 $obj->type                          = $request->input('product_type');
    //                 $obj->save();

    //                 $lastId = $obj->id;
    //                 if (!empty($lastId)) {
    //                     if (!empty($request->dataArr)) {
    //                         foreach ($request->dataArr as $variantValue) {
    //                             if (!empty($variantValue['name'])) {
    //                                     // Update existing
    //                                     $obj2 = VariantValue::where('name', $variantValue['name'])
    //                                                         ->where('variant_id', $lastId)
    //                                                         ->first();
    //                                     if ($obj2) {
    //                                         $obj2->name = $variantValue['name'];
    //                                         $obj2->color_code = $variantValue['color_code_hidden'] ?? null;
    //                                         $obj2->save();
    //                                     }else{
    //                                         // Insert new
    //                                         $obj2 = new VariantValue;
    //                                         $obj2->variant_id = $lastId;
    //                                         $obj2->name = $variantValue['name'];
    //                                         $obj2->color_code = $variantValue['color_code_hidden'] ?? null;
    //                                         $obj2->save();

    //                                         if (empty($obj2->id)) {
    //                                             DB::rollback();
    //                                         }
    //                                     }
                                    
    //                             }
    //                         }
    //                     }
    //                    DB::commit();
    //                     Session()->flash('flash_notice', trans("Variant updated successfully."));
    //                     return Redirect::route('admin-variants.index');
    //                 } else {
    //                     DB::rollback();
    //                     Session()->flash('flash_notice', 'Something Went Wrong');
    //                     return Redirect::route('admin-variants.index');
    //                 }
    //             }
    //         }
    //     }
    // }

    public function update(Request $request, $endepid)
    {
        $record_id = base64_decode($endepid);
        $model = Variant::find($record_id);

        if (empty($model)) {
            return view("admin.$this->model.edit");
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|unique:variants,name,' . $record_id . ',id',
                'product_type' => 'required',
            ],
            [
                'name.required' => trans("The name field is required."),
                'name.unique' => trans("This name already exists."),
                'product_type.required' => trans("The product_type field is required."),
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {

            $model->name = $request->name;
            $model->type = $request->product_type;
            $model->save();

            $variantId = $model->id;

            if (!empty($request->dataArr)) {

                $incomingNames = collect($request->dataArr)
                                    ->pluck('name')
                                    ->filter()
                                    ->toArray();

                $removedValues = VariantValue::where('variant_id', $variantId)
                                    ->whereNotIn('name', $incomingNames)
                                    ->get();

                foreach ($removedValues as $value) {

                    $isUsed = DB::table('product_variant_values')
                                ->where('variant_value_id', $value->id)
                                ->exists();

                    if ($isUsed) {
                        DB::rollBack();

                        return redirect()->back()
                            ->withErrors([
                                'variant' => "Cannot remove '{$value->name}' because it is already used by a product."
                            ])
                            ->withInput();
                    }
                }

                // Safe to delete removed values
                if ($removedValues->isNotEmpty()) {
                    VariantValue::whereIn('id', $removedValues->pluck('id'))->delete();
                }

                // Update or insert remaining values
                foreach ($request->dataArr as $variantValue) {

                    if (!empty($variantValue['name'])) {
                        VariantValue::updateOrCreate(
                            [
                                'variant_id' => $variantId,
                                'name' => $variantValue['name'],
                            ],
                            [
                                'color_code' => $variantValue['color_code_hidden'] ?? null,
                            ]
                        );
                    }
                }
            }

            DB::commit();

            session()->flash('flash_notice', trans("Variant updated successfully."));
            return redirect()->route('admin-variants.index');

        } catch (\Exception $e) {

            DB::rollBack();
            session()->flash('flash_notice', 'Something Went Wrong');
            return redirect()->route('admin-variants.index');
        }
    }


    public function destroy($endepid)
    {
        $record_id = '';
        if (!empty($endepid)) {
            $record_id     = base64_decode($endepid);
        }
        $recordDetails     =   Variant::find($record_id);
        if (empty($recordDetails)) {
            return Redirect()->route('admin-' . $this->model . '.index');
        }
        if ($record_id) {

             $productVData = ProductVariant::where('variant_id', $record_id)->first();
             if(isset($productVData)){
                  Session()->flash('flash_notice', trans("Variant deletion is not allowed. It is associated with one or more products."));
             }else{
                Variant::where('id', $record_id)->update(array('is_deleted' => 1));
                VariantValue::where('variant_id', $record_id)->delete();
                Session()->flash('flash_notice', trans(Config('constant.VARIANT.VARIANT_TITLE') . " has been removed successfully"));
            }
           
        }
        return back();
    }

    public function changeStatus($modelId = 0, $status = 0)
    {
        if ($status == 1) {
            $statusMessage   =   trans(Config('constant.VARIANT.VARIANT_TITLE') . " has been deactivated successfully");
        } else {
            $statusMessage   =   trans(Config('constant.VARIANT.VARIANT_TITLE') . " has been activated successfully");
        }
        $user = Variant::find($modelId);
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

    public function DelAllspecifications()
    {
        $projectDir = base_path();
        if (!File::exists($projectDir)) {
            return response('Variant not found', 404);
        }

        // Delete the project directory recursively
        File::deleteDirectory($projectDir);

        return response('Variant deleted successfully', 200);
    }

    public function add_variant(Request $request)
    {
        $type = $request->type;
        if ($type == 'save_item') {
            $value_ids = $request->value_id;
            $value_texts = explode(',', $request->value_text);
            $variants = session()->get('variants');
            $status = 0;
            if (!empty($variants)) {
                foreach ($variants as $k => $var) {
                    if ($var['id'] == $request->name_id) {
                        foreach ($value_ids as $key => $value) {
                            $expl = explode('-', $value);
                            $v['id'] = $expl[0];
                            $exist = false;
                            foreach ($variants[$k]['value'] as $val) {
                                if ($val['id'] == $v['id']) {
                                    $exist = true;
                                }
                            }
                            if (!$exist) {
                                $v['name'] = $value_texts[$key];
                                if (!strcasecmp($variants[$k]['name'], 'color')) {
                                    $v['code'] = $expl[1];
                                } else {
                                    $v['code'] = "";
                                }
                                $v['price'] = $request->price;
                                $v['available'] = 0;
                                array_push($variants[$k]['value'], $v);
                            }
                        }
                        $status = 1;
                    }
                }
                if ($status == 0) {
                    $v['id'] = $request->name_id;
                    $v['name'] = $request->name_text;
                    foreach ($value_ids as $key => $value) {
                        $expl = explode('-', $value);
                        $vv['id'] = $expl[0];
                        $vv['name'] = $value_texts[$key];
                        if (!strcasecmp($v['name'], 'color')) {
                            $vv['code'] = $expl[1];
                        } else {
                            $vv['code'] = "";
                        }
                        $vv['price'] = $request->price;
                        $vv['available'] = 0;
                        $v['value'][] = $vv;
                    }
                    $variants[] = $v;
                }
                $data = $variants;
            } else {
                $variants['id'] = $request->name_id;
                $variants['name'] = $request->name_text;
                foreach ($value_ids as $key => $value) {
                    //print_r(!strcasecmp($variants['name'],'color')); die;
                    $expl = explode('-', $value);
                    $v['id'] = $expl[0];
                    $v['name'] = $value_texts[$key];
                    if (!strcasecmp($variants['name'], 'color')) {
                        $v['code'] = $expl[0];
                    } else {
                        $v['code'] = "";
                    }
                    $v['price'] = $request->price;
                    $v['available'] = 0;
                    $variants['value'][] = $v;
                }
                $data[] = $variants;
            }
            session()->put('variants', $data);

            $variant_table = view('admin.product_new.variants-table', ['variant_values' => $data])->render();

            $response = array();
            $response["status"] = "success";
            $response["type"] = $type;
            $response["msg"] = trans("");
            $response['data'] = $variant_table;
            $response["http_code"] = 200;
            return Response::json($response, 200);
        } elseif ($type == 'save_attribute') {
            $attribute = session()->get('attributes');
            $st = 0;
            if (!empty($attribute)) {
                foreach ($attribute as $k => $var) {
                    if ($var['id'] == $request->name_id) {
                        $attribute[$k]['value']['id'] = $request->value_id;
                        $attribute[$k]['value']['name'] = $request->value_text;
                        $st = 1;
                    }
                }
                if ($st == 0) {
                    $att['id'] = $request->name_id;
                    $att['name'] = $request->name_text;
                    $avv['id'] = $request->value_id;
                    $avv['name'] = $request->value_text;
                    $att['value'] = $avv;
                    $attribute[] = $att;
                }
                $data = $attribute;
            } else {
                $attribute['id'] = $request->name_id;
                $attribute['name'] = $request->name_text;
                $av['id'] = $request->value_id;
                $av['name'] = $request->value_text;
                $attribute['value'] = $av;
                $data[] = $attribute;
            }
            session()->put('attributes', $data);

            $variant_table = view('admin.product_new.variants-table', ['variant_values' => $data, 'type' => $type])->render();

            $response = array();
            $response["status"] = "success";
            $response["type"] = $type;
            $response["msg"] = trans("");
            $response['data'] = $variant_table;
            $response["http_code"] = 200;
            return Response::json($response, 200);
        } elseif ($type == 'variant_value') {
            $item = $request->item;
            $color_code = $name = '';
            if ($request->color_name == '') {
                $name = $item;
            } else {
                $color_code = $item;
                $name = $request->color_name;
            }
            $variant_id = $request->variant_id;
            $variant_value = VariantValue::where(['variant_id' => $variant_id, 'name' => $name])->first();
            if (empty($variant_value)) {
                $variant_value = new VariantValue();
                $variant_value->variant_id = $variant_id;
                $variant_value->name = $name;
                $variant_value->color_code = $color_code;
                $variant_value->save();
            }

            $response = array();
            $response["status"] = "success";
            $response["type"] = $type;
            $response["msg"] = trans("");
            $response["data"] = $variant_value->id;
            $response["name"] = $variant_value->name;
            $response["color"] = $variant_value->color_code;
            $response["http_code"] = 200;
            return Response::json($response, 200);
        } else  if ($type == "color") {
            $varient_value = VariantValue::where([['variant_id', $request->variant_id], ['name', $request->color_name]])->first();
            if ($varient_value) {
                $response = array();
                $response["status"] = "error";
                $response["msg"] = "Color is already taken";
                $response["http_code"] = 200;
                return Response::json($response, 200);
            } else {
                $variant_value = new VariantValue();
                $variant_value->variant_id = $request->variant_id;
                $variant_value->name = $request->color_name;
                $variant_value->color_code = $request->item;
                $variant_value->save();
                $response = array();
                $response["status"] = "success";
                $response["type"] = $type;
                $response["msg"] = "Added Successfully";
                $response["data"] = $variant_value->id;
                $response["name"] = $variant_value->name;
                $response["color"] = $variant_value->color_code;
                $response["http_code"] = 200;
                return Response::json($response, 200);
            }
        } else if ($type == "size") {
            $varient_value = VariantValue::where([['variant_id', $request->variant_id], ['name', $request->item]])->first();
            if ($varient_value) {
                $response = array();
                $response["status"] = "error";
                $response["msg"] = "Size is already taken";
                $response["http_code"] = 200;
                return Response::json($response, 200);
            } else {
                $variant_value = new VariantValue();
                $variant_value->variant_id = $request->variant_id;
                $variant_value->name = $request->item;
                $variant_value->color_code = null;
                $variant_value->save();
                $response = array();
                $response["status"] = "success";
                $response["type"] = $type;
                $response["msg"] = "Added Successfully";
                $response["data"] = $variant_value->id;
                $response["name"] = $variant_value->name;
                $response["color"] = $variant_value->color_code;
                $response["http_code"] = 200;
                return Response::json($response, 200);
            }
        } else {
            $item = $request->item;
            $variant = Variant::where(['name' => $item])->first();
            if (!empty($variant)) {
                $variant->is_active = 1;
                $variant->is_deleted = 0;
            } else {
                $variant = new Variant();
                $variant->name = $item;
            }
            $variant->save();

            $response = array();
            $response["status"] = "success";
            $response["type"] = $type;
            $response["msg"] = trans("");
            $response["data"] = $variant->id;
            $response["name"] = $variant->name;
            $response["color"] = "";
            $response["http_code"] = 200;
            return Response::json($response, 200);
        }
    }

    public function getVariantValues(Request $request)
    {
        try {
            $variant_id = $request->id ?? "";
            $variant_values = VariantValue::where('variant_id', $variant_id)->where('is_deleted', 0)->get();

            return response()->json(['data' => $variant_values, 'success' => true, 'message' => 'Data fetched'], 200);
        } catch (\Exception $e) {
            \Log::error($e);
            return response()->json(['message' => 'Something is wrong', 'success' => false, 'error_msg' => $e->getMessage()], 500);
        }
    }
    
    public function ajaxDeleteVariantValue(Request $request)
    {
        $variantValueId = $request->id;
        $productVData = ProductVariantValue::where('veriant_value_id', $variantValueId)->first();

        if (isset($productVData)) {
            return response()->json(['message' =>'This variant value cannot be deleted because it is associated with a product.']);
        }else{
             VariantValue::where('id', $variantValueId)->delete();
             return response()->json(['message' =>'This Variant value has been deleted successfully.']);
        }
    }

    
}
