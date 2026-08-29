<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\ShippingZone;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\ShippingWeight;
use App\Models\ShippingCharges;
use App\Models\Country;
use App\Models\State;
use App\Models\City; 

use Illuminate\Support\Facades\File;
use Redirect,DB,Response;

class ShippingChargesController extends Controller
{
    public $model = 'shipping-charges';
    public function __construct(Request $request)
    {
        $this->listRouteName = 'admin-shipping-charges.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;

    }

    public function index(Request $request)
    {
        
        try {
            $DB = ShippingCharges::query();
            $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'shipping_charges.created_at';
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
                    $DB->whereBetween('shipping_charges.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
                } elseif (!empty($searchData['date_from'])) {
                    $dateS = $searchData['date_from'];
                    $DB->where('shipping_charges.created_at', '>=', [$dateS . " 00:00:00"]);
                } elseif (!empty($searchData['date_to'])) {
                    $dateE = $searchData['date_to'];
                    $DB->where('shipping_charges.created_at', '<=', [$dateE . " 00:00:00"]);
                }
                foreach ($searchData as $fieldName => $fieldValue) {
                    if ($fieldValue != "") {
                        if ($fieldName == "name") {
                            $DB->where("shipping_charges.name", 'like', '%' . $fieldValue . '%');
                        }
                        if ($fieldName == "is_active") {
                            $DB->where("shipping_charges.is_active", $fieldValue);
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
            $countries = Country::select('id', 'name')->get();
            $states = State::select('id', 'name')->get();
            // $countries = Country::select('id', 'name')->where('is_active', '1')->get();
            // $states = State::select('id', 'name')->where('is_active', '1')->get();
            // $shippingzones = ShippingZone::select('id', 'name')->where('is_active', '1')->where('is_deleted', '0')->get();
            $shippingzones = ShippingZone::select('id', 'name')->where('is_active', '1')->where('is_deleted', '0')->whereIn('id', function ($query) {
                                    $query->select('shipping_zone_id')
                                        ->from('shipping_weight')
                                        ->whereNotNull('shipping_zone_id');  // Ensure only zones with a valid shipping_weight are included
                                    })
                                ->get();
            // $cities = City::select('id', 'name')->where('is_active', '1')->get();
            $cities = City::select('id', 'name')->get();
            return view('admin.shipping-charges.create', compact('countries','states','cities','shippingzones'));
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }
//     public function store(Request $request)
// {
//     try {
//         // Debug the incoming request data
//      //   Log::debug('Request Data:', $request->all());
        
//         // Validate the form data
//         $validator = Validator::make(
//             $request->all(),
//             [
//                 'country_id' => 'required|exists:countries,id',
//                 'shipping_zone_id' => 'required|exists:shipping_zone,id',
//                 'state_id' => 'required|array', // States should be an array
//                 'city_id' => 'required', // Allow both array and string (e.g., 'all')
//                 'shipping_method' => 'required|in:free,flat', // Either free or flat method
//             ],
//             [
//                 'country_id.required' => trans('The country field is required.'),
//                 'shipping_zone_id.required' => trans('The zone field is required.'),
//                 'state_id.required' => trans('The state field is required.'),
//                 'city_id.required' => trans('The city field is required.'),
//                 'shipping_method.required' => trans('The shipping method field is required.'),
//             ]
//         );

//         // Check if validation fails
//         // if ($validator->fails()) {
//         //     Log::debug('Validation Errors:', $validator->errors()->all());
//         //     return Redirect::back()->withErrors($validator)->withInput();
//         // }

//         // Begin transaction to save data
//         DB::beginTransaction();
        
//         // Create ShippingCharges record
//         $shippingCharge = new ShippingCharges();
//         $shippingCharge->country_id = $request->input('country_id');
//         $shippingCharge->shipping_zone_id = $request->input('shipping_zone_id');
//         $shippingCharge->shipping_method = $request->input('shipping_method');
//         $shippingCharge->city_id = $request->input('city_id');
//         $shippingCharge->pincode_not_delivery = $request->input('pincode_not_delivery', null); // Optional field
    
//         // Handle the state_id input:
//         $stateIds = $request->input('state_id');
//         if ($stateIds) {
//             if (in_array('all', $stateIds)) {
//                 $stateIds = []; // Or handle as per your requirement
//             }
//             $shippingCharge->state_id = implode(',', $stateIds); // Save as comma-separated values
//         }

//         $shippingCharge->save();
        
//         // Commit the transaction
//         DB::commit();

//         return redirect()->route('admin-shipping-charges.index')->with('success', 'Shipping Charges created successfully');
//     } catch (Exception $e) {
//         Log::error('Error saving shipping charges: ' . $e->getMessage());
//         return redirect()->back()->with(['error' => 'Something went wrong', 'error_msg' => $e->getMessage()]);
//     }
// }


public function store(Request $request)
{
    try {
        // Validate the form data
        $validator = Validator::make(
            $request->all(),
            [
                'country_id' => 'required|exists:countries,id',
                'shipping_zone_id' => 'required|exists:shipping_zone,id',
                'state_id' => 'required|array', // States should be an array
                'city_id' => 'required|array', // Allow both array and string (e.g., 'all')
                'shipping_method' => 'required|in:free,flat', // Either free or flat method
            ],
            [
                'country_id.required' => trans('The country field is required.'),
                'shipping_zone_id.required' => trans('The zone field is required.'),
                'state_id.required' => trans('The state field is required.'),
                'city_id.required' => trans('The city field is required.'),
                'shipping_method.required' => trans('The shipping method field is required.'),
            ]
        );

        // Check if validation fails
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        // Begin transaction to save data
        DB::beginTransaction();
        // dd($request->all());
        // Retrieve the state_ids and shipping_method from the request
        $stateIds = $request->input('state_id');
        if ($stateIds) {
            if (in_array('all', $stateIds)) {
                $stateIds = []; // Handle as per your requirement
            }
            $stateIdsString = implode(',', $stateIds); // Save as comma-separated values
        }

        $cityIds = $request->input('city_id');
        if ($cityIds) {
            if (in_array('all', $cityIds)) {
                // $cityIds = [];
                $cityIds = 'all';
                $cityIdsString = $cityIds;
            }else{
                $cityIdsString = implode(',', $cityIds);
            }
            
        }
        // Get the selected shipping method (free or flat)
        $shippingMethod = $request->input('shipping_method');

        // Deactivate the opposite shipping method for the same country, zone, and state
        $oppositeShippingMethod = ($shippingMethod == 'free') ? 'flat' : 'free';

        // Check if opposite method already exists and deactivate it
        $existingOppositeMethod = ShippingCharges::where('country_id', $request->input('country_id'))
                                    ->where('shipping_zone_id', $request->input('shipping_zone_id'))
                                    ->where(function ($query) use ($stateIds) {
                                        // Loop through the state_ids and check if they exist in the comma-separated string
                                        foreach ($stateIds as $stateId) {
                                            $query->orWhere('state_id', 'like', '%'.$stateId.'%'); // This will match comma-separated values
                                        }
                                    })
                                    ->where('shipping_method', $oppositeShippingMethod)
                                    ->where('is_active', 1)  // Check if it's active
                                    ->first();  // Get the first record that matches

        // If opposite method exists and is active, deactivate it
        if ($existingOppositeMethod) {
            $existingOppositeMethod->update(['is_active' => 0]); // Set is_active to 0
        }

        // Create the new shipping charge record for the selected shipping method
        $shippingCharge = new ShippingCharges();
        $shippingCharge->country_id = $request->input('country_id');
        $shippingCharge->shipping_zone_id = $request->input('shipping_zone_id');
        $shippingCharge->shipping_method = $shippingMethod; // Insert the current shipping method (free or flat)
        // $shippingCharge->city_id = $request->input('city_id');
        $shippingCharge->city_id = $cityIdsString;
        $shippingCharge->pincode_not_delivery = $request->input('pincode_not_delivery', null); // Optional field
        $shippingCharge->state_id = $stateIdsString; // Save as comma-separated values

        // Set this new entry as active
        $shippingCharge->is_active = 1; // Set the new entry as active

        // Save the new shipping charge record
        $shippingCharge->save();

        // Commit the transaction
        DB::commit();

        return redirect()->route('admin-shipping-charges.index')->with('success', 'Shipping Charges created successfully');
    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Error saving shipping charges: ' . $e->getMessage());
        return redirect()->back()->with(['error' => 'Something went wrong', 'error_msg' => $e->getMessage()]);
    }
}



    

    public function edit(Request $request, $token = null)
    {
        try {
            $categoryId = '';
            if (!empty($token)) {

                $categoryId = base64_decode($token);

                $shipping_charges = ShippingCharges::find($categoryId);

                return View("admin.$this->model.edit", compact('shipping_charges'));
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

            $shipping_charges = ShippingCharges::find($categoryId);
            if (empty($shipping_charges)) {
                return View("admin.$this->model.edit");
            } else {
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
                        $obj                                = $shipping_charges;
                        $obj->name                          = $request->input('name');
                        $obj->save();
                        $lastId = $obj->id;
                        if(!empty($lastId)){
                            DB::commit();
                        }else{
                            DB::rollback();
                            Session()->flash('flash_notice', 'Something Went Wrong');
                            return Redirect::route('admin-shipping-charges.index');
                        }
                        Session()->flash('flash_notice', trans("Shipping Charges updated successfully."));
                        return Redirect::route('admin-shipping-charges.index');
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
            $category = ShippingCharges::find($categoryId);
            if (empty($category)) {
                return Redirect()->route($this->model . '.index');
            }
            if ($category) {
                ShippingCharges::where('id', $categoryId)->delete();
               // ShippingWeight::where('shipping_zone_id',$categoryId)->delete();

                Session()->flash('flash_notice', trans("Shipping Charges has been removed successfully."));
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
            $statusMessage = trans("Shipping Charges has been actvated successfully");
        } else {
            $statusMessage = trans("Shipping Charges has been deactivated successfully");
        }
        $category = ShippingCharges::find($modelId);
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


    // public function getStateByCountry($country_id)
    // {
    //     // Country ke id ke basis par zones ko fetch karna
    //     $states = State::where('country_id', $country_id)->get();

    //     // Zones ko response ke form mein return karna
    //     return response()->json($states);
    // }

    
    public function getStateByCountry($country_id)
    {
        $states = State::where('country_id', $country_id)->get();
    
        // Build the query for shipping charges with EXISTS subquery and is_active condition
        // $query = ShippingCharges::whereExists(function($query) use ($country_id) {
        //     // This is the EXISTS subquery
        //     $query->select(DB::raw(1))  // Select 1 to check for existence, no need to fetch any columns
        //           ->from('shipping_zone')  // Specify the table
        //           ->whereColumn('shipping_charges.shipping_zone_id', 'shipping_zone.id')  // Matching condition
        //           ->where('shipping_charges.country_id', $country_id);  // Filter by country_id
        // })
        // ->where('is_active', 1);  // Ensure that is_active is 1

        $existingStates = ShippingCharges::join('shipping_zone', 'shipping_charges.shipping_zone_id', '=', 'shipping_zone.id')
            // ->where('shipping_charges.country_id', $country_id)->pluck('shipping_charges.state_id')->toArray();
            ->where('shipping_charges.country_id', $country_id)->where('shipping_charges.is_active', 1)->pluck('shipping_charges.state_id')->toArray();

        // Log the SQL query (before execution)
        // $sql = $query->toSql();
        // $bindings = $query->getBindings();  // Get the query bindings (parameter values)
        // $fullSql = vsprintf(str_replace('?', '%s', $sql), $bindings);  // Replace '?' with actual values

        // // Output the SQL query for debugging
        // dd($fullSql);  // This will show the final SQL query

        // Now execute the query to get the state_ids
        //$existingStates = $query->pluck('state_id')->toArray();  // Pluck the state_ids that are linked to active zones

        // If state_id is stored as a comma-separated string, convert it into an array
        $existingStates = array_map(function($state) {
            return explode(',', $state);  // Split comma-separated state_ids into an array
        }, $existingStates);

        // Flatten the array of arrays into a single array
        $existingStates = array_merge(...$existingStates);

        // Convert all values to integers (to ensure comparison works correctly)
        $existingStates = array_map('intval', $existingStates);

        // Return both the states and existingStates as JSON
        return response()->json([
            'states' => $states,  // List of all states for the country
            'existingStates' => $existingStates  // List of states that are linked to active shipping zones
        ]);
    }

    
    
    public function getCitiesByStates(Request $request)
    {
        // Multiple states ko fetch karna
        $state_ids = $request->state_ids;

        // Selected states ke basis par cities fetch karna
        $cities = City::whereIn('state_id', $state_ids)->get();

        // Cities ko JSON response mein return karna
        return response()->json($cities);
    }

    public function getweightAmountListZoneWise(Request $request)
    {
        $shipping_zone_id = $request->shipping_zone_id; 

        // Fetch the shipping amount list based on zone and method
        $amountList = ShippingWeight::where('shipping_zone_id', $shipping_zone_id)->where('is_active', '1')->where('is_deleted', '0')->get();

        return response()->json($amountList);
    }

}
