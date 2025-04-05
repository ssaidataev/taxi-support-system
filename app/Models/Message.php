<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="Message",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="support_request_id", type="integer"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="message", type="string")
 * )
 */
class Message extends Model
{
    use HasFactory;

    protected $fillable = ['support_request_id', 'user_id', 'message'];

    public function supportRequest(): BelongsTo
    {
        return $this->belongsTo(SupportRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
