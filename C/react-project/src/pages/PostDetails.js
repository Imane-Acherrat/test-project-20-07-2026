import React, { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { Layout } from '../components/Layout';
import { useAuth } from '../context/AuthContext';
import { postService } from '../services/api';

export const PostDetails = () => {
    const { id } = useParams();
    const navigate = useNavigate();
    const { user } = useAuth();
    const [post, setPost] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        postService
            .getPost(id)
            .then((data) => {
                // Handle cases where the response is wrapped in { post: {...} } or directly {...}
                const postData = data.post || data;
                setPost(postData);
            })
            .catch((err) => {
                console.error('Failed to fetch post details:', err);
                navigate('/');
            })
            .finally(() => setLoading(false));
    }, [id, navigate]);

    const handleDelete = async () => {
        if (window.confirm('Are you sure you want to delete this post?')) {
            try {
                await postService.deletePost(id);
                navigate('/');
            } catch (err) {
                alert('Failed to delete post.');
            }
        }
    };

    if (loading) {
        return (
            <Layout>
                <div className="text-center py-20 text-slate-500 font-medium">Loading post details...</div>
            </Layout>
        );
    }

    if (!post) return null;

    // Optional chaining prevents reading properties of undefined
    const isOwner = Boolean(user && post?.creator && user.id === post.creator.id);

    return (
        <Layout>
            <div className="max-w-3xl mx-auto bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <img
                    src={post.image || 'https://via.placeholder.com/600x400'}
                    alt="Full view"
                    className="w-full max-h-[450px] object-cover"
                />
                <div className="p-6 space-y-4">
                    <div className="flex items-center justify-between">
                        {/* Optional chaining on post.creator */}
                        <Link
                            to={post.creator?.username ? `/users/${post.creator.username}` : '#'}
                            className="flex items-center space-x-3"
                        >
                            <img
                                src={post.creator?.profileImage || 'https://via.placeholder.com/40'}
                                alt={post.creator?.name || 'Creator'}
                                className="w-12 h-12 rounded-full border object-cover"
                            />
                            <div>
                                <h4 className="font-bold text-slate-800">{post.creator?.name || 'Anonymous'}</h4>
                                <p className="text-xs text-slate-400">
                                    {post.creator?.username ? `@${post.creator.username}` : ''}
                                </p>
                            </div>
                        </Link>

                        {isOwner && (
                            <div className="space-x-2">
                                <button
                                    onClick={() => navigate(`/posts/${post.id}/edit`)}
                                    className="px-3 py-1.5 border border-slate-300 text-sm font-semibold rounded-lg hover:bg-slate-50"
                                >
                                    Edit
                                </button>
                                <button
                                    onClick={handleDelete}
                                    className="px-3 py-1.5 bg-rose-600 text-white text-sm font-semibold rounded-lg hover:bg-rose-700"
                                >
                                    Delete
                                </button>
                            </div>
                        )}
                    </div>

                    <p className="text-slate-800 leading-relaxed">{post.description}</p>

                    <div className="flex flex-wrap gap-2">
                        {post.hashtags?.map((tag) => (
                            <span key={tag} className="text-xs text-indigo-600 font-bold bg-indigo-50 px-2.5 py-1 rounded-md">
                                #{tag}
                            </span>
                        ))}
                    </div>

                    <div className="pt-4 border-t text-xs text-slate-400 flex justify-between">
                        <span>Published on {post.createdAt ? new Date(post.createdAt).toLocaleString() : ''}</span>
                        <span>{post.likesCount || 0} Likes</span>
                    </div>
                </div>
            </div>
        </Layout>
    );
};