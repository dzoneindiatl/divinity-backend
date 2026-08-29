<?php

namespace App\Http\Controllers\Admin;

use App\Config;
use Carbon\Carbon;
use App\Models\City;
use Illuminate\Http\Request;
use Redirect,DB,Response,Str;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Helpers\FileUploadHelper;
use Yajra\DataTables\DataTables;
use App\Http\Requests\City\CityRequest;
use App\Models\State;

class CityController extends Controller
{
    ////////////////////Show State/////////////
    public function index(Request $request)
    {
       
    
        
        if ($request->ajax()) {
            $decodedId = base64_decode($request->endesid);
            $data = City::with('state')->select([
                'id', 'state_id', 'postal_code', 'name', 'std_code','short_name',
                'is_active', 'created_at'
            ])->where('state_id',$decodedId);
       
    
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
                })->addColumn('state_name', function ($row) {
                    return $row->state->name ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $id = base64_encode($row->id);
    
                    $statusBtn = $row->is_active
                        ? "<button class='btn btn-sm btn-danger status-toggle' data-id='{$row->id}' data-status='0' title='Deactivate'><i class='ri-close-line'></i></button>"
                        : "<button class='btn btn-sm btn-success status-toggle' data-id='{$row->id}' data-status='1' title='Activate'><i class='ri-check-line'></i></button>";
    
                    $editBtn = "<a href='" . route("admin-city.edit", $id) . "' class='btn btn-sm btn-info'><i class='ri-edit-line'></i></a>";
    
                    $deleteBtn = "<form method='POST' action='" . route("admin-city.destroy", $id) . "' style='display:inline-block;' onsubmit='return confirm(\"Are you sure?\")'>"
                        . csrf_field()
                        . method_field('DELETE')
                        . "<button type='submit' class='btn btn-sm btn-danger'><i class='ri-delete-bin-5-line'></i></button></form>";
                              
    
                    return "<div class='hstack gap-1 flex-wrap'>{$statusBtn}{$editBtn}{$deleteBtn}</div>";
                })
                ->rawColumns(['action', 'is_active'])
                ->make(true);
        }
    
        $searchVariable = [
            'is_active' => $request->is_active,
            'date_from' => $request->date_from,
            'date_to'   => $request->date_to,
            'state_id' => $request->state_id,
        ];
    
        return view("admin.city.index", compact('searchVariable'));
    }
    

/////////////Create city/////////
public function create(Request $request)
{     
    $endesid =  base64_decode($request->endesid);
// dd($endesid );
   

    // $states = State::where('id', $endesid)->get();
    $states = State::all();
    
    return view("admin.city.add", compact('states','endesid'));

     
}

///////////////Store city///////////
public function store(CityRequest $request)
    {
        return $this->saveOrUpdateState($request);
    }

    //////////edit State///////
    public function edit($id)
    {
        $city = City::findOrFail(base64_decode($id));
        if(empty($city)) {
            return redirect()->back();
        }
        $states = State:: where('country_id', $city->country_id)->get();

        return view('admin.city.edit', compact('city','states'));
    }

///////////update City///////////////

    public function update(CityRequest $request, $encodedId)
    {
        $id = base64_decode($encodedId);
        return $this->saveOrUpdateState($request, $id);
    }


    //////////////////////Common Function Add And Edit//////////////////
    private function saveOrUpdateState(CityRequest $request, $id = null,)
    {
        // dd($id);
        DB::beginTransaction();
        // try {
            $data = $request->only([
               'state_id', 'postal_code', 'name', 'std_code','short_name',
            ]);

            if ($id) {
                $city = City::findOrFail($id);
                $city->update($data);
                $message = 'City updated successfully!';
            } else {
                City::create($data);
                $message = 'City created successfully!';
            }



            DB::commit();

             $endesid = base64_encode($data['state_id']);

             return redirect()->route('admin-city.index', ['endesid' => $endesid])->with('success', $message);
            

        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return redirect()->back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        // }
    }

    ////////////////////Delete City////////////////
    public function destroy($id)
    {
        $city = City::findOrFail(base64_decode($id));
        $city->delete();
        return redirect()->back()->with('success', 'City deleted successfully.');
    }

  ////////////////////Change Status Active And Deactive//////////////
    public function status($id, $status)
    {
        City::where('id', $id)->update(['is_active' => $status]);
        return redirect()->back()->with('success', 'City updated.');
    }
}