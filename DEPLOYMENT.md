# Deployment Instructions

## Quick Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/zobaer99/multipledatatask.git
   cd multipledatatask
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   # Create database 'multipledatatask' in MySQL
   php artisan migrate
   php artisan db:seed --class=TaskSeeder
   ```

5. **Run the application**
   ```bash
   php artisan serve
   ```
   Navigate to: `http://127.0.0.1:8000`

## Features Included

✅ **Complete Task Management System**
✅ **Dynamic Repeater Fields** 
✅ **Bulk Data Insertion (up to 100 tasks)**
✅ **Real-time Validation**
✅ **Performance Monitoring**
✅ **RESTful API with 7 endpoints**
✅ **Comprehensive Documentation**
✅ **Responsive Dashboard**
✅ **Navigation System**

## Performance Metrics
- **Bulk Insert**: 6,711+ tasks/second
- **API Response**: <50ms average
- **Database**: Optimized with proper indexing

## Project Structure
- **Frontend**: Bootstrap 5 + jQuery + AJAX
- **Backend**: Laravel 11 + MySQL
- **API**: RESTful with JSON responses
- **Documentation**: Complete API docs included