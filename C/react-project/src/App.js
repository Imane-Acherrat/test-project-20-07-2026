import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { AuthProvider } from './context/AuthContext';
import { ProtectedRoute } from './components/ProtectedRoute';
import { Home } from './pages/Home';
import { Login } from './pages/Login';
import { PostDetails } from './pages/PostDetails';
import { PostForm } from './pages/PostForm';
import { Profile } from './pages/Profile';
import { EditProfile } from './pages/EditProfile';

export default function App() {
  return (
    <AuthProvider>
      <Router>
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/login" element={<Login />} />
          <Route path="/posts/:id" element={<PostDetails />} />
          <Route path="/users/:username" element={<Profile />} />

          {/* Authenticated Protected Routes */}
          <Route
            path="/posts/create"
            element={
              <ProtectedRoute>
                <PostForm />
              </ProtectedRoute>
            }
          />
          <Route
            path="/posts/:id/edit"
            element={
              <ProtectedRoute>
                <PostForm />
              </ProtectedRoute>
            }
          />

          {/* edit profile route */}
          <Route
            path="/profile/edit"
            element={
              <ProtectedRoute>
                <EditProfile />
              </ProtectedRoute>
            }
          />

        </Routes>
      </Router>
    </AuthProvider>
  );
}