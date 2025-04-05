<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\SupportRequest;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Messages", description="Операции с сообщениями поддержки")
 */
class MessageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/messages/{supportRequestId}",
     *     summary="Получить все сообщения по обращению",
     *     tags={"Messages"},
     *     @OA\Parameter(
     *         name="supportRequestId",
     *         in="path",
     *         description="ID обращения",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список сообщений",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Обращение не найдено"
     *     )
     * )
     */
    public function index($supportRequestId)
    {
        $supportRequest = SupportRequest::find($supportRequestId);

        if (!$supportRequest) {
            return response()->json(['error' => 'Support request not found'], 404);
        }

        $messages = Message::where('support_request_id', $supportRequestId)->get();

        return response()->json($messages, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/messages",
     *     summary="Создать новое сообщение",
     *     tags={"Messages"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"support_request_id", "sender", "message"},
     *             @OA\Property(property="support_request_id", type="integer", example=1),
     *             @OA\Property(property="sender", type="string", example="client"),
     *             @OA\Property(property="message", type="string", example="Здравствуйте, нужна помощь")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Сообщение создано",
     *         @OA\JsonContent(ref="#/components/schemas/Message")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'support_request_id' => 'required|exists:support_requests,id',
            'sender' => 'required|string|in:client,admin',
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create($validated);

        return response()->json($message, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/messages/view/{id}",
     *     summary="Просмотр одного сообщения",
     *     tags={"Messages"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID сообщения",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Данные сообщения",
     *         @OA\JsonContent(ref="#/components/schemas/Message")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Сообщение не найдено"
     *     )
     * )
     */
    public function show($id)
    {
        $message = Message::find($id);

        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        return response()->json($message);
    }
}
