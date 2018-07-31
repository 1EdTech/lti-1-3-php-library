var c = document.getElementById("breakout");
var ctx = window.c.getContext("2d");

var bgctx = document.getElementById("breakoutbg").getContext("2d");
bgctx.beginPath();
var bggrad = ctx.createLinearGradient(0,0,0,c.height);
bggrad.addColorStop(0,"rgb(157, 208, 239)");
bggrad.addColorStop(0.8,"rgb(0, 116, 188)");
bggrad.addColorStop(1,"rgb(0, 46, 112)");
bgctx.fillStyle = bggrad;
bgctx.rect(0, 0, c.width, c.height);
bgctx.fill();

var score = 0;

var ball = {
    pos : {
        x: window.c.width/2-200,
        y : window.c.height/2-2,
    },
    vel : {
        x : 6,
        y : 6,
    },
    r: 10,
    rot: 0,
    velr: 0,
    render: function() {
        this.pos.x += this.vel.x;
        this.pos.y += this.vel.y;
        if (this.oob(window.c.width, this.pos.x, this.r)) {
            this.vel.x = -this.vel.x;
            this.pos.x += this.vel.x;
            this.pos.y -= this.vel.y;
        }
        if (this.oob(window.c.height, this.pos.y, this.r)) {
            if (this.pos.y > window.c.height - this.r) {
                window.endGame();
            }
            this.vel.y = -this.vel.y;
            this.pos.y += this.vel.y;
            this.pos.x -= this.vel.x;
        }

        window.ctx.save();
        window.ctx.beginPath();
        var gradient = ctx.createRadialGradient(this.pos.x, this.pos.y, 2, this.pos.x, this.pos.y, 10);
        window.ctx.fillStyle = "rgb(110, 32, 22)";
        window.ctx.strokeStyle = "rgb(110, 32, 22)";
        window.ctx.setLineDash([5,2,4,6,1]);
        window.ctx.lineWidth = 4;
        window.ctx.translate(this.pos.x, this.pos.y);
        window.ctx.rotate(this.rot * Math.PI);
        window.ctx.arc(0, 0, this.r, 0, 2 * Math.PI);
        if (this.vel.x > 0) {
            this.velr = 0.01;
        } else if (this.vel.x < 0) {
            this.velr = -0.01;
        } else {
            this.velr = 0;
        }
        this.rot += this.velr;
        window.ctx.fill();
        window.ctx.stroke();
        window.ctx.restore();

    },
    oob : function(max, curr, offset) {
        if (curr < offset || curr > (max-offset)) {
            return true;
        }
    },
    left : function() {
        return this.pos.x - this.r;
    },
    right : function() {
        return this.pos.x + this.r;
    },
    top : function() {
        return this.pos.y - this.r;
    },
    bottom : function() {
        return this.pos.y + this.r;
    },
};

var paddle = {
    pos : {
        x: window.c.width/2+2,
        y : window.c.height-40,
    },
    width : 80,
    height : 20,
    render: function() {
        window.ctx.beginPath();
        var gradient = ctx.createLinearGradient(this.pos.x,this.pos.y,this.pos.x,this.pos.y + this.height);
        gradient.addColorStop(0,"#999999");
        gradient.addColorStop(0.7,"#eeeeee");
        gradient.addColorStop(1,"#999999");
        ctx.fillStyle = gradient;
        var hh = this.height/2;
        window.ctx.rect(this.pos.x + hh, this.pos.y, this.width - this.height, this.height);
        window.ctx.arc(this.pos.x + hh, this.pos.y + hh, hh, 0.5 * Math.PI, 1.5 * Math.PI);
        window.ctx.arc(this.pos.x + this.width - hh, this.pos.y + hh, hh, 1.5 * Math.PI, 0.5 * Math.PI);
        window.ctx.fill();
        window.ctx.stroke();
    },
    left : function() {
        return this.pos.x;
    },
    right : function() {
        return this.pos.x + this.width;
    },
    top : function() {
        return this.pos.y;
    },
    bottom : function() {
        return this.pos.y + this.height;
    },
    test_hit : function() {
        var hitx = this.test_hit_x();
        var hity = this.test_hit_y();
        if (!hitx || !hity) {
            return 0;
        }
        if (hity) {
            window.ball.vel.y = -Math.abs(window.ball.vel.y);
            window.ball.pos.y += window.ball.vel.y;
            window.ball.pos.x -= window.ball.vel.x;
        }
        if (hitx) {
            var xdiff = window.ball.pos.x - (this.pos.x + (this.width/2));
            window.ball.vel.x = Math.ceil(xdiff / 5);
            window.ball.pos.x += window.ball.vel.x;
        }
        return 1;
    },
    test_hit_x : function() {
        if (this.left() > window.ball.right()) {
            return 0;
        }
        if (this.right() < window.ball.left()) {
            return 0;
        }
        return 1;
    },
    test_hit_y : function() {
        if (this.top() > window.ball.bottom()) {
            return 0;
        }
        if (this.bottom() < window.ball.top()) {
            return 0;
        }
        return 1;
    },
    move : function() {
        if (window.press_left) {
            if (this.pos.x > 0) {
                this.pos.x -= 8;
            }
        }
        if (window.press_right) {
            if (this.pos.x < window.c.width - this.width) {
                this.pos.x += 8;
            }
        }
    }
};

