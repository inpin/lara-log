LaraLog
============
Important note: this product is forked and edited from base [laravel-likeable](https://github.com/rtconner/laravel-likeable) package.

[![Build Status](https://travis-ci.org/inpin/lara-log.svg?branch=master)](https://travis-ci.org/inpin/lara-log)
[![StyleCI](https://github.styleci.io/repos/139015333/shield?branch=master)](https://github.styleci.io/repos/139015333)
[![Maintainability](https://api.codeclimate.com/v1/badges/c28c17bee1e0e6482921/maintainability)](https://codeclimate.com/github/inpin/lara-log/maintainability)
[![Latest Stable Version](https://poser.pugx.org/inpin/lara-log/v/stable)](https://packagist.org/packages/inpin/lara-log)
[![Total Downloads](https://poser.pugx.org/inpin/lara-log/downloads)](https://packagist.org/packages/inpin/lara-log)
[![Latest Unstable Version](https://poser.pugx.org/inpin/lara-log/v/unstable)](https://packagist.org/packages/inpin/lara-log)
[![License](https://poser.pugx.org/inpin/lara-log/license)](https://packagist.org/packages/inpin/lara-log)

Trait for Laravel Eloquent models to allow easy implementation of a "like" or "favorite" or "remember" or what ever you want features.

#### Composer Install (for Laravel 5.5 and above)

	composer require inpin/lara-like

#### Install and then run the migrations

```php
'providers' => [
    \Inpin\LaraLike\LaraLikeServiceProvider::class,
],
```

```bash
php artisan vendor:publish --provider="Inpin\LaraLike\LaraLikeServiceProvider" --tag=migrations
php artisan migrate
```

#### Setup your models

```php
class Book extends \Illuminate\Database\Eloquent\Model {
    use Inpin\LaraLike\Likeable;
}
```

#### Sample Usage

```php
$book->like(); // like the book for current user
$book->like($user); // pass in your own user
$book->like(0); // just add likes to the count, and don't track by user
$book->like('api'); // like the book for current user with guard 'api'
$book->like(null, 'bookmark') // add book for current user to bookmarks
$book->like($user, 'bookmark') // pass user and type

$book->unlike(); // remove like from the book
$book->unlike($user); // pass in your own user id
$book->unlike(0); // remove likes from the count -- does not check for user
$book->unlike('api'); // remove like from book for current user with guard 'api'
$book->unlike(null, 'bookmark') // remove current book from current user bookmarks
$book->unlike($user, 'bookmark') // pass user and type

$book->likes; // Iterable Illuminate\Database\Eloquent\Collection of existing likes 
$book->likes()->where('type', 'bookmark')

$book->liked(); // check if currently logged in user liked the book
$book->liked($myUserId);

$book->likeCount($type); // determine number of likes for given $type (default type is 'like')

Article::whereLikedBy($myUserId) // find only books where user liked them
	->with('likeCounter') // highly suggested to allow eager load
	->get();
```
note: default type is 'like'.

#### Credits

 - Mohammad Nourinik - http://inpinapp.com
