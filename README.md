#  FounderX – Backend (Laravel API)

Backend service for **FounderX**, an AI-powered platform that evaluates startup pitches and generates structured scoring & feedback.  

Built using **Laravel** to manage system logic, data storage, authentication, and AI service integration.

---

## 🛠 Tech Stack
- Laravel  
- RESTful APIs  
- MySQL  
- Eloquent ORM  
- Built-in Authentication  
- Postman (Testing)

---

## 🤖 AI Integration (Core Feature)
FounderX relies on an external AI model to analyze startup pitches and generate:  
- Structured scoring  
- Detailed feedback  

The backend acts as the orchestration layer between:  
- The mobile application  
- The AI service  
- The database  

**AI Flow:**  
1. User submits pitch (text input)  
2. Backend validates & stores pitch  
3. Backend sends structured request to AI service  
4. Receives score + feedback response  
5. Stores results in database  
6. Returns formatted response to Flutter app  

The backend ensures data consistency, structured communication, and secure handling of AI responses.

---

## 🔐 Authentication & Security
- Laravel Authentication system  
- Password hashing  
- Middleware-protected routes  
- Request validation  
- Controlled API access

---

## 🗄 Database Design
- Designed with scalability in mind:  
  - User → Has Many → Pitches  
  - Pitch → Has One → Evaluation  
- Normalized relational structure  
- Clear separation of responsibilities

---

## 📁 Data & File Handling
- Structured storage organization  
- Unique identifiers for pitches  
- Error handling for failed requests

---

## 🧪 API Testing
- All endpoints tested via Postman  
- Verified full request/response lifecycle  
- End-to-end testing with Flutter app

---

## 🎯 Backend Role in the System
The backend is responsible for:  
- Managing business logic  
- Securing the system  
- Handling AI communication  
- Structuring data for frontend consumption  
- Ensuring system reliability and scalability

