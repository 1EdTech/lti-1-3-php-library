var c = document.getElementById("breakout");
var ctx = window.c.getContext("2d");

var score = 0;

var ball = {
    pos : {
        x: window.c.width/2+2,
        y : window.c.height/2+2,
    },
    vel : {
        x : 6,
        y : 6,
    },
    r: 10,
    render: function() {
        this.pos.x += this.vel.x;
        this.pos.y += this.vel.y;
        if (this.oob(window.c.width, this.pos.x, this.r)) {
            this.vel.x = -this.vel.x;
        }
        if (this.oob(window.c.height, this.pos.y, this.r)) {
            this.vel.y = -this.vel.y;
        }

        window.ctx.beginPath();
        window.ctx.arc(this.pos.x, this.pos.y, this.r, 0, 2 * Math.PI);
        window.ctx.stroke();
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

};

function brick() {
    this.pos = {
        x: 40,
        y: 40,
    },
    this.hit = 0,
    this.width = 40,
    this.height = 20,
    this.render = function() {
        if (this.hit) {
            return;
        }
        window.ctx.beginPath();
        window.ctx.rect(this.pos.x, this.pos.y, this.width, this.height);
        window.ctx.stroke();
    },
    this.test_hit = function() {
        if (this.hit) {
            return 0;
        }
        var hitx = this.test_hit_x();
        var hity = this.test_hit_y();
        if (!hitx || !hity) {
            return 0;
        }
        if (hity) {
            window.ball.vel.y = -window.ball.vel.y;
            window.ball.pos.y += window.ball.vel.y;
        } else if (hitx) {
            window.ball.vel.x = -window.ball.vel.x;
            window.ball.pos.x += window.ball.vel.x;
        }
        this.hit = 1;
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
var bricks = [];

for (var h = 0; h < 6; h++) {
    for (var w = 0; w < 18; w++) {
        var brickid = (18*h)+w;
        console.log(brickid);
        bricks[brickid] = new brick();
        bricks[brickid].pos.x = 40+(w*40);
        bricks[brickid].pos.y = 40+(h*20);
    }
}

pause = false;

var frame = function() {
    window.ctx.clearRect(0, 0, window.c.width, window.c.height);
    for (var i = 0; i < window.bricks.length; i++) {
        window.bricks[i].render();
        if (window.bricks[i].test_hit()) {
            break;
        }
    }
    window.ball.render();
    if (!pause) {
        requestAnimationFrame(frame);
    }
    pause = false;
}
frame();
