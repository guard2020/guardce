# Authentication

---

- [User Login](#section-login)
- [User Logout](#section-logout)
- [Authentication](#section-authentication)

<a name="section-login"></a>
## User Login

The user is required to log in and authenticate before using the Dashboard. The User Login is the entry point of the application 
where user provides an email address and password to successfully login.

After successful authentication and authorization user can access the applications features. The browser session also 
remembers that the user is already logged in while the session is not terminated. As the Dashboard stores a session token 
in the browser session.

The Dashboard has a role and permission system that limits the functionality and permissions of the user according 
to its role.

<a name="section-logout"></a>
##User Logout

The user can safely and with one click logout from the application. This will make sure that the login session token is deleted.
After logout the user will have to perform the login again in order to access the dashboard functionalities.

<a name="section-authentication"></a>
##Authentication Middleware

The authentication system of the Dashboard works as a middleware in the entire system. This means that between each routing request
the system checks if the user logged in successfully and the session token is correct. As the session token is only known to the users browser
this assures a security that only a user using that exact browser session is able to access and perform actions as that user in the dashboard.