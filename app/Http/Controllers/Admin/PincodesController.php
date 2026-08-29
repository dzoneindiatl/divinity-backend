<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Pincodes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Http\Requests\Pincodes\PincodesRequest;



class PincodesController extends Controller
{
    ////////////////////Show Country/////////////
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pincodes::with('country', 'state', 'city');

            // Apply search filters
            if ($request->filled('status')) {
                $data->where('status', $request->status);
            }

            if ($request->filled('pincode')) {
                $data->where('pincode', 'LIKE', '%' . $request->pincode . '%');
            }

            if ($request->filled('date_from')) {
                $data->whereDate('created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
            }

            if ($request->filled('date_to')) {
                $data->whereDate('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
            }

            // Handle global search
            if ($request->filled('global_search')) {
                $searchValue = $request->input('global_search');
                $data->where(function($query) use ($searchValue) {
                    $query->where('pincode', 'LIKE', '%' . $searchValue . '%')
                          ->orWhereHas('country', function($q) use ($searchValue) {
                              $q->where('name', 'LIKE', '%' . $searchValue . '%');
                          })
                          ->orWhereHas('state', function($q) use ($searchValue) {
                              $q->where('name', 'LIKE', '%' . $searchValue . '%');
                          })
                          ->orWhereHas('city', function($q) use ($searchValue) {
                              $q->where('name', 'LIKE', '%' . $searchValue . '%');
                          });
                });
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('code', function ($row) {
                    return $row->pincode;
                })
                ->editColumn('extra_charge', function ($row) {
                    return $row->extra_charge ?? 0;
                })
                ->editColumn('city', function ($row) {
                    return $row?->city?->name;
                })
                ->editColumn('state', function ($row) {
                    return $row?->state?->name;
                })
                ->editColumn('country', function ($row) {
                    return $row?->country?->name;
                })

                /* ->editColumn('code', function ($row) {
                $pincodes = json_decode($row->pincode); 
                $formattedPincodes = '<table class="table table-bordered table-sm text-center mb-0">';
                $formattedPincodes .= '';
                if (is_array($pincodes)) {
                    foreach ($pincodes as $index => $pincode) {

                        $formattedPincodes .= '<td>' . htmlspecialchars($pincode) . '</td>';

                    }
                } else {
                    $formattedPincodes .= '<tr><td colspan="2">' . htmlspecialchars($row->pincode) . '</td></tr>';
                }

                $formattedPincodes .= ' </table>';
                return '
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal' . $row->id . '">
                        View
                    </button>
                        <!-- Modal -->
                        <div class="modal fade" id="viewModal' . $row->id . '" tabindex="-1" aria-labelledby="viewModalLabel' . $row->id . '" aria-hidden="true">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="viewModalLabel' . $row->id . '">Pincode List: ' . $row->code . '</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    
                                    <div class="modal-body">
                                        ' . $formattedPincodes . '
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                }) */
                ->editColumn('delivery_type', function ($row) {
                    return $row->delivery == 1
                        ? '<span class="badge bg-success">Available for Delivery</span>'
                        : ($row->delivery == 2
                            ? '<span class="badge bg-danger">Non Delivery</span>'
                            : ($row->delivery == 3
                                ? '<span class="badge bg-warning">Delivery with Extra Charge</span>'
                                : '<span class="badge bg-secondary">No Delivery Info</span>'
                            )
                        );
                })
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

                    $editBtn = "<a href='" . route("admin-pincodes.edit", $id) . "' class='btn btn-sm btn-info'><i class='ri-edit-line'></i></a>";

                    $deleteBtn = "<form method='POST' action='" . route("admin-pincodes.destroy", $id) . "' style='display:inline-block;' onsubmit='return confirm(\"Are you sure?\")'>"
                        . csrf_field()
                        . method_field('DELETE')
                        . "<button type='submit' class='btn btn-sm btn-danger'><i class='ri-delete-bin-5-line'></i></button></form>";


                    return "<div class='hstack gap-1 flex-wrap'>{$statusBtn}{$editBtn}{$deleteBtn}</div>";
                })
                ->rawColumns(['action', 'country_flag', 'status', 'delivery_type', 'code'])
                ->make(true);
        }

