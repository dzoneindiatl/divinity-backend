<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\ProductSpecification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class ProductSpecificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $model = 'specifications';
    public function __construct(Request $request)
    {
        $this->middleware('permission:view_specification|create_specification|edit_specification|delete_specification', ['only' => ['index','show']]);
        $this->middleware('permission:create_specification', ['only' => ['create','store']]);
        $this->middleware('permission:edit_specification', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_specification', ['only' => ['destroy']]);
         
        $this->listRouteName = 'admin-specifications.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;
    }
    public function index(Request $request)
    {
        // echo 't1';die;
         try {
            $DB = ProductSpecification::query();
            $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'created_at';
            $order = $request->input('order') ? $request->input('order') : 'desc';
            $offset = !empty($request->input('offset')) ? $request->input('offset') : 0;
            $limit =  !empty($request->input('limit')) ? $request->input('limit') : Config("Reading.records_per_page");

            $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();

            $totalResults = $DB->count();
            if ($request->ajax()) {

                return  view("admin.specifications.load_more_data", compact('results', 'totalResults'));
            } else {

                return  view("admin.specifications.index", compact('results', 'totalResults'));
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
        return view('admin.specifications.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $this->validate($request, [
               'name' => 'required|string|max:250|unique:product_specifications',
               //'description' => 'required',
               'image' => 'required|mimes:jpg,jpeg,png,svg,webp|max:1024',
        ]);

       $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '-image.' . $extension;

            $folderName = strtoupper(date('M') . date('Y')) . "/";
            $folderPath = config('constant.SPECIFICATION_IMAGE_ROOT_PATH') . $folderName;

            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0777, true);
            }

            $file->move($folderPath, $fileName);

            // store relative path in DB
            $imagePath = $folderName . $fileName;
        }
        ProductSpecification::create([
            'name' => $request->name,
            'icon' => $imagePath,
            'item_order' => $request->priority,
        ]);

        Session()->flash('success', "Collection has been added successfully");
        return Redirect()->route('admin-specifications.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
         $id = base64_decode($request->id);
        $model = ProductSpecification::findorfail($id);

        return view('admin.specifications.edit')->with(['model' => $model]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(string $id, Request $request)
    {
        $id = base64_decode($id);
        $model = ProductSpecification::findOrFail($id);

        $this->validate($request, [
            'name' => 'required|string|max:250|unique:product_specifications,name,' . $model->id,
            //'description' => 'required',
            'image' => 'nullable|mimes:jpg,jpeg,png,svg,webp|max:1024',
        ]);

        $input = $request->except('image');
        $imagePath = $model->image;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '-image.' . $extension;

            $folderName = strtoupper(date('M') . date('Y')) . "/";
            $folderPath = config('constant.SPECIFICATION_IMAGE_ROOT_PATH') . $folderName;

            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0777, true);
            }

            $file->move($folderPath, $fileName);
            $imagePath = $folderName . $fileName;
        }

        $model->update(array_merge($input, ['icon' => $imagePath]));
        return redirect()->route('admin-specifications.index')
            ->withSuccess('Product specification updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $decodedId = base64_decode($id);
        $model = ProductSpecification::findOrFail($decodedId);
        $model->delete();
        return redirect()
            ->route('admin-specifications.index')
            ->with('success', 'Deleted successfully.');
    }


     
}
