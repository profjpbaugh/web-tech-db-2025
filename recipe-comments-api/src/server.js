import 'dotenv/config';
import express from 'express';
import cors from 'cors';
import { connectToMongo } from './db.js';
import commentsRouter from './routes/comments.js';

const app = express();
app.use(cors());
app.use(express.json());

app.get('/health', (req, res) => res.json({ ok: true }));

app.use('/api', commentsRouter);

const port = process.env.PORT || 3000;
const url = process.env.MONGO_URL;
const dbName = process.env.MONGO_DB;

connectToMongo(url, dbName)
  .then(() => {
    app.listen(port, () => {
      console.log(`API listening on http://localhost:${port}`);
    });
  })
  .catch((err) => {
    console.error('Failed to connect to Mongo:', err.message);
    process.exit(1);
  });
