# Test Project Outline – Module E – Survey Platform with Webhooks

## Competition Time

6 hours

---

# Introduction

Module E focuses on the implementation of a simplified online survey platform inspired by Typeform.

The application allows authenticated users to create interactive surveys composed of multiple question types, publish them using a unique public link, collect participant responses, and automatically send each submission to external systems using configurable webhooks.

The project evaluates both front-end and back-end development skills, including authentication, database design, REST APIs, asynchronous processing, and HTTP integrations.

### Scenario

Your company has been contracted to develop an online survey platform for businesses wishing to collect customer feedback, registrations, and questionnaires.

Survey creators must be able to design surveys visually, publish them, receive responses, and integrate the platform with external applications using webhooks.

Participants should be able to complete surveys without creating an account.

---

# General Description of Project and Tasks

In this module, you must develop a complete survey platform.

The application must:

- Allow users to create an account and authenticate.
- Create and manage surveys.
- Build surveys using multiple question types.
- Publish surveys through public links.
- Collect participant responses.
- Store submissions.
- Configure multiple webhooks.
- Deliver webhook notifications asynchronously.
- Display submission history.
- Maintain delivery logs.

---

### Competitor Information

The application will be evaluated using multiple surveys and multiple participant submissions.

The application must:

- Validate all submitted data.
- Prevent unauthorized access.
- Process webhook deliveries asynchronously.
- Continue functioning even if webhook deliveries fail.
- Protect private resources.
- Follow a clean project architecture.

---

# Requirements

## 1. Authentication

Users must be able to:

- Register.
- Log in.
- Log out.

The application must:

- Protect all private pages.
- Restrict access to resources belonging to their owner.

---

## 2. Survey Management

Authenticated users must be able to:

- Create surveys.
- Edit surveys.
- Delete surveys.
- Duplicate surveys.
- Publish or disable surveys.
- Configure a confirmation message.

Each survey must contain at least:

- Title
- Description
- Status
- Public identifier
- Confirmation message
- Creation date
- Last update date

---

## 3. Survey Builder

Users must be able to build surveys by adding multiple questions.

The application must support at least:

- Short Answer
- Long Answer
- Single Choice
- Multiple Choice
- Dropdown
- Number
- Email
- Date
- Rating

Choice-based questions must allow:

- Creating options.
- Editing options.
- Deleting options.
- Reordering options.

Questions themselves must also be reorderable.

---

## 4. Survey Publication

Published surveys must receive a unique public URL.

The application must:

- Allow public access without authentication.
- Prevent access to unpublished surveys.
- Display an appropriate error page when necessary.

---

## 5. Survey Submission

Participants must be able to submit responses.

The application must:

- Display questions dynamically.
- Validate required fields.
- Store complete submissions.
- Associate answers with their questions.
- Display the confirmation message after submission.

---

## 6. Submission Management

Survey creators must be able to:

- View all submissions.
- Search submissions.
- Paginate submissions.
- View submission details.
- Delete submissions.

The dashboard must display:

- Number of surveys.
- Published surveys.
- Total responses.
- Recent surveys.
- Recent activity.

---

## 7. Webhook Configuration

Each survey may contain one or more webhooks.

Each webhook must include:

- Name
- Destination URL
- Active status
- Optional secret
- Creation date

---

## 8. Webhook Delivery

Whenever a participant submits a survey:

- Save the submission.
- Queue a delivery job for every active webhook.
- Send an HTTP POST request.
- Deliver the payload as JSON.
- Record the delivery result.

The application must support:

- HMAC SHA-256 signatures.
- Retry failed deliveries.
- Delivery history.
- Error handling.

A webhook failure must never prevent a submission from being saved.

---

## 9. Internal API

The application must expose REST endpoints for:

- Surveys
- Questions
- Submissions
- Webhooks
- Retry operations

Private routes must require authentication.

---

## 10. Test Endpoint

Create an internal endpoint allowing webhook testing.

The endpoint must:

- Receive JSON payloads.
- Validate the payload.
- Store received data.
- Return a JSON response.

The application must provide an administration page to inspect received payloads.

---

## 11. Technical Requirements

The project must:

- Use Laravel.
- Use MySQL or PostgreSQL.
- Use Eloquent relationships.
- Use Laravel Queues.
- Use the Laravel HTTP Client.
- Use Form Requests.
- Use environment variables.
- Organize the application using Controllers, Services, Jobs, and Models.
- Return consistent API responses.
- Handle errors correctly.

---

## Deliverables

The competitor must submit:

1. Complete source code.
2. README file.
3. Installation instructions.
4. Queue worker startup command.
5. Postman or Bruno collection.
6. Database migrations.
7. Seeders or sample data.
8. Git repository containing meaningful commits.

---

## Assessment

The project will be evaluated by:

- Creating multiple surveys.
- Publishing surveys.
- Submitting responses.
- Testing webhook deliveries.
- Simulating delivery failures.
- Retrying failed deliveries.

Assessment focuses on:

- Authentication.
- Survey builder.
- Question management.
- Public submission.
- Webhook integration.
- Queue processing.
- User experience.
- Code quality.
- API design.
- Error handling.

---

## Mark Distribution

| WSOS Section | Description                            | Points |
| ------------ | -------------------------------------- | -----: |
| 1            | Work Organization and Self-Management  |      3 |
| 2            | Communication and Interpersonal Skills |      0 |
| 3            | Design Implementation                  |      4 |
| 4            | Front-End Development                  |      7 |
| 5            | Back-End Development                   |     11 |
| **Total**    |                                        | **25** |
