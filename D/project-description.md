# Test Project Outline – Module D – Real-Time Visual Collaboration Tool

## Competition Time

3,5 hours

---

# Introduction

Module D focuses on the implementation of a real-time collaborative drawing application.

The application allows multiple users to join the same collaboration room and interact on a shared canvas simultaneously using WebSockets.

Participants must build a responsive application capable of synchronizing drawing operations, object modifications, and user activities in real time.

### Scenario

Your company has been contracted to develop an online collaborative whiteboard that enables distributed teams to brainstorm ideas visually.

Multiple users should be able to work together on the same canvas while seeing each other's actions instantly.

The application must synchronize all connected users without requiring the page to be refreshed.

---

## General Description of Project and Tasks

In this module, you must develop a real-time visual collaboration application.

The application must:

- Allow users to join collaboration rooms.
- Synchronize the canvas between all connected users.
- Support multiple drawing tools.
- Allow users to manipulate existing objects.
- Display connected users.
- Synchronize cursor movements.
- Handle users joining and leaving rooms.
- Maintain the current canvas state on the server.
- Recover the complete canvas for newly connected users.

---

### Competitor Information

The application will be assessed by interacting simultaneously with multiple browser windows.

The application must:

- Synchronize changes in real time.
- Prevent inconsistent canvas states.
- Continue functioning when users join or leave.
- Store the canvas state in server memory.
- Use WebSockets for all collaborative features.
- Organize the project using a clean architecture.

---

# Requirements

## 1. Collaboration Rooms

Users must be able to join a collaboration room.

When a room does not exist, it must be created automatically.

Each room must maintain an independent canvas.

Users connected to different rooms must never receive updates from other rooms.

When a new user joins a room, the server must send the complete current canvas before receiving new drawing events.

---

## 2. Canvas Workspace

The application must provide a drawing canvas.

The canvas must support:

- Mouse interactions
- Continuous drawing
- Object rendering
- Canvas clearing

The canvas must automatically update whenever another user performs an action.

---

## 3. Drawing Tools

The application must provide at least the following tools:

- Line
- Rectangle
- Circle
- Text
- Eraser

Each created object must contain at least:

- Identifier
- Object type
- Position
- Dimensions
- Stroke color
- Fill color
- Stroke width

Objects must remain editable after being created.

---

## 4. Object Manipulation

Users must be able to select existing objects.

The selected object may be:

- Moved
- Deleted

All modifications must immediately appear on every connected client.

Deleting an object removes it from every user's canvas.

---

## 5. Live Collaboration

The application must synchronize all user actions in real time.

When a user performs an action, every connected user in the same room must immediately see the update.

The application must synchronize:

- Object creation
- Object modification
- Object deletion
- Canvas clearing
- Cursor position
- User connection
- User disconnection

A list of connected users must always be visible.

---

## 6. WebSocket Communication

The application must use WebSockets for all collaborative features.

The following events must be implemented:

| Event | Description |
|--------|-------------|
| `join-room` | Join a collaboration room |
| `user-joined` | Notify users that someone joined |
| `user-left` | Notify users that someone left |
| `draw-object` | Create a new object |
| `update-object` | Update an existing object |
| `delete-object` | Remove an object |
| `cursor-move` | Share cursor position |
| `clear-canvas` | Remove all objects |
| `canvas-state` | Send the complete canvas |

Example event:

```json
{
    "event": "draw-object",
    "data": {
        "id": "obj-15",
        "type": "rectangle",
        "x": 120,
        "y": 80,
        "width": 150,
        "height": 90
    }
}
```

---

## 7. Server State Management

The server must maintain the current state of every collaboration room.

Each room must store:

- Connected users
- Canvas objects
- Active cursors

The application is **not required** to use a database.

All information may be stored in memory.

When a new user joins, the server must send the latest canvas state before broadcasting future updates.

---

## 8. Validation and Error Handling

The application must validate incoming events.

Validation should include:

- Existing room identifier
- Valid object identifiers
- Supported drawing tool
- Valid object dimensions
- Required properties
- Existing objects before update or deletion

Invalid events must not crash the server.

Unexpected errors should return meaningful error messages.

---

## 9. Technical Requirements

The project must:

- Use HTML5 Canvas.
- Use JavaScript or TypeScript.
- Use a WebSocket library such as Socket.IO.
- Separate client and server code.
- Organize the project using a clean folder structure.
- Use reusable components or modules whenever possible.
- Synchronize updates without refreshing the page.
- Keep the user interface responsive during collaboration.

---

## Deliverables

The competitor must submit:

1. Complete source code.
2. README file.
3. Installation instructions.
4. Application startup commands.
5. Project structure respecting good development practices.
6. Git repository containing meaningful commits.

---

## Assessment

The project will be evaluated by opening multiple browser windows connected to the same room.

Assessment focuses on:

- Real-time synchronization.
- Correct use of WebSockets.
- Canvas functionality.
- Object manipulation.
- User experience.
- Code quality and organization.
- Error handling.
- Git usage.

---

## Mark Distribution

| WSOS Section | Description | Points |
|--------------|--------------------------------------|------:|
| 1 | Work Organization and Self-Management | 3 |
| 2 | Communication and Interpersonal Skills | 0 |
| 3 | Design Implementation | 4 |
| 4 | Front-End Development | 8 |
| 5 | Back-End Development | 10 |
| **Total** | | **25** |