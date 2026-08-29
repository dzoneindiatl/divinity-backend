<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Helpers\FileUploadHelper;
use Yajra\DataTables\DataTables;
use App\Http\Requests\State\StateRequest;

class StateController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('permission:view_state|create_state|edit_state|delete_state', ['only' => ['index', 'show']]);
        $this->middleware('permission:create_state', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_state', ['only' => ['edit', 'update', 'status']]);
        $this->middleware('permission:delete_state', ['only' => ['destroy']]);
    }
    ////////////////////Show State/////////////
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $decodedId = base64_decode($request->endesid);
            $data = State::with('country')->select([
                'id', 'country_id', 'shortname', 'code', 'name',
                'is_active', 'created_at'
            ])->where('country_id',$decodedId);

            // Filter: country_id
            if ($request->filled('country_id')) {

                $decodedId = base64_decode($request->country_id);
                $data->where('country_id', $decodedId);
            }
    
            // Filter: Status
            if ($request->filled('is_active')) {
                $data->where('is_active', $request->is_active);
            }
    
            // Filter: Date Range
            if ($request->filled('date_from')) {
                $data->whereDate('created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
            }
    
            if ($request->filled('date_to')) {
                $data->whereDate('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
            }
    
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('is_active', function ($row) {
                    return $row->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })->addColumn('country_name', function ($row) {
                    return $row->country->name ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $id = base64_encode($row->id);
    
                    $statusBtn = $row->is_active
                        ? "<button class='btn btn-sm btn-danger status-toggle' data-id='{$row->id}' data-status='0' title='Deactivate'><i class='ri-close-line'></i></button>"
                        : "<button class='btn btn-sm btn-success status-toggle' data-id='{$row->id}' data-status='1' title='Activate'><i class='ri-check-line'></i></button>";
    
                    $editBtn = "<a href='" . route("admin-state.edit", $id) . "' class='btn btn-sm btn-info'><i class='ri-edit-line'></i></a>";
    
                    $deleteBtn = "<form method='POST' action='" . route("admin-state.destroy", $id) . "' style='display:inline-block;' onsubmit='return confirm(\"Are you sure?\")'>"
                        . csrf_field()
                        . method_field('DELETE')
                        . "<button type='submit' class='btn btn-sm btn-danger'><i class='ri-delete-bin-5-line'></i></button></form>";
    
                    $state = '<div class="dropdown dropdown-inline">
                                <a href="javascript:;" class="btn btn-light" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ri-list-check"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <ul class="nav nav-hoverable flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="' . route("admin-city.index",['endesid'=>$id]) . '">
                                                <span class="nav-text">City</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                              </div>';
                              
    
                    return "<div class='hstack gap-1 flex-wrap'>{$statusBtn}{$editBtn}{$deleteBtn}{$state}</div>";
                })
                ->rawColumns(['action', 'is_active'])
                ->make(true);
        }
    
        $searchVariable = [
            'is_active' => $request->is_active,
            'date_from' => $request->date_from,
            'date_to'   => $request->date_to,
            'country_id' => $request->country_id,
        ];
    


        return view("admin.state.index", compact('searchVariable'));
    }
    

    /////////////Create State/////////
    public function create(Request $request)
    {
        //  $endesid = $request->endesid;
        $endesid =  base64_decode($request->endesid);

        $countries = Country::all();

        return view('admin.state.add',compact('endesid','countries'));
    }

    ///////////////Store State///////////
    public function store(StateRequest $request)
    {
        // dd($request->all());
        return $this->saveOrUpdateState($request);
    }

    //////////edit State///////
    public function edit($id)
    {   
        
         $state = State::findOrFail(base64_decode($id));
         $countries = Country::all();
    
        return view('admin.state.edit', compact('state','countries'));
    }

    ///////////update State///////////////

    public function update(StateRequest $request)
    {
        $id = $request->id ? base64_decode($request->id):0;
        return $this->saveOrUpdateState($request, $id);
    }


    //////////////////////Common Function Add And Edit//////////////////
    private function saveOrUpdateState(StateRequest $request, $id = null)
    {  
        DB::beginTransaction();
        try {
            $data = $request->only([
                'country_id', 'shortname', 'code', 'name', 'is_free_shipping', 'free_shipping_min_cart_amount',
            ]);
            
            // Handle weight ranges data
            if ($request->has('weight_ranges') && is_array($request->weight_ranges)) {
                // Filter out empty weight ranges
                $weightRanges = array_filter($request->weight_ranges, function($range) {
                    return !empty($range['weight_from']) && !empty($range['weight_to']) && !empty($range['delivery_charge']);
                });
                // Convert to the format you specified
                $data['weight_ranges'] = array_values($weightRanges);
            }
            
            if ($id) {
                $state = State::findOrFail($id);
                $state->update($data);
                $message = 'State updated successfully!';
            } else {
                State::create($data);
                $message = 'State created successfully!';
            }

            DB::commit();

            $endesid = base64_encode($data['country_id']);

            return redirect()->route('admin-state.index', ['endesid' => $endesid])->with('success', $message);
        
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    ////////////////////Delete State////////////////
    public function destroy($id)
    {
        $state = State::findOrFail(base64_decode($id));
        $state->delete();
        return redirect()->back()->with('success', 'State deleted successfully.');
    }

  ////////////////////Change Status Active And Deactive//////////////
    public function status($id, $status)
    {
        State::where('id', $id)->update(['is_active' => $status]);
        return redirect()->back()->with('success', 'Status updated.');
    }
}