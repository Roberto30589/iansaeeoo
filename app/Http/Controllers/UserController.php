<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Plant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Actions\Fortify\PasswordValidationRules;

class UserController extends Controller
{
    use PasswordValidationRules;
    
    public function index(){
        return Inertia::render('Users/Index');
    }
    
    public function table(){
        $users = User::with('roles')->select('id', 'name', 'email', 'created_at')->get();
        return DataTables::of($users)->make(true);
    }
    
    public function add(){
        $roles = Role::all();
        $plants = Plant::where('enabled', true)->get();
        return Inertia::render('Users/Form', [
            'roles' => $roles,
            'plants' => $plants,
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
            // If roles are provided, associate them with the user
            if ($request->roles) {
                $user->roles()->sync($request->roles);
            }
            // If plants are provided, associate them with the user
            if ($request->plants) {
                $user->plants()->sync($request->plants);
            }
            return redirect()->route('users')->with('success', 'Usuario creado correctamente');
        }else{
            return redirect()->back()->with('error', 'Error al crear el usuario');
        }
    }


    public function select($id)
    {
        $user = User::with('roles','plants')->findOrFail($id);
        $roles = Role::all();
        $plants = Plant::where('enabled', true)->get();
        return Inertia::render('Users/Select', [
            'user' => $user,
            'roles' => $roles,
            'plants' => $plants,
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
            $user->plants()->sync($request->plants);
            return redirect()->route('users')->with('success', 'Usuario actualizado correctamente');
        }else{
            return redirect()->back()->with('error', 'Error al actualizar el usuario');
        }
    }
    
    public function delete($id)
    {
        $user = User::findOrFail($id);    

        $timestamp = now()->timestamp;
        $user->email = $user->email . '_deleted_' . $timestamp;
        $user->save();
        if ($user->delete()) {
            return back()->with('success', 'Usuario eliminado correctamente');
        } else {
            return back()->with('error', 'Error al eliminar al Usuario');
        }
    }
}
