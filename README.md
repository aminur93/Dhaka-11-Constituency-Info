# A service-based web application for Dhaka-11 constituency

A service-based web application for Dhaka-11 constituency, providing comprehensive information about the elected representative, voter demographics, and civic resources. Built with a modular microservices architecture, it offers RESTful APIs, an interactive dashboard, search and filter functionality, and scalable deployment using Docker and AWS, enabling efficient data management, transparency, and civic engagement.
---

## Live Demo

---

## Description

A service-based web application for Dhaka-11 constituency, providing detailed information about the elected representative Dr. M. A. Kaiyum. Built with modular microservices, RESTful APIs, and scalable architecture, this app enables easy management of constituency data, voter info, and civic resources for educational and engagement purposes.

---

## Features

* AREA & CONSTITUENCY MANAGEMENT
* SERVICE REQUEST MODULES
* VOLUNTEER & FIELD TEAM MODULE 
* COMMUNICATION & ENGAGEMENT
* ISSUE TRACKING & ANALYTICS
* NOTIFICATIONS
* AUDIT LOG
* Role-based Access Control (Admin, Teacher, Student)
* REST API based architecture
* Interactive Dashboard
* Search and Filter functionality

---

## Architecture

The system follows a **modular architecture**:

```
Frontend (Next.js)
│
▼
API Gateway / Auth Service
│
▼
Backend (Laravel API)
│
▼
Database (PostgreSQL)
```

**Highlights:**

* Frontend communicates via RESTful APIs.
* Backend is Laravel based.
* Docker used for containerization.
* Cloud deployment on AWS (EC2 for server, S3 for static/media files).

---

## Technology Stack

* **Backend:** Laravel (PHP)
* **Frontend:** Next.js (React)
* **Database:** PostgreSQL
* **Continuous Integration:** GitHub Actions
* **Continuous Deployment:** Docker
* **Cloud Hosting:** AWS EC2, AWS S3

---

## Roadmap

* [x] Backend (Laravel) setup
* [x] Frontend (Next.js) setup
* [x] PostgreSQL database design
* [x] Docker containerization
* [ ] Admin panel for content management
* [ ] Analytics dashboard
* [ ] Notifications system

---

## Future Improvements

* AI-assisted student performance analysis
* Multi-language support
* Mobile-friendly PWA version
* Role-based access control enhancements

---

## Installation

1. Clone the repository:

```bash
git clone https://github.com/aminur93/dhaka-11-constituency-info.git
cd dhaka-11-constituency-info
```

2. Start Docker containers:

```bash
docker-compose up -d
```

3. Backend setup:

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
```

4. Frontend setup:

```bash
cd frontend
cp .env.example .env
npm install
npm run dev
```

---

## Environment Variables

**Backend (.env)**

```env
APP_NAME=dhaka-11
APP_ENV=local
APP_KEY=
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=dhaka-11
DB_USERNAME=postgres
DB_PASSWORD=yourpassword
```

**Frontend (.env)**

```env
NEXT_PUBLIC_API_BASE_URL=http://localhost:8000/api
```

---

## Contribution

* Fork the repository
* Create your feature branch (`git checkout -b feature/your-feature`)
* Commit your changes (`git commit -m 'Add new feature'`)
* Push to branch (`git push origin feature/your-feature`)
* Open a pull request

---

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

**Happy Coding 🚀**

