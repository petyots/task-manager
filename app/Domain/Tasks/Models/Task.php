<?php

namespace App\Domain\Tasks\Models;

use App\Domain\Tasks\Database\Factories\TaskFactory;
use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Domain\Tasks\Models\Task
 *
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property string $name
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read User $user
 * @method static \App\Domain\Tasks\Database\Factories\TaskFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task newQuery()
 * @method static \Illuminate\Database\Query\Builder|Task onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Task query()
 * @method static \Illuminate\Database\Query\Builder|Task withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Task withoutTrashed()
 * @mixin \Eloquent
 */
class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'status',
        'uuid',
        'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): TaskFactory
    {
        return TaskFactory::new();
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
