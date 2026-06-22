<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AiChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ai_chat_session_id',
        'role',
        'content',
    ];

    public function session()
    {
        return $this->belongsTo(AiChatSession::class, 'ai_chat_session_id');
    }
}
