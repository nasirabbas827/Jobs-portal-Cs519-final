# Job Portal - Your Gateway to Opportunities

Jobs-portal-Cs519-final is a recruitment platform for candidates and recruiters.

![PHP](https://img.shields.io/badge/PHP-2563EB?style=flat-square) ![MySQL](https://img.shields.io/badge/MySQL-0F172A?style=flat-square)

## Overview

The data model covers `applications`, `candidates`, `jobs`, `messages`, `notifications`, `recruiters`, `users`.

## Application areas

| Area | Pages in the repository |
| --- | --- |
| Candidate | [`candidate/apply_job.php`](candidate/apply_job.php), [`candidate/candidate_dashboard.php`](candidate/candidate_dashboard.php), [`candidate/job_details.php`](candidate/job_details.php), [`candidate/reply_message.php`](candidate/reply_message.php), [`candidate/update_profile.php`](candidate/update_profile.php) |
| Root Pages | [`handle_reset_password.php`](handle_reset_password.php), [`login.php`](login.php), [`register.php`](register.php), [`reset_password.php`](reset_password.php) |
| Recruiter | [`recruiter/add_job.php`](recruiter/add_job.php), [`recruiter/application_details.php`](recruiter/application_details.php), [`recruiter/edit_job.php`](recruiter/edit_job.php), [`recruiter/generate_report.php`](recruiter/generate_report.php), [`recruiter/recruiter_dashboard.php`](recruiter/recruiter_dashboard.php) |

## Getting started

### 1. Get the project

```bash
git clone https://github.com/nasirabbas827/Jobs-portal-Cs519-final.git
cd Jobs-portal-Cs519-final
```

### 2. Configure PHP and the database

1. Use a local PHP server with MySQL or MariaDB, such as XAMPP.
2. Import [`Database/jobs_db.sql`](Database/jobs_db.sql) into your local database server.
3. Set your local database connection details in [`candidate/config.php`](candidate/config.php), [`config.php`](config.php), [`recruiter/config.php`](recruiter/config.php).
4. Keep the existing asset and upload directory paths intact.

### 3. Start the application

With the database running, start a local development server from the repository root:

```bash
php -S 127.0.0.1:8000
```

Open http://127.0.0.1:8000/index.php. If using Apache/XAMPP instead, place the project in its document root and open the matching localhost folder.

## Repository guide

| Path | Purpose |
| --- | --- |
| [`Database`](Database) | Database scripts |
| [`config.php`](config.php) | Application/database configuration |
| [`css`](css) | Stylesheets |
| [`images`](images) | Image assets |
| [`index.php`](index.php) | Web entry page |

## Development notes

These setup instructions are based on the repository source. The application has not been executed as part of this documentation update; verify dependencies and configuration in your local environment.
