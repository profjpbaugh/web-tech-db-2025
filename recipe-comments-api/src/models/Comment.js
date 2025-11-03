import mongoose from 'mongoose';

const replySchema = new mongoose.Schema(
  {
    user: { type: String, required: true, trim: true, maxlength: 100 },
    text: { type: String, required: true, trim: true, maxlength: 2000 },
    createdAt: { type: Date, default: Date.now }
  },
  { _id: true }
);

const commentSchema = new mongoose.Schema(
  {
    recipeId: { type: Number, required: true, index: true },
    user: { type: String, required: true, trim: true, maxlength: 100 },
    comment: { type: String, required: true, trim: true, maxlength: 4000 },
    rating: { type: Number, min: 1, max: 5 },
    createdAt: { type: Date, default: Date.now },
    replies: { type: [replySchema], default: [] }
  },
  { versionKey: false }
);

commentSchema.index({ recipeId: 1, createdAt: -1 });

export const Comment = mongoose.model('Comment', commentSchema);
