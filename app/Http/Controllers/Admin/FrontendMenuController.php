<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FrontendMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $model = 'frontend-menus';
    public function __construct(Request $request)
    {
        // $this->middleware('permission:view_collection|create_collection|edit_collection|delete_collection', ['only' => ['index','show']]);
        // $this->middleware('permission:create_collection', ['only' => ['create','store']]);
        // $this->middleware('permission:edit_collection', ['only' => ['edit','update']]);
        // $this->middleware('permission:delete_collection', ['only' => ['destroy']]);
         
        $this->listRouteName = 'admin-frontend-menus.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;
    }
    public function index(Request $request)
    {
        

        try {
            $DB = Menu::query();
            $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'created_at';
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
                    $DB->whereBetween('price_drops.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
                } elseif (!empty($searchData['date_from'])) {
                    $dateS = $searchData['date_from'];
                    $DB->where('price_drops.created_at', '>=', [$dateS . " 00:00:00"]);
                } elseif (!empty($searchData['date_to'])) {
                    $dateE = $searchData['date_to'];
                    $DB->where('price_drops.created_at', '<=', [$dateE . " 00:00:00"]);
                }

                foreach ($searchData as $fieldName => $fieldValue) {
                    if ($fieldValue != "") {
                        if ($fieldName == "title") {
                            $DB->where("title", 'like', '%' . $fieldValue . '%');
                        }
                    }
                }
            }
            
            $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();



            // Build a count map for collection IDs
           
            $totalResults = $DB->count();
            if ($request->ajax()) {

                return  view("admin.menus.load_more_data", compact('results', 'totalResults'));
            } else {

                return  view("admin.menus.index", compact('results', 'totalResults'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
     public function create()
    {
        $parentMenus = Menu::whereNull('parent_id')->get();
        return view('admin.menus.create', compact('parentMenus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required||string|max:255|unique:menus,title',
            'url' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer',
            'is_active' => 'required'
        ]);

        Menu::create($request->all());

        return redirect()->route('admin-menus.index')->with('success', 'Menu created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
     public function edit($id)
    {
        $id = base64_decode($id);
        $menu = Menu::findorfail($id);
        $parentMenus = Menu::whereNull('parent_id')->where('id', '!=', $menu->id)->get();

        return view('admin.menus.edit', compact('menu', 'parentMenus'));
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'title' => 'required||string|max:255|unique:menus,title',
            'url' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer',
        ]);

        $menu->update($request->all());

        return redirect()->route('admin.menus.index')->with('success', 'Menu updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();

        return redirect()->route('admin.menus.index')->with('success', 'Menu deleted successfully.');
    }
    
}
