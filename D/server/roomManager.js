class RoomManager {
    constructor() {
        this.rooms = new Map();
    }

    // Creating or retrieving a room by its ID
    getOrCreateRoom(roomId) {
        if (!this.rooms.has(roomId)) {
            this.rooms.set(roomId, {
                objects: new Map(),
                users: new Map()
            });
        }
        return this.rooms.get(roomId);
    }

    // Adding a user to a room
    addUser(roomId, socketId, user) {
        const room = this.getOrCreateRoom(roomId);
        room.users.set(socketId, { ...user, id: socketId });
        return room;
    }

    // Removing a user from a room
    removeUser(roomId, socketId) {
        if (!this.rooms.has(roomId)) return null;
        const room = this.rooms.get(roomId);
        room.users.delete(socketId);
        return room;
    }

    // Adding an object to a room's canvas
    addObject(roomId, object) {
        const room = this.getOrCreateRoom(roomId);
        if (!object || !object.id || !object.type) return false;
        room.objects.set(object.id, object);
        return true;
    }

    // Updating an object in a room's canvas
    updateObject(roomId, objectData) {
        if (!this.rooms.has(roomId)) return false;
        const room = this.rooms.get(roomId);
        if (!room.objects.has(objectData.id)) return false;

        // Merge existing object properties with new ones
        const current = room.objects.get(objectData.id);
        room.objects.set(objectData.id, { ...current, ...objectData });
        return true;
    }

    // Deleting an object from a room's canvas
    deleteObject(roomId, objectId) {
        if (!this.rooms.has(roomId)) return false;
        const room = this.rooms.get(roomId);
        return room.objects.delete(objectId);
    }

    // Clearing all objects from a room's canvas
    clearCanvas(roomId) {
        if (!this.rooms.has(roomId)) return false;
        const room = this.rooms.get(roomId);
        room.objects.clear();
        return true;
    }

    getCanvasState(roomId) {
        const room = this.getOrCreateRoom(roomId);
        return Array.from(room.objects.values());
    }

    getUsers(roomId) {
        const room = this.getOrCreateRoom(roomId);
        return Array.from(room.users.values());
    }
}

module.exports = new RoomManager();