<?php

namespace SaliBhdr\TyphoonRate;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use SaliBhdr\TyphoonRate\Models\Rating;

/**
 * @property int $total_liked
 * @property bool $is_liked
 *
 * Trait LikeableModel
 * @package SaliBhdr\TyphoonRate
 */
trait LikeableModel
{

    protected $user_id;

    /**
     * @param $user_id
     */
    protected function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * returns user Id
     *
     * you can specify your logic by overriding this method
     *
     * @return int|null
     */
    protected function getUserId()
    {
        return $this->user_id ?? Auth::id();
    }

    /**
     * This model has many ratings.
     *
     * @return Rating | MorphMany
     */
    public function ratings()
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
        $this->ratings()->updateOrCreate(
            [
                'user_id'   => $user_id ?? $this->getUserId(),
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
        $this->ratings()
            ->where('user_id', $user_id ?? $this->getUserId())
            ->where('rate_type', 'like')
            ->where('score', 1.00)
            ->delete();
    }

    /**
     * sum of specific user votes on a specific rate subject
     *
     * @param null|int $user_id : if you want to add like for another user (other than auth)
     * @return bool
     */
    public function isLiked($user_id = null)
    {
        return $this->ratings()
            ->where('user_id', $user_id ?? $this->getUserId())
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
        return $this->ratings()
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
    public function getRateStatsAttribute()
    {
        return [
            'liked_count'   => $this->totalLiked(),
            'is_liked' => $this->isLiked(),
        ];
    }

}
