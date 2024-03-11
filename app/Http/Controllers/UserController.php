<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Validator;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function showloginForm()
    {
        if(Auth::check())
        {
            return redirect('/dashboard');
        }
        return view('auth.login');
    }
    public function login (Request $request)
    {
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        if (Auth::attempt($credentials)) {
            return response()->json(['status' => true], 200);
        }

        return response()->json(['status' => false, 'errors' => 'These credentials do not match our records'], 401);
    }
    public function dashboard()
    {
        return view('admin.dashboard');
    }
    public function index()
    {
        $users = User::with('roles:name')->orderBy('id', 'desc')->where('id', '!=', Auth::id())->paginate(10);

        return view('users.index', ['users' => $users]);
    }
    public function create()
    {
        return view('users.create');
    }
    public function getAllRoles()
    {
        $roles = Role::select('id', 'name')->get();

        return response()->json(['status' => true, 'roles' => $roles]);
    }
    public function store(Request $request)
    {
        $id = $request->id ?? 0;
        $validationData = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
        $validator = Validator::make($request->all(), $validationData);

    
        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()->all()], 422);
    } else {
        $data =  $request->all();
        
        $user = User::updateOrCreate([
            'email' => $data['email']],[
            'name' => $data['name'],
            'password' => Hash::make($data['password']),
        ]);
        $role = Role::where('id', $request->role_id)->first();
        if(!$id)
        if ($role) {
            $user->assignRole($role->name);
        } else {
            $user->syncRoles([$role->name]);
        }

        return response()->json(['status' => true, 'message' => 'User ' . ($id ? 'updated' : 'created') . ' successfully.']);
    }
   
}
public function edit($id)
{
    $user = User::with('roles:id,name')->find($id);

    if (!$user) {
        return response()->json(['status' => false, 'message' => 'User not found'], 404);
    }

    return response()->json(['status' => true, 'user' => $user]);

}
public function destroy($id)
{
    $user = User::with('roles:id,name')->find($id);

    if (!$user) {
        return response()->json(['status' => false, 'message' => 'User not found'], 404);
    } else {
        $user->delete();
    }

    return response()->json(['status' => true, 'message' => 'User deleted successfully.']);

}
public function getAllPermissions()
{
    $permission =  Permission::select('id','name')->get();

    return response()->json(['status' => true, 'permission' => $permission]);

}
public function assignPermissions(Request $request)
{
    $validator = Validator::make($request->all(), [
        'id' => 'required|numeric|exists:users,id',
        'permissions' => 'required|array',
    ]);
    
    if ($validator->fails()) {
        return response()->json(['status' => false, 'errors' => $validator->errors()->all()], 422);
    }
    
    $id = $request->id ?? 0;
    $user = User::find($id);
    
    if (!$user) {
        return response()->json(['status' => false, 'errors' => ['User not found']], 404);
    }
    
    $permissionIds = $request->input('permissions');
    
    $permissions = Permission::whereIn('id', $permissionIds)->get();
    
    $user->syncPermissions($permissions);
    
    return response()->json(['status' => true, 'message' => 'Permissions assigned successfully.']);
    
}
}