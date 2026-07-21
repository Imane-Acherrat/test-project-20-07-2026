import React, { useState } from 'react';
import { useNavigate, useLocation, Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

export const Login = () => {
    const { login } = useAuth();
    const navigate = useNavigate();
    const location = useLocation();
    const from = location.state?.from?.pathname || '/';

    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            await login({ email, password });
            navigate(from, { replace: true });
        } catch (err) {
            setError('Invalid credentials or authentication error.');
        }
    };

    return (
        <div className="w-100 h-[100vh] mx-auto flex items-center justify-center bg-slate-100">
            <div className="w-96 bg-white p-8 rounded-2xl border shadow-lg space-y-6">
                <h2 className="text-2xl font-black text-center text-indigo-600">Welcome Back</h2>
                {error && <p className="text-rose-600 text-xs text-center">{error}</p>}
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-xs font-bold mb-1">Email</label>
                        <input
                            type="email"
                            required
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            className="w-full border p-2.5 text-sm rounded-xl"
                        />
                    </div>
                    <div>
                        <label className="block text-xs font-bold mb-1">Password</label>
                        <input
                            type="password"
                            required
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            className="w-full border p-2.5 text-sm rounded-xl"
                        />
                    </div>
                    <button type="submit" className="w-full bg-indigo-600 text-white font-bold py-2.5 rounded-xl">
                        Log In
                    </button>
                </form>
                <p className="text-xs text-center text-slate-500">
                    Don't have an account? <Link to="/register" className="text-indigo-600 font-bold">Sign Up</Link>
                </p>
            </div>
        </div>
    );
};