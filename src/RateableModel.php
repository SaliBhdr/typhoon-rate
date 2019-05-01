<?php

namespace SaliBhdr\TyphoonRate;

use SaliBhdr\TyphoonRate\Models\Rating;
use Illuminate\Support\Facades\Auth;

trait RateableModel
{
    /**
     * get percentage of ratings
     *
     * @param int $numberOfStars
     * @return float|int
     */
    public function ratingPercent($numberOfStars = 5)
    {
        $quantity = $this->ratingCount();
        $total = $this->sumRating();

        return ($quantity * $numberOfStars) > 0
            ? ($total * 100) / ($quantity * $numberOfStars)
            : 0;
    }

    /**
     * total number of votes on specific subject
     *
     * @return mixed
     */
    public function ratingCount()
    {
        return $this->ratings()->count();
    }

    /**
     * This model has many ratings.
     *
     * @return Rating
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
        $score = $this->ratings()->sum('score');

        return is_null($score) ? $score : (float) $score;
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
        if (is_null($user_id)) {
            $user_id = Auth::id();
        }

        $rating = new Rating();
        $rating->score = $score;
        $rating->user_id = $user_id;

        $this->ratings()->save($rating);
    }

    /**
     * rate an specific ratable subject
     *
     * with this method user can only vote once for a rateable
     * any time the user tries to vote for that subject again the vote score would be updated
     *
     * @param $score
     * @param null $user_id
     */
    public function rateOnce($score, $user_id = null)
    {
        if (is_null($user_id)) {
            $user_id = Auth::id();
        }

        $this->ratings()->updateOrCreate(
            [
                'user_id' => $user_id,
            ],
            [
                'score' => $score
            ]
        );
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
     * average rating of a rateable
     *
     * @return float
     */
    public function averageRating()
    {
        $score = $this->ratings()->avg('score');

        return is_null($score) ? $score : (float) $score;
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
     * average rating of specific user
     *
     * @return float
     */
    public function userAverageRating()
    {
        $score = $this->ratings()->where('user_id', Auth::id())->avg('score');

        return is_null($score) ? $score : (float) $score;
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
     * sum of specific user votes on a specific rate subject
     *
     * @return float
     */
    public function userSumRating()
    {
        $score = $this->ratings()->where('user_id', Auth::id())->sum('score');

        return is_null($score) ? $score : (float) $score;
    }

    /**
     * total number of votes on specific subject (in attribute form)
     *
     * @return float
     */
    public function getTotalVotesAttribute()
    {
        return $this->ratingCount();
    }
}
