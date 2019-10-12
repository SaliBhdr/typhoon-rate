<?php

namespace SaliBhdr\TyphoonRate;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use SaliBhdr\TyphoonRate\Models\Rating;

/**
 * @property int $average_rating
 * @property int $sum_rating
 * @property int $user_average_rating
 * @property int $user_sum_rating
 * @property int $total_votes
 * @property array $rate_stats
 *
 * Trait RateableModel
 * @package SaliBhdr\TyphoonRate
 */
trait RateableModel
{
    protected $user_id;

    /**
     * @param $user_id
     * @return RateableModel
     */
    protected function setRateUserId($user_id)
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
    protected function getRateUserId()
    {
        return $this->user_id ?? Auth::id();
    }

    /**
     * number of stars that you set for model
     *
     * @return int
     */
    protected function maxRatePoint()
    {
        return 5;
    }

    /**
     * get percentage of ratings
     *
     * @return float|int
     */
    public function ratingPercent()
    {
        $maxRatePoint = $this->maxRatePoint();
        $quantity     = $this->totalVotes();
        $total        = $this->sumRating();

        return ($quantity * $maxRatePoint) > 0
            ? ($total * 100) / ($quantity * $maxRatePoint)
            : 0;
    }

    /**
     * total number of votes on specific subject
     *
     * @return integer
     */
    public function totalVotes()
    {
        return $this->ratings()
            ->where('rate_type', 'star')
            ->count();
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
     * get sum of all votes (in attribute form)
     *
     * @return mixed
     */
    public function sumRating()
    {
        $score = $this->ratings()
            ->where('rate_type', 'star')
            ->sum('score');

        return is_null($score)
            ? $score
            : (float)$score;
    }

    /**
     * rate an specific ratable subject
     *
     * with this method user can rate as many times as they like
     *
     * @param $score
     * @param null $user_id : if you want to add rating for another user (other than auth)
     */
    public function rate($score, $user_id = null)
    {
        $rating            = new Rating();
        $rating->score     = $score;
        $rating->user_id   = $user_id ?? $this->getRateUserId();
        $rating->rate_type = 'star';

        $this->ratings()->save($rating->toArray());
    }

    /**
     * rate an specific ratable subject once
     *
     * with this method user can only vote once for a rateable
     * any time the user tries to vote for that subject again the vote score would be updated
     *
     * @param $score
     * @param null $user_id
     */
    public function rateOnce($score, $user_id = null)
    {
        $this->ratings()->updateOrCreate(
            [
                'user_id'   => $user_id ?? $this->getRateUserId(),
                'rate_type' => 'star'
            ],
            [
                'score' => $score
            ]
        );
    }

    /**
     * average rating of a rateable
     *
     * @return float
     */
    public function averageRating()
    {
        $score = $this->ratings()
            ->where('rate_type', 'star')
            ->avg('score');

        return is_null($score)
            ? $score
            : (float)$score;
    }

    /**
     * average rating of specific user
     *
     * @param null $user_id
     * @return float
     */
    public function userAverageRating($user_id = null)
    {
        $score = $this->ratings()
            ->where('rate_type', 'star')
            ->where('user_id', $user_id ?? $this->getRateUserId())
            ->avg('score');

        return is_null($score)
            ? $score
            : (float)$score;
    }

    /**
     * sum of specific user votes on a specific rate subject
     *
     * @return float
     */
    public function userSumRating()
    {
        $score = $this->ratings()
            ->where('rate_type', 'star')
            ->where('user_id', $this->getRateUserId())
            ->sum('score');

        return is_null($score)
            ? $score
            : (float)$score;
    }

    /**
     *
     * average rating of a rateable (in attribute form)
     *
     * @return float
     */
    public function getAverageRatingAttribute()
    {
        return $this->averageRating();
    }

    /**
     * get sum of all votes (in attribute form)
     *
     * @return float
     */
    public function getSumRatingAttribute()
    {
        return $this->sumRating();
    }

    /**
     *  average rating of specific user (in attribute form)
     *
     * @return float
     */
    public function getUserAverageRatingAttribute()
    {
        return $this->userAverageRating();
    }

    /**
     * sum of specific user votes on a specific rate subject (in attribute form)
     *
     * @return float
     */
    public function getUserSumRatingAttribute()
    {
        return $this->userSumRating();
    }

    /**
     * total number of votes on specific subject (in attribute form)
     *
     * @return float
     */
    public function getTotalVotesAttribute()
    {
        return $this->totalVotes();
    }

    /**
     * get all statistics about user in an array
     *
     * @return array
     */
    public function getRateStats()
    {
        return [
            'avg_rating'      => $this->averageRating(),
            'user_avg_rating' => $this->userAverageRating(),
            'total_votes'     => $this->totalVotes(),
            'percentage'      => $this->ratingPercent(),
            'sum'             => $this->sumRating(),
        ];
    }
    /**
     * get all statistics about user in an array(in attribute form)
     *
     * @return array
     */
    public function getRateStatsAttribute()
    {
        return $this->getRateStats();
    }

}
