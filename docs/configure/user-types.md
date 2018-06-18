# User Types

[User Types] are classifications that you may assign to a user at an [Organization].
This means, if a user is associated to more than one [Organization], they can be assigned a different user type depending on the Organization.

A user can be assigned to none, one or many [User Types].  In addition, the associated [User Types] can be sorted to establish a priority.

Common usages of [User Types] are:
* Departments
* Responsibilities
* Roles

### Examples
Rudy Fruity is an executive; Rudy is the CEO (<- a [User Type]) of Flipbox Enterprises.  Rudy is also a consultant (<- a [User Type]) and on the 
board (<- a [User Type]) of Spark Technologies.   This is what Rudy's [User Type] associations would look like for each company:

|                       | CEO           | Consultant    | Board
| --------------------- | :-----------: | :-----------: | :-----------: 
| Flipbox Enterprises   | X             |               |               
| Spark Technologies    |               | X             | X             

[Organization]: ../objects/organization.md
[User Types]: ../objects/user-type.md
[User Type]: ../objects/user-type.md
