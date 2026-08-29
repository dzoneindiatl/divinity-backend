<?php

namespace App\Http\Controllers\Admin;

use App\Config;
use Carbon\Carbon;
use App\Models\Country;
use App\Models\Couriers;
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
use App\Http\Requests\Country\CountryRequest;
use App\Http\Requests\Couriers\CouriersRequest;


class CouriersController extends Controller
{
    ////////////////////Show Country/////////////
    public function index(Request $request)
    {
       
        if ($request->ajax()) {
            $data = Couriers::select([
              
                'id', 'name', 'tracking_url','slug','status', 'created_at'
            ]);

            if ($request->filled('status')) {
                $data->where('status', $request->status);
            }
    

            if ($request->filled('date_from')) {
                $data->whereDate('created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
            }
    
            if ($request->filled('date_to')) {
                $data->whereDate('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
            }
    
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    return $row->status
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                
                  ->addColumn('action', function ($row) {
                    $id = base64_encode($row->id);
    
                    $statusBtn = $row->status
                        ? "<button class='btn btn-sm btn-danger status-toggle' data-id='{$row->id}' data-status='0' title='Deactivate'><i class='ri-close-line'></i></button>"
                        : "<button class='btn btn-sm btn-success status-toggle' data-id='{$row->id}' data-status='1' title='Activate'><i class='ri-check-line'></i></button>";
    
                    $editBtn = "<a href='" . route("admin-couriers.edit", $id) . "' class='btn btn-sm btn-info'><i class='ri-edit-line'></i></a>";
    
                    $deleteBtn = "<form method='POST' action='" . route("admin-couriers.destroy", $id) . "' style='display:inline-block;' onsubmit='return confirm(\"Are you sure?\")'>"
                        . csrf_field()
                        . method_field('DELETE')
                        . "<button type='submit' class='btn btn-sm btn-danger'><i class='ri-delete-bin-5-line'></i></button></form>";
                        
    
                    return "<div class='hstack gap-1 flex-wrap'>{$statusBtn}{$editBtn}{$deleteBtn}</div>";
                })
                
                ->rawColumns(['action', 'country_flag', 'status'])
                ->make(true);
        }

        $searchVariable = [
            'status' => $request->status,
            'date_from' => $request->date_from,
            'date_to'   => $request->date_to,
        ];
    
        return view("admin.couriers.index", compact('searchVariable'));
    }


    
public function create()
{
    return view('admin.couriers.add');
}

///////////////Store Country///////////
public function store(CouriersRequest $request)
    {
        // dd($request->all());
        return $this->saveOrUpdateCountry($request);
    }

    //////////edit Country///////
    public function edit($id)
    {

        $country = Couriers::findOrFail(base64_decode($id));
        // dd($country);
        return view('admin.couriers.edit', compact('country'));
    }

///////////update Country///////////////

    public function update(CouriersRequest $request, $encodedId)
    {  
        
        $id = base64_decode($encodedId);
        return $this->saveOrUpdateCountry($request, $id);
    }


    //////////////////////Common Function Add And Edit//////////////////
    private function saveOrUpdateCountry(CouriersRequest $request, $id = null)
    {    
        DB::beginTransaction();
        // try {
           $data = $request->only(['name', 'tracking_url']);

           $data['slug'] = $this->generateUniqueSlug($request->name, $id);

            if ($id) {
                $courier = Couriers::findOrFail($id);
                $courier->update($data);
                $message = 'Courier updated successfully!';
            } else {
                Couriers::create($data);
                $message = 'Courier created successfully!';
            }

            DB::commit();
            return redirect()->route('admin-couriers.index')->with('success', $message);

        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return redirect()->back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        // }
    }

    ////////////////////Delete Country////////////////
    public function destroy($id)
    {
        $country = Couriers::findOrFail(base64_decode($id));
        $country->delete();
        return redirect()->back()->with('success', 'Country deleted successfully.');
    }

  ////////////////////Change Status Active And Deactive//////////////
    public function status($id, $status)
    {   
         
        Couriers::where('id', $id)->update(['status' => $status]);

        return redirect()->back()->with('success', 'Status updated.');
    }


    private function generateUniqueSlug($name, $id = null)
{
    $slug = Str::slug($name);
    $original = $slug;
    $count = 1;

    // Keep incrementing until slug is unique
    while (Couriers::where('slug', $slug)
        ->when($id, fn($q) => $q->where('id', '!=', $id))
        ->exists()) {
        $slug = $original . '-' . $count++;
    }

    return $slug;
}
}