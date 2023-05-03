<h1 align="center">Laravel User Activity</h1>
<p align="center"><a href="https://packagist.org/packages/haruncpi/laravel-user-activity"><img src="https://badgen.net/packagist/v/haruncpi/laravel-user-activity" /></a>
    <a href="https://creativecommons.org/licenses/by/4.0/"><img src="https://badgen.net/badge/licence/CC BY 4.0/23BCCB" /></a>
     <a href=""><img src="https://badgen.net/packagist/dt/haruncpi/laravel-user-activity"/></a>
    <a href="https://twitter.com/laravelarticle"><img src="https://badgen.net/badge/twitter/@laravelarticle/1DA1F2?icon&label" /></a>
    <a href="https://facebook.com/laravelarticle"><img src="https://badgen.net/badge/facebook/laravelarticle/3b5998"/></a>
</p>

<p align="center">Easily monitor your user activity with beautiful responsive & easy user-interface!</p>

![Image description](previews/preview.png)

## Documentation
Checkout features & full documentation of [Laravel User Activity](https://laravelarticle.com/laravel-user-activity)

## Other Packages
- [Laravel H](https://github.com/haruncpi/laravel-h) - A helper package for Laravel Framework.
- [Laravel ID generator](https://github.com/haruncpi/laravel-id-generator) - A laravel package for custom database ID generation.
- [Laravel Simple Filemanager](https://github.com/haruncpi/laravel-simple-filemanager) - A simple filemanager for Laravel.
- [Laravel Option Framework](https://github.com/haruncpi/laravel-option-framework) - Option framework for Laravel.

### Change Log

v1.0.4
- Completely enable or disable logging by `activated` config value
- Added Base model logging compatibility

v1.0.3
- Minor improvements

v1.0.2
- Create log type added
- User model configuration
- UI ajax loading indicator

----
### Add Support For Laravel 10 and PHP >=8.0
- Creation of dynamic property Haruncpi\LaravelUserActivity\Listeners\LoginListener::$request is deprecated
- Creation of dynamic property Haruncpi\LaravelUserActivity\Listeners\LockoutListener::$request is deprecated

LockoutListener, LoginListener -> add public ?Request $request = null
- \App\User -> \App\Models\User

----
- Composer has been added to use this package in Laravel 10 project.
- The original package's github page can be accessed at the [link here](https://github.com/haruncpi/laravel-user-activity).
- Changes made in this package can be seen in the [commit link here](https://github.com/mchtylmz/laravel-user-activity-laravel10/commit/bfd328806002fc5bcfcdcbd590da8e7d4a5e5355).
- This package was first published by [haruncpi](https://github.com/haruncpi).
- Wait pull request [Link Here](https://github.com/haruncpi/laravel-user-activity/pull/45)


```bash
composer require mchtylmz/haruncpi-laravel-user-activity-laravel10
```
