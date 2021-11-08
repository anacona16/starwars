## StarWars

This simple Symfony app consumes some resource from an StarWars API.

This app is using Symfony 5.3 and is deployed in Heroku http://starwars-anacona16.herokuapp.com/ using a Postgres database.

The first time the user access to the app we download the info from the API, and then we save it to the database.
Right now the API does not provide an identifier for each resource, so we are using the url property as a primary id.

We are using a custom pagination using KNP/Paginator bundle, we display the results form the database all the time, but
we get the total results number from the API, we are doing this to draw/print a real paginator.

On each list (films and characters) we display basic info, later when the user click on any of them, we download
additional info like species.

In the character list I'm not displaying all the result in a single request, once again the pagination is working in a
real scenario.

Each time an user logs in the app we are sending a notification alert, I'm using a personal MAILER_DSN to make this work.
The "email from" and the "email to" values are environment variables configured inside Heroku. 

### Users

Password: Pass123

- user@user.com
- joe@doe.com

### Things to improve

There are somethings to improve in this solution, for example, I can find a better strategy to "compare" the already 
saved info in the database.

Would be great to have tests.

Also, we can add some interfaces for the DataNegotiation and DataRetriever classes. 
