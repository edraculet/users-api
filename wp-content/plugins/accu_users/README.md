# AccuUsers Manager Plugin

##  Description 

AccuUsers plugin allows users whose login credentials are saved externally, to be able to login in a Wordpress website.

The plugin takes an API Url and verifies against it the user email and user password a customer inputs in the login form.
First step is to check inside Wordpress database for an user with the given data.
When the user is found, it is immediately logged in.

If Wordpress does not recognize the user, then the external verification is initiated.

If the user email / password combination is found externally, the API responds with a ***200 status code***.
If there is no user account with the provided data, the API responds with a **400 status code**.

Wordpress receives the response and verifies it locally.

If status code 200 is received, an user registered with the same credentials is searched in WP database.
If the user is found then it has the permission to login.
If the user is not found in local Wordpress database, then a new user is created with the provided credentials and logged in.
After login in, user is taken to a private page.

If status code 400 is received, the user is not allowed to login to Wordpress website.


### Testing plugin

**Admin credentials:**

`Username: admin`

`Password: use_pass`


**API User data for 200 response:**

`eve.holt@reqres.in / any password`
`michael.lawson@reqres.in / any password`
`lindsay.ferguson@reqres.in / any password`


**API User data for 400 response:** 

any other input data.




