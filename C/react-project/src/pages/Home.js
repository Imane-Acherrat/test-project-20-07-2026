import React, { useState, useEffect } from 'react';
import { useSearchParams } from 'react-router-dom';
import { Layout } from '../components/Layout';
import { PostCard } from '../components/PostCard';
import { postService } from '../services/api';

export const Home = () => {
    const [searchParams, setSearchParams] = useSearchParams();
    const [posts, setPosts] = useState([]);
    const [pagination, setPagination] = useState({});
    const [loading, setLoading] = useState(true);

    // Filter States
    const search = searchParams.get('search') || '';
    const hashtag = searchParams.get('hashtag') || '';
    const sort = searchParams.get('sort') || 'latest';
    const page = parseInt(searchParams.get('page') || '1', 10);

    
    useEffect(() => {
        const fetchPosts = async () => {
            setLoading(true);
            try {
                const data = await postService.getPosts({ page, limit: 6, search, hashtag, sort });
                setPosts(data.data || []);
                setPagination(data.pagination || {});
            } catch (err) {
                console.error('Failed to load posts', err);
            } finally {
                setLoading(false);
            }
        };
        fetchPosts();
    }, [search, hashtag, sort, page]);

    const updateFilters = (key, value) => {
        const newParams = new URLSearchParams(searchParams);
        if (value) {
            newParams.set(key, value);
        } else {
            newParams.delete(key);
        }
        newParams.set('page', '1');
        setSearchParams(newParams);
    };

    return (
        <Layout onHashtagSelect={(tag) => updateFilters('hashtag', tag)}>
            <div className="space-y-6">
                {/* Controls Header */}
                <div className="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap gap-4 items-center justify-between">
                    <input
                        type="text"
                        placeholder="Search descriptions..."
                        value={search}
                        onChange={(e) => updateFilters('search', e.target.value)}
                        className="px-4 py-2 border rounded-xl w-64 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    />

                    <div className="flex items-center space-x-3">
                        {hashtag && (
                            <span className="bg-indigo-100 text-indigo-700 text-xs px-3 py-1.5 rounded-full font-bold flex items-center space-x-1">
                                <span>#{hashtag}</span>
                                <button onClick={() => updateFilters('hashtag', '')} className="ml-1 hover:text-indigo-900">
                                    ×
                                </button>
                            </span>
                        )}

                        <select
                            value={sort}
                            onChange={(e) => updateFilters('sort', e.target.value)}
                            className="px-3 py-2 border rounded-xl text-sm bg-white font-medium focus:ring-2 focus:ring-indigo-500"
                        >
                            <option value="latest">Latest</option>
                            <option value="oldest">Oldest</option>
                            <option value="popular">Most Popular</option>
                        </select>
                    </div>
                </div>

                {/* Content Area */}
                {loading ? (
                    <div className="text-center py-20 text-slate-400 font-medium">Loading posts...</div>
                ) : posts.length === 0 ? (
                    <div className="text-center py-20 bg-white rounded-2xl border border-dashed border-slate-300">
                        <p className="text-slate-500 font-medium">No posts found matching your criteria.</p>
                    </div>
                ) : (
                    <div className="grid grid-cols-2 gap-6">
                        {posts.map((post) => (
                            <PostCard key={post.id} post={post} />
                        ))}
                    </div>
                )}

                {pagination.totalPages > 1 && (
                    <div className="flex justify-center items-center space-x-4 pt-4">
                        <button
                            disabled={!pagination.hasPreviousPage}
                            onClick={() => updateFilters('page', page - 1)}
                            className="px-4 py-2 border rounded-xl text-sm font-semibold disabled:opacity-40 bg-white"
                        >
                            Previous
                        </button>
                        <span className="text-sm text-slate-600 font-medium">
                            Page {pagination.currentPage} of {pagination.totalPages}
                        </span>
                        <button
                            disabled={!pagination.hasNextPage}
                            onClick={() => updateFilters('page', page + 1)}
                            className="px-4 py-2 border rounded-xl text-sm font-semibold disabled:opacity-40 bg-white"
                        >
                            Next
                        </button>
                    </div>
                )}
            </div>
        </Layout>
    );
};