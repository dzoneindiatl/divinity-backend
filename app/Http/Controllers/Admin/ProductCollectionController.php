<?php

namespace App\Http\Controllers\Admin;

use File;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductCollection;
use App\Http\Controllers\Controller;

class ProductCollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $model = 'collections';
    public function __construct(Request $request)
    {
        $this->middleware('permission:view_collection|create_collection|edit_collection|delete_collection', ['only' => ['index','show']]);
        $this->middleware('permission:create_collection', ['only' => ['create','store']]);
        $this->middleware('permission:edit_collection', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_collection', ['only' => ['destroy']]);
         
        $this->listRouteName = 'admin-collections.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;
    }
    public function index(Request $request)
    {
         try {
            $DB = ProductCollection::query();
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

            $allProducts = Product::pluck('collection_ids')->filter()->toArray(); // Filter removes null/empty values

            $collectionCountMap = [];

            // Build a count map for collection IDs
            $collectionCountMap = collect($allProducts)
            ->flatMap(fn($product) => explode(',', $product))
            ->countBy();
            foreach ($results as $result) {
                $result->total_product = $collectionCountMap->get($result->id, 0); 
            }
            $totalResults = $DB->count();
            if ($request->ajax()) {

                return  view("admin.collections.load_more_data", compact('results', 'totalResults'));
            } else {

                return  view("admin.collections.index", compact('results', 'totalResults'));
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
        return view('admin.collections.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $this->validate($request, [
               'title' => 'required|string|max:250|unique:product_collections',
               //'description' => 'required',
               'image' => 'required|mimes:jpg,jpeg,png,svg,webp|max:1024',
        ]);

       $imagePath = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '-image.' . $extension;

            $folderName = strtoupper(date('M') . date('Y')) . "/";
            $folderPath = config('constant.COLLECTION_IMAGE_ROOT_PATH') . $folderName;

            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0777, true);
            }

            $file->move($folderPath, $fileName);
            $imagePath = $folderName . $fileName;
        }
        ProductCollection::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
            'collection_type'=>$request->collection_type,
        ]);

        Session()->flash('success', "Collection has been added successfully");
        return Redirect()->route('admin-collections.index');
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
    public function edit(string $id)
    {
         $id = base64_decode($id);
        $model = ProductCollection::findorfail($id);
        $allProducts = Product::pluck('collection_ids')->filter()->toArray(); // Filter removes null/empty values
        $collectionCountMap = [];
        $products = Product::whereRaw("FIND_IN_SET(?, collection_ids)", [$model->id])->get();
        // Build a count map for collection IDs
        $collectionCountMap = collect($allProducts)
        ->flatMap(fn($product) => explode(',', $product))
        ->countBy();

        return view('admin.collections.edit')->with(['model' => $model, 'products' => $products]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(string $id, Request $request)
    {
        $id = base64_decode($id);
        $model = ProductCollection::findOrFail($id);

        $this->validate($request, [
            'title' => 'required|string|max:250|unique:product_collections,title,' . $model->id,
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
            $folderPath = config('constant.COLLECTION_IMAGE_ROOT_PATH') . $folderName;

            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0777, true);
            }

            $file->move($folderPath, $fileName);
            $imagePath = $folderName . $fileName;
        }

        $model->update(array_merge($input, ['image' => $imagePath]));
        return redirect()->route('admin-collections.index')
            ->withSuccess('Product collection updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $decodedId = base64_decode($id);
        $model = ProductCollection::findOrFail($decodedId);
        $model->delete();
        return redirect()
            ->route('admin-collections.index')
            ->with('success', 'Collection deleted successfully.');
    }

    public function removeProduct(string $id, string $collectionId)
    {
        $productId = base64_decode($id);
        $collectionId = base64_decode($collectionId);
        $product = Product::findOrFail($productId);
        $collectionIds = explode(',', $product->collection_ids);
        $updatedCollectionIds = array_filter($collectionIds, fn($id) => $id != $collectionId);
        $product->collection_ids = implode(',', $updatedCollectionIds);
        $product->save();
        return redirect()
             ->back()
            //->route('admin-collections.index')
            ->withSuccess('Collection ID removed from the product successfully.');
    }


     
}
