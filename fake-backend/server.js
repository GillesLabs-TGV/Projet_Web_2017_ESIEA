"use strict";
var express = require('express');

var app = express();

app.get('/extract', function(req, res) {
	console.log("GET request to '/extract'")
	let answer = Math.floor(Math.random() * (4 - 1 + 1)) + 1;
	res.setHeader('Content-Type', 'application/json')
	res.setHeader('Access-Control-Allow-Origin', 'http://localhost:8080')
	res.send(JSON.stringify({
	"extractFilename": "sound.mp3",
	"correctAnswer": answer
}));
});

app.listen(8081);