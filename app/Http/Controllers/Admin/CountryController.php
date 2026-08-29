<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Helpers\FileUploadHelper;
use Yajra\DataTables\DataTables;
use App\Http\Requests\Country\CountryRequest;

class CountryController extends Controller
{
    ////////////////////Show Country/////////////
    public function index(Request $request)
    {
        //   dd($data);
        if ($request->ajax()) {
            $data = Country::select([
              
                'id', 'name', 'sortname', 'code', 'country_flag',
                'country_time_zone', 'is_active', 'currency_symbol', 'currency_amount', 'created_at'
            ])->orderBy('is_active', 'desc')->orderBy('name', 'asc');

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
                ->editColumn('country_flag', function ($row) {
                    return $row->country_flag
                        ? '<img src="' . asset($row->country_flag) . '" width="40" height="30">'
                        : '';
                })
                ->editColumn('is_active', function ($row) {
                    return $row->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    $id = base64_encode($row->id);
    
                    $statusBtn = $row->is_active
                        ? "<button class='btn btn-sm btn-danger status-toggle' data-id='{$row->id}' data-status='0' title='Deactivate'><i class='ri-close-line'></i></button>"
                        : "<button class='btn btn-sm btn-success status-toggle' data-id='{$row->id}' data-status='1' title='Activate'><i class='ri-check-line'></i></button>";
    
                    $editBtn = "<a href='" . route("admin-country.edit", $id) . "' class='btn btn-sm btn-info'><i class='ri-edit-line'></i></a>";
    
                    $deleteBtn = "<form method='POST' action='" . route("admin-country.destroy", $id) . "' style='display:inline-block;' onsubmit='return confirm(\"Are you sure?\")'>"
                        . csrf_field()
                        . method_field('DELETE')
                        . "<button type='submit' class='btn btn-sm btn-danger'><i class='ri-delete-bin-5-line'></i></button></form>";
                        $state = ' <div class="dropdown dropdown-inline"><a href="javascript:;" class="btn btn-light" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-list-check"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                        <ul class="nav nav-hoverable flex-column">
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="' . route("admin-state.index",['endesid'=>$id]) . '">
                                    <span class="nav-text">State</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>';
    
                    return "<div class='hstack gap-1 flex-wrap'>{$statusBtn}{$editBtn}{$deleteBtn}{$state}</div>";
                })
                
                ->rawColumns(['action', 'country_flag', 'is_active'])
                ->make(true);
        }
    
        // For non-AJAX: maintain search state
        $searchVariable = [
            'is_active' => $request->is_active,
            'date_from' => $request->date_from,
            'date_to'   => $request->date_to,
        ];
    
        return view("admin.country.index", compact('searchVariable'));
    }

/////////////Create Country/////////
public function create()
{
    return view('admin.country.add');
}

///////////////Store Country///////////
public function store(CountryRequest $request)
    {
        // dd($request->all());
        return $this->saveOrUpdateCountry($request);
    }

    //////////edit Country///////
    public function edit($id)
    {
        $country = Country::findOrFail(base64_decode($id));
        // dd($country);
        return view('admin.country.edit', compact('country'));
    }

///////////update Country///////////////

    public function update(CountryRequest $request, $encodedId)
    {
        $id = base64_decode($encodedId);
        return $this->saveOrUpdateCountry($request, $id);
    }


    //////////////////////Common Function Add And Edit//////////////////
    private function saveOrUpdateCountry(CountryRequest $request, $id = null)
    {
        
        DB::beginTransaction();
        // try {
            $data = $request->only([
                'name', 'code', 'sortname', 'country_time_zone', 'currency_symbol', 'currency_amount', 'country_flag'
            ]);
            if ($request->hasFile('country_flag')) {
                $data['country_flag'] = FileUploadHelper::uploadToFrontendOnly(
                    $request->file('country_flag'),
                    'country_flag',
                    config('constant.Country_IMAGE_ROOT_PATH')
                );
            }

            if ($id) {
                $country = Country::findOrFail($id);
                $country->update($data);
                $message = 'Country updated successfully!';
            } else {
                Country::create($data);
                $message = 'Country created successfully!';
            }

            DB::commit();
            return redirect()->route('admin-country.index')->with('success', $message);

        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return redirect()->back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        // }
    }

    ////////////////////Delete Country////////////////
    public function destroy($id)
    {
        $country = Country::findOrFail(base64_decode($id));
        $country->delete();
        return redirect()->back()->with('success', 'Country deleted successfully.');
    }

  ////////////////////Change Status Active And Deactive//////////////
    public function status($id, $status)
    {
        Country::where('id', $id)->update(['is_active' => $status]);
        return redirect()->back()->with('success', 'Status updated.');
    }
}