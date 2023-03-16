# Backend for picking an item from the eshop

### Api for web app

the way it works:
+ providing stored products and its details
+ storing customer's details
+ storing customer's orders and ordered products

Api communicates with front-end via json.
The data are stored in Mysql database.

 communication from front-end to database:
1. validating the received data
2. creating a connection with the database
3. getting or storing the data from front-end when some conditions are satisfied
4. sending json response with appropriate data

 on top of that:
+ handling errors and exceptions during validation and storing or getting the data
+ handling some security issues such as html and sql injection
