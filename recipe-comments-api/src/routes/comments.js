import { Router } from 'express';
import { Comment } from '../models/Comment.js';

const router = Router();

// GET /api/recipes/:id/comments
router.get('/recipes/:id/comments', async (req, res) => {
  const recipeId = Number(req.params.id);
  if (!Number.isInteger(recipeId) || recipeId <= 0) {
    return res.status(400).json({ error: 'Invalid recipe id' });
  }
  try {
    const docs = await Comment.find({ recipeId })
      .sort({ createdAt: -1 })
      .lean()
      .exec();
    return res.json(docs);
  } catch {
    return res.status(500).json({ error: 'Failed to fetch comments' });
  }
});

// POST /api/recipes/:id/comments
// Body: { "user": "abrown", "comment": "Loved this!", "rating": 5 }
router.post('/recipes/:id/comments', async (req, res) => {
  const recipeId = Number(req.params.id);
  const { user, comment, rating } = req.body || {};

  if (!Number.isInteger(recipeId) || recipeId <= 0) {
    return res.status(400).json({ error: 'Invalid recipe id' });
  }
  if (!user || !comment) {
    return res.status(400).json({ error: 'Missing user or comment' });
  }

  try {
    const doc = await Comment.create({ recipeId, user, comment, rating });
    return res.status(201).json(doc);
  } catch (err) {
    return res.status(422).json({ error: 'Validation failed', details: err.message });
  }
});

// POST /api/comments/:commentId/replies
// Body: { "user": "jdoe", "text": "Same here!" }
router.post('/comments/:commentId/replies', async (req, res) => {
  const { commentId } = req.params;
  const { user, text } = req.body || {};

  if (!user || !text) {
    return res.status(400).json({ error: 'Missing user or text' });
  }

  try {
    const result = await Comment.findByIdAndUpdate(
      commentId,
      { $push: { replies: { user, text } } },
      { new: true, runValidators: true }
    ).lean();

    if (!result) {
      return res.status(404).json({ error: 'Comment not found' });
    }
    return res.status(201).json(result);
  } catch (err) {
    return res.status(422).json({ error: 'Validation failed', details: err.message });
  }
});

export default router;
