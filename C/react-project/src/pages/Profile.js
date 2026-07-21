import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { Layout } from '../components/Layout';
import { PostCard } from '../components/PostCard';
import { useAuth } from '../context/AuthContext';
import { profileService } from '../services/api';

export const Profile = () => {
    const { username } = useParams();
    const { user } = useAuth();
    const [profile, setProfile] = useState(null);
    const [posts, setPosts] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        setLoading(true);
        Promise.all([
            profileService.getPublicProfile(username),
            profileService.getCreatorPosts(username, { page: 1, limit: 10 }),
        ])
            .then(([profData, postsData]) => {
                setProfile(profData);
                setPosts(postsData.data || []);
            })
            .catch((err) => console.error(err))
            .finally(() => setLoading(false));
    }, [username]);

    if (loading) return <Layout><div className="text-center py-20">Loading profile...</div></Layout>;
    if (!profile) return <Layout><div className="text-center py-20">User not found.</div></Layout>;

    // Check if viewing logged-in user's profile
    const isOwnProfile = user && user.id === profile.id;

    return (
        <Layout>
            <div className="space-y-6">
                {/* Profile Header */}
                <div className="bg-white border rounded-2xl p-6 shadow-sm flex items-center justify-between">
                    <div className="flex items-center space-x-6">
                        <img
                            src={profile.profileImage || 'https://via.placeholder.com/100'}
                            alt={profile.name}
                            className="w-24 h-24 rounded-full object-cover border"
                        />
                        <div className="space-y-2">
                            <h2 className="text-2xl font-black text-slate-900">{profile.name}</h2>
                            <p className="text-sm font-semibold text-slate-400">@{profile.username}</p>
                            <p className="text-sm text-slate-600">{profile.bio || 'No bio provided.'}</p>
                            <div className="flex space-x-6 text-sm pt-2">
                                <span><strong>{profile.postsCount || 0}</strong> Posts</span>
                                <span><strong>{profile.likesReceived || 0}</strong> Total Likes</span>
                            </div>
                        </div>
                    </div>

                    {/* Edit Profile button */}
                    {isOwnProfile && (
                        <Link
                            to="/profile/edit"
                            className="px-4 py-2 border border-slate-300 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 transition"
                        >
                            Edit Profile
                        </Link>
                    )}
                </div>

                {/* User Posts Grid */}
                <h3 className="text-lg font-bold text-slate-800">Posts by {profile.name}</h3>
                {posts.length === 0 ? (
                    <p className="text-slate-400 text-sm">No posts published yet.</p>
                ) : (
                    <div className="grid grid-cols-2 gap-6">
                        {posts.map((post) => (
                            <PostCard key={post.id} post={{ ...post, creator: profile }} />
                        ))}
                    </div>
                )}
            </div>
        </Layout>
    );
};