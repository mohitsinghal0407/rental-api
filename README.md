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
   
   cd rental-api
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate:fresh
   php artisan db:seed
   php artisan serve
    
