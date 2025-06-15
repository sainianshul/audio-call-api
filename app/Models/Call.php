<?php

namespace App\Models;

use App\Enums\CallStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Call extends Model
{
    protected $fillable = [
        'caller_id',
        'receiver_id',
        'status',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'status'      => CallStatus::class,
        'started_at'  => 'datetime',
        'ended_at'    => 'datetime',
    ];

    protected $visible = [
        'id',
        'caller_id',
        'receiver_id',
        'status',
        'started_at',
        'ended_at',
    ];

    public function caller()
    {
        return $this->belongsTo(User::class, 'caller_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Check if a user has an active call.
     * @param int $userId
     * @return bool
     */
    public static function userHasActiveCall(int $userId): bool
    {
        return self::whereIn('status', [CallStatus::Initiated, CallStatus::Accepted])
            ->where(function ($query) use ($userId) {
                $query->where('caller_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })->exists();
    }

    /**
     * Start a new call between two users.
     *  @param int $callerId
     * @param int $receiverId
     * @return self
     */
    public static function startCall(int $callerId, int $receiverId): self
    {
        return self::create([
            'caller_id'   => $callerId,
            'receiver_id' => $receiverId,
            'status'      => CallStatus::Initiated,
        ]);
    }

    /**
     * Respond to a call with a status.
     * @param CallStatus $response
     * @return self
     */

    public function respondToCall(CallStatus $response): self
    {
        $this->status = $response;
        if ($response === CallStatus::Accepted) {
            $this->started_at = now();
        }
        $this->save();

        return $this;
    }

    /**
     * End the call.
     * @return self
     */
    public function endCall(): self
    {
        $this->status = CallStatus::Ended;
        $this->ended_at = now();
        $this->save();

        return $this;
    }

    public function isAccepted(): bool
    {
        return $this->status === CallStatus::Accepted;
    }
    public function isEnded(): bool
    {
        return $this->status === CallStatus::Ended;
    }

    public static function getStatuses(): array
    {
        return CallStatus::cases();
    }
}
