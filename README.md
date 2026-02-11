#  FounderX – Backend (Laravel API)

![GitHub Repo stars](https://img.shields.io/github/stars/amiramahmoud77/FounderX?style=social)
![GitHub top language](https://img.shields.io/github/languages/top/amiramahmoud77/FounderX)
![GitHub repo size](https://img.shields.io/github/repo-size/amiramahmoud77/FounderX)

Backend service for **FounderX**, an AI-powered platform that evaluates startup pitches and generates structured scoring & feedback.  
Built using **Laravel**, it handles authentication, database management, and AI integration.

---

## 🛠 Tech Stack
- **Backend:** Laravel 10.x (PHP 8.x)  
- **Database:** MySQL  
- **ORM:** Eloquent  
- **Authentication:** Built-in Laravel Auth  
- **Testing:** Postman  

---

## 🤖 AI Integration
The core feature is analyzing startup pitches using an external AI model:  
- Generates structured scoring and detailed feedback  
- Ensures secure communication and data consistency  
- Orchestrates requests between the mobile app, AI service, and database  

**AI Flow:**  
1. User submits pitch (text input)  
2. Backend validates & stores pitch  
3. Sends request to AI service  
4. Receives scoring & feedback  
5. Stores results in database  
6. Returns formatted response to Flutter app

---

## 🔐 Authentication & Security
- User authentication (register/login)  
- Password hashing  
- Middleware-protected routes  
- Request validation  
- Controlled API access

---

## 🗄 Database Design
- Scalable relational structure:  
  - **User → Has Many → Pitches**  
  - **Pitch → Has One → Evaluation**  
- Normalized schema for clarity and maintainability  
- Handles data relationships cleanly for frontend consumption

---

## 📁 Data & File Handling
- Structured storage organization  
- Unique identifiers for pitches  
- Error handling for failed requests  

---

## 🧪 API Endpoints
| Method | Endpoint            | Description              |
|--------|--------------------|--------------------------|
| POST   | `/api/register`     | Register a new user      |
| POST   | `/api/login`        | Login user               |
| GET    | `/api/pitches`      | Get all startup pitches  |
| POST   | `/api/pitches`      | Create a new pitch       |
| GET    | `/api/investors`    | Get all investors        |
| POST   | `/api/feedback`     | Submit feedback          |

---

## 🎯 Backend Responsibilities
- Manage business logic  
- Secure system data  
- Handle AI communication  
- Structure data for frontend use  
- Ensure system reliability and scalability


   git clone https://github.com/amiramahmoud77/FounderX.git

