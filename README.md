# 🧠 AI Vector Search – Category & Embedding Import

This Laravel 9-based project provides a system to manage **categories, subcategories, services**, and their associated **keyword embeddings** using data from an Excel file.

---

## 🚀 Setup Instructions

### 1. Clone the Repository

git clone https://github.com/Akshaysinh4101/ai_vector_search.git
cd ai_vector_search


### 2. Install Dependencies

composer install

### 3. Copy .env and Configure

cp .env.example .env

Then open the .env file and update your database configuration:

DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password


### 4. Run Migrations
php artisan migrate


### 5. Import Categories from Excel

Excel file is located in:
storage/app/files/categories.xlsx


### 6. Run Import Command
php artisan import:categories categories.xlsx

### 7.  Start the Server
php artisan serve
