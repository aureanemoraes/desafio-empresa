<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    /**
     * Para envio das notificações via request
     */
    public function sendFormNotifications($answerPublicId)
    {
        try {
            $lastAnswer = Answer::where('public_id', $answerPublicId)->first();

            if (!isset($lastAnswer)) {
                return response()->json(['data' => 'Answer not found.'], 404);
            }

            NotificationService::notify($lastAnswer);

            return response()->json([], 204);
        } catch (\Exception $e) {
            Log::error('Error on  endFormNotifications' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
