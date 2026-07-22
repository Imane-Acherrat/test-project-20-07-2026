const express = require('express');
const http = require('http');
const { Server } = require('socket.io');

// Initialize Express app and HTTP server
const app = express();
const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: '*',
        methods: ['GET', 'POST']
    }
});

const rooms = new Map();

// Socket.IO connection handling
io.on('connection', (socket) => {
    let currentRoom = null;
    let currentUser = null;

    socket.on('join-room', ({ roomId, username }) => {
        currentRoom = roomId || 'room-1';
        currentUser = username || `User-${socket.id.substr(0, 4)}`;

        socket.join(currentRoom);

        if (!rooms.has(currentRoom)) {
            rooms.set(currentRoom, { objects: [], users: new Map() });
        }

        const room = rooms.get(currentRoom);
        room.users.set(socket.id, { id: socket.id, username: currentUser });

        const userList = Array.from(room.users.values());

        socket.emit('canvas-state', {
            objects: room.objects,
            users: userList
        });

        io.to(currentRoom).emit('user-joined', { users: userList });
    });

    socket.on('draw-object', (object) => {
        if (!currentRoom || !rooms.has(currentRoom)) return;
        const room = rooms.get(currentRoom);
        room.objects.push(object);
        socket.to(currentRoom).emit('draw-object', object);
    });

    socket.on('update-object', (objectData) => {
        if (!currentRoom || !rooms.has(currentRoom)) return;
        const room = rooms.get(currentRoom);
        const index = room.objects.findIndex((o) => o.id === objectData.id);
        if (index !== -1) {
            room.objects[index] = { ...room.objects[index], ...objectData };
        }
        socket.to(currentRoom).emit('update-object', objectData);
    });

    socket.on('delete-object', ({ id }) => {
        if (!currentRoom || !rooms.has(currentRoom)) return;
        const room = rooms.get(currentRoom);
        room.objects = room.objects.filter((o) => o.id !== id);
        socket.to(currentRoom).emit('delete-object', { id });
    });

    socket.on('clear-canvas', () => {
        if (!currentRoom || !rooms.has(currentRoom)) return;
        const room = rooms.get(currentRoom);
        room.objects = [];
        socket.to(currentRoom).emit('clear-canvas');
    });

    socket.on('cursor-move', (data) => {
        if (!currentRoom) return;
        socket.to(currentRoom).emit('cursor-move', {
            userId: socket.id,
            username: currentUser,
            x: data.x,
            y: data.y
        });
    });

    socket.on('disconnect', () => {
        if (currentRoom && rooms.has(currentRoom)) {
            const room = rooms.get(currentRoom);
            room.users.delete(socket.id);

            const userList = Array.from(room.users.values());
            io.to(currentRoom).emit('user-left', { id: socket.id, users: userList });

            if (room.users.size === 0) {
                rooms.delete(currentRoom);
            }
        }
    });
});

// Start the server
const PORT = 3000;
server.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});