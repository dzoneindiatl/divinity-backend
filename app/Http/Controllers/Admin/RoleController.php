<?php

namespace App\Http\Controllers\Admin;

use DB;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    function __construct(){
         $this->middleware('permission:view_role|create_role|edit_role|delete_role', ['only' => ['index','store']]);
         $this->middleware('permission:create_role', ['only' => ['create','store']]);
         $this->middleware('permission:edit_role', ['only' => ['edit','update']]);
         $this->middleware('permission:delete_role', ['only' => ['destroy']]);
    }

    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => Role::WhereNotIn('id',array('3','5'))->orderBy('id','DESC')->get()
        ]);  // remove customer and subscriber from list
    }

    public function create(): View
    {
        $menus = Permission::groupBy('menu_name')->get();
        //dd($menus);
        return view('admin.roles.add', [
            'menus' => $menus, 
            'permissions' => Permission::get()
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required|string|max:250|unique:roles,name',
            'permissions' => 'required',
         ]);

        $role = Role::create(['name' => $request->name]);

        $permissions = Permission::whereIn('id', $request->permissions)->get(['name'])->toArray();
        
        $role->syncPermissions($permissions);

        return redirect()->route('admin-roles.index')
                ->withSuccess('New role is added successfully.');
    }

    public function show(Role $role): View
    {
        $rolePermissions = Permission::join("role_has_permissions","permission_id","=","id")
            ->where("role_id",$role->id)
            ->select('name')
            ->get();
        return view('roles.show', [
            'role' => $role,
            'rolePermissions' => $rolePermissions
        ]);
    }

    public function edit($id,Request $request): View
    {
       
        $id = base64_decode($id);  
        $role= Role::findorfail($id); 

        if($role->name=='Super Admin'){
            abort(403, 'SUPER ADMIN ROLE CAN NOT BE EDITED');
        }

        // $rolePermissions = DB::table("role_has_permissions")->where("role_id",$role->id)
        //     ->pluck('permission_id')
        //     ->all();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)

            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')

            ->all();

        $menus = Permission::groupBy('menu_name')->get();    
        return view('admin.roles.edit', [
            'role' => $role,
            'permissions' => Permission::get(),
            'menus' => $menus,
            'rolePermissions' => $rolePermissions
        ]);
    }

    public function update($id,Request $request): RedirectResponse
    {
        if($id != ''){
            $id  = base64_decode($id);
            $role = Role::findorfail($id);
           

            $this->validate($request, [
               'name' => 'required|string|max:250|unique:roles,name,'.$id,
                'permissions' => 'required',
            ]);
            $input = $request->only('name');

            $role->update($input);

            $permissions = Permission::whereIn('id', $request->permissions)->get(['name'])->toArray();

            $role->syncPermissions($permissions);    
            
            return redirect()->route('admin-roles.index')
                    ->withSuccess('Role is updated successfully.');
        }else{
            return redirect()->back();
        }
        
    }

    public function destroy(Role $role): RedirectResponse
    {
        if($role->name=='Super Admin'){
            abort(403, 'SUPER ADMIN ROLE CAN NOT BE DELETED');
        }
        if(auth()->user()->hasRole($role->name)){
            abort(403, 'CAN NOT DELETE SELF ASSIGNED ROLE');
        }
        $role->delete();
        return redirect()->route('roles.index')
                ->withSuccess('Role is deleted successfully.');
    }

}
