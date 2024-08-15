<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Respondent;
use Illuminate\Http\Request;
use App\Services\FormService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Rules\ValidNotificationConfig;

class FormController extends Controller
{
    public function index()
    {
        return Form::simplePaginate(); // não conhecia
    }

    public function store(Request $request)
    {
        $validData = $request->validate([
            'title' => 'required|max:255',
            'fields' => 'required|array',
            'fields.*.label' => 'required|string',
            'fields.*.type' => 'required|string',
            'fields.*.required' => 'required|boolean',
            'notifications_config' => ['sometimes', new ValidNotificationConfig()],
        ]);

        if(Gate::denies('forms:create')) {
            return response([], 404);
        };

        $validData['user_id'] = auth()->user()->public_id;

        try {
            DB::beginTransaction();

            $form = Form::create($validData);

            DB::commit();

            return response(["data" => $form], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response(["data" => $e->getMessage() ?? 'Error on create a new form.'], $e->getCode() ?? 500);
        }
    }

    public function show(Form $form)
    {
        return $form; // gosto de usar ModelResource para controlar o retorno dos dados
    }

    public function update(Form $form, Request $request)
    {
        if(Gate::denies('forms:update', $form)) { // gosto de usar FormRequest só pra separar funcionalidade mesmo, e adicionar o gate lá
            return response([], 404);
        };

        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'fields' => 'required|array',
            'fields.*.label' => 'required|string',
            'fields.*.type' => 'required|string',
            'fields.*.required' => 'required|boolean',
            'fields.*.field_id' => 'sometimes|string',
            'notifications_config' => ['sometimes', new ValidNotificationConfig()],
        ]);

        try {
            DB::beginTransaction();

            $form->update($validatedData);

            DB::commit();

            return response(["data" => $form], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response(["data" => $e->getMessage() ?? 'Error on create a new form.'], $e->getCode() ?? 500);
        }
    }

    public function destroy(Form $form)
    {
        if(Gate::denies('forms:delete', $form)) {
            return response([], 404);
        };

        $form->delete();

        return response([], 204);
    }
}
