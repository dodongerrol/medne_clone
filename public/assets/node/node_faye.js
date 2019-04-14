 	/*var http = require('http'),
    faye = require('faye');

var bayeux = new faye.NodeAdapter({mount: '/faye', timeout: 45});

bayeux.attach(server);
server.listen(8000);
//bayeux.listen(3000);
*/


var http = require('http'),
    faye = require('faye');

var server = http.createServer(),
    //bayeux = new faye.NodeAdapter({mount: '/'});
    bayeux = new faye.NodeAdapter({mount: '/faye', timeout: 45});

bayeux.attach(server);
server.listen(5000);


/*
var http = require('http'),
    faye = require('faye');

var bayeux = new faye.NodeAdapter({mount: '/faye', timeout: 45});

bayeux.listen(8000);
*/