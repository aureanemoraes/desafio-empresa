<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Answer;
use App\Models\Respondent;
use Illuminate\Http\Request;
use App\Services\AnswerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\SimpleMailNotification;
use Illuminate\Support\Facades\Mail;
use App\Enums\NotificationContentType;
use App\Services\DataTreater\EmailData;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AnswerController extends Controller
{


	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		$validated = Validator::make($request->all(), [
            'respondent_id' => 'sometimes|nullable|exists:respondents,public_id',
            'form_id' => 'required|exists:forms,slug',
            'question' => 'required|string',
            'value' => 'required',
            'field_id' => 'required',
            'type' => 'required|string',
            'is_last' => 'sometimes|boolean',
            'respondent_email' => 'sometimes|email',
        ])->after(function ($validator) use ($request) {
            /**
             * realizando a validação do campo respondent_email somente se nas configurações do formulário existir um NotificationContentType::COPIA_RESPOSTAS_FORMULARIO)
             */
            $form = Form::where('slug', $request->input('form_id'))->first();

            if ($form && !empty($form->notifications_config) && $form->containsContentType(NotificationContentType::COPIA_RESPOSTAS_FORMULARIO)) {
                if (!$request->has('respondent_email')) {
                    $validator->errors()->add('respondent_email', 'The respondent_email field is required when the form contains COPIA_RESPOSTAS_FORMULARIO.');
                }
            }
        })->validate();


        DB::beginTransaction();

        try {
            $answer = Answer::create([
                'respondent_id' => $validated["respondent_id"] ?? Respondent::create(["form_id" => $validated["form_id"]])->public_id,
                'form_id' => $validated["form_id"],
                'question' => $validated['question'],
                'value' => $validated['value'],
                'type' => $validated['type'],
                'field_id' => $validated['field_id']
            ]);

            if ($request->has("is_last")) {
                $answer->respondent?->fill(['email' => $validated['respondent_email'] ?? null])->save();
                $answer->respondent?->setAsCompleted();

                AnswerService::notify($answer);
            }

            DB::commit();

            return response()->json(['data' => $answer], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing answer: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }

	}

	public function index(){}
	public function show(string $id){}
	public function update(Request $request, string $id){}
	public function destroy(string $id){}
}
