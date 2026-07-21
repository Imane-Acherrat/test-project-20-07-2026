import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Layout } from '../components/Layout';
import { useAuth } from '../context/AuthContext';
import { profileService } from '../services/api';

export const EditProfile = () => {
    const { user, setUser } = useAuth();
    const navigate = useNavigate();

    const [name, setName] = useState('');
    const [username, setUsername] = useState('');
    const [bio, setBio] = useState('');
    const [profileImage, setProfileImage] = useState(null);
    const [preview, setPreview] = useState('');
    const [errors, setErrors] = useState({});
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        if (user) {
            setName(user.name || '');
            setUsername(user.username || '');
            setBio(user.bio || '');
            setPreview(user.profileImage || '');
        }
    }, [user]);

    const handleImageChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setProfileImage(file);
            setPreview(URL.createObjectURL(file));
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSubmitting(true);
        setErrors({});

        const formData = new FormData();
        formData.append('name', name);
        formData.append('username', username);
        formData.append('bio', bio);
        if (profileImage) {
            formData.append('profileImage', profileImage);
        }

        try {
            const updatedUser = await profileService.updateProfile(formData);
            setUser(updatedUser); // Update global user context state
            localStorage.setItem('user_data', JSON.stringify(updatedUser));
            navigate(`/users/${updatedUser.username || username}`);
        } catch (err) {
            if (err.response && err.response.data.errors) {
                setErrors(err.response.data.errors);
            } else {
                alert('Failed to update profile. Please check your inputs.');
            }
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <Layout>
            <div className="max-w-xl mx-auto bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h2 className="text-xl font-bold mb-6 text-slate-800">Edit Profile</h2>

                <form onSubmit={handleSubmit} className="space-y-5">
                    {/* Avatar Upload / Preview */}
                    <div className="flex items-center space-x-6">
                        <img
                            src={preview || 'https://via.placeholder.com/100'}
                            alt="Avatar Preview"
                            className="w-20 h-20 rounded-full object-cover border-2 border-indigo-500 shadow-sm"
                        />
                        <div>
                            <label className="block text-sm font-semibold text-slate-700 mb-1">
                                Profile Picture
                            </label>
                            <input
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                onChange={handleImageChange}
                                className="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100"
                            />
                            {errors.profileImage && (
                                <p className="text-rose-600 text-xs mt-1">{errors.profileImage[0]}</p>
                            )}
                        </div>
                    </div>

                    {/* Name */}
                    <div>
                        <label className="block text-sm font-semibold text-slate-700 mb-1">Display Name</label>
                        <input
                            type="text"
                            required
                            value={name}
                            onChange={(e) => setName(e.target.value)}
                            className="w-full border rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        />
                        {errors.name && <p className="text-rose-600 text-xs mt-1">{errors.name[0]}</p>}
                    </div>

                    {/* Username */}
                    <div>
                        <label className="block text-sm font-semibold text-slate-700 mb-1">Username</label>
                        <input
                            type="text"
                            required
                            value={username}
                            onChange={(e) => setUsername(e.target.value)}
                            className="w-full border rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        />
                        {errors.username && <p className="text-rose-600 text-xs mt-1">{errors.username[0]}</p>}
                    </div>

                    {/* Bio */}
                    <div>
                        <label className="block text-sm font-semibold text-slate-700 mb-1">Bio</label>
                        <textarea
                            rows={3}
                            value={bio}
                            onChange={(e) => setBio(e.target.value)}
                            className="w-full border rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            placeholder="Tell others about yourself..."
                        />
                        {errors.bio && <p className="text-rose-600 text-xs mt-1">{errors.bio[0]}</p>}
                    </div>

                    {/* Buttons */}
                    <div className="flex space-x-3 pt-2">
                        <button
                            type="submit"
                            disabled={submitting}
                            className="flex-1 bg-indigo-600 text-white font-bold py-2.5 rounded-xl hover:bg-indigo-700 transition disabled:opacity-50"
                        >
                            {submitting ? 'Saving...' : 'Save Changes'}
                        </button>
                        <button
                            type="button"
                            onClick={() => navigate(-1)}
                            className="px-5 border border-slate-300 text-slate-700 font-semibold py-2.5 rounded-xl hover:bg-slate-50"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </Layout>
    );
};