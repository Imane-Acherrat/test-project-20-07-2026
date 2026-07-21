import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api'; // Laravel Module B URL

const api = axios.create({
    baseURL: API_BASE_URL,
});

// Interceptor to attach Authorization Bearer token automatically
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

export const authService = {
    login: async (credentials) => {
        const res = await api.post('/auth/login', credentials);
        if (res.data.token) {
            localStorage.setItem('auth_token', res.data.token);
            localStorage.setItem('user_data', JSON.stringify(res.data.user));
        }
        return res.data;
    },
    register: async (userData) => {
        const res = await api.post('/auth/register', userData);
        if (res.data.token) {
            localStorage.setItem('auth_token', res.data.token);
            localStorage.setItem('user_data', JSON.stringify(res.data.user));
        }
        return res.data;
    },
    logout: async () => {
        try {
            await api.post('/auth/logout');
        } finally {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_data');
        }
    },
};
export const profileService = {
    getOwnProfile: () => api.get('/profile').then((res) => res.data),

    updateProfile: (formData) => {
        formData.append('_method', 'PUT');
        return api.post('/profile', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        }).then((res) => res.data);
    },

    getPublicProfile: (username) =>
        api.get(`/users/${username}`).then((res) => res.data),
    getCreatorPosts: (username, params) =>
        api.get(`/users/${username}/posts`, { params }).then((res) => res.data),
};

export const postService = {
    getPosts: (params) => api.get('/posts', { params }).then((res) => res.data),
    getPost: (id) => api.get(`/posts/${id}`).then((res) => res.data),
    createPost: (formData) =>
        api.post('/posts', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        }).then((res) => res.data),

    updatePost: (id, formData) => {
        formData.append('_method', 'PUT');
        return api.post(`/posts/${id}`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        }).then((res) => res.data);
    },

    deletePost: (id) => api.delete(`/posts/${id}`).then((res) => res.data),
    likePost: (id) => api.post(`/posts/${id}/like`).then((res) => res.data),
    unlikePost: (id) => api.delete(`/posts/${id}/like`).then((res) => res.data),
    getTrendingHashtags: (params) =>
        api.get('/hashtags/trending', { params }).then((res) => res.data),
};

export default api;