function fire() {
    this.r = 0,
    this.a = 0,
    this.render = function() {
        if (this.a < 0.2) {
            this.reset();
        }

        this.pos.x += this.vel.x + (Math.random() * 2) - 1;
        this.pos.y += this.vel.y + (Math.random() * 2) - 1;

        this.r *= 0.95;
        this.a *= 0.95;

        window.ctx.beginPath();
        window.ctx.fillStyle = 'rgba(239, ' + this.green + ', 66,'+ this.a +')';
        window.ctx.arc(this.pos.x, this.pos.y, this.r, 0, 2 * Math.PI);
        window.ctx.fill();

        if (this.green < 232) {
            this.green += 8;
        }
    },
    this.reset = function() {
        this.pos = {
            x: window.ball.pos.x,
            y : window.ball.pos.y,
        };
        this.vel = {
            x : (Math.random() * 4) - 2,
            y : (Math.random() * 4) - 2,
        };
        this.r = (Math.random() * 8) + 1;
        this.a = 0.8;
        this.green = 62;
    }
}

function brick() {
    this.id = 0,
    this.pos = {
        x: 40,
        y: 40,
    },
    this.vely = 0,
    this.rot = 0,
    this.velr = 0,
    this.hit = false,
    this.last_hitx = false,
    this.last_hity = false,
    this.width = 40,
    this.height = 20,
    this.render = function() {
        if (this.hit) {
            if (this.pos.y > window.c.height + 60) {
                return;
            }
            this.vely++;
            this.pos.y += this.vely;
            ctx.save()
            window.ctx.beginPath();
            this.rot += this.velr;
            ctx.translate(this.pos.x + (this.width/2), this.pos.y + (this.height/2));
            ctx.rotate(this.rot * Math.PI);
            var gradient = ctx.createRadialGradient(-(this.width/2) +10, -(this.height/2) +5, 0, -(this.width/2) + 40, -(this.height/2) + 15, 40);
            gradient.addColorStop(0, 'rgba(137, 211, 234, 0.2)');
            gradient.addColorStop(1, 'rgba(137, 211, 234, 1)');
            ctx.strokeStyle = 'rgba(254, 254, 254, 0.8)';
            ctx.fillStyle = gradient;
            window.ctx.rect(-(this.width/2), -(this.height/2), this.width, this.height);
            window.ctx.fill();
            window.ctx.stroke();
            ctx.restore();
            return;
        }
            window.ctx.beginPath();
            var gradient = ctx.createRadialGradient(this.pos.x + 10, this.pos.y + 5, 0, this.pos.x + 40, this.pos.y + 15, 40);
            gradient.addColorStop(0, 'rgba(137, 211, 234, 0.2)');
            gradient.addColorStop(1, 'rgba(137, 211, 234, 1)');
            ctx.strokeStyle = 'rgba(254, 254, 254, 0.8)';
            ctx.fillStyle = gradient;
            window.ctx.rect(this.pos.x, this.pos.y, this.width, this.height);
            window.ctx.fill();
            window.ctx.stroke();
    },
    this.test_hit = function() {
        if (this.hit) {
            return 0;
        }
        var hitx = this.test_hit_x();
        var hity = this.test_hit_y();
        if (!hitx || !hity) {
            this.last_hitx = hitx;
            this.last_hity = hity;
            return 0;
        }
        if (this.last_hity) {
            window.ball.vel.y = -window.ball.vel.y;
            window.ball.pos.y += window.ball.vel.y;
            window.ball.pos.x -= window.ball.vel.x;
        }
        if (this.last_hitx) {
            window.ball.vel.x = -window.ball.vel.x;
            window.ball.pos.x += window.ball.vel.x;
            window.ball.pos.y -= window.ball.vel.y;
        }
        if (!this.last_hity && this.last_hitx) {
            window.ball.vel.x = -window.ball.vel.x;
            window.ball.pos.x += window.ball.vel.x;
            window.ball.vel.y = -window.ball.vel.y;
            window.ball.pos.y += window.ball.vel.y;
        }
        this.last_hitx = hitx;
        this.last_hity = hity;
        this.hit = true;
        this.velr = (Math.random() * 0.04) - 0.02;
        window.score++;
        return 1;
    },
    this.test_hit_x = function() {
        if (this.left() > window.ball.right()) {
            return 0;
        }
        if (this.right() < window.ball.left()) {
            return 0;
        }
        return 1;
    },
    this.test_hit_y = function() {
        if (this.top() > window.ball.bottom()) {
            return 0;
        }
        if (this.bottom() < window.ball.top()) {
            return 0;
        }
        return 1;
    },


    this.left = function() {
        return this.pos.x;
    },
    this.right = function() {
        return this.pos.x + this.width;
    },
    this.top = function() {
        return this.pos.y;
    },
    this.bottom = function() {
        return this.pos.y + this.height;
    }
};

