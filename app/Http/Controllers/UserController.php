<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Actions\Fortify\PasswordValidationRules;

class UserController extends Controller
{
    use PasswordValidationRules;
    
    function index(){
        return Inertia::render('Users/Index');
    }
    
    public function table(){
        $users = User::with('roles')->select('id', 'name', 'email', 'created_at')->get();
        return DataTables::of($users)->make(true);
    }
    
    function add(){
        $roles = Role::all();
        return Inertia::render('Users/Form', [
            'roles' => $roles,
        ]);
    }

    public function create(Request $request)
    {
        $input = $request->all();
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
        ])->validate();

        $validated['password'] = Hash::make($validated['password']);

        $user = new User();
        $user->fill($validated);
        if($user->save()){
            $user->roles()->sync($request->roles);
            return redirect()->route('users')->with('success', 'Usuario creado correctamente');
        }else{
            return redirect()->back()->with('error', 'Error al crear el usuario');
        }
    }


    public function select($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        return Inertia::render('Users/Select', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            //si el email es diferente al actual, entonces se valida que sea unico            
            'email' => ['required', 'string', 'email', 'max:255', $request->email == $user->email ? '' : 'unique:users'],
            //si updatePassword es true, entonces se valida la contraseña, pero si no, no se valida
            'password' => $request->updatePassword ? $this->passwordRules() : '',         
        ]);

        if(!$request->updatePassword){
            unset($validated['password']);
        }else{
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->fill($validated);
        if($user->save()){
            $user->roles()->sync($request->roles);
            return redirect()->route('users')->with('success', 'Usuario actualizado correctamente');
        }else{
            return redirect()->back()->with('error', 'Error al actualizar el usuario');
        }
    }

    public function delete(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->enabled = 0;
        $user->save();
        return redirect()->route('users')->with('success', 'Usuario eliminado correctamente');
    }
}
