import React, { useEffect, useState } from 'react';
import { Link, useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { postService } from '../services/api';

export const Layout = ({ children, onHashtagSelect }) => {
    const { user, logout } = useAuth();
    const navigate = useNavigate();
    const location = useLocation();
    const [trending, setTrending] = useState([]);

    useEffect(() => {
        postService
            .getTrendingHashtags({ limit: 8, days: 7 })
            .then((res) => setTrending(res.data || []))
            .catch((err) => console.error('Error fetching trending hashtags:', err));
    }, []);

    const handleHashtagClick = (tag) => {
        if (onHashtagSelect) {
            onHashtagSelect(tag);
        } else {
            navigate(`/?hashtag=${tag}`);
        }
    };

    return (
        <div className="w-100 h-[100vh] mx-auto flex flex-col bg-slate-50 overflow-hidden font-sans text-slate-800 border border-slate-200 shadow-2xl">
            {/* Top Navigation Bar */}
            <header className="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between flex-shrink-0 z-10">
                <Link to="/" className="text-2xl font-black bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">
                    Minstagram
                </Link>

                <nav className="flex items-center space-x-6">
                    <Link to="/" className={`font-semibold hover:text-indigo-600 ${location.pathname === '/' ? 'text-indigo-600' : 'text-slate-600'}`}>
                        Home
                    </Link>

                    {user ? (
                        <>
                            <Link to="/posts/create" className="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">
                                + New Post
                            </Link>
                            <Link to={`/users/${user.username}`} className="flex items-center space-x-2 text-slate-700 hover:text-indigo-600">
                                <img
                                    src={user.profileImage || 'https://via.placeholder.com/40'}
                                    alt={user.name}
                                    className="w-8 h-8 rounded-full object-cover border border-slate-300"
                                />
                                <span className="font-semibold text-sm">{user.name}</span>
                            </Link>
                            <button onClick={logout} className="text-sm font-medium text-rose-600 hover:text-rose-700">
                                Logout
                            </button>
                        </>
                    ) : (
                        <div className="space-x-3">
                            <Link to="/login" className="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg font-medium hover:bg-slate-50">
                                Log In
                            </Link>
                            <Link to="/register" className="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700">
                                Sign Up
                            </Link>
                        </div>
                    )}
                </nav>
            </header>

            <div className="flex-1 flex overflow-hidden">
                <main className="flex-1 overflow-y-auto p-6">{children}</main>

                <aside className="w-80 border-l border-slate-200 bg-white p-5 flex flex-col flex-shrink-0">
                    <h3 className="font-bold text-lg text-slate-900 mb-4 flex items-center space-x-2">
                        <span>💖</span>
                        <span>Trending Hashtags</span>
                    </h3>
                    <div className="flex flex-col space-y-2 overflow-y-auto">
                        {trending.length === 0 ? (
                            <p className="text-slate-400 text-sm">No trending topics yet.</p>
                        ) : (
                            trending.map((item, idx) => (
                                <button
                                    key={item.name}
                                    onClick={() => handleHashtagClick(item.name)}
                                    className="flex items-center justify-between p-2.5 rounded-xl hover:bg-indigo-50 transition text-left group"
                                >
                                    <div className="flex flex-col">
                                        <span className="text-xs font-semibold text-slate-400">#{idx + 1} Trending</span>
                                        <span className="font-bold text-indigo-900 group-hover:text-indigo-600">#{item.name}</span>
                                    </div>
                                    <span className="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded-full font-medium">
                                        {item.postsCount} posts
                                    </span>
                                </button>
                            ))
                        )}
                    </div>
                </aside>
            </div>
        </div>
    );
};