# Typhoon Rate

![Salibhdr|typhoon](https://drive.google.com/a/domain.com/thumbnail?id=12yntFCiYIGJzI9FMUaF9cRtXKb0rXh9X)

[![Total Downloads](https://poser.pugx.org/SaliBhdr/typhoon-rate/downloads)](https://packagist.org/packages/SaliBhdr/typhoon-rate)
[![Latest Stable Version](https://poser.pugx.org/SaliBhdr/typhoon-rate/v/stable)](https://packagist.org/packages/SaliBhdr/typhoon-rate)
[![Latest Unstable Version](https://poser.pugx.org/SaliBhdr/typhoon-rate/v/unstable)](https://packagist.org/packages/SaliBhdr/typhoon-rate)
[![License](https://poser.pugx.org/SaliBhdr/typhoon-rate/license)](https://packagist.org/packages/SaliBhdr/typhoon-rate)

## Introduction


Typhoon Rate is a Laravel package that automatically adds rating, star and liking functionality to model.

Available rate methods in this package:

  1) **Rate System** : Rate model based a certain score
  2) **Like and unlike System** : Like and unlike model
  2) **Like and dislike System** : Like and dislike model

## Installation

#### Install with Composer
```php
 $ composer require salibhdr/typhoon-rate
```
## Getting started

After installing the Typhoon Rate library, register the SaliBhdr\TyphoonCache\ServiceProviders\TyphoonRateableServiceProvider::class in your config/app.php configuration file:

##### Laravel

----

```php
'providers' => [

     // Other service providers...
     
     SaliBhdr\TyphoonCache\ServiceProviders\TyphoonCacheServiceProvider::class,
],
```


##### Lumen

----

Register The Service Provider In bootstrap/app.php:
```php
$app->register(SaliBhdr\TyphoonCache\ServiceProviders\TyphoonRateableServiceProvider::class);
```

Copy the package migration with the publish command:

```php
php artisan vendor:publish --provider="SaliBhdr\TyphoonCache\ServiceProviders\TyphoonRateableServiceProvider"
```
It will generate the `<timestamp>_create_ratings_table.php` migration. You may now run it with the artisan migrate command:

```php
php artisan migrate
```

## Usage

### 1) Rate System:

----

Typhoon rate uses `SaliBhdr\TyphoonRate\RateableModel` trait to rate models.

first use this trait in your model :

```php
<?php

    namespace App;
    
    use Illuminate\Database\Eloquent\Model;
    use SaliBhdr\TyphoonRate\RateableModel;
    
    class Book extends Model
    {
        use RateableModel;
    }
```

Then you can define the number of stars fot the model by overriding maxRatePoint() method, default is standard 5 star method.

You can even specify the method that specifies the user id for rating purposes by overriding getRateUserId(), default is auth id.


```php
<?php

    namespace App;
    
    use Illuminate\Database\Eloquent\Model;
    use SaliBhdr\TyphoonRate\RateableModel;
    use Illuminate\Support\Facades\Auth;
    
    class Book extends Model
    {
        use RateableModel;
        
            /**
             * returns user Id
             *
             * you can specify your logic by overriding this method
             *
             * @return int|null
             */
            protected function getRateUserId()
            {
                return Auth::id();
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
    }
```


If you want to let user to rate a subject more than once :
 
```php

$record = Book::find($record_id);

$record->rate(4);

```

If you want to let user to rate a subject only once :

```php

$record = Book::find($record_id);

$record->rateOnce(4);

```

If you want a user to rate other than auth user, you can specify another user:


```php

$record = Book::find($record_id);

$record->rate(4,$user_id);

$record->rateOnce(4,$user_id);

/*--Or you can set user before rate----*/

$record->setRateUserId($user_id)->rate(4);

$record->setRateUserId($user_id)->rateOnce(4);

```

Here Are the methods that you can retrieve the stats about the record:

```php

$record = Book::find($record_id);

/*--------average rating of record for all users-----*/
$record->averageRating();

/*--------average rating of record for auth user-----*/
//1- if you don't specify a user it will get avrage rate of auth user
//2- if user rated once then avg rating is the actual rate
$record->userAverageRating();

/*-------- avrage rating for specific user-----*/
$record->userAverageRating($user_id);
//or
$record->setRateUserId($user_id)->userAverageRating();

/*-------- number of votes -----*/
$record->totalVotes();

/*-------- rate perecentage -----*/
// avrage_rating / total
$record->ratingPercent();

/*-------- total rate sum -----*/
$record->sumRating();

```

If you only work with authenticated user you can use properties instead of methods. 
laravel will use magic methods to retrieve this properties : 

```php

$record = Book::find($record_id);

/*--------average rating of record for all users-----*/
$record->average_rating;

/*--------average rating of record for auth user-----*/
//1- if you don't specify a user it will get avrage rate of auth user
//2- if user rated once then avg rating is the actual rate
$record->user_average_rating;

/*-------- number of votes -----*/
$record->total_votes;

/*-------- rate perecentage -----*/
// avrage_rating / total
$record->rating_percent;

/*-------- total rate sum -----*/
$record->sum_rating;

```

If You want to get all the data above in one array you can do in like this:


```php

/*--------average rating of record for all users-----*/
$record->getRateStats();
```
in property form:

```php

/*--------average rating of record for all users-----*/
$record->rate_stats;
```

The two methods above will return an array of all statistics:

```php

dd($record->rate_stats);

@example :
        //example of 5 star format
        [
            'avg_rating'      => 2.5,
            'user_avg_rating' => 5,
            'total_votes'     => 1124,
            'percentage'      => 50%, 
            'sum'             => 2810,
        ];
```

### 1) Like and unlike System :

----

Typhoon rate uses `SaliBhdr\TyphoonRate\LikeableUnlikeableModel` trait to like and unlike models.

like and unlike system only tracks likes and dislikes does'nt work with this trait.


first use this trait in your model :

```php
<?php

    namespace App;
    
    use Illuminate\Database\Eloquent\Model;
    use SaliBhdr\TyphoonRate\LikeableUnlikeableModel;
    
    class Book extends Model
    {
        use LikeableUnlikeableModel;
    }
```

You can even specify the method that specifies the user id for rating purposes by overriding getLikeUserId(), default is auth id.

```php
<?php

    namespace App;
    
    use Illuminate\Database\Eloquent\Model;
    use SaliBhdr\TyphoonRate\LikeableUnlikeableModel;
    use Illuminate\Support\Facades\Auth;
    
    class Book extends Model
    {
        use LikeableUnlikeableModel;
        
            /**
             * returns user Id
             *
             * you can specify your logic by overriding this method
             *
             * @return int|null
             */
            protected function getLikeUserId()
            {
                return Auth::id();
            }
           
    }
```

**Notice:** Any method that has user_id input, you can specify user_id like this too:
```php
@example

$record->like($user_id);
$record->setLikeUserId($user_id)->like();

```

If you want auth user to like the subject :

```php

$record = Book::find($record_id);

$record->like();
$record->unlike();

```

If you want a user to like other than auth user, you can specify another user:

```php

$record = Book::find($record_id);

$record->like($user_id);
$record->unlike($user_id);

/*--Or you can set user before rate----*/

$record->setLikeUserId($user_id)->like();
$record->setLikeUserId($user_id)->unlike();



You can toggle like with toggleLike() method , this method will automatically like and unlike subject:

```php

$record = Book::find($record_id);

// for auth user
$record->toggleLike();

//or for none auth users 
$record->toggleLike($user_id);
$record->setLikeUserId($user_id)->toggleLike();

```

Here Are the methods that you can retrieve the stats about the record:

```php

$record = Book::find($record_id);

/*--------total likes of record for all users-----*/
$record->totalLiked();

/*--------shows that is user liked the record for auth user-----*/
//1- if you don't specify a user it will get like of auth user
$record->isLiked();

/*-------- like for specific user-----*/
$record->isLiked($user_id);
//or
$record->setRateUserId($user_id)->isLiked();


```

If you only work with authenticated user you can use properties instead of methods. 
laravel will use magic methods to retrieve this properties : 

```php

$record = Book::find($record_id);

/*--------total likes of record for all users-----*/
$record->total_liked;

/*--------shows that is user liked the record for auth user-----*/
//1- if you don't specify a user it will get like of auth user
$record->is_liked;

```

If You want to get all the data above in one array you can do in like this:


```php

/*--------like stats of record for all users-----*/
$record->getLikeStats();
```
in property form:

```php

/*--------like stats of record for all users-----*/
$record->like_stats;
```

The two methods above will return an array of all statistics:

```php

dd($record->like_stats);

@example :
        //example of like and unlike
        [
            'total_likes' => 50,
            'is_liked'    => true,
        ];
```


### 1) Like and dislike System :

----

Typhoon rate uses `SaliBhdr\TyphoonRate\LikeableDislikeableModel` trait to like and dislike models.

like and dislike system will track both likes and dislikes of users.


first use this trait in your model :

```php
<?php

    namespace App;
    
    use Illuminate\Database\Eloquent\Model;
    use SaliBhdr\TyphoonRate\LikeableDislikeableModel;
    
    class Book extends Model
    {
        use LikeableDislikeableModel;
    }
```

You can even specify the method that specifies the user id for rating purposes by overriding getLikeUserId(), default is auth id.

```php
<?php

    namespace App;
    
    use Illuminate\Database\Eloquent\Model;
    use SaliBhdr\TyphoonRate\LikeableDislikeableModel;
    use Illuminate\Support\Facades\Auth;
    
    class Book extends Model
    {
        use LikeableDislikeableModel;
        
            /**
             * returns user Id
             *
             * you can specify your logic by overriding this method
             *
             * @return int|null
             */
            protected function getLikeUserId()
            {
                return Auth::id();
            }
           
    }
```


like and toggle like are the same as above but instead of unlike you must user dislike
```php

$record = Book::find($record_id);

//for auth user
$record->dislike();

//for another user
$record->dislike($user_id);
$record->setLikeUserId($user_id)->dislike();

```

Here Are the methods that you can retrieve the stats about the record:

```php

$record = Book::find($record_id);

$record->isLiked(); // accepts user_id to

$record->isDisliked(); // accepts user_id to

$this->totalVotes(),
$this->totalLiked(),
$this->totalDisliked(),


If you only work with authenticated user you can use properties instead of methods. 
laravel will use magic methods to retrieve this properties : 

```php

$record->is_liked; // is liked by user

$record->is_disliked; // is disliked by user

$this->total_votes; //sum of like and dislikes
$this->total_liked; // total likes
$this->total_disliked; // total dislikes

```

If You want to get all the data above in one array you can do in like this:


```php

/*--------like stats of record for all users-----*/
$record->getLikeStats();
```
in property form:

```php

/*--------like stats of record for all users-----*/
$record->like_stats;
```

The two methods above will return an array of all statistics:

```php

dd($record->like_stats);

@example :
        //example of like and dislike
        [
            'total_votes'    => 30,
            'total_likes'    => 20,
            'total_dislikes' => 10,
            'is_liked'       => true,
            'is_disliked'    => false,
        ];
```

----

**Notice :** You can use rate and like system for one model at once but you can not use
both unlike and dislike method together it will throw an error.

## Todos

 - Write Tests
 - Add More methods
 - add charts 
 - like and dislike percentage
 
License
----
Typhoon-Rate is released under the MIT License.

Built with ❤ for you.

**Free Software, Hell Yeah!**

Contributing
----
Contributions, useful comments, and feedback are most welcome!
