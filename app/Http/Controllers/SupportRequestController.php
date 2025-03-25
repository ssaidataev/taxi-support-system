<?php

namespace App\Http\Controllers;

use App\Models\SupportRequest;
use App\Models\Message;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="SupportRequests", description="Операции с обращениями поддержки")
 */
class SupportRequestController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/support-requests",
     *     summary="Создать новое обращение",
     *     tags={"SupportRequests"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type"},
     *             @OA\Property(property="type", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="order_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Создано новое обращение",
     *         @OA\JsonContent(ref="#/components/schemas/SupportRequest")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'description' => 'nullable|string',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        $supportRequest = SupportRequest::create([
            'user_id' => auth()->id(),
            'type' => $request->type,
            'description' => $request->description,
            'order_id' => $request->order_id,
        ]);

        return response()->json($supportRequest, 201);
    }
}
