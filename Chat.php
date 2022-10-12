<?php

namespace App\Models;

use App\Models\User;
use App\Repositories\SongDedicationRepository;
use App\Traits\HasMeta;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Models\Chat
 *
 * @property int $id
 * @property string|null $uuid
 * @property int|null $chat_room_id
 * @property string|null $message
 * @property int|null $user_id
 * @property array|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\ChatRoom|null $chatRoom
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|\Spatie\MediaLibrary\MediaCollections\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read User|null $user
 * @method static Builder|Chat filterByBody($keyword = null)
 * @method static Builder|Chat filterByRoomId($chat_room_id = null)
 * @method static Builder|Chat filterByUserId($user_id = null)
 * @method static Builder|Chat newModelQuery()
 * @method static Builder|Chat newQuery()
 * @method static Builder|Chat query()
 * @method static Builder|Chat whereChatRoomId($value)
 * @method static Builder|Chat whereCreatedAt($value)
 * @method static Builder|Chat whereId($value)
 * @method static Builder|Chat whereMessage($value)
 * @method static Builder|Chat whereMeta($value)
 * @method static Builder|Chat whereUpdatedAt($value)
 * @method static Builder|Chat whereUserId($value)
 * @method static Builder|Chat whereUuid($value)
 * @mixin \Eloquent
 */
class Chat extends Model implements HasMedia
{
	use HasMeta, HasUuid, InteractsWithMedia, LogsActivity;

	protected $guarded = [];
	protected $casts = [
		'meta' => 'array'
	];
	protected $table = 'chats';
	protected static $logName = 'chat';
	protected static $logFillable = [ '*' ];
	protected static $logOnlyDirty = true;
	protected static $logAttributesToIgnore = [ 'updated_at' ];

	// Relations
	public function chatRoom(): BelongsTo
	{
		return $this->belongsTo( ChatRoom::class );
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo( User::class );
	}

	// Booted
	public static function booted()
	{
		static::created( function ( self $chat ) {
			resolve( SongDedicationRepository::class )
				->filterFromChat( $chat );
		} );
	}

	protected static function ensureUpdatable(): void
	{
	}

	// Filters

	public function scopeFilterByBody( Builder $query, $keyword = null ): void
	{
		$query->when( $keyword, function ( $q, $keyword ) {
			return $q->where( 'body', 'like', '%' . $keyword . '%' );
		} );
	}

	public function scopeFilterByRoomId( Builder $query, $chat_room_id = null ): void
	{
		$query->when( $chat_room_id, function ( $q, $chat_room_id ) {
			return $q->whereChatRoomId( $chat_room_id );
		} );
	}

	public function scopeFilterByUserId( Builder $query, $user_id = null ): void
	{
		$query->when( $user_id, function ( $q, $user_id ) {
			return $q->whereUserId( $user_id );
		} );
	}
}