        $searchVariable = [
            'status' => $request->status,
            'date_from' => $request->date_from,
            'date_to'   => $request->date_to,
            'pincode'   => $request->pincode,
        ];

        return view("admin.pincodes.index", compact('searchVariable'));
    }



    public function create()
    {
        $countries = Country::all();
        return view('admin.pincodes.add', compact('countries'));
    }

    ///////////////Store Pincode///////////
    public function store(PincodesRequest $request)
    {
        return $this->saveOrUpdateCountry($request);
    }


    public function edit($id)
    {

        $country = Pincodes::findOrFail(base64_decode($id));

        $countries = Country::all();
        $states = State::where('country_id', $country->country_id)->get();
        $cities = City::where('state_id', $country->state_id)->get();

        $pincodesArray = $country->pincode ? json_decode($country->pincode, true) : [];

        return view('admin.pincodes.edit', compact('country', 'countries', 'states', 'cities', 'pincodesArray'));
    }







    ///////////update Country///////////////

    public function update(PincodesRequest $request, $encodedId)
    {
        $id = base64_decode($encodedId);
        return $this->saveOrUpdateCountry($request, $id);
    }


    //////////////////////Common Function Add And Edit//////////////////


    private function saveOrUpdateCountry(Request $request, $id = null)
    {
        DB::beginTransaction();
        try {
            $deliverySets = [];
            if (!empty($request->delivery_sets)) {
                foreach ($request->delivery_sets as $k => $set) {
                    $deliverySets[$k]['country_id'] = $request->country_id;
                    $deliverySets[$k]['state_id'] = $request->state_id;
                    $deliverySets[$k]['city_id'] = $request->city_id;
                    $deliverySets[$k]['delivery'] = $set['delivery_type'];
                    $deliverySets[$k]['extra_charge'] = $set['extra_delivery_charge'];
                    $deliverySets[$k]['pincode'] = $set['pincode'];
                }
            }
            //prx($deliverySets);

            Pincodes::upsert(
                $deliverySets,
                ['pincode'],
                ['delivery', 'extra_charge', 'country_id', 'state_id', 'city_id']
            );

            //$commonData = $request->only(['country_id', 'state_id', 'city_id']);

            /* if ($id) {
                // UPDATE only one delivery set
                $set = $request->delivery_sets[0];
                $pincodes = array_filter(array_map('trim', explode(',', $set['pincode'])));
                

                $data = [
                    ...$commonData,
                    'delivery_type' => $set['delivery_type'],
                    'extra_delivery_charge' => $set['delivery_type'] == '3' ? ($set['extra_delivery_charge'] ?? 0) : null,
                    'pincode' => json_encode($pincodes),
                ];
    
                Pincodes::findOrFail($id)->update($data);
                $message = 'Pincode updated successfully.';
            } else {
                
                foreach ($request->delivery_sets as $set) {
                    $pincodes = array_filter(array_map('trim', explode(',', $set['pincode'])));
                    if (empty($pincodes)) continue;
    
                    Pincodes::create([
                        ...$commonData,
                        'delivery_type' => $set['delivery_type'],
                        'extra_delivery_charge' => $set['delivery_type'] == '3' ? ($set['extra_delivery_charge'] ?? 0) : null,
                        'pincode' => json_encode($pincodes),
                    ]);
                }
    
                } */

            $message = 'Pincode(s) updated/created successfully.';
            DB::commit();
            return redirect()->route('admin-pincodes.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }





    ////////////////////Delete Pincodes////////////////
    public function destroy($id)
    {
        $country = Pincodes::findOrFail(base64_decode($id));
        $country->delete();
        return redirect()->back()->with('success', 'Country deleted successfully.');
    }

    ////////////////////Change Status Active And Deactive//////////////
    public function status($id, $status)
    {
        Pincodes::where('id', $id)->update(['status' => $status]);

        return redirect()->back()->with('success', 'Status updated.');
    }
}
