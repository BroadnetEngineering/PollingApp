const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const app = express();
const port = 4000;

const db = require('./db');

db.connect((err) => {
	if (err) {
		console.error('Database connection failed: ' + err.stack);
		return;
	}
	console.log('Connected to database.');
});
// enable CORS for development
app.use(cors());

// parse application/x-www-form-urlencoded
app.use(bodyParser.urlencoded({ extended: false }));

// parse application/json
app.use(bodyParser.json());

app.get('/polls', (req, res) => {
	db.query(
		`SELECT polldb.answers.*, polldb.polls.question from polldb.answers
    INNER JOIN polldb.polls ON polldb.answers.poll_id = polldb.polls.poll_id;
    `,
		(err, data) => {
			if (err) throw err;
			res.json(data);
		}
	);
});

app.post('/newPoll', (req, res) => {
	const question = req.body.question;

	const answers = req.body.answers;
	const query = `INSERT INTO polldb.polls (question) values("${question}")`;
	db.query(query, (err, data) => {
		if (err) throw err;
		answers.map((answer) => {
			db.query(
				`INSERT INTO polldb.answers (text, poll_id) values("${answer.text}", ${data.insertId})`,
				(err, data) => {
					if (err) throw err;
				}
			);
		});
		res.json({ poll_id: data.insertId });
	});
});

app.post('/editPoll', async (req, res) => {
	const question = req.body.question;
	const poll_id = req.body.poll_id;
	const answers = req.body.answers;
	const newAnswers = req.body.newAnswers;

	let currentAnswerIDs = [];

	db.query(
		`SELECT answer_id from polldb.answers where poll_id = ${poll_id}`,
		(err, data) => {
			data.forEach((d) => currentAnswerIDs.push(d.answer_id));

			const pollQuery = `UPDATE polldb.polls SET question = "${question}" WHERE poll_id = ${poll_id};`;

			db.query(pollQuery, (err, data) => {
				if (err) throw err;
			});

			answers.map((answer) => {
				const answerQuery = `UPDATE polldb.answers SET text = "${answer.text}" WHERE answer_id = ${answer.answer_id};`;

				db.query(answerQuery, (err, data) => {
					if (err) {
						console.log(answerQuery);
						throw err;
					}
				});
				currentAnswerIDs = currentAnswerIDs.filter(
					(id) => id != answer.answer_id
				);
			});

			newAnswers.map((answer) => {
				const answerQuery = `INSERT INTO polldb.answers (text, poll_id) values("${answer.text}", ${answer.poll_id})`;
				db.query(answerQuery, (err, data) => {
					if (err) {
						console.log(answerQuery);
						throw err;
					}
				});
			});

			currentAnswerIDs.map((id) => {
				db.query(`DELETE from polldb.answers WHERE answer_id=${id}`);
			});
            res.json({ status: 200 })
		}
	);
});

app.post('/undoVote', (req, res) => {
	db.query(
		`UPDATE polldb.answers SET votes = votes - 1 WHERE answer_id=${req.body.answer_id}`,
		(err, data) => {
			if (err) throw err;
		}
	);
});

app.post('/vote', (req, res) => {
	db.query(
		`UPDATE polldb.answers SET votes = votes + 1 WHERE answer_id=${req.body.answer_id}`,
		(err, data) => {
			if (err) throw err;
			db.query(
				`SELECT * FROM polldb.answers WHERE poll_id=(SELECT poll_id FROM polldb.answers WHERE answer_id = ${req.body.answer_id})`,
				(err, data) => {
					console.log(data);
					if (err) throw err;
					res.json(data);
				}
			);
		}
	);
});

app.post('/deletePoll', (req, res) => {
	db.query(
		`DELETE FROM polldb.answers WHERE poll_id=${req.body.poll_id}`,
		(err, data) => {
			if (err) throw err;
		}
	);
	db.query(
		`DELETE FROM polldb.polls WHERE poll_id=${req.body.poll_id}`,
		(err, data) => {
			if (err) throw err;
			res.json({ status: 200 });
		}
	);
});

app.listen(port, () =>
	console.log(`Example app listening at http://localhost:${port}`)
);
