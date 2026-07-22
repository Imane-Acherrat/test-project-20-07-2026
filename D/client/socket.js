/**
 * Socket.IO client instance for real-time communication with the server.
 * Connects to the server at 'http://localhost:3000' with auto-reconnection enabled.
 */
export const socket = io('http://localhost:3000', {
  autoConnect: true,
  reconnection: true
});