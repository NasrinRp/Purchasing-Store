<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActiveCode extends Model
{
    use HasFactory;

    protected $table = 'active_code';
    protected $fillable = [
        'user_id',
        'code',
        'expired_at',
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    public function scopeGenerateCode($query, $user): int
    {
        if ($activeCode = $this->checkUserHasAliveCode($user)) {
            $code = $activeCode->code;
        } else {
            do {
                $code = mt_rand(100000, 999999);
            } while ($this->checkCodeIsUnique($user, $code));

            $user->activeCodes()->create([
                'code' => $code,
                'expired_at' => now()->addMinutes(10)
            ]);
            //TODO send code for user phone number
        }

        return $code;
    }

    private function checkCodeIsUnique($user, int $code): bool
    {
        return !! $user->activeCodes()->where('code', $code)->first();
    }

    private function checkUserHasAliveCode($user)
    {
        return $user->activeCodes()->where('expired_at', '>', now())->first();
    }


}
