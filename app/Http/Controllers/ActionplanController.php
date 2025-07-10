<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\Actionplan;
use App\Models\User;
use App\Models\Plant;

class ActionplanController extends Controller
{
    public function index(){
        return Inertia::render('Actionplans/Index');
    }
    
    public function table(){
        //actionplan model with areas where areas are enabled
        $actionplans = Actionplan::with()->get();
        return DataTables::of($actionplans)->make(true);
    }
    
    public function add(){
        $plants = Auth::user()->plants()->where('enabled', true)->get();
        $users = User::all();
        return Inertia::render('Actionplans/Form', [
            'plants' => $plants,
            'users' => $users,
        ]);
    }
 
    public function create(Request $request)
    {
        $input = $request->all();
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            //array de areas
            'areas' => ['required', 'array'],
            //las areas requieren un nombre
            'areas.*.name' => ['required', 'string', 'max:255'],
            //las areas pueden tener un enabled
            'areas.*.enabled' => ['nullable', 'boolean'],
        ])->after(function ($validator) use ($input) {
            // Check if areas are unique
            $areaNames = array_column($input['areas'], 'name');
            if (count($areaNames) !== count(array_unique($areaNames))) {
                $validator->errors()->add('areas', 'Las áreas deben tener nombres únicos.');
            }
        })->after(function ($validator) use ($input) {
            // Check if actionplan name is unique
            if (Actionplan::where('name', $input['name'])->exists()) {
                $validator->errors()->add('name', 'El nombre de la actionplana ya existe.');
            }
        })->after(function ($validator) use ($input) {
            // Check if areas are not empty
            if (empty($input['areas'])) {
                $validator->errors()->add('areas', 'Debe proporcionar al menos una área.');
            }   
        })->validate();

        $actionplan = new Actionplan();
        $actionplan->fill($validated);
        if($actionplan->save()){
            //sync areas
            foreach ($validated['areas'] as $areaData) {
                $area = new Area();
                $area->name = $areaData['name'];
                $area->actionplan_id = $actionplan->id;
                $area->enabled = $areaData['enabled']; // Default to true
                $area->save();
            }
            return redirect()->route('actionplans')->with('success', 'Actionplana creada correctamente');
        }else{
            return redirect()->back()->with('error', 'Error al crear la actionplana');
        }
    }

    public function select($id)
    {
        $actionplan = Actionplan::with('areas')->findOrFail($id);
        return Inertia::render('Actionplans/Form', [
            'actionplan' => $actionplan,
        ]);
    }

    public function update(Request $request, $id)
    {
        $actionplan = Actionplan::findOrFail($id);
        $input = $request->all();
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            //array de areas
            'areas' => ['required', 'array'],
            //las areas requieren un nombre y pueden tener un id si ya existen
            'areas.*.id' => ['nullable', 'integer', 'exists:areas,id'],
            //las areas requieren un nombre
            'areas.*.name' => ['required', 'string', 'max:255'],
            //las areas pueden tener un enabled
            'areas.*.enabled' => ['nullable', 'boolean'],
        ])->after(function ($validator) use ($input, $actionplan) {
            // Check if areas are unique
            $areaNames = array_column($input['areas'], 'name');
            if (count($areaNames) !== count(array_unique($areaNames))) {
                $validator->errors()->add('areas', 'Las áreas deben tener nombres únicos.');
            }
        })->after(function ($validator) use ($input, $actionplan) {
            // Check if actionplan name is unique, excluding the current actionplan
            if (Actionplan::where('name', $input['name'])->where('id', '!=', $actionplan->id)->exists()) {
                $validator->errors()->add('name', 'El nombre de la actionplana ya existe.');
            }
        })->after(function ($validator) use ($input) {
            // Check if areas are not empty
            if (empty($input['areas'])) {
                $validator->errors()->add('areas', 'Debe proporcionar al menos una área.');
            }
        })->validate();

        $actionplan->fill($validated);
        if($actionplan->save()){
            //sync areas
            foreach ($validated['areas'] as $areaData) {
                $area = Area::updateOrCreate(
                    ['id' => $areaData['id'] ?? null, 'actionplan_id' => $actionplan->id],
                    ['name' => $areaData['name'], 'enabled' => $areaData['enabled']]
                );
            }
            return redirect()->route('actionplans')->with('success', 'Actionplana actualizada correctamente');
        }else{
            return redirect()->back()->with('error', 'Error al actualizar la actionplana');
        }
    }

    public function delete($id)
    {
        $actionplan = Actionplan::findOrFail($id);
        $actionplan->enabled = false; // Disable the actionplan instead of deleting it
        if ($actionplan->save()) {
            return back()->with('success', 'Actionplana eliminada correctamente');
        } else {
            return back()->with('error', 'Error al eliminar la actionplana');
        }
    }
}
