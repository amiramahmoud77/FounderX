# FounderX – Backend (Laravel API)

Backend service for **FounderX**, an AI-powered platform that evaluates startup pitches and generates structured scoring & feedback.

Built with **Laravel 10**, it handles user authentication, pitch storage, AI integration, and secure API responses for the mobile app.

## Demo

Or click here:  
[View Demo Video on Google Drive →](https://drive.google.com/file/d/19VU5X4wPSf-ZCf1DGChqESOXFAHWUT4U/view?usp=sharing)
## 🛠 Tech Stack

- **Backend**: Laravel 10.x (PHP 8.x)
- **Database**: MySQL
- **ORM**: Eloquent
- **Authentication**: Laravel Built-in Auth + Sanctum (API tokens)
- **AI Integration**: External AI model (via HTTP requests)
- **Testing**: Postman / Laravel PHPUnit
- **Deployment**: Ready for Laravel Forge / Vapor / Heroku

## 🤖 AI Integration Flow

1. User submits pitch text via Flutter app  
2. Backend validates & stores pitch  
3. Sends pitch to external AI service  
4. Receives structured scoring + detailed feedback  
5. Saves evaluation in DB  
6. Returns formatted JSON to Flutter app  

## 🔐 Security & Authentication

- Register / Login endpoints  
- Password hashing (bcrypt)  
- Sanctum API tokens for protected routes  
- Request validation & middleware  
- Rate limiting (optional)

## 🗄 Database Schema (Simplified)

- **users** → id, name, email, password  
- **pitches** → id, user_id, title, description, created_at  
- **evaluations** → id, pitch_id, score (json), feedback (text), created_at  

## 📁 API Endpoints (Main)

| Method | Endpoint              | Description                     | Auth Required |
|--------|-----------------------|---------------------------------|---------------|
| POST   | /api/register         | Register new user               | No            |
| POST   | /api/login            | Login & get token               | No            |
| GET    | /api/pitches          | Get user's pitches              | Yes           |
| POST   | /api/pitches          | Create new pitch                | Yes           |
| GET    | /api/pitches/{id}     | Get pitch details + evaluation  | Yes           |
| POST   | /api/feedback         | Submit manual feedback (admin)  | Yes           |

Full API docs coming soon (Swagger / Postman collection).
## 🚀 Installation & Run

Follow these steps to set up and run the backend locally:

1. **Clone the repository**  
   ```bash
   git clone https://github.com/amiramahmoud77/FounderX.git
   cd FounderX
   ```

2. **Install dependencies**  
   ```bash
   composer install
   ```

3. **Copy and configure the .env file**  
   ```bash
   cp .env.example .env
   ```
   Then open `.env` and update the following:

   ```env
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```
   Also add your AI service credentials (example):
   ```env
   AI_SERVICE_URL=https://api.example.com/analyze
   AI_API_KEY=sk-your-api-key-here
   ```

4. **Generate application key & run migrations**  
   ```bash
   php artisan key:generate
   php artisan migrate
   ```

5. **Start the Laravel development server**  
   ```bash
   php artisan serve
   ```
   → The API will be live at:  
   **http://127.0.0.1:8000**

6. **Test the API endpoints**  
   Use **Postman**, **Insomnia**, or **cURL**  
   Base URL: `http://127.0.0.1:8000/api`

   Example endpoints to try:
   - `POST /api/register` → Register new user
   - `POST /api/login` → Get auth token
   - `POST /api/pitches` → Submit a startup pitch













