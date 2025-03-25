<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportRequest;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="AdminSupportRequests", description="Операции с обращениями для администраторов")
 */
class SupportRequestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admin/support-requests",
     *     summary="Получить список всех обращений",
     *     tags={"AdminSupportRequests"},
     *     @OA\Response(
     *         response=200,
     *         description="Список всех обращений",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/SupportRequest")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $supportRequests = SupportRequest::all();
        return response()->json($supportRequests);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/support-requests/{id}/attach-order",
     *     summary="Закрепить заказ за обращением",
     *     tags={"AdminSupportRequests"},
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
     *         description="Заказ успешно закреплен за обращением"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Заказ не найден"
     *     )
     * )
     */
    public function attachOrder(Request $request, $id)
    {
        $supportRequest = SupportRequest::findOrFail($id);
        $order = Order::find($request->order_id);

        if ($order) {
            $supportRequest->order()->associate($order);
            $supportRequest->save();

            return response()->json([
                'message' => 'Order successfully attached to the support request.',
                'support_request' => $supportRequest,
            ]);
        }

        return response()->json(['error' => 'Order not found.'], 404);
    }
}
