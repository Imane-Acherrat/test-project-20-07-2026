# Real-Time Collaborative Whiteboard

A lightweight, multi-user collaborative whiteboard application built with **HTML5 Canvas**, **JavaScript**, **Node.js**, and **Socket.io**. Multiple users can join rooms, draw shapes, write text, select/move elements, and track each other's live cursors in real time.

---

## Features

- **Real-Time Collaboration**: Draw, move, and clear objects with multi-user sync.
- **Live User List & Cursors**: See active connected users per room and track cursor movements live.
- **Drawing Tools**:
  - Shapes: Rectangles, Circles, Lines
  - Text insertion
  - Selection / Drag-and-Drop movement
  - Custom stroke color, fill color, and stroke width
- **Room System**: Join specific rooms via URL query parameters (`?room=room-1&user=omar`).
- **Responsive Workspace**: Automatic canvas scaling with double-buffered rendering using `requestAnimationFrame`.

---

## Tech Stack

- **Frontend**: Vanilla JavaScript (ES Modules), HTML5 Canvas, CSS3
- **Backend**: Node.js, Express, Socket.io

---
