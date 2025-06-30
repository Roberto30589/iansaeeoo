<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\Models\Plant;
use App\Models\Area;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PlantController extends Controller
{

    function index(){
        return Inertia::render('Plants/Index');
    }
    
    public function table(){
        //plant model with areas where areas are enabled
        $plants = Plant::with(['areas' => function($query) {
            $query->where('enabled', true);
        }])->where('enabled',true)->get();
        return DataTables::of($plants)->make(true);
    }
    
    function add(){
        return Inertia::render('Plants/Form');
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
            // Check if plant name is unique
            if (Plant::where('name', $input['name'])->exists()) {
                $validator->errors()->add('name', 'El nombre de la planta ya existe.');
            }
        })->after(function ($validator) use ($input) {
            // Check if areas are not empty
            if (empty($input['areas'])) {
                $validator->errors()->add('areas', 'Debe proporcionar al menos una área.');
            }   
        })->validate();

        $plant = new Plant();
        $plant->fill($validated);
        if($plant->save()){
            //sync areas
            foreach ($validated['areas'] as $areaData) {
                $area = new Area();
                $area->name = $areaData['name'];
                $area->plant_id = $plant->id;
                $area->enabled = $areaData['enabled']; // Default to true
                $area->save();
            }
            return redirect()->route('plants')->with('success', 'Planta creada correctamente');
        }else{
            return redirect()->back()->with('error', 'Error al crear la planta');
        }
    }

    public function select($id)
    {
        $plant = Plant::with('areas')->findOrFail($id);
        return Inertia::render('Plants/Form', [
            'plant' => $plant,
        ]);
    }

    public function update(Request $request, $id)
    {
        $plant = Plant::findOrFail($id);
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
        ])->after(function ($validator) use ($input, $plant) {
            // Check if areas are unique
            $areaNames = array_column($input['areas'], 'name');
            if (count($areaNames) !== count(array_unique($areaNames))) {
                $validator->errors()->add('areas', 'Las áreas deben tener nombres únicos.');
            }
        })->after(function ($validator) use ($input, $plant) {
            // Check if plant name is unique, excluding the current plant
            if (Plant::where('name', $input['name'])->where('id', '!=', $plant->id)->exists()) {
                $validator->errors()->add('name', 'El nombre de la planta ya existe.');
            }
        })->after(function ($validator) use ($input) {
            // Check if areas are not empty
            if (empty($input['areas'])) {
                $validator->errors()->add('areas', 'Debe proporcionar al menos una área.');
            }
        })->validate();

        $plant->fill($validated);
        if($plant->save()){
            //sync areas
            foreach ($validated['areas'] as $areaData) {
                $area = Area::updateOrCreate(
                    ['id' => $areaData['id'] ?? null, 'plant_id' => $plant->id],
                    ['name' => $areaData['name'], 'enabled' => $areaData['enabled']]
                );
            }
            return redirect()->route('plants')->with('success', 'Planta actualizada correctamente');
        }else{
            return redirect()->back()->with('error', 'Error al actualizar la planta');
        }
    }
}
