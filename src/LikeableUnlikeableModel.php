<?php

namespace SaliBhdr\TyphoonRate;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use SaliBhdr\TyphoonRate\Models\Rating;

/**
 * @property int $total_liked
 * @property bool $is_liked
 * @property bool $like_stats
 *
 * Trait LikeableUnlikeableModel
 * @package SaliBhdr\TyphoonRate
 */
trait LikeableUnlikeableModel
{

    protected $user_id;

    /**
     * @param $user_id
     * @return LikeableUnlikeableModel
     */
    protected function setLikedUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * returns user Id
     *
     * you can specify your logic by overriding this method
     *
     * @return int|null
     */
    protected function getLikedUserId()
    {
        return $this->user_id ?? Auth::id();
    }

    /**
     * This model has many ratings.
     *
     * @return Rating | MorphMany
     */
    public function likings()
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    /**
     * like an specific ratable subject once
     *
     * with this method user can only vote once for a rateable
     * any time the user tries to like that subject again the like score would be updated
     *
     * @param null|int $user_id : if you want to add like for another user (other than auth)
     */
    public function like($user_id = null)
    {
        $this->likings()->updateOrCreate(
            [
                'user_id'   => $user_id ?? $this->getLikedUserId(),
                'rate_type' => 'like',
            ],
            [
                'score' => 1
            ]
        );
    }

    /**
     * delete the record and unlike it
     *
     * @param null|int $user_id : if you want to add like for another user (other than auth)
     * @throws \Exception
     */
    public function unlike($user_id = null)
    {
        $this->likings()
            ->where('user_id', $user_id ?? $this->getLikedUserId())
            ->where('rate_type', 'like')
            ->where('score', 1.00)
            ->delete();
    }


    /**
     * toggle like and unlike
     *
     * @param int|null $user_id
     * @throws \Exception
     */
    public function toggleLike($user_id = null)
    {
        $likedRecord = $this->likings()
            ->where('user_id', $user_id ?? $this->getLikedUserId())
            ->where('rate_type', 'like')
            ->where('score', 1.00)
            ->first();

        if (isset($likedRecord))
            $this->unlike($user_id);
        else
            $this->like($user_id);
    }

    /**
     * sum of specific user votes on a specific rate subject
     *
     * @param null|int $user_id : if you want to add like for another user (other than auth)
     * @return bool
     */
    public function isLiked($user_id = null)
    {
        return $this->likings()
            ->where('user_id', $user_id ?? $this->getLikedUserId())
            ->where('rate_type', 'like')
            ->where('score', 1.00)
            ->exists();
    }

    /**
     * sum of specific user votes on a specific rate subject
     *
     * @return bool
     */
    public function totalLiked()
    {
        return $this->likings()
            ->where('rate_type', 'like')
            ->where('score', 1.00)
            ->count();
    }

    /**
     * sum of specific user votes on a specific rate subject (in attribute form)
     *
     * @return bool
     */
    public function getIsLikedAttribute()
    {
        return $this->isLiked();
    }

    /**
     * total number of likes on specific subject (in attribute form)
     *
     * @return int
     */
    public function getTotalLikedAttribute()
    {
        return $this->totalLiked();
    }

    /**
     * get all statistics about user in an array
     *
     * @return array
     */
    public function getLikeStats()
    {
        return [
            'total_likes' => $this->totalLiked(),
            'is_liked'    => $this->isLiked(),
        ];
    }

    /**
     *
     * get all statistics about user in an array
     *
     * @return array
     */
    public function getLikeStatsAttribute()
    {
        return $this->getLikeStats();
    }

}
