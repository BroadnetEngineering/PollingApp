const mysql = require('mysql');

const connection = mysql.createConnection({
	host: 'localhost',
	user: 'root',
	password: 'pollappdb',
	port: '3306',
});

module.exports = connection;
