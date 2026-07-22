import { CanvasRenderer } from './canvas.js';
import { socket } from './socket.js';

// Global state variables
let renderer = null;

let activeTool = 'rectangle';
let strokeColor = '#000000';
let fillColor = '#ffffff';
let strokeWidth = 2;

let isInteracting = false;
let startCoords = { x: 0, y: 0 };
let currentObject = null;
let draggedObject = null;
let dragOffset = { x: 0, y: 0 };
let currentUsername = 'Anonymous';

let animFrameId = null;

// Utility function to generate unique IDs for objects
const generateId = () => `obj-${Date.now()}-${Math.random().toString(36).substr(2, 5)}`;

// Function to get canvas coordinates from mouse or touch events
function getCanvasCoords(e, canvas) {
    const rect = canvas.getBoundingClientRect();
    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
    const clientY = e.touches ? e.touches[0].clientY : e.clientY;

    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;

    return {
        x: Math.round((clientX - rect.left) * scaleX),
        y: Math.round((clientY - rect.top) * scaleY)
    };
}

// Function to resize the canvas to fit its container
function resizeCanvasToContainer(canvas) {
    const container = canvas.parentElement;
    if (!container) return;

    canvas.width = container.clientWidth;
    canvas.height = container.clientHeight;
    if (renderer) renderer.render();
}

// Initialize the application once the DOM is fully loaded
document.addEventListener('DOMContentLoaded', () => {
    const canvasEl = document.getElementById('canvas');
    if (!canvasEl) return;

    renderer = new CanvasRenderer(canvasEl);

    resizeCanvasToContainer(canvasEl);
    window.addEventListener('resize', () => resizeCanvasToContainer(canvasEl));

    // Extract room ID and username from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const roomId = urlParams.get('room') || 'room-1';

    currentUsername = urlParams.get('user') || `User-${Math.floor(1000 + Math.random() * 9000)}`;

    const roomDisplay = document.getElementById('room-display');
    if (roomDisplay) roomDisplay.innerText = `Room: ${roomId}`;

    initSocketEvents(roomId, currentUsername);
    initUIEventListeners();
    initCanvasMouseEvents(canvasEl);
});

// Initialize socket event listeners for real-time collaboration
function initSocketEvents(roomId, username) {
    socket.off();

    socket.emit('join-room', { roomId, username });

    socket.on('canvas-state', ({ objects, users }) => {
        renderer.setObjects(objects);
        updateUserList(users);
    });

    socket.on('user-joined', ({ users }) => {
        updateUserList(users);
    });

    socket.on('user-left', ({ id, users }) => {
        renderer.removeCursor(id);
        updateUserList(users);
    });

    socket.on('draw-object', (object) => {
        renderer.addOrUpdateObject(object);
    });

    socket.on('update-object', (objectData) => {
        renderer.addOrUpdateObject(objectData);
    });

    socket.on('delete-object', ({ id }) => {
        renderer.deleteObject(id);
    });

    socket.on('cursor-move', ({ userId, username, x, y }) => {
        renderer.updateCursor(userId, { x, y, username });
    });

    socket.on('clear-canvas', () => {
        renderer.clear();
    });

    socket.on('connect_error', (err) => {
        console.error('Socket connection error:', err.message);
    });
}

