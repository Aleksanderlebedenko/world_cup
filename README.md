### WorldCup scoreboard

The application shows the scoreboard of the championship of national teams.
It has multiple national teams and every team has to have a match with each other.
Initially, we already have pairs which:

- will play
- currently playing
- already played.

After loading the page `http://localhost:8765/` upcoming events will change the scoreboard accordingly to randomly
generated events every few seconds.
If you reload the page, every data stay.
The match duration is about 20 seconds.

If you want to reset data to the initial one, you need to run the command:

    make reset-data

#### How to start

Run command:

    make build

if you are already built it, to run the application, you can use the command:

    make upd

### How to run tests

Run command:

    make test

Alternatively, you can run tests inside `php` container:

    bin/phpunit

### Tips

- If you don't have `make`, you can run commands from Makefile manually
- Docker is required to run the application.

### Before checking the code

I've tried to keep `SOLID` and other principles for this app.
But some places violate this, but in the code in the comments these places described in detail, why it is done
in this way.

### From a technical perspective

- Decided to use Symfony, because it already has all that the application needs (container, controllers, auto wiring...). It eliminates the hangover of installing or creating all these things manually and allows concentrate on the business tasks.
- As storage decided to use `redis`, please do not pay much attention to how it stores and about the logic of sync events and pairs in the storage. It was done only for demonstration. In the real project, it anyways will look totally different, except for existing appropriate interfaces.
- Also, I didn't pay much attention to security, environments, infrastructure.., because I think it is out of the scope of the task.