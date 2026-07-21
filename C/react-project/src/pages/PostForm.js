import React, { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { Layout } from '../components/Layout';
import { postService } from '../services/api';

export const PostForm = () => {
    const { id } = useParams();
    const isEdit = Boolean(id);
    const navigate = useNavigate();

    const [description, setDescription] = useState('');
    const [hashtags, setHashtags] = useState('');
    const [image, setImage] = useState(null);
    const [preview, setPreview] = useState('');
    const [errors, setErrors] = useState({});
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        if (isEdit) {
            postService.getPost(id).then((post) => {
                setDescription(post.description);
                setHashtags(post.hashtags?.join(', ') || '');
                setPreview(post.image);
            });
        }
    }, [id, isEdit]);

    const handleImageChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setImage(file);
            setPreview(URL.createObjectURL(file));
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSubmitting(true);
        setErrors({});

        const formData = new FormData();
        formData.append('description', description);
        if (image) formData.append('image', image);

        const tagArray = hashtags.split(',').map((t) => t.trim()).filter(Boolean);
        tagArray.forEach((t) => formData.append('hashtags[]', t));

        try {
            if (isEdit) {
                await postService.updatePost(id, formData);
            } else {
                await postService.createPost(formData);
            }
            navigate('/');
        } catch (err) {
            if (err.response && err.response.data.errors) {
                setErrors(err.response.data.errors);
            } else {
                alert('An unexpected error occurred.');
            }
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <Layout>
            <div className="max-w-xl mx-auto bg-white border rounded-2xl p-6 shadow-sm">
                <h2 className="text-xl font-bold mb-6">{isEdit ? 'Edit Post' : 'Create New Post'}</h2>
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-sm font-semibold mb-1">Image</label>
                        {preview && <img src={preview} alt="Preview" className="w-full h-48 object-cover rounded-xl mb-3" />}
                        <input type="file" accept="image/*" onChange={handleImageChange} className="text-sm w-full" />
                        {errors.image && <p className="text-rose-600 text-xs mt-1">{errors.image[0]}</p>}
                    </div>

                    <div>
                        <label className="block text-sm font-semibold mb-1">Description</label>
                        <textarea
                            rows={4}
                            value={description}
                            onChange={(e) => setDescription(e.target.value)}
                            className="w-full border rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500"
                            placeholder="What's on your mind?"
                        />
                        {errors.description && <p className="text-rose-600 text-xs mt-1">{errors.description[0]}</p>}
                    </div>

                    <div>
                        <label className="block text-sm font-semibold mb-1">Hashtags (comma separated)</label>
                        <input
                            type="text"
                            value={hashtags}
                            onChange={(e) => setHashtags(e.target.value)}
                            placeholder="technology, webdev, react"
                            className="w-full border rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500"
                        />
                    </div>

                    <button
                        type="submit"
                        disabled={submitting}
                        className="w-full bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700 disabled:opacity-50"
                    >
                        {submitting ? 'Saving...' : isEdit ? 'Update Post' : 'Publish Post'}
                    </button>
                </form>
            </div>
        </Layout>
    );
};