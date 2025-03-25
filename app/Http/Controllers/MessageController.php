<?php

namespace App\Http\Controllers;

use App\Models\Message;
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
     *     )
     * )
     */
    public function index($supportRequestId)
    {
        $messages = Message::where('support_request_id', $supportRequestId)->get();
        return response()->json($messages);
    }
}
