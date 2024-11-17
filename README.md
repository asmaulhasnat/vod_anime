# vod_anime
 
# Anime Application

This project consists of an application with two main functionalities:
1. Data Import - Fetching data about popular anime from the Jikan API and saving it to a MySQL database.
2. API Endpoints - Providing endpoints to retrieve anime data by slug.

## Requirements

- PHP 8.0+
- Laravel 11
- MySQL
- Composer

## Setup

1. **Clone the Repository**
   ```bash
   git clone https://github.com/asmaulhasnat/vod_anime.git
2. **Install Dependencies**
   ```bash
   composer install
3. **Set Up Environment Variables**
   ```bash
   cp .env.example .env
- Open the .env file in a text editor and set up your database details and other setting
   ```bash
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=laravel
    DB_USERNAME=root
    DB_PASSWORD=


    ANIME_API_PATH='https://api.jikan.moe/v4/top/anime?type=ova'
- Replace laravel, root, and your_password with your actual database name, username, and password for database  and other setting as you need.

4. **SGenerate Application Key**
   ```bash
   php artisan key:generate

5. **Run Migrations**
   ```bash
   php artisan migrate

6. **Fetch Anime Data**
  - Use the following command to fetch the 25 most popular anime from the Jikan API and save it in the database. For more data  run this command multiple times.
    ```bash
   php artisan fetch:top-ova-anime

7. **Run the Server**
- To start the application, run the Laravel development server:
  ```bash
   php artisan serve

8. **Testing API Path**
   ```bash
    http://127.0.0.1:8000/api/anime/Monster