press_left = false;
press_right = false;

document.addEventListener('keydown', (event) => {
    const keyName = event.key;
    if (keyName == "ArrowLeft") {
        press_left = true;
    }
    if (keyName == "ArrowRight") {
        press_right = true;
    }
  });

document.addEventListener('keyup', (event) => {
    const keyName = event.key;
    if (keyName == "ArrowLeft") {
        press_left = false;
    }
    if (keyName == "ArrowRight") {
        press_right = false;
    }
});
var bricks = [];

for (var h = 0; h < 6; h++) {
    for (var w = 0; w < 18; w++) {
        var brickid = (18*h)+w;
        bricks[brickid] = new brick();
        bricks[brickid].pos.x = 40+(w*40);
        bricks[brickid].pos.y = 40+(h*20);
        bricks[brickid].id = brickid;
    }
}
var fires = [];

for (var i = 0; i < 80; i++) {
    fires[i] = new fire();
}
startFireCount = 1;

pause = false;

var frame = function() {
    if (window.score >= window.bricks.length) {
        endGame();
    }
    window.ctx.clearRect(0, 0, window.c.width, window.c.height);
    for (var i = 0; i < window.bricks.length; i++) {
        window.bricks[i].render();
    }
    for (var i = 0; i < window.bricks.length; i++) {
        if (window.bricks[i].test_hit()) {
            break;
        }
    }
    for (var i = 0; i < window.fires.length && i < startFireCount; i++) {
        window.fires[i].render();
    }
    if (startFireCount <= window.fires.length) {
        startFireCount++;
    }
    window.paddle.move();
    window.paddle.render();
    window.paddle.test_hit();
    window.ball.render();

    if (!pause) {
        requestAnimationFrame(frame);
    }
    pause = false;
}

start_time = Math.floor(Date.now() / 1000);
frame();

var endGame = function() {
    window.pause = true;
    var xhttp = new XMLHttpRequest();
    xhttp.open("GET", "score.php?grade=" + window.score, true);
    xhttp.send();
    var xhttp = new XMLHttpRequest();
    var time_taken = Math.floor(Date.now() / 1000) - start_time;
    xhttp.open("GET", "time.php?time=" + time_taken, true);
    xhttp.send();
    window.getScoreBoard();
}

var getScoreBoard = function() {
    var xhttp = new XMLHttpRequest();
    xhttp.addEventListener("load", resultsListner);
    xhttp.open("GET", "memberships.php", true);
    xhttp.send();
}

var resultsListner = function() {
    var scores = JSON.parse(this.responseText);
    console.log(scores);
    var output = '';
    for (var i = 0; i < scores.length; i++) {
        output += '<tr><td>' + scores[i].score + '</td><td>' + scores[i].name + '</td></tr>';
    }
    document.getElementById("leadertable").innerHTML = output;
}

getScoreBoard();
