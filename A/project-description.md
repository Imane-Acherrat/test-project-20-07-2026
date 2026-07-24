# Test Project Outline – Module A – Laravel SSR

## Competition Time

4 hours

---

# Introduction

Module A focuses on building a Laravel Server-Side Rendering (SSR) application that processes industrial sensor logs from a smart factory.

The objective is to import large CSV files containing sensor readings, filter only important machine anomalies, store them in a database, and provide engineers with a simple dashboard to monitor critical alerts.

---

## Scenario

You are developing an internal monitoring tool for a fully automated factory.

Thousands of IoT sensors continuously monitor robotic arms and industrial machines. Every second, they generate status logs containing information such as:

- Temperature
- Vibration amplitude
- Error codes
- Machine unit
- Sensor identifier

Since millions of sensor readings are generated every minute, storing every record would quickly consume unnecessary storage and slow down the monitoring system.

Your application acts as a **Log Sifter**, keeping only meaningful anomalies while discarding normal sensor readings.

After processing the uploaded logs, engineers can immediately review critical events and manage them through a Laravel web interface.

---

# General Description of the Project

Develop a Laravel SSR application that allows engineers to:

- Authenticate into the monitoring system.
- Upload a CSV file containing raw sensor logs.
- Process the file efficiently.
- Save only important sensor alerts.
- Display processing statistics.
- View all detected alerts.
- Mark alerts as resolved or as false positives (not important).

The application must use Laravel Blade views and standard Laravel features.

No frontend framework is required.

---

# Functional Requirements

## 1. Authentication

Implement Laravel Authentication.

Only authenticated users can access the dashboard.

The application must support at least two roles:

- Engineer
- Administrator

Both roles can:

- Upload sensor log files.
- View alerts.

Only administrators can:

- Delete alerts.

---

## 2. Database

Create a table named:

**SensorAlert**

Each stored alert contains:

| Field | Type |
|---------|------|
| id | Primary Key |
| sensor_id | String |
| machine_unit | String |
| error_code | String |
| vibration_amplitude | Integer |
| severity | Enum (Info, Warning, Critical) |
| status | Enum (Open, Resolved, Not Important) |
| created_at | Timestamp |

Only abnormal events are stored.

---

# Application Routes

The following routes must be implemented.

| Method | Route | Description | Authentication |
|--------|-------|-------------|----------------|
| GET | / | Redirect to login or dashboard | Public |
| GET | /login | Display the login page | Public |
| POST | /login | Authenticate a user | Public |
| POST | /logout | Logout the current user | Authenticated |
| GET | /dashboard | Display the monitoring dashboard | Authenticated |
| GET | /sensor-alerts/import | Display the CSV upload page | Authenticated |
| POST | /sensor-alerts/import | Upload and process a CSV file | Authenticated |
| GET | /sensor-alerts | Display all stored alerts | Authenticated |
| PATCH | /sensor-alerts/{id}/status | Update the alert status (Resolved / Not Important) | Authenticated |
| DELETE | /sensor-alerts/{id} | Delete an alert | Administrator only |


## 3. CSV Import

Provide a page where the engineer can upload a CSV file.

The CSV contains sensor readings similar to  the attached file (assets/data-sample.csv).

| sensor_id | machine_unit | error_code | vibration_amplitude |
|------------|--------------|------------|---------------------|
| S001 | ARM-01 | E100 | 35 |
| S014 | ARM-02 | E205 | 96 |
| S020 | ARM-07 | E110 | 82 |

The application must process the CSV line by line (or in chunks) instead of loading the entire file into memory.

---

## 4. Filtering Rules

For each row:

If:

```
vibration_amplitude > 80
```

The event is considered abnormal.

Store it in the database.

Assign its severity using the following rules:

| Vibration | Severity |
|------------|----------|
| 81–90 | Warning |
| 91+ | Critical |

Otherwise:

Discard the record.

Do not store it.

---

## 5. Dashboard

After the upload finishes, redirect the user to the dashboard.

The dashboard must display:

### Summary Cards

Display at least:

- Total rows processed
- Alerts stored
- Rows discarded
- Percentage of discarded data

Example:

```
Processed:
500 logs

Stored:
38 alerts

Discarded:
462 logs

Discarded:
92.4%
```

---

### Alerts Table

Display all stored alerts.

Columns:

- Sensor ID
- Machine Unit
- Error Code
- Vibration
- Severity
- Status
- Actions

Critical alerts should be visually highlighted (red badge or row).

---

## 6. Alert Management

Each alert starts with the status:

```
Open
```

The engineer can change its status to:

- Resolved
- Not Important

Status updates must be persisted in the database.

---

## 7. Validation

Validate uploaded files.

Requirements:

- CSV only
- Maximum file size: 10 MB

Display validation errors using Laravel's standard validation messages.

---

## 8. User Interface

Use Laravel Blade templates.

The interface should contain:

- Login page
- Upload page
- Dashboard
- Alerts table


---

# Technical Requirements

The application must:

- Use Laravel.
- Use Blade (SSR).
- Use MySQL.
- Use Eloquent ORM.
- Use Migrations.
- Use Seeders (optional).
- Use Laravel Authentication.
- Use Request Validation.
- Handle invalid uploads gracefully.

---

# Deliverables

The submission must contain:

- Laravel source code.
- SQL database export.
- `.env.example`
- `README.md` containing installation instructions.

---

## Assessment

Module A will be assessed using the provided version of Google Chrome.

Assessment will include functional testing, user experience, and automated verification.

Automated unit and integration tests may be used during the evaluation process. Therefore, competitors **must implement the routes, route names, database schema, validation rules, and expected application behavior exactly as described in this specification.** Any deviation from the required structure may prevent the automated tests from validating the application successfully.

## Mark distribution

The table below outlines how marks are broken down and how they align with the WorldSkills Occupation Standards (WSOS). Please read the Technical Description for a full explanation of the WorldSkills Occupation Standards.

| WSOS SECTION | Description                            | Points |
| ------------ | -------------------------------------- | ------ |
| 1            | Work organization and self-management  | 2      |
| 2            | Communication and interpersonal skills | 0      |
| 3            | Design Implementation                  | 2      |
| 4            | Front-End Development                  | 5      |
| 5            | Back-End Development                   | 16     |
| **Total**    |                                        | 25     |

The application should be fully functional within the allocated competition time.