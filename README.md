# Book Rental Service API

This project is a Book Rental Service API built with **Laravel 11** and **PHP 8.1**. It allows users to search for books, rent them, return them, and view their rental history. The API also automatically marks rentals as overdue and sends email notifications for overdue rentals.

## Features

- **Search for Books**: Search for books by title and genre.
- **Rent Books**: Rent books for a maximum of 2 weeks.
- **Return Books**: Easily return rented books.
- **View Rental History**: Access your rental history.
- **Automatic Overdue Marking**: Rentals are automatically marked as overdue if not returned within the due date.
- **Email Notifications**: Users receive email notifications for overdue rentals.
- **Statistics**: Get stats on the most overdue books and the most popular rentals.

## Requirements

- PHP >= 8.1
- Laravel >= 11
- Composer
- MySQL or another compatible database

## Installation

Follow these steps to set up the project:

1. **Clone the repository**:
   ```bash
   git clone https://{username}:{token}@github.com/mohitsinghal0407/rental-api.git
2. **Navigate to the project directory**:
   ```bash  
   cd rental-api
3. **Setup the environment**:
   ```bash  
   cp .env.example .env
4. **Install Dependencies**:
   ```bash  
   composer install
5. **Generate the application key**:
   ```bash 
   php artisan key:generate
6. **Migrate the database**:
   ```bash 
   php artisan migrate:fresh
7. **Seed the database**:
   ```bash 
   php artisan db:seed
8. **Serve the application**:
   ```bash 
   php artisan serve
9. **Run the test cases**:
   ```bash 
   php artisan test

## Workflow

- Import the postman collection i.e. https://github.com/mohitsinghal0407/rental-api/blob/main/Books%20Rental%20API.postman_collection.json
- run the api's one by one as we need it, by doing this we have some rentals data to test the api's
- By manually or via tinker change the overdue date to see the command effect - it will make overdue true and send a email if it comes in the criteria.
- For email notification, please update the .env file and for your information, I've used mailtrap.io at local to test the email notification.

## **Command to make rentals overdue & send notifications**:
   ```bash 
   php artisan rentals:mark-overdue
