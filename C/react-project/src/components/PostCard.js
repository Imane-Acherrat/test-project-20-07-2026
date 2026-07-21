import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { postService } from '../services/api';

export const PostCard = ({ post }) => {
    const { user } = useAuth();
    const navigate = useNavigate();
    const [likesCount, setLikesCount] = useState(post.likesCount || 0);
    const [isLiked, setIsLiked] = useState(post.isLiked || false);
    const [loading, setLoading] = useState(false);

    const handleLikeToggle = async (e) => {
        e.stopPropagation();
        if (!user) {
            navigate('/login');
            return;
        }
        if (loading) return;

        setLoading(true);
        const prevLikes = likesCount;
        const prevIsLiked = isLiked;

        setIsLiked(!prevIsLiked);
        setLikesCount(prevIsLiked ? prevLikes - 1 : prevLikes + 1);

        try {
            if (prevIsLiked) {
                const res = await postService.unlikePost(post.id);
                setLikesCount(res.likesCount);
                setIsLiked(res.isLiked);
            } else {
                const res = await postService.likePost(post.id);
                setLikesCount(res.likesCount);
                setIsLiked(res.isLiked);
            }
        } catch (err) {
            // Rollback on failure
            setLikesCount(prevLikes);
            setIsLiked(prevIsLiked);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition flex flex-col">
            {/* Creator Info */}
            <div className="p-4 flex items-center space-x-3">
                <Link to={`/users/${post.creator.username}`}>
                    <img
                        src={post.creator.profileImage || 'https://via.placeholder.com/40'}
                        alt={post.creator.name}
                        className="w-10 h-10 rounded-full object-cover border"
                    />
                </Link>
                <div className="flex flex-col">
                    <Link to={`/users/${post.creator.username}`} className="font-bold text-slate-800 text-sm hover:underline">
                        {post.creator.name}
                    </Link>
                    <span className="text-xs text-slate-400">@{post.creator.username} • {new Date(post.createdAt).toLocaleDateString()}</span>
                </div>
            </div>

            {/* Image */}
            <div className="cursor-pointer" onClick={() => navigate(`/posts/${post.id}`)}>
                <img src={post.image} alt="Post content" className="w-full h-64 object-cover" />
            </div>

            {/* Body */}
            <div className="p-4 flex-1 flex flex-col justify-between space-y-3">
                <div>
                    <p className="text-slate-700 text-sm line-clamp-2">{post.description}</p>
                    <div className="flex flex-wrap gap-1 mt-2">
                        {post.hashtags?.map((tag) => (
                            <span key={tag} className="text-xs text-indigo-600 font-semibold bg-indigo-50 px-2 py-0.5 rounded">
                                #{tag}
                            </span>
                        ))}
                    </div>
                </div>

                <div className="flex items-center justify-between border-t border-slate-100 pt-3">
                    <button
                        onClick={handleLikeToggle}
                        className={`flex items-center space-x-1.5 text-sm font-semibold px-3 py-1.5 rounded-lg transition ${isLiked ? 'bg-rose-50 text-rose-600' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                            }`}
                    >
                        <span>{isLiked ? '❤️' : '🤍'}</span>
                        <span>{likesCount}</span>
                    </button>
                    <Link to={`/posts/${post.id}`} className="text-xs font-semibold text-slate-500 hover:text-indigo-600">
                        View Details →
                    </Link>
                </div>
            </div>
        </div>
    );
};