// Initialize mouse and touch event listeners for the canvas
function initCanvasMouseEvents(canvas) {
    let lastCursorSend = 0;

    canvas.addEventListener('mousedown', (e) => {
        const coords = getCanvasCoords(e, canvas);
        isInteracting = true;
        startCoords = coords;

        if (activeTool === 'select') {
            const clickedObj = renderer.findObjectAt(coords.x, coords.y);
            if (clickedObj) {
                renderer.selectedObjectId = clickedObj.id;
                draggedObject = { ...clickedObj };
                dragOffset = {
                    x: coords.x - clickedObj.x,
                    y: coords.y - clickedObj.y
                };
            } else {
                renderer.selectedObjectId = null;
            }
            renderer.render();
            return;
        }

        if (activeTool === 'text') {
            const textObj = {
                id: generateId(),
                type: 'text',
                x: coords.x,
                y: coords.y,
                text: 'Text',
                fontSize: 20,
                strokeColor,
                fillColor: strokeColor,
                strokeWidth
            };
            renderer.addOrUpdateObject(textObj);
            socket.emit('draw-object', textObj);
            isInteracting = false;
            return;
        }

        currentObject = {
            id: generateId(),
            type: activeTool,
            x: coords.x,
            y: coords.y,
            strokeColor,
            fillColor,
            strokeWidth
        };
    });

    canvas.addEventListener('mousemove', (e) => {
        const coords = getCanvasCoords(e, canvas);

        const now = Date.now();
        if (now - lastCursorSend > 50) {
            socket.emit('cursor-move', { x: coords.x, y: coords.y, username: currentUsername });
            lastCursorSend = now;
        }

        if (!isInteracting) return;

        if (animFrameId) cancelAnimationFrame(animFrameId);

        animFrameId = requestAnimationFrame(() => {
            if (activeTool === 'select' && draggedObject) {
                draggedObject.x = coords.x - dragOffset.x;
                draggedObject.y = coords.y - dragOffset.y;

                renderer.addOrUpdateObject(draggedObject);

                socket.emit('update-object', {
                    id: draggedObject.id,
                    x: draggedObject.x,
                    y: draggedObject.y
                });
                return;
            }

            if (currentObject) {
                if (activeTool === 'rectangle') {
                    currentObject.width = coords.x - startCoords.x;
                    currentObject.height = coords.y - startCoords.y;
                } else if (activeTool === 'circle') {
                    currentObject.radius = Math.round(Math.hypot(coords.x - startCoords.x, coords.y - startCoords.y));
                } else if (activeTool === 'line') {
                    currentObject.x1 = startCoords.x;
                    currentObject.y1 = startCoords.y;
                    currentObject.x2 = coords.x;
                    currentObject.y2 = coords.y;
                }

                renderer.addOrUpdateObject(currentObject);
            }
        });
    });

    const handleMouseUp = () => {
        if (!isInteracting) return;
        isInteracting = false;

        if (activeTool === 'select' && draggedObject) {
            socket.emit('update-object', draggedObject);
            draggedObject = null;
            return;
        }

        if (currentObject) {
            const isValidShape =
                (currentObject.type === 'rectangle' && Math.abs(currentObject.width || 0) > 2 && Math.abs(currentObject.height || 0) > 2) ||
                (currentObject.type === 'circle' && (currentObject.radius || 0) > 2) ||
                (currentObject.type === 'line' && (currentObject.x1 !== currentObject.x2 || currentObject.y1 !== currentObject.y2));

            if (isValidShape) {
                socket.emit('draw-object', currentObject);
            } else {
                renderer.deleteObject(currentObject.id);
            }
            currentObject = null;
        }
    };

    canvas.addEventListener('mouseup', handleMouseUp);
    canvas.addEventListener('mouseleave', handleMouseUp);
}

// Initialize UI event listeners for tool selection and color changes
function initUIEventListeners() {
    document.querySelectorAll('.tool-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tool-btn').forEach((b) => b.classList.remove('active'));
            btn.classList.add('active');
            activeTool = btn.dataset.tool;
        });
    });

    document.getElementById('stroke-color')?.addEventListener('change', (e) => {
        strokeColor = e.target.value;
    });

    document.getElementById('fill-color')?.addEventListener('change', (e) => {
        fillColor = e.target.value;
    });

    document.getElementById('stroke-width')?.addEventListener('change', (e) => {
        strokeWidth = parseInt(e.target.value, 10);
    });

    document.getElementById('btn-clear')?.addEventListener('click', () => {
        renderer.clear();
        socket.emit('clear-canvas');
    });

    window.addEventListener('keydown', (e) => {
        if ((e.key === 'Delete' || e.key === 'Backspace') && renderer.selectedObjectId) {
            const idToDelete = renderer.selectedObjectId;
            renderer.deleteObject(idToDelete);
            socket.emit('delete-object', { id: idToDelete });
        }
    });
}

// Function to update the user list in the UI
function updateUserList(users) {
    const userListEl = document.getElementById('user-list');
    if (!userListEl || !Array.isArray(users)) return;

    userListEl.innerHTML = users
        .map((u) => `<li class="user-badge"><span class="status-dot"></span>${u.username || 'User'}</li>`)
        .join('');
}