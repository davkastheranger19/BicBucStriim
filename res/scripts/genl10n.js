/* eslint-disable no-console */
/* Reads messages.yml and generates JSON files from it */
const read = require('read-yaml');
const fs = require('fs');

console.log(`converting ${process.argv[2]} ...`);
console.log(`storing results in dir ${process.argv[3]} ...`);

var messages = read.sync(process.argv[2]);
var targetDir = process.argv[3];

var generatedMessages = new Map();
var langs = ['de', 'en', 'es', 'fr', 'gl', 'hu', 'it', 'nl','pl'];
langs.map((l) => generatedMessages.set(l, {}));

function mapkv(k) {
	var key = k[0];
	var msgEntries = k[1];
	Object.entries(msgEntries).forEach((k2) => {
		if (!generatedMessages.has(k2[0])) { 
			console.log(`language ${k2[0]} found, but not configured, ignoring ...`);
			return;
		}
		var langEntries = generatedMessages.get(k2[0]);
		langEntries[key] = k2[1];
	});
}

Object.entries(messages).map((k) => mapkv(k));
generatedMessages.forEach((v,k,_) => {
	//const obj = {[k]:v}
	fs.writeFileSync(`${targetDir}/${k}.json`, JSON.stringify(v));
});
console.log('\nGenerated language files for:');
generatedMessages.forEach((v,k,_) => console.log(`${k}: ${Object.entries(v).length} entries`));
