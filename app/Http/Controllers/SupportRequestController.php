<?php

namespace App\Http\Controllers;

use App\Models\SupportRequest;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(name="SupportRequests", description="Операции с обращениями поддержки")
 */
class SupportRequestController extends Controller
{

    // In SupportRequestController.php

    /**
     * @OA\Get(
     *     path="/api/support-requests",
     *     summary="Получить список всех обращений",
     *     tags={"SupportRequests"},
     *     @OA\Response(
     *         response=200,
     *         description="Список обращений",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/SupportRequest"))
     *     )
     * )
     */
    public function index(Request $request)
    {
        // Получаем все обращения (если они есть)
        $supportRequests = SupportRequest::all();

        // Возвращаем список обращений в виде JSON (или вы можете вернуть Blade шаблон)
        return response()->json($supportRequests);
    }


    /**
     * @OA\Get(
     *     path="/api/support-requests/{id}",
     *     summary="Получить конкретное обращение",
     *     tags={"SupportRequests"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID обращения",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Обращение найдено",
     *         @OA\JsonContent(ref="#/components/schemas/SupportRequest")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Обращение не найдено"
     *     )
     * )
     */
    public function show($id)
    {
        $supportRequest = SupportRequest::find($id);

        if (!$supportRequest) {
            return response()->json(['error' => 'Support request not found'], 404);
        }

        return response()->json($supportRequest);
    }

    /**
     * @OA\Post(
     *     path="/api/support-requests/{supportRequestId}/messages",
     *     summary="Ответить на обращение",
     *     tags={"SupportRequests"},
     *     @OA\Parameter(
     *         name="supportRequestId",
     *         in="path",
     *         description="ID обращения",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"message"},
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Сообщение добавлено",
     *         @OA\JsonContent(ref="#/components/schemas/Message")
     *     )
     * )
     */
    public function addMessage(Request $request, $supportRequestId)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $supportRequest = SupportRequest::find($supportRequestId);

        if (!$supportRequest) {
            return response()->json(['error' => 'Support request not found'], 404);
        }

        $message = Message::create([
            'support_request_id' => $supportRequestId,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return response()->json($message, 201);
    }

    /**
     * @OA\Post(
     *     path="/api/support-requests/{id}/attach-order",
     *     summary="Закрепить заказ за обращением",
     *     tags={"SupportRequests"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID обращения",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_id"},
     *             @OA\Property(property="order_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Заказ прикреплен",
     *         @OA\JsonContent(ref="#/components/schemas/SupportRequest")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Обращение или заказ не найдены"
     *     )
     * )
     */
    public function attachOrder(Request $request, $id)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $supportRequest = SupportRequest::find($id);

        if (!$supportRequest) {
            return response()->json(['error' => 'Support request not found'], 404);
        }

        $order = Order::find($request->order_id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Прикрепляем заказ к обращению
        $supportRequest->order_id = $order->id;
        $supportRequest->save();

        return response()->json($supportRequest);
    }

    /**
     * @OA\Post(
     *     path="/api/support-requests",
     *     summary="Создать новое обращение в поддержку",
     *     tags={"SupportRequests"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type", "description"},
     *             @OA\Property(property="type", type="string", example="жалоба"),
     *             @OA\Property(property="description", type="string", example="Я не получил свой заказ."),
     *             @OA\Property(property="order_id", type="integer", example=123)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Обращение создано",
     *         @OA\JsonContent(ref="#/components/schemas/SupportRequest")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $supportRequest = SupportRequest::create([
            'user_id' => auth()->id(), // предполагается, что пользователь авторизован
            'type' => $request->type,
            'description' => $request->description,
            'order_id' => $request->order_id,
        ]);

        return response()->json($supportRequest, 201);
    }


}
