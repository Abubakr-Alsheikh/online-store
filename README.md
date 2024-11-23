# Online Store Project

This project implements a dynamic e-commerce website using a combination of front-end and back-end technologies.  While showcasing dummy products, the website emulates the core features of a typical e-commerce platform, allowing users to browse products, manage a shopping cart, and simulate the order placement and review process.


## Features

* **Dynamic Products:** Product information is dynamically retrieved from a MySQL database.
* **Interactive Shopping Cart:**  Users can add products, adjust quantities, and remove items dynamically with AJAX updates.
* **Secure User Authentication:**  Robust user registration and login system with password hashing for security.
* **Streamlined Checkout:** Guides users through providing shipping information and reviewing order summaries.
* **Order Management:** Users can access and review their order history.


## Technologies Used

* **Front-end:**
    * HTML
    * CSS
    * JavaScript
    * Bootstrap
* **Back-end:**
    * PHP
* **Database:**
    * MySQL


## Project Structure

The project is structured to promote code reusability and maintainability:

* **/includes:** Contains common components like database connection, header, and footer files.
* **index.php:** Homepage displaying featured products.
* **products.php:** Lists all available products.
* **product-details.php:** Displays detailed information about a specific product.
* **cart.php:** Shopping cart management.
* **checkout.php:** Handles order placement and processing.
* **account.php:** Displays user account information and order history.
* **login.php & register.php:** User authentication and registration.
* **add_to_cart.php:** Handles adding products to the cart via AJAX.


## Installation

1. Clone the repository: `git clone https://github.com/Abubakr-Alsheikh/online-store.git`
2. Set up a MySQL database and import the database schema (provided in `database_schema.sql`).
3. Configure the database connection in `includes/database.php`.
4. Set up a web server (e.g., Apache, XAMPP) and place the project files in the webroot directory.


## Usage

Navigate to the project's URL in your web browser. Browse products, add them to your cart, and proceed through the checkout process.  Registered users can log in to view their order history.


## Future Improvements

* User reviews and ratings
* Advanced search and filtering
* Admin dashboard for product and order management
* Integration with payment gateways
