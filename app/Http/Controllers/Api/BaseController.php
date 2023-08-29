<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
            'errors'  => $errorMessages
        ];

        if(!empty($errorMessages)) {
            $response['errors'] = $this->adaptErrorMessages($errorMessages);
        }

        return response()->json($response, $code);
    }

    public function adaptErrorMessages($errors)
    {
        $messages = [];
        $errors = array_values($errors);
        for ($i = 0; $i < count($errors);$i++) {
            $messages[] = is_array($errors[$i]) ? $errors[$i][0] : $errors[$i];
        }
        return $messages;
    }
}
