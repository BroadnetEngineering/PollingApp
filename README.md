# Polling App
Design a polling application.

### Your application should accomplish the following:
* Able to create, read, update, and delete polls
* Show all available polls to take
* Count a poll taker only once, and allow them to update their answer (within reason, you shouldn't create an account system, think of another way to track a user)
* Show poll results to user after taking the poll

### The app should follow these rules:
* Use a MySQL database
* Be written in PHP for backend technology, frontend is up to you
* No existing MVC frameworks

## Notes on my submission:
Since I wasn't allowed to use a framework, I essentially half-way built a mini-framework for the purpose of the project.

All the frontend is in jQuery, and it lives in the `index.php` file.  The "controller" is the `api.php` file, which listens for AJAX POSTs from the front end.  It then communicates with the objects in the `/app` directory to do databasing, etc., and returns JSON responses to the front end.  This approach saved me having to write a router, but it means the `api.php` file is doing that work with a big nested `switch` statement, which is not my favorite thing.

The `DB` object is a super-simple DBAL implemented as a singleton.  That object also handles stuff like checking to make sure the database is set up, and if it's not, it'll create them and load up a little bit of test data.  I didn't bother making a full migration management system.

I chose the Google Charts API for the little pie charts it generates (they even include a little bonus interactivity.)

All Javascript libraries load from CDNs, and there's no webpack, node, or CSS preprocessing or anything.  It uses the default Bootstrap 4 styles for everything.

It uses cookies to store info about which polls you have answered, and which of the answers are yours.  When you change an vote, it basically deletes the vote you previously made, then lets you re-answer from scratch.  You'll have to clear cookies or use a private browser window to answer a given poll more than once in a day, though you can change your answer as much as you like.

## Installation
I'm using composer for class autoloading and a few helpers.  I'm using PHP 7.2 in my development environment, but I don't think it'll having any problem running on 5.6 if that's what you're used to.

* Clone the repo into your web directory of choice. Checkout the `Ian_Monroe` branch if you need to.
* Make sure you have an empty MySQL database set up someplace and you know the credentials.
* Copy the `.env.example` file to `.env` in the root of the project
* Edit your `.env` file to reflect your MySQL credentials.
* Pull down the composer stuff with `composer update` or `composer install`.  Either should work.
* Navigate your browser (I tested with Chrome and Netscape) to the location you put the files
* It'll automatically create the database, give you a little testing data, and it should work

### Caveats, etc.

The requirement of no frameworks slowed me down considerably.  Had that not been a requirement, I could have done the same work in half the time, and it would have been far more full-featured.  This is far from production-ready code.

* There's no CSFR protection, though it should be pretty resistant to sql injection
* It's not particularly DRY code.
* There's no tests
* There's no validation on the forms.
* The cookie-based system for preventing multiple votes isn't particularly robust; I had aspirations of building something a little fancier. To that end, the DB stores information about the IP address and user agent for each vote, even though it's not getting used for anything.  Something for future development, I guess.


