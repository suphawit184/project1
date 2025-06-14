const express = require('express');
const cors = require('cors');
const app = express();

app.use(cors());
app.use(express.json());

app.get('/internship/api/test', (req, res) => {
  res.json({ message: 'Connected from Express backend!' });
});

app.listen(3000, () => {
  console.log('Server is running at http://localhost:3000');
});
