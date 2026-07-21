# Test Project Outline – Module C – Social Media Frontend using an API

## Competition time

3.50 hours

## Introduction

Module F focuses on the implementation of a frontend for a REST API.

## General Description of Project and Tasks

You are asked to create an interactive frontend for a social media platform. The frontend will consume the REST API developed in Module B, allowing users to browse posts, manage their profiles, publish content, and interact with other users.

This application must consume the REST API implemented during Module B.

### Competitor Information

- The frontend can be implemented using a framework and other libraries.
- The application must be a Single Page Application (SPA).
- Refreshing the page must preserve the current application state whenever possible (authentication, current page, filters, etc.).

## Requirements

### 1. Authentication and Access Control

The application is publicly accessible.

Unauthenticated users can:

- Browse the home feed
- View public profiles
- View posts
- Search posts
- Filter by hashtags
- View trending hashtags

Authenticated users can additionally:

- Create posts
- Edit and delete their own posts
- Like and unlike posts
- View and update their profile

Pages requiring authentication must redirect unauthenticated users to the login page.

After a successful login, users should be redirected back to the originally requested page.

Authentication must persist after refreshing the page.

---

### 2. Home Feed

The home feed is the default page.

It displays posts returned by the backend API.

Each post must display:

- Creator avatar
- Creator name
- Username
- Post image
- Description
- Hashtags
- Publication date
- Number of likes
- Like button

The page must support:

- Pagination
- Search by description
- Filter by hashtag
- Sorting (Latest / Oldest / Most Popular)

Changing filters or pages must update the displayed posts without reloading the application.

---

### 3. Post Details

Selecting a post opens a dedicated page.

The page displays:

- Full image
- Description
- Creator information
- Hashtags
- Publication date
- Number of likes
- Like / Unlike button

If the authenticated user owns the post, Edit and Delete buttons must also be available.

---

### 4. Create and Edit Posts

Authenticated users can create new posts.

The form must allow:

- Uploading an image
- Writing a description
- Selecting or entering hashtags

Validation errors returned by the API must be displayed to the user.

Users may edit only their own posts.

Deleting a post requires confirmation before the request is sent.

---

### 5. Profile Page

Each user has a public profile page displaying:

- Profile picture
- Name
- Username
- Bio
- Number of posts
- Total likes received

The profile page must also display the user's posts using pagination.

Authenticated users can edit their own profile information.

---

### 6. Likes

Authenticated users can:

- Like a post
- Unlike a post

The interface must immediately update:

- Like button state
- Number of likes

without requiring a page refresh.

---

### 7. Search and Hashtags

Users must be able to:

- Search posts by description
- Filter posts using hashtags
- Combine search, hashtag filters, sorting and pagination

Trending hashtags must be displayed in a dedicated sidebar or section.

Selecting a hashtag automatically filters the home feed.

---

### 8. Design

The application should provide a modern social media interface.

The layout must clearly separate:

- Navigation
- Home feed
- Profile pages
- Post creation
- Trending hashtags

Loading indicators should be displayed while communicating with the API.

Empty states and error messages should be clearly presented.

The application must be optimized for a desktop viewport of **1280 × 800** pixels.

---

## Assessment

Module C will be assessed using the provided version of Google Chrome.

Assessment focuses on:

- Correct integration with the REST API
- User experience
- Responsive interactions
- Authentication handling
- Navigation
- Overall design quality


## Mark distribution

| WSOS SECTION | Description                            | Points |
| ------------ | -------------------------------------- | ------ |
| 1            | Work organization and self-management  | 2      |
| 2            | Communication and interpersonal skills | 0      |
| 3            | Design Implementation                  | 8      |
| 4            | Front-End Development                  | 15     |
| 5            | Back-End Development                   | 0      |
| **Total**    |                                        | **